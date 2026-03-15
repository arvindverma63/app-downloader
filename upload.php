<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['myFile'])) {
    $uploadDir = 'uploads/';
    $originalName = basename($_FILES['myFile']['name']);
    
    // Generate a unique, unchangeable hash for the link
    $fileId = bin2hex(random_bytes(16)); 
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    $newName = $fileId . '.' . $extension;

    if (move_uploaded_file($_FILES['myFile']['tmp_name'], $uploadDir . $newName)) {
        // In a real app, store $fileId and $originalName in a database here.
        $downloadLink = "download.php?id=" . $newName;
        echo "File uploaded successfully!<br>";
        echo "Permanent Link: <a href='$downloadLink'>$downloadLink</a>";
    } else {
        echo "Upload failed.";
    }
}
?>