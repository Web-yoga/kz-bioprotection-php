<?php

declare(strict_types=1);

echo basename(__FILE__, '.php') . PHP_EOL;

$dictionaryPayload = isset($dictionary) && is_array($dictionary) ? $dictionary : [];
$pageHomePayload = isset($pageContent) && is_array($pageContent) ? $pageContent : [];
?>
<pre><?= htmlspecialchars((string) json_encode($dictionaryPayload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), ENT_QUOTES, 'UTF-8'); ?></pre>
<pre><?= htmlspecialchars((string) json_encode($pageHomePayload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), ENT_QUOTES, 'UTF-8'); ?></pre>
<?php
require TEMPLATES_PATH . '/partials/slider.php';
require TEMPLATES_PATH . '/partials/contact-form.php';
