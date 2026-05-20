<?php

declare(strict_types=1);

/**
 * Public URL for a CMS upload referenced by a node with a `path` key (same shape as image/file fields from the API).
 *
 * @param mixed $pathField e.g. `['path' => 'folder/file.jpg']`
 */
function uploadsPublicUrlFromPathField(mixed $pathField): string
{
	if (!is_array($pathField)) {
		return '';
	}

	$path = isset($pathField['path']) ? trim((string) $pathField['path']) : '';
	if ($path === '') {
		return '';
	}

	return UPLOADS_BASE_URL . ltrim($path, '/');
}
