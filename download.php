<?php
if (isset($_GET['id'])) {
    $fileId = basename($_GET['id']); // Security: prevents directory traversal
    $filePath = 'uploads/' . $fileId;

    if (file_exists($filePath)) {
        // Headers to force download
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $fileId . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        
        readfile($filePath);
        exit;
    } else {
        die("Invalid or expired link.");
    }
}
?>