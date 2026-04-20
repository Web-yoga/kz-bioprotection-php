<?php

declare(strict_types=1);

/**
 * Escapes HTML and allows line breaks via literal <br> / <br/> / <br /> in the source string (e.g. from API).
 */
function escapeHtmlAllowBr(string $value): string
{
	$escaped = htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

	return (string) preg_replace('/&lt;br\s*\/?&gt;/i', '<br>', $escaped);
}
