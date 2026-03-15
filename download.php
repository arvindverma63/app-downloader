<?php
if (isset($_GET['dir']) && isset($_GET['file'])) {
    $dir = basename($_GET['dir']);
    $file = basename($_GET['file']);
    $filePath = "uploads/$dir/$file";

    if (file_exists($filePath)) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $file . '"');
        readfile($filePath);
        exit;
    }
}
die("File not found.");
