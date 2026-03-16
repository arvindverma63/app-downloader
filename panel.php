<?php
$uploadBaseDir = 'uploads/';
if (!is_dir($uploadBaseDir)) mkdir($uploadBaseDir, 0777, true);

$sharingId = isset($_GET['id']) ? basename($_GET['id']) : bin2hex(random_bytes(6));
$targetDir = $uploadBaseDir . $sharingId . '/';
if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

// Get File List for initial load
$files = array_diff(scandir($targetDir), array('.', '..'));
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");
$permanentLink = $protocol . "://" . $_SERVER['HTTP_HOST'] . explode('?', $_SERVER['REQUEST_URI'])[0] . "?id=" . $sharingId;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Smart File Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .progress-wrapper {
            display: none;
        }
    </style>
</head>

<body class="bg-slate-50 min-h-screen p-4 md:p-10 text-slate-800">

    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden mb-6">
            <div class="bg-indigo-600 p-6 md:px-10 flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="text-white text-center md:text-left">
                    <h1 class="text-2xl font-bold tracking-tight">Cloud Management</h1>
                    <p class="text-indigo-100 text-sm opacity-80 font-mono mt-1"><?= $sharingId ?></p>
                </div>
                <div class="flex gap-2">
                    <button onclick="copyToClipboard('<?= $permanentLink ?>')" class="bg-white/20 hover:bg-white/30 text-white px-5 py-2.5 rounded-xl text-sm font-semibold backdrop-blur-md transition-all flex items-center gap-2">
                        <i class="fa-solid fa-share-nodes"></i> Copy Share Link
                    </button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200">
                    <h3 class="font-bold mb-4 flex items-center gap-2"><i class="fa-solid fa-arrow-up-from-bracket text-indigo-500"></i> New Upload</h3>

                    <form id="uploadForm">
                        <label class="group relative flex flex-col items-center justify-center w-full h-48 border-2 border-dashed border-slate-200 rounded-2xl cursor-pointer hover:bg-slate-50 hover:border-indigo-400 transition-all">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <i class="fa-solid fa-file-circle-plus text-3xl text-slate-300 group-hover:text-indigo-500 mb-3"></i>
                                <p class="text-xs text-slate-500 font-medium">Click to select files</p>
                            </div>
                            <input type="file" id="fileInput" name="myFiles[]" multiple class="hidden" onchange="uploadFiles()">
                        </label>
                    </form>

                    <div id="progressContainer" class="progress-wrapper mt-6">
                        <div class="flex justify-between mb-2">
                            <span id="statusLabel" class="text-xs font-bold text-indigo-600 uppercase italic tracking-widest">Uploading...</span>
                            <span id="percentLabel" class="text-xs font-bold text-slate-500">0%</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2">
                            <div id="progressBar" class="bg-indigo-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200 min-h-[400px]">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="font-bold text-lg flex items-center gap-2"><i class="fa-solid fa-folder-open text-amber-500"></i> My Files</h3>
                        <button onclick="refreshFileList()" class="text-slate-400 hover:text-indigo-600 transition"><i class="fa-solid fa-rotate"></i></button>
                    </div>

                    <div id="fileListContainer" class="space-y-3">
                        <div class="animate-pulse text-center py-10 text-slate-400">Loading files...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const sharingId = "<?= $sharingId ?>";

        // 1. Initial Load
        document.addEventListener('DOMContentLoaded', refreshFileList);

        // 2. AJAX Upload with Progress
        function uploadFiles() {
            const fileInput = document.getElementById('fileInput');
            const files = fileInput.files;
            if (files.length === 0) return;

            const formData = new FormData();
            for (let i = 0; i < files.length; i++) {
                formData.append('myFiles[]', files[i]);
            }

            const xhr = new XMLHttpRequest();
            const progressContainer = document.getElementById('progressContainer');
            const progressBar = document.getElementById('progressBar');
            const percentLabel = document.getElementById('percentLabel');

            progressContainer.style.display = 'block';

            xhr.upload.addEventListener('progress', (e) => {
                if (e.lengthComputable) {
                    const percent = Math.round((e.loaded / e.total) * 100);
                    progressBar.style.width = percent + '%';
                    percentLabel.innerText = percent + '%';
                }
            });

            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    setTimeout(() => {
                        progressContainer.style.display = 'none';
                        progressBar.style.width = '0%';
                        refreshFileList();
                        fileInput.value = ''; // Reset input
                    }, 1000);
                }
            };

            xhr.open('POST', `api.php?action=upload&id=${sharingId}`, true);
            xhr.send(formData);
        }

        // 3. Refresh File List (AJAX)
        async function refreshFileList() {
            const container = document.getElementById('fileListContainer');
            try {
                const response = await fetch(`api.php?action=list&id=${sharingId}`);
                const files = await response.json();

                if (files.length === 0) {
                    container.innerHTML = `<div class="text-center py-10 opacity-40"><i class="fa-solid fa-box-open text-4xl mb-2"></i><p>No files yet</p></div>`;
                    return;
                }

                container.innerHTML = files.map(file => `
                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100 hover:border-indigo-200 transition-all group">
                        <div class="flex items-center gap-4 truncate">
                            <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-indigo-500 shadow-sm border border-slate-100">
                                <i class="fa-solid fa-file-lines"></i>
                            </div>
                            <div class="truncate">
                                <p class="text-sm font-bold text-slate-700 truncate">${file}</p>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Permanent</p>
                            </div>
                        </div>
                        <div class="flex gap-1">
                            <a href="download.php?dir=${sharingId}&file=${encodeURIComponent(file)}" class="p-2.5 text-slate-400 hover:text-indigo-600 hover:bg-white rounded-lg transition">
                                <i class="fa-solid fa-download"></i>
                            </a>
                            <button onclick="deleteFile('${file}')" class="p-2.5 text-slate-400 hover:text-red-500 hover:bg-white rounded-lg transition">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </div>
                    </div>
                `).join('');
            } catch (err) {
                container.innerHTML = "Error loading files.";
            }
        }

        // 4. Delete File (AJAX)
        async function deleteFile(filename) {
            if (!confirm(`Delete ${filename}?`)) return;
            await fetch(`api.php?action=delete&id=${sharingId}&file=${encodeURIComponent(filename)}`);
            refreshFileList();
        }

        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => alert("Share link copied!"));
        }
    </script>
</body>

</html>