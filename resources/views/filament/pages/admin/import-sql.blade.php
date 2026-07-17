<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Import SQL - Admin Panel</title>
    @vite('resources/css/filament/admin/theme.css')
    <style>
        body { min-height: 100vh; }
        body.light-mode { background: linear-gradient(135deg, #f0f2f5 0%, #e2e8f0 100%); }
        body.dark-mode { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="w-full max-w-lg bg-white dark:bg-gray-900 rounded-xl shadow-2xl ring-1 ring-gray-950/5 dark:ring-white/10 p-8">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <svg class="w-8 h-8 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                    </svg>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Import Database</h2>
                        <p class="text-sm text-gray-500">Upload file .sql untuk mengimpor ke database</p>
                    </div>
                </div>
                <button id="themeToggle" class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 transition" title="Ganti tema">
                    <svg id="sunIcon" class="w-5 h-5 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                    </svg>
                    <svg id="moonIcon" class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                    </svg>
                </button>
            </div>

            @if(session('success'))
                <div class="rounded-lg bg-green-50 dark:bg-green-950 p-4 mb-4 border border-green-200 dark:border-green-800">
                    <p class="text-sm text-green-700 dark:text-green-300">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="rounded-lg bg-red-50 dark:bg-red-950 p-4 mb-4 border border-red-200 dark:border-red-800">
                    <p class="text-sm text-red-700 dark:text-red-300">{{ session('error') }}</p>
                </div>
            @endif

            <form method="POST" action="/tool-import" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label for="sql_file" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Pilih file .sql
                    </label>
                    <input type="file" name="sql_file" id="sql_file" accept=".sql,.txt" required
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 dark:file:bg-purple-950 dark:file:text-purple-300" />
                    @error('sql_file')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="rounded-lg bg-yellow-50 dark:bg-yellow-950 p-4 border border-yellow-200 dark:border-yellow-800">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mt-0.5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                        <p class="text-sm text-yellow-700 dark:text-yellow-300">
                            <strong>Peringatan:</strong> Import akan mengeksekusi seluruh perintah SQL dalam file. Pastikan data Anda telah di-backup sebelumnya.
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 transition">
                        Import Database
                    </button>
                    <a href="/tool-backup" class="inline-flex items-center justify-center gap-2 rounded-lg bg-yellow-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-yellow-500 transition mr-2">
                        Backup Dulu
                    </a>
                    <a href="/admin" class="inline-flex items-center justify-center gap-2 rounded-lg bg-gray-200 dark:bg-gray-700 px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none transition">
                        Kembali ke Dashboard
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        (function() {
            function getTheme() {
                const stored = localStorage.getItem('theme');
                if (stored === 'light') return 'light';
                if (stored === 'dark') return 'dark';
                return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }

            function applyTheme(theme) {
                const html = document.documentElement;
                const body = document.body;
                const sun = document.getElementById('sunIcon');
                const moon = document.getElementById('moonIcon');

                if (theme === 'dark') {
                    html.classList.add('dark');
                    body.classList.remove('light-mode');
                    body.classList.add('dark-mode');
                    sun?.classList.remove('hidden');
                    moon?.classList.add('hidden');
                } else {
                    html.classList.remove('dark');
                    body.classList.remove('dark-mode');
                    body.classList.add('light-mode');
                    sun?.classList.add('hidden');
                    moon?.classList.remove('hidden');
                }
            }

            applyTheme(getTheme());

            document.getElementById('themeToggle')?.addEventListener('click', function() {
                const isDark = document.documentElement.classList.contains('dark');
                const newTheme = isDark ? 'light' : 'dark';
                localStorage.setItem('theme', newTheme);
                applyTheme(newTheme);
            });

            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function() {
                if (!localStorage.getItem('theme')) {
                    applyTheme(getTheme());
                }
            });
        })();
    </script>
</body>
</html>