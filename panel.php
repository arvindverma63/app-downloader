<?php
// 1. CONFIGURATION & DIRECTORY SETUP
$uploadBaseDir = 'uploads/';
if (!is_dir($uploadBaseDir)) mkdir($uploadBaseDir, 0777, true);

// Get the ID from URL or generate a new one if it's a fresh visit
// This ID is the "Permanent" part of your link
$sharingId = isset($_GET['id']) ? basename($_GET['id']) : bin2hex(random_bytes(6));
$targetDir = $uploadBaseDir . $sharingId . '/';

if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

// 2. HANDLE UPLOAD / UPDATE
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['myFiles'])) {
    foreach ($_FILES['myFiles']['tmp_name'] as $key => $tmpName) {
        if ($_FILES['myFiles']['error'][$key] == 0) {
            $name = basename($_FILES['myFiles']['name'][$key]);
            // Moving the file to the fixed sharing ID folder
            move_uploaded_file($tmpName, $targetDir . $name);
        }
    }
    header("Location: panel.php?id=$sharingId&msg=updated");
    exit;
}

// 3. HANDLE DELETE (EDITING THE BUCKET)
if (isset($_GET['delete'])) {
    $fileToDelete = basename($_GET['delete']);
    if (file_exists($targetDir . $fileToDelete)) {
        unlink($targetDir . $fileToDelete);
    }
    header("Location: panel.php?id=$sharingId&msg=deleted");
    exit;
}

// 4. GET FILE LIST
$files = array_diff(scandir($targetDir), array('.', '..'));
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");
$permanentLink = $protocol . "://" . $_SERVER['HTTP_HOST'] . explode('?', $_SERVER['REQUEST_URI'])[0] . "?id=" . $sharingId;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Permanent File Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="bg-gray-100 min-h-screen p-4 md:p-10">

    <div class="max-w-4xl mx-auto bg-white shadow-2xl rounded-2xl overflow-hidden border border-gray-200">
        <div class="bg-slate-900 p-6 text-white flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <h1 class="text-xl font-bold flex items-center gap-2">
                    <i class="fa-solid fa-link text-blue-400"></i> Permanent Link Panel
                </h1>
                <p class="text-gray-400 text-xs mt-1 font-mono"><?= $permanentLink ?></p>
            </div>
            <button onclick="copyToClipboard('<?= $permanentLink ?>')" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg text-sm font-bold transition flex items-center gap-2">
                <i class="fa-solid fa-copy"></i> Copy Link
            </button>
        </div>

        <div class="p-6 md:p-10">
            <form action="" method="POST" enctype="multipart/form-data" class="mb-10">
                <label class="relative group block w-full border-2 border-dashed border-gray-300 rounded-xl p-12 text-center hover:border-blue-500 hover:bg-blue-50 transition-all cursor-pointer">
                    <input type="file" name="myFiles[]" multiple class="hidden" onchange="this.form.submit()">
                    <div class="space-y-4">
                        <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto group-hover:scale-110 transition">
                            <i class="fa-solid fa-cloud-arrow-up text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-lg font-semibold text-gray-700">Upload or Update Files</p>
                            <p class="text-gray-500 text-sm">The link above will always contain these files.</p>
                        </div>
                    </div>
                </label>
            </form>

            <div class="space-y-4">
                <h3 class="text-gray-800 font-bold text-lg flex items-center gap-2">
                    <i class="fa-solid fa-list-ul"></i> Managed Files (<?= count($files) ?>)
                </h3>

                <?php if (empty($files)): ?>
                    <div class="text-center py-20 border-2 border-dotted border-gray-100 rounded-xl">
                        <p class="text-gray-400 italic">This bucket is empty. Upload something to start.</p>
                    </div>
                <?php else: ?>
                    <div class="grid gap-3">
                        <?php foreach ($files as $file): ?>
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-white hover:shadow-md border border-transparent hover:border-gray-200 transition">
                                <div class="flex items-center gap-4 min-w-0">
                                    <i class="fa-solid fa-file-lines text-2xl text-blue-500"></i>
                                    <div class="truncate">
                                        <p class="font-medium text-gray-800 truncate"><?= $file ?></p>
                                        <p class="text-[10px] text-gray-400 uppercase tracking-tighter">Verified File</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="download.php?dir=<?= $sharingId ?>&file=<?= urlencode($file) ?>"
                                        class="p-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                        <i class="fa-solid fa-download"></i>
                                    </a>
                                    <a href="?id=<?= $sharingId ?>&delete=<?= urlencode($file) ?>"
                                        onclick="return confirm('Remove this file from the link?')"
                                        class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert("Link copied! Use this link to manage these files anytime.");
            });
        }
    </script>
</body>

</html>