<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure File Share</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="bg-slate-50 min-h-screen flex items-center justify-center p-6">

    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl p-8">
        <div class="text-center mb-8">
            <div class="bg-indigo-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-cloud-arrow-up text-indigo-600 text-2xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-slate-800">File Uploader</h1>
            <p class="text-slate-500 text-sm">Upload any file to get an immutable link</p>
        </div>

        <form action="upload.php" method="POST" enctype="multipart/form-data" class="space-y-6">
            <div class="relative group">
                <label class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-slate-300 rounded-xl cursor-pointer bg-slate-50 hover:bg-indigo-50 hover:border-indigo-400 transition-all">
                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                        <i class="fa-solid fa-file-import text-slate-400 group-hover:text-indigo-500 mb-3 text-3xl"></i>
                        <p class="mb-2 text-sm text-slate-700 font-semibold">Click to upload or drag and drop</p>
                        <p class="text-xs text-slate-500">Any file type supported (Max 100MB)</p>
                    </div>
                    <input type="file" name="myFile" class="hidden" required onchange="displayFileName(this)" />
                </label>
            </div>

            <div id="file-name-container" class="hidden flex items-center p-3 bg-indigo-50 rounded-lg border border-indigo-100">
                <i class="fa-solid fa-paperclip text-indigo-500 mr-2"></i>
                <span id="file-name" class="text-sm text-indigo-700 truncate font-medium"></span>
            </div>

            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-xl transition duration-200 shadow-lg shadow-indigo-200 flex items-center justify-center gap-2">
                <span>Generate Secure Link</span>
                <i class="fa-solid fa-bolt text-xs"></i>
            </button>
        </form>

        <div class="mt-6 pt-6 border-t border-slate-100 text-center">
            <p class="text-xs text-slate-400 uppercase tracking-widest font-bold">Privacy Guaranteed</p>
        </div>
    </div>

    <script>
        function displayFileName(input) {
            const container = document.getElementById('file-name-container');
            const nameSpan = document.getElementById('file-name');
            if (input.files && input.files.length > 0) {
                container.classList.remove('hidden');
                nameSpan.textContent = input.files[0].name;
            }
        }
    </script>
</body>

</html>

