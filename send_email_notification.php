<?php
require_once __DIR__ . '/email_config.php';
function sendRakthaMitraEmail(string $to, string $subject, string $message): bool {
 if (!RAKTHAMITRA_EMAIL_ENABLED || !filter_var($to, FILTER_VALIDATE_EMAIL)) return false;
 $headers = "From: " . RAKTHAMITRA_FROM_EMAIL . "\r\n" . "Content-Type: text/plain; charset=UTF-8";
 return @mail($to, $subject, $message, $headers);
}
?>