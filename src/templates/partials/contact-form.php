<?php

declare(strict_types=1);

echo basename(__FILE__, '.php') . PHP_EOL;

$feedbackFormPayload = isset($feedbackForm) && is_array($feedbackForm) ? $feedbackForm : [];
?>
<pre><?= htmlspecialchars((string) json_encode($feedbackFormPayload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), ENT_QUOTES, 'UTF-8'); ?></pre>
