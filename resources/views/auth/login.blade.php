<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Manajemen Gudang Cloud</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Instrument Sans', sans-serif;
        }
    </style>
</head>

<body
    class="bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-slate-100 min-h-screen flex items-center justify-center relative overflow-hidden selection:bg-blue-500/30 selection:text-blue-200 transition-colors duration-300">

    <div
        class="absolute -top-40 -left-40 w-96 h-96 bg-blue-600/10 dark:bg-blue-600/20 rounded-full blur-3xl pointer-events-none transition-colors duration-300">
    </div>
    <div
        class="absolute -bottom-40 -right-40 w-96 h-96 bg-indigo-600/10 dark:bg-indigo-600/20 rounded-full blur-3xl pointer-events-none transition-colors duration-300">
    </div>
    <div
        class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-violet-600/3 dark:bg-violet-600/5 rounded-full blur-3xl pointer-events-none transition-colors duration-300">
    </div>

    <button type="button" id="themeToggle"
        class="absolute top-6 right-6 p-3 rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-amber-500 shadow-sm focus:outline-none backdrop-blur-md transition-all duration-300 cursor-pointer dark:bg-slate-900/60 dark:border-slate-800 dark:text-amber-400 dark:hover:text-amber-300 dark:shadow-none"
        title="Ubah Tema">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
        </svg>
    </button>

    <div class="w-full max-w-md p-6 relative z-10">

        <div class="text-center mb-8">
            <div
                class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-500 shadow-lg shadow-blue-500/20 dark:shadow-blue-500/30 mb-4 transition-transform duration-500 hover:scale-105">
                <svg class="w-8 h-8 text-white animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M12 11v6m0 0l-3-3m3 3l3-3" />
                </svg>
            </div>
            <h1
                class="text-3xl font-bold bg-gradient-to-r from-slate-900 via-slate-800 to-slate-600 dark:from-white dark:via-slate-100 dark:to-slate-400 bg-clip-text text-transparent tracking-tight">
                Manajemen Gudang PT. Sejahtera Jaya Mandiri</h1>
            <p class="text-slate-500 dark:text-slate-400 mt-2 text-sm">Sistem Manajemen Inventaris & Logistik
                Terintegrasi</p>
        </div>

        <div
            class="backdrop-blur-xl bg-white/80 border border-slate-200/80 dark:bg-slate-900/60 dark:border-slate-800 rounded-2xl p-8 shadow-2xl shadow-slate-200/40 dark:shadow-black/50 relative transition-all duration-300">
            <div
                class="absolute top-0 inset-x-0 h-[1px] bg-gradient-to-r from-transparent via-blue-500/20 dark:via-blue-500/30 to-transparent">
            </div>

            <h2 class="text-xl font-semibold text-slate-900 dark:text-slate-100 mb-6">Masuk ke Akun Anda</h2>

            <form id="loginForm" class="space-y-5" novalidate>
                <div class="space-y-1.5">
                    <label for="email"
                        class="text-xs font-medium text-slate-500 dark:text-slate-400 tracking-wide">Alamat
                        Email</label>
                    <div
                        class="relative rounded-lg focus-within:ring-2 focus-within:ring-blue-500/20 transition-all duration-200">
                        <span
                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" />
                            </svg>
                        </span>
                        <input type="email" id="email" name="email" placeholder="nama@perusahaan.com" required autofocus
                            tabindex="1" autocomplete="email"
                            class="w-full pl-10 pr-4 py-3 bg-slate-100/80 border border-slate-200 hover:border-slate-300 rounded-lg text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:border-blue-500 focus:outline-none dark:bg-slate-950/50 dark:border-slate-800 dark:hover:border-slate-700 dark:focus:bg-slate-950 dark:text-slate-100 dark:placeholder-slate-600 transition-all duration-200">
                    </div>
                    <span id="emailError" class="text-xs text-rose-500 mt-1 hidden transition-all duration-200">Format
                        email tidak valid.</span>
                </div>

                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label for="password"
                            class="text-xs font-medium text-slate-500 dark:text-slate-400 tracking-wide">Password</label>
                    </div>
                    <div
                        class="relative rounded-lg focus-within:ring-2 focus-within:ring-blue-500/20 transition-all duration-200">
                        <span
                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </span>
                        <input type="password" id="password" name="password" placeholder="Masukkan password" required
                            tabindex="2" autocomplete="current-password"
                            class="w-full pl-10 pr-10 py-3 bg-slate-100/80 border border-slate-200 hover:border-slate-300 rounded-lg text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:border-blue-500 focus:outline-none dark:bg-slate-950/50 dark:border-slate-800 dark:hover:border-slate-700 dark:focus:bg-slate-950 dark:text-slate-100 dark:placeholder-slate-600 transition-all duration-200">
                        <button type="button" id="togglePassword" tabindex="-1"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none dark:text-slate-500 dark:hover:text-slate-300 transition-colors duration-200">
                            <svg id="eyeIconOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg id="eyeIconClosed" class="w-5 h-5 hidden" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.024 10.024 0 014.13-5.24m5.99-3.46a9.96 9.96 0 014.37 1.25M15 12a3 3 0 11-6 0 3 3 0 016 0zm-3-9V3m0 2.458V4.5M9 21h6" />
                            </svg>
                        </button>
                    </div>
                    <span id="passwordError"
                        class="text-xs text-rose-500 mt-1 hidden transition-all duration-200">Password tidak boleh
                        kosong.</span>
                </div>

                <div class="flex items-center justify-between text-sm py-1">
                    <label class="inline-flex items-center cursor-pointer select-none">
                        <input type="checkbox" id="rememberMe" tabindex="3"
                            class="w-4 h-4 rounded bg-slate-100 border-slate-300 text-blue-600 focus:ring-blue-500/20 focus:outline-none dark:bg-slate-950 dark:border-slate-800 dark:text-blue-600 dark:focus:ring-blue-500/20 dark:focus:ring-offset-slate-900 transition duration-200">
                        <span
                            class="ml-2 text-xs text-slate-500 dark:text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">Ingat
                            Saya</span>
                    </label>
                </div>

                <button type="submit" id="loginBtn" tabindex="4"
                    class="w-full flex items-center justify-center gap-2 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-semibold rounded-lg text-sm shadow-lg shadow-blue-500/20 hover:shadow-blue-500/35 focus:ring-2 focus:ring-blue-500/30 active:scale-[0.98] disabled:opacity-50 disabled:pointer-events-none transition-all duration-200 cursor-pointer">
                    <svg id="btnSpinner" class="w-4 h-4 text-white animate-spin hidden" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <span id="btnText">Masuk Ke Sistem</span>
                </button>
            </form>
        </div>
    </div>

</body></html>