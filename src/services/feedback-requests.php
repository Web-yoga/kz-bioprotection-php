<?php

declare(strict_types=1);

function logFeedbackRequestEvent(string $event, array $context = []): void
{
	$normalizedContext = [];
	foreach ($context as $key => $value) {
		if (!is_string($key) || $key === '') {
			continue;
		}
		if (is_scalar($value) || $value === null) {
			$normalizedContext[$key] = $value;
		}
	}

	$contextPayload = $normalizedContext === []
		? ''
		: ' ' . json_encode($normalizedContext, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
	error_log('[feedback-requests] ' . $event . $contextPayload);
}

function getFeedbackRequesterIp(): string
{
	if (isset($_SERVER['HTTP_CF_CONNECTING_IP']) && is_string($_SERVER['HTTP_CF_CONNECTING_IP'])) {
		$ip = trim($_SERVER['HTTP_CF_CONNECTING_IP']);
		if ($ip !== '') {
			return $ip;
		}
	}

	if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && is_string($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$forwardedList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
		$candidate = trim((string) ($forwardedList[0] ?? ''));
		if ($candidate !== '') {
			return $candidate;
		}
	}

	if (isset($_SERVER['REMOTE_ADDR']) && is_string($_SERVER['REMOTE_ADDR'])) {
		$ip = trim($_SERVER['REMOTE_ADDR']);
		if ($ip !== '') {
			return $ip;
		}
	}

	return 'unknown';
}

function isFeedbackRateLimitExceeded(int $maxRequests = 3, int $windowSeconds = 600): bool
{
	$ip = getFeedbackRequesterIp();
	$storageDir = rtrim(STORAGE_PATH, '/\\') . DIRECTORY_SEPARATOR . 'rate-limits';
	if (!is_dir($storageDir) && !mkdir($storageDir, 0775, true) && !is_dir($storageDir)) {
		logFeedbackRequestEvent('rate_limit_storage_unavailable', ['ip' => $ip]);
		return false;
	}

	$storageFile = $storageDir . DIRECTORY_SEPARATOR . 'feedback-' . hash('sha256', $ip) . '.json';
	$now = time();
	$windowStart = $now - $windowSeconds;
	$timestamps = [];

	if (is_file($storageFile)) {
		$raw = @file_get_contents($storageFile);
		$decoded = is_string($raw) ? json_decode($raw, true) : null;
		if (is_array($decoded)) {
			foreach ($decoded as $entry) {
				if (is_int($entry) && $entry >= $windowStart && $entry <= $now) {
					$timestamps[] = $entry;
				}
			}
		}
	}

	if (count($timestamps) >= $maxRequests) {
		@file_put_contents($storageFile, json_encode($timestamps), LOCK_EX);
		logFeedbackRequestEvent('blocked_rate_limit', ['ip' => $ip, 'window' => $windowSeconds, 'limit' => $maxRequests]);
		return true;
	}

	$timestamps[] = $now;
	@file_put_contents($storageFile, json_encode($timestamps), LOCK_EX);
	return false;
}

function passesFeedbackHoneypot(): bool
{
	$honeypotValue = isset($_POST['website']) && is_string($_POST['website']) ? trim($_POST['website']) : '';
	if ($honeypotValue !== '') {
		logFeedbackRequestEvent('blocked_honeypot', ['ip' => getFeedbackRequesterIp()]);
		return false;
	}

	return true;
}

function passesFeedbackMinimumFillTime(int $minSeconds = 4, int $maxSeconds = 86400): bool
{
	$submittedAt = isset($_POST['submitted_at']) && is_string($_POST['submitted_at'])
		? (int) trim($_POST['submitted_at'])
		: 0;

	if ($submittedAt <= 0) {
		logFeedbackRequestEvent('blocked_missing_submitted_at', ['ip' => getFeedbackRequesterIp()]);
		return false;
	}

	$age = time() - $submittedAt;
	if ($age < $minSeconds || $age > $maxSeconds) {
		logFeedbackRequestEvent('blocked_invalid_submit_time', ['ip' => getFeedbackRequesterIp(), 'age' => $age]);
		return false;
	}

	return true;
}

function resolveFeedbackRequestLocale(string $fallbackLanguage): string
{
	$supportedLanguages = ['ru', 'en', 'kz'];
	$submittedLocale = isset($_POST['locale']) && is_string($_POST['locale'])
		? trim($_POST['locale'])
		: '';

	if ($submittedLocale !== '' && in_array($submittedLocale, $supportedLanguages, true)) {
		return $submittedLocale;
	}

	return in_array($fallbackLanguage, $supportedLanguages, true) ? $fallbackLanguage : 'en';
}

function normalizeFeedbackRequestTextField(string $fieldName, int $maxLength = 1000): string
{
	if (!isset($_POST[$fieldName]) || !is_string($_POST[$fieldName])) {
		return '';
	}

	$value = trim($_POST[$fieldName]);
	if ($value === '') {
		return '';
	}

	if (function_exists('mb_substr')) {
		return mb_substr($value, 0, $maxLength);
	}

	return substr($value, 0, $maxLength);
}

function validateFeedbackTechnicalFile(): array
{
	if (!isset($_FILES['technical']) || !is_array($_FILES['technical'])) {
		return ['is_valid' => true, 'error' => ''];
	}

	$file = $_FILES['technical'];
	$errorCode = isset($file['error']) ? (int) $file['error'] : UPLOAD_ERR_NO_FILE;
	if ($errorCode === UPLOAD_ERR_NO_FILE) {
		return ['is_valid' => true, 'error' => ''];
	}

	if ($errorCode !== UPLOAD_ERR_OK) {
		return ['is_valid' => false, 'error' => 'upload_error_' . $errorCode];
	}

	$fileSize = isset($file['size']) ? (int) $file['size'] : 0;
	$maxBytes = 10 * 1024 * 1024;
	if ($fileSize <= 0 || $fileSize > $maxBytes) {
		return ['is_valid' => false, 'error' => 'file_size_invalid'];
	}

	$originalName = isset($file['name']) && is_string($file['name']) ? $file['name'] : '';
	$extension = strtolower((string) pathinfo($originalName, PATHINFO_EXTENSION));
	$extension = preg_replace('/[^a-z0-9]/', '', $extension) ?? '';
	$allowedExtensions = [
		'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'rtf',
		'jpg', 'jpeg', 'png', 'webp',
		'zip',
	];
	if ($extension === '' || !in_array($extension, $allowedExtensions, true)) {
		return ['is_valid' => false, 'error' => 'file_extension_not_allowed'];
	}

	$tmpName = isset($file['tmp_name']) && is_string($file['tmp_name']) ? $file['tmp_name'] : '';
	if ($tmpName === '' || !is_uploaded_file($tmpName)) {
		return ['is_valid' => false, 'error' => 'file_not_uploaded'];
	}

	if (function_exists('finfo_open')) {
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$detectedMime = $finfo ? (string) finfo_file($finfo, $tmpName) : '';
		if ($finfo) {
			finfo_close($finfo);
		}

		$allowedMimes = [
			'application/pdf',
			'application/msword',
			'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			'application/vnd.ms-excel',
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'text/plain',
			'application/rtf',
			'text/rtf',
			'image/jpeg',
			'image/png',
			'image/webp',
			'application/zip',
			'application/x-zip-compressed',
			'application/octet-stream',
		];
		if ($detectedMime !== '' && !in_array($detectedMime, $allowedMimes, true)) {
			return ['is_valid' => false, 'error' => 'file_mime_not_allowed'];
		}
	}

	return ['is_valid' => true, 'error' => ''];
}

function buildUploadedRequestFileUrl(string $relativePath): string
{
	$relative = '/' . ltrim(str_replace('\\', '/', $relativePath), '/');
	$host = isset($_SERVER['HTTP_HOST']) && is_string($_SERVER['HTTP_HOST']) ? trim($_SERVER['HTTP_HOST']) : '';
	if ($host === '') {
		return $relative;
	}

	$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
		|| (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443);
	$scheme = $isHttps ? 'https' : 'http';

	return $scheme . '://' . $host . $relative;
}

function storeFeedbackRequestTechnicalFile(): array
{
	if (!isset($_FILES['technical']) || !is_array($_FILES['technical'])) {
		return ['url' => '', 'error' => ''];
	}

	$file = $_FILES['technical'];
	$errorCode = isset($file['error']) ? (int) $file['error'] : UPLOAD_ERR_NO_FILE;
	if ($errorCode === UPLOAD_ERR_NO_FILE) {
		return ['url' => '', 'error' => ''];
	}

	if ($errorCode !== UPLOAD_ERR_OK) {
		return ['url' => '', 'error' => 'upload_error_' . $errorCode];
	}

	$tmpFile = isset($file['tmp_name']) && is_string($file['tmp_name']) ? $file['tmp_name'] : '';
	if ($tmpFile === '' || !is_uploaded_file($tmpFile)) {
		return ['url' => '', 'error' => 'file_not_uploaded'];
	}

	$originalName = isset($file['name']) && is_string($file['name']) ? $file['name'] : 'attachment';
	$extension = strtolower((string) pathinfo($originalName, PATHINFO_EXTENSION));
	$extension = preg_replace('/[^a-z0-9]/', '', $extension) ?? '';
	if ($extension === '') {
		$extension = 'bin';
	}

	$uploadRelativeDir = 'uploads/requests/' . date('Y/m');
	$uploadAbsoluteDir = rtrim(PUBLIC_PATH, '/\\') . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $uploadRelativeDir);
	if (!is_dir($uploadAbsoluteDir) && !mkdir($uploadAbsoluteDir, 0775, true) && !is_dir($uploadAbsoluteDir)) {
		return ['url' => '', 'error' => 'upload_dir_unavailable'];
	}

	try {
		$fileSuffix = bin2hex(random_bytes(6));
	} catch (Throwable) {
		$fileSuffix = (string) mt_rand(100000, 999999);
	}

	$fileName = 'request-' . date('Ymd-His') . '-' . $fileSuffix . '.' . $extension;
	$destinationAbsolutePath = $uploadAbsoluteDir . DIRECTORY_SEPARATOR . $fileName;
	if (!move_uploaded_file($tmpFile, $destinationAbsolutePath)) {
		return ['url' => '', 'error' => 'move_uploaded_file_failed'];
	}

	$relativePath = $uploadRelativeDir . '/' . $fileName;
	return ['url' => buildUploadedRequestFileUrl($relativePath), 'error' => ''];
}

function buildCockpitRequestEndpoint(): string
{
	$apiBaseUrl = trim((string) getenv('COCKPIT_API_BASE_URL'));
	if ($apiBaseUrl === '' && defined('API_BASE_URL') && is_string(API_BASE_URL)) {
		$apiBaseUrl = trim(API_BASE_URL);
	}

	if ($apiBaseUrl === '') {
		return '';
	}

	return rtrim($apiBaseUrl, '/') . '/api/content/item/requests';
}

function submitFeedbackRequestToCockpit(array $payload): bool
{
	$endpoint = buildCockpitRequestEndpoint();
	if ($endpoint === '') {
		return false;
	}

	$apiKey = trim((string) getenv('COCKPIT_API_KEY'));
	if ($apiKey !== '') {
		$separator = str_contains($endpoint, '?') ? '&' : '?';
		$endpoint .= $separator . 'token=' . rawurlencode($apiKey);
	}

	$jsonBody = json_encode(['data' => $payload], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
	if (!is_string($jsonBody) || $jsonBody === '') {
		return false;
	}

	$headers = ['Content-Type: application/json', 'Accept: application/json'];
	if ($apiKey !== '') {
		$headers[] = 'api-key: ' . $apiKey;
		$headers[] = 'Authorization: Bearer ' . $apiKey;
	}

	if (function_exists('curl_init')) {
		$curlHandle = curl_init($endpoint);
		if ($curlHandle === false) {
			return false;
		}

		curl_setopt_array($curlHandle, [
			CURLOPT_POST => true,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => $headers,
			CURLOPT_POSTFIELDS => $jsonBody,
			CURLOPT_CONNECTTIMEOUT => 5,
			CURLOPT_TIMEOUT => 15,
		]);

		curl_exec($curlHandle);
		$httpCode = (int) curl_getinfo($curlHandle, CURLINFO_RESPONSE_CODE);
		curl_close($curlHandle);

		return $httpCode >= 200 && $httpCode < 300;
	}

	$context = stream_context_create([
		'http' => [
			'method' => 'POST',
			'header' => implode("\r\n", $headers),
			'content' => $jsonBody,
			'timeout' => 15,
			'ignore_errors' => true,
		],
	]);
	$response = @file_get_contents($endpoint, false, $context);

	if ($response === false || !isset($http_response_header) || !is_array($http_response_header)) {
		return false;
	}

	foreach ($http_response_header as $headerLine) {
		if (preg_match('/^HTTP\/\d+\.\d+\s+(\d+)/', $headerLine, $matches) === 1) {
			$httpCode = (int) $matches[1];
			return $httpCode >= 200 && $httpCode < 300;
		}
	}

	return false;
}

function processFeedbackRequestFormSubmission(string $fallbackLanguage): bool
{
	if (!passesFeedbackHoneypot()) {
		return false;
	}

	if (!passesFeedbackMinimumFillTime()) {
		return false;
	}

	if (isFeedbackRateLimitExceeded()) {
		return false;
	}

	$fileValidation = validateFeedbackTechnicalFile();
	if (!isset($fileValidation['is_valid']) || $fileValidation['is_valid'] !== true) {
		logFeedbackRequestEvent('blocked_invalid_file', [
			'ip' => getFeedbackRequesterIp(),
			'reason' => is_string($fileValidation['error'] ?? null) ? $fileValidation['error'] : 'unknown',
		]);
		return false;
	}

	$payload = [
		'locale' => resolveFeedbackRequestLocale($fallbackLanguage),
		'company' => normalizeFeedbackRequestTextField('company'),
		'person' => normalizeFeedbackRequestTextField('person'),
		'product' => normalizeFeedbackRequestTextField('product', 3000),
		'contact' => normalizeFeedbackRequestTextField('contact', 3000),
		'technical' => '',
	];

	$storedTechnicalFile = storeFeedbackRequestTechnicalFile();
	$technicalFileUrl = is_array($storedTechnicalFile) && isset($storedTechnicalFile['url']) && is_string($storedTechnicalFile['url'])
		? $storedTechnicalFile['url']
		: '';
	$technicalStoreError = is_array($storedTechnicalFile) && isset($storedTechnicalFile['error']) && is_string($storedTechnicalFile['error'])
		? $storedTechnicalFile['error']
		: '';

	if ($technicalStoreError !== '') {
		logFeedbackRequestEvent('technical_upload_failed', ['ip' => getFeedbackRequesterIp(), 'reason' => $technicalStoreError]);
		return false;
	}

	if ($technicalFileUrl !== '') {
		$payload['technical'] = $technicalFileUrl;
	}

	$isSaved = submitFeedbackRequestToCockpit($payload);
	if (!$isSaved) {
		logFeedbackRequestEvent('cockpit_submit_failed', ['ip' => getFeedbackRequesterIp()]);
	}

	return $isSaved;
}
