<?php
session_start();

$uploadBaseDir = 'uploads/';
if (!is_dir($uploadBaseDir)) mkdir($uploadBaseDir, 0777, true);

// 1. Determine the Unique ID (The permanent part of the URL)
// If editing, use the existing ID. If new, generate a random hex.
$sharingId = isset($_POST['sharing_id']) ? basename($_POST['sharing_id']) : bin2hex(random_bytes(8));
$targetDir = $uploadBaseDir . $sharingId . '/';

if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

$uploadedFiles = [];

// 2. Handle Multiple Files
if (!empty($_FILES['myFiles']['name'][0])) {
    foreach ($_FILES['myFiles']['tmp_name'] as $key => $tmpName) {
        $originalName = basename($_FILES['myFiles']['name'][$key]);
        $safeName = preg_replace("/[^a-zA-Z0-9.]/", "_", $originalName); // Clean filename

        if (move_uploaded_file($tmpName, $targetDir . $safeName)) {
            $uploadedFiles[] = $safeName;
        }
    }
}

// Redirect back to the UI with the permanent ID
header("Location: index.php?id=" . $sharingId . "&status=success");
exit;
