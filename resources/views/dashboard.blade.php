<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Manajemen Gudang</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="bg-slate-100 dark:bg-slate-950 text-slate-900 dark:text-slate-100 min-h-screen p-8 transition-colors duration-300">
    <div
        class="max-w-4xl mx-auto bg-white dark:bg-slate-900 p-8 rounded-2xl shadow-lg border border-slate-200 dark:border-slate-800">
        <h1 class="text-3xl font-bold mb-4">TESTING!</h1>
        <p class="text-slate-600 dark:text-slate-400">Login Anda telah berhasil menggunakan Firebase Authentication &
            Laravel Sanctum.</p>

        <button onclick="logout()"
            class="mt-6 px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white font-semibold rounded-lg text-sm transition duration-200">
            Keluar (Logout)
        </button>
    </div>

    <script>
        function logout() {
            localStorage.removeItem('access_token');
            localStorage.removeItem('user_role');
            window.location.href = '/login';
        }
    </script>
</body>

</html>