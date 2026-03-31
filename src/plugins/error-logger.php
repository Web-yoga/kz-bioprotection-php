<?php

declare(strict_types=1);

/**
 * Global PHP error logger.
 *
 * Logs PHP runtime errors, uncaught exceptions and fatal shutdown errors
 * to a single file, so issues stay visible even when output is hidden.
 */
function setupGlobalPhpErrorLogger(
    string $logFilePath,
    int $maxLogSizeBytes = 1048576,
    int $maxRotatedFiles = 3
): void
{
    static $isInitialized = false;

    if ($isInitialized) {
        return;
    }

    $logDirectory = dirname($logFilePath);
    if (!is_dir($logDirectory)) {
        @mkdir($logDirectory, 0775, true);
    }

    $rotateIfNeeded = static function () use ($logFilePath, $maxLogSizeBytes, $maxRotatedFiles): void {
        if (!is_file($logFilePath)) {
            return;
        }

        clearstatcache(true, $logFilePath);
        $currentSize = @filesize($logFilePath);
        if (!is_int($currentSize) || $currentSize < $maxLogSizeBytes) {
            return;
        }

        $maxRotatedFiles = max(1, $maxRotatedFiles);
        for ($index = $maxRotatedFiles; $index >= 1; $index--) {
            $source = $index === 1 ? $logFilePath : $logFilePath . '.' . ($index - 1);
            $target = $logFilePath . '.' . $index;

            if (!is_file($source)) {
                continue;
            }

            if (is_file($target)) {
                @unlink($target);
            }

            @rename($source, $target);
        }
    };

    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', $logFilePath);
    error_reporting(E_ALL);

    $formatThrowable = static function (Throwable $throwable): string {
        $message = sprintf(
            '[%s] Uncaught %s: %s in %s:%d',
            date('Y-m-d H:i:s'),
            get_class($throwable),
            $throwable->getMessage(),
            $throwable->getFile(),
            $throwable->getLine()
        );

        $trace = $throwable->getTraceAsString();
        if ($trace !== '') {
            $message .= PHP_EOL . $trace;
        }

        return $message;
    };

    set_error_handler(static function (
        int $severity,
        string $message,
        string $file = '',
        int $line = 0
    ) use ($rotateIfNeeded): bool {
        if (!(error_reporting() & $severity)) {
            return false;
        }

        $rotateIfNeeded();
        error_log(sprintf(
            '[%s] PHP Error [%d]: %s in %s:%d',
            date('Y-m-d H:i:s'),
            $severity,
            $message,
            $file,
            $line
        ));

        // Suppress default output handler; everything is written to file.
        return true;
    });

    set_exception_handler(static function (Throwable $throwable) use ($formatThrowable, $rotateIfNeeded): void {
        $rotateIfNeeded();
        error_log($formatThrowable($throwable));
        http_response_code(500);
    });

    register_shutdown_function(static function () use ($rotateIfNeeded): void {
        $lastError = error_get_last();
        if ($lastError === null) {
            return;
        }

        $fatalErrorTypes = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR];
        if (!in_array($lastError['type'] ?? 0, $fatalErrorTypes, true)) {
            return;
        }

        $rotateIfNeeded();
        error_log(sprintf(
            '[%s] Fatal Error [%d]: %s in %s:%d',
            date('Y-m-d H:i:s'),
            $lastError['type'],
            (string) ($lastError['message'] ?? ''),
            (string) ($lastError['file'] ?? ''),
            (int) ($lastError['line'] ?? 0)
        ));
    });

    $isInitialized = true;
}

