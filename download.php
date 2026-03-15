<?php
if (isset($_GET['dir']) && isset($_GET['file'])) {
    $dir = basename($_GET['dir']);
    $file = basename($_GET['file']);
    $path = "uploads/$dir/$file";

    if (file_exists($path)) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $file . '"');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;
    }
}
die("Error: File not found.");
