<?php
// Utility functions

function logFileDownload($mysqli, $file_id, $downloader_email) {
    $stmt = $mysqli->prepare("
        INSERT INTO download_logs 
        (file_id, uploader_email, downloader_email, download_time) 
        SELECT 
            ?, 
            u.email as uploader_email,
            ?,
            NOW()
        FROM uploads u 
        WHERE u.id = ?
    ");
    $stmt->bind_param("isi", $file_id, $downloader_email, $file_id);
    return $stmt->execute();
}
?> 