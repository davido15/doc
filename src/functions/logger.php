<?php
function logError($message) {
    $logFile = __DIR__ . '/../logs/error.log';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message" . PHP_EOL;
    // Suppress warning with @, and fallback to PHP error_log if it fails
    if (@file_put_contents($logFile, $logMessage, FILE_APPEND) === false) {
        error_log("[Logger] Failed to write to $logFile: $message");
    }
}
?> 