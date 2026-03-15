<?php
$sharingId = isset($_GET['id']) ? $_GET['id'] : '';
$files = [];
if ($sharingId && is_dir("uploads/$sharingId")) {
    $files = array_diff(scandir("uploads/$sharingId"), array('.', '..'));
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="bg-slate-100 min-h-screen p-10">

    <div class="max-w-2xl mx-auto bg-white shadow-2xl rounded-3xl overflow-hidden">
        <div class="bg-indigo-600 p-6 text-white text-center">
            <h1 class="text-2xl font-bold">Secure Multi-File Manager</h1>
            <?php if ($sharingId): ?>
                <p class="text-indigo-100 text-sm mt-2">Permanent Link: <b>index.php?id=<?= $sharingId ?></b></p>
            <?php endif; ?>
        </div>

        <div class="p-8">
            <form action="upload.php" method="POST" enctype="multipart/form-data" class="space-y-4">
                <input type="hidden" name="sharing_id" value="<?= $sharingId ?>">

                <label class="block border-2 border-dashed border-slate-200 rounded-2xl p-10 text-center hover:border-indigo-400 transition cursor-pointer">
                    <input type="file" name="myFiles[]" multiple class="hidden" onchange="this.form.submit()">
                    <i class="fa-solid fa-plus-circle text-4xl text-indigo-500 mb-3"></i>
                    <p class="text-slate-600 font-medium">Click to upload multiple files</p>
                    <p class="text-xs text-slate-400 mt-1">Files will be added to this permanent URL</p>
                </label>
            </form>

            <?php if (!empty($files)): ?>
                <div class="mt-8">
                    <h3 class="text-slate-700 font-bold mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-folder-open text-amber-500"></i> Current Files
                    </h3>
                    <div class="space-y-3">
                        <?php foreach ($files as $file): ?>
                            <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-100">
                                <span class="text-slate-700 text-sm font-medium"><?= $file ?></span>
                                <div class="flex gap-2">
                                    <a href="download.php?dir=<?= $sharingId ?>&file=<?= $file ?>" class="p-2 text-indigo-600 hover:bg-indigo-100 rounded-lg">
                                        <i class="fa-solid fa-download"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>