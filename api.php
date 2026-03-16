<?php
$uploadBaseDir = 'uploads/';
$action = $_GET['action'] ?? '';
$sharingId = basename($_GET['id'] ?? '');
$targetDir = $uploadBaseDir . $sharingId . '/';

if (!$sharingId) exit;

header('Content-Type: application/json');

switch ($action) {
    case 'upload':
        if (isset($_FILES['myFiles'])) {
            if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
            foreach ($_FILES['myFiles']['tmp_name'] as $key => $tmpName) {
                $name = basename($_FILES['myFiles']['name'][$key]);
                move_uploaded_file($tmpName, $targetDir . $name);
            }
            echo json_encode(['status' => 'success']);
        }
        break;

    case 'list':
        if (is_dir($targetDir)) {
            $files = array_values(array_diff(scandir($targetDir), array('.', '..')));
            echo json_encode($files);
        } else {
            echo json_encode([]);
        }
        break;

    case 'delete':
        $file = basename($_GET['file'] ?? '');
        if ($file && file_exists($targetDir . $file)) {
            unlink($targetDir . $file);
            echo json_encode(['status' => 'deleted']);
        }
        break;
}