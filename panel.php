<?php
session_start();
$uploadBaseDir = 'uploads/';
if (!is_dir($uploadBaseDir)) mkdir($uploadBaseDir, 0777, true);

// 1. Get or Create the Permanent ID
$sharingId = isset($_GET['id']) ? basename($_GET['id']) : bin2hex(random_bytes(6));
$targetDir = $uploadBaseDir . $sharingId . '/';
if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

// 2. Handle File Uploads
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['myFiles'])) {
    foreach ($_FILES['myFiles']['tmp_name'] as $key => $tmpName) {
        if ($_FILES['myFiles']['error'][$key] == 0) {
            $name = basename($_FILES['myFiles']['name'][$key]);
            move_uploaded_file($tmpName, $targetDir . $name);
        }
    }
    header("Location: panel.php?id=$sharingId");
    exit;
}

// 3. Handle File Deletion (The "Edit" part)
if (isset($_GET['delete'])) {
    $fileToDelete = basename($_GET['delete']);
    if (file_exists($targetDir . $fileToDelete)) {
        unlink($targetDir . $fileToDelete);
    }
    header("Location: panel.php?id=$sharingId");
    exit;
}

// 4. Scan for existing files
$files = array_diff(scandir($targetDir), array('.', '..'));
$currentUrl = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Management Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="bg-slate-50 min-h-screen py-12 px-4">

    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-slate-200">

            <div class="md:flex">
                <div class="md:w-1/3 bg-indigo-700 p-8 text-white">
                    <div class="mb-8">
                        <i class="fa-solid fa-folder-tree text-4xl mb-4"></i>
                        <h1 class="text-2xl font-bold">File Panel</h1>
                        <p class="text-indigo-200 text-sm">Manage your permanent share link.</p>
                    </div>

                    <div class="space-y-4">
                        <div class="bg-indigo-800/50 p-4 rounded-xl border border-indigo-400/30">
                            <p class="text-xs uppercase font-bold tracking-wider text-indigo-300 mb-2">Share Link</p>
                            <input type="text" readonly value="<?= $currentUrl ?>" id="shareUrl" class="w-full bg-transparent text-sm truncate focus:outline-none">
                            <button onclick="copyLink()" class="mt-2 text-xs bg-white text-indigo-700 px-3 py-1 rounded font-bold hover:bg-indigo-50 transition">
                                <i class="fa-solid fa-copy mr-1"></i> Copy Link
                            </button>
                        </div>
                    </div>
                </div>

                <div class="md:w-2/3 p-8">
                    <form action="" method="POST" enctype="multipart/form-data" class="mb-10">
                        <div class="group relative border-2 border-dashed border-slate-200 rounded-2xl p-6 hover:border-indigo-400 hover:bg-indigo-50/50 transition-all text-center">
                            <input type="file" name="myFiles[]" multiple class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" onchange="this.form.submit()">
                            <div class="space-y-2">
                                <i class="fa-solid fa-cloud-arrow-up text-3xl text-slate-400 group-hover:text-indigo-500"></i>
                                <p class="text-slate-600 font-medium">Drop files here or click to upload</p>
                                <p class="text-xs text-slate-400">Multiple files supported</p>
                            </div>
                        </div>
                    </form>

                    <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center justify-between">
                        Your Files
                        <span class="text-xs bg-slate-100 px-2 py-1 rounded-full text-slate-500"><?= count($files) ?> Files</span>
                    </h2>

                    <?php if (empty($files)): ?>
                        <div class="text-center py-10">
                            <i class="fa-solid fa-ghost text-slate-200 text-5xl mb-3"></i>
                            <p class="text-slate-400">No files uploaded yet.</p>
                        </div>
                    <?php else: ?>
                        <div class="grid gap-3">
                            <?php foreach ($files as $file): ?>
                                <div class="flex items-center justify-between p-4 rounded-xl border border-slate-100 bg-white hover:shadow-md transition">
                                    <div class="flex items-center gap-3 truncate">
                                        <div class="bg-amber-100 p-2 rounded-lg">
                                            <i class="fa-solid fa-file text-amber-600"></i>
                                        </div>
                                        <span class="text-sm font-semibold text-slate-700 truncate"><?= $file ?></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="download.php?dir=<?= $sharingId ?>&file=<?= $file ?>" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="Download">
                                            <i class="fa-solid fa-download"></i>
                                        </a>
                                        <a href="?id=<?= $sharingId ?>&delete=<?= $file ?>" class="p-2 text-red-400 hover:bg-red-50 hover:text-red-600 rounded-lg transition" onclick="return confirm('Delete this file?')" title="Delete">
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
    </div>

    <script>
        function copyLink() {
            var copyText = document.getElementById("shareUrl");
            copyText.select();
            document.execCommand("copy");
            alert("Link copied to clipboard!");
        }
    </script>
</body>

</html>