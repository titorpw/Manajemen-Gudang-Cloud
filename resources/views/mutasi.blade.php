<!DOCTYPE html>
<html lang="id">

<head>
    <script>
        (function () {
            const savedTheme = localStorage.getItem('gudang_cloud_theme');
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (savedTheme === 'dark' || (!savedTheme && systemPrefersDark)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mutasi Stok - Manajemen Gudang</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .no-transition, .no-transition * {
            transition: none !important;
            animation: none !important;
        }

        body {
            font-family: 'Instrument Sans', sans-serif;
        }

        @media (min-width: 768px) {
            html, body {
                height: 100vh !important;
                overflow: hidden !important;
            }
        }

        ::-webkit-scrollbar {
            display: none !important;
        }
        * {
            scrollbar-width: none !important;
            -ms-overflow-style: none !important;
        }

        @media (min-width: 768px) {
            #sidebar {
                transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), padding 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                height: 100vh;
                position: sticky;
                top: 0;
            }
            
            #sidebar .link-text,
            #sidebar .link-badge,
            #sidebar .user-info-text {
                transition: opacity 0.25s ease-in-out, max-width 0.3s cubic-bezier(0.4, 0, 0.2, 1), margin 0.3s ease-in-out;
                opacity: 1;
                max-width: 250px;
                display: inline-block;
                overflow: hidden;
                white-space: nowrap;
            }

            #sidebar .logo-text-wrapper {
                transition: opacity 0.25s ease-in-out, max-width 0.3s cubic-bezier(0.4, 0, 0.2, 1), margin 0.3s ease-in-out;
                opacity: 1;
                max-width: 250px;
                display: flex;
                flex-direction: column;
                overflow: hidden;
                white-space: nowrap;
            }
            
            #sidebar.w-20 {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }
            
            #sidebar.w-20 .logo-container {
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 1rem;
            }

            #sidebar.w-20 .logo-container > div {
                gap: 0 !important;
            }
            
            #sidebar.w-20 .logo-text-wrapper,
            #sidebar.w-20 .link-text,
            #sidebar.w-20 .link-badge,
            #sidebar.w-20 .user-info-text {
                opacity: 0;
                max-width: 0;
                margin-left: 0 !important;
                margin-right: 0 !important;
                pointer-events: none;
                flex: none !important;
            }
            
            #sidebar.w-20 nav a,
            #sidebar.w-20 nav > div {
                justify-content: center;
                padding-left: 0;
                padding-right: 0;
                width: 3rem;
                height: 3rem;
                margin: 0 auto;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                position: relative !important;
            }

            #sidebar.w-20 nav a svg,
            #sidebar.w-20 nav > div svg {
                position: absolute !important;
                left: 50% !important;
                top: 50% !important;
                transform: translate(-50%, -50%) !important;
                margin: 0 !important;
            }
            
            #sidebar.w-20 .user-profile-container {
                justify-content: center;
                padding-left: 0;
                padding-right: 0;
                gap: 0 !important;
            }
            
            #sidebar.w-20 .sidebar-actions {
                flex-direction: column;
                width: 100%;
                gap: 0.5rem;
            }
            
            #sidebar.w-20 #desktopCollapseBtn {
                margin: 0 auto;
            }
            
            #sidebar.w-20 #collapseChevron {
                transform: rotate(180deg);
            }
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-slate-100 min-h-screen md:h-screen md:overflow-hidden flex flex-col md:flex-row relative overflow-x-hidden selection:bg-blue-500/30 selection:text-blue-200 transition-colors duration-300 no-transition">
    <script>
        (function() {
            const token = localStorage.getItem('access_token');
            if (!token) {
                window.location.href = '/login';
            }
        })();
    </script>

    <div class="absolute -top-40 -left-40 w-96 h-96 bg-blue-600/5 dark:bg-blue-600/10 rounded-full blur-3xl pointer-events-none transition-colors duration-300"></div>
    <div class="absolute top-1/3 right-0 w-[500px] h-[500px] bg-indigo-600/3 dark:bg-indigo-600/5 rounded-full blur-3xl pointer-events-none transition-colors duration-300"></div>

    <div class="md:hidden flex items-center justify-between px-6 py-4 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 w-full sticky top-0 z-30 shadow-sm backdrop-blur-md bg-white/80 dark:bg-slate-900/80">
        <div class="flex items-center gap-2.5">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-500 flex items-center justify-center shadow-md shadow-blue-500/10">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                </svg>
            </div>
            <span class="font-bold text-lg tracking-tight bg-gradient-to-r from-slate-950 to-slate-700 dark:from-white dark:to-slate-300 bg-clip-text text-transparent">Gudang Cloud</span>
        </div>
        <button id="mobileMenuBtn" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 dark:text-slate-400">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
            </svg>
        </button>
    </div>

    <div id="sidebarOverlay" class="fixed inset-0 bg-slate-900/40 dark:bg-slate-950/60 z-30 opacity-0 pointer-events-none transition-opacity duration-300 md:hidden"></div>

    <aside id="sidebar" class="fixed md:static inset-y-0 left-0 w-72 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 flex flex-col justify-between p-6 z-40 transform -translate-x-full md:translate-x-0 transition-transform duration-300 md:transition-none shadow-2xl md:shadow-none h-full">
        <script>
            (function() {
                if (localStorage.getItem('sidebar_collapsed') === 'true' && window.innerWidth >= 768) {
                    const sidebar = document.getElementById('sidebar');
                    if (sidebar) {
                        sidebar.classList.remove('w-72');
                        sidebar.classList.add('w-20');
                    }
                }
            })();
        </script>
        <div class="flex flex-col gap-8">
            <div class="flex items-center justify-between gap-3 logo-container">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-500 flex items-center justify-center shadow-lg shadow-blue-500/20 shrink-0">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                        </svg>
                    </div>
                    <div class="flex flex-col logo-text-wrapper">
                        <span class="font-bold text-base tracking-tight text-slate-900 dark:text-white logo-text">Gudang Cloud</span>
                        <span class="text-xs text-slate-400 dark:text-slate-500 logo-subtext">PT. Sejahtera Jaya Mandiri</span>
                    </div>
                </div>
                <button type="button" id="desktopCollapseBtn" class="hidden md:flex p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors cursor-pointer" title="Sembunyikan Sidebar">
                    <svg id="collapseChevron" class="w-5 h-5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                    </svg>
                </button>
            </div>

            <nav class="flex flex-col gap-1.5">
                <a href="/dashboard" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-slate-200 transition-all duration-200 font-medium cursor-pointer">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                    </svg>
                    <span class="link-text">Dashboard</span>
                </a>
                <a href="/katalog" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-slate-200 transition-all duration-200 font-medium cursor-pointer">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <span class="link-text">Katalog Barang</span>
                </a>
                <a href="/mutasi" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 transition-all duration-200 font-semibold shadow-sm shadow-blue-500/5">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                    <span class="link-text">Mutasi Stok</span>
                </a>
            </nav>
        </div>

        <div class="flex flex-col gap-5 border-t border-slate-200 dark:border-slate-800 pt-5">
            <div class="flex items-center gap-3 px-2 user-profile-container">
                <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-slate-200 to-slate-300 dark:from-slate-800 dark:to-slate-700 flex items-center justify-center shadow-sm shrink-0">
                    <span id="userInitial" class="font-bold text-sm text-slate-700 dark:text-slate-300">&nbsp;</span>
                </div>
                <div class="flex flex-col min-w-0 flex-1 user-info-text">
                    <span id="userName" class="font-bold text-sm text-slate-800 dark:text-slate-200 truncate">&nbsp;</span>
                    <span id="userRoleBadge" class="inline-flex self-start px-2 py-0.5 rounded text-[10px] font-bold tracking-wide uppercase mt-1 opacity-0">&nbsp;</span>
                </div>
                <script>
                    (function() {
                        const storedName = localStorage.getItem('user_name');
                        const storedRole = localStorage.getItem('user_role');
                        if (storedName) {
                            document.getElementById('userName').textContent = storedName;
                            document.getElementById('userInitial').textContent = storedName.charAt(0).toUpperCase();
                        }
                        if (storedRole) {
                            const badge = document.getElementById('userRoleBadge');
                            if (badge) {
                                badge.textContent = storedRole === 'staf' ? 'Staf Gudang (Admin)' : 'Manager Gudang';
                                if (storedRole === 'staf') {
                                    badge.className = "inline-flex self-start px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wide uppercase mt-1.5 bg-blue-500/10 text-blue-500 dark:bg-blue-500/20";
                                } else {
                                    badge.className = "inline-flex self-start px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wide uppercase mt-1.5 bg-amber-500/10 text-amber-500 dark:bg-amber-500/20";
                                }
                            }
                        }
                    })();
                </script>
            </div>

            <div class="flex gap-2 sidebar-actions">
                <button type="button" id="sidebarThemeToggle" class="flex-1 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-400 hover:text-amber-500 dark:text-amber-400 dark:hover:text-amber-300 flex items-center justify-center transition-all duration-200 cursor-pointer" title="Ubah Tema">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                    </svg>
                </button>
                <button type="button" id="logoutBtn" class="flex-1 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 dark:bg-rose-950/20 dark:hover:bg-rose-900/30 dark:text-rose-400 flex items-center justify-center transition-all duration-200 cursor-pointer font-semibold text-sm gap-2" title="Keluar">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </button>
            </div>
        </div>
    </aside>

    <main id="mutasiPage" class="flex-1 p-6 md:p-10 w-full overflow-y-auto max-w-7xl mx-auto space-y-8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight bg-gradient-to-r from-slate-900 via-slate-800 to-slate-600 dark:from-white dark:via-slate-100 dark:to-slate-400 bg-clip-text text-transparent">Mutasi Stok</h1>
                <p class="text-slate-500 dark:text-slate-400 mt-1 text-sm">Catat dan pantau aliran keluar masuk barang inventaris gudang.</p>
            </div>
            <button type="button" id="btnTambahMutasi" class="inline-flex items-center gap-2 px-5 py-3 bg-gradient-to-r from-blue-600 to-indigo-500 hover:from-blue-700 hover:to-indigo-600 text-white font-semibold rounded-xl text-sm transition-all duration-200 shadow-md shadow-blue-500/10 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                Catat Mutasi Baru
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <div class="backdrop-blur-xl bg-white/80 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800/80 rounded-2xl p-6 shadow-sm flex items-center gap-5 transition-all duration-200 hover:shadow-md">
                <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-450 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
                <div class="space-y-0.5">
                    <span class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Total Mutasi</span>
                    <h3 id="totalMutasiCount" class="text-2xl font-bold text-slate-800 dark:text-white">0</h3>
                </div>
            </div>

            <div class="backdrop-blur-xl bg-white/80 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800/80 rounded-2xl p-6 shadow-sm flex items-center gap-5 transition-all duration-200 hover:shadow-md">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-455 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 13l-3 3m0 0l-3-3m3 3V8m0 13a9 9 0 110-18 9 9 0 010 18z" />
                    </svg>
                </div>
                <div class="space-y-0.5">
                    <span class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Barang Masuk (IN)</span>
                    <h3 id="totalMasukCount" class="text-2xl font-bold text-slate-800 dark:text-white">0</h3>
                </div>
            </div>

            <div class="backdrop-blur-xl bg-white/80 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800/80 rounded-2xl p-6 shadow-sm flex items-center gap-5 transition-all duration-200 hover:shadow-md">
                <div class="w-12 h-12 rounded-xl bg-rose-50 dark:bg-rose-950/20 text-rose-600 dark:text-rose-450 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 11l3-3m0 0l3 3m-3-3v8m0 5a9 9 0 110-18 9 9 0 010 18z" />
                    </svg>
                </div>
                <div class="space-y-0.5">
                    <span class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Barang Keluar (OUT)</span>
                    <h3 id="totalKeluarCount" class="text-2xl font-bold text-slate-800 dark:text-white">0</h3>
                </div>
            </div>
        </div>

        <section class="backdrop-blur-xl bg-white/85 dark:bg-slate-900/50 border border-slate-200/80 dark:border-slate-800/80 rounded-2xl shadow-sm overflow-hidden flex flex-col">
            <div class="p-6 border-b border-slate-200 dark:border-slate-800 bg-white/50 dark:bg-slate-900/20 flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="relative w-full md:max-w-md shrink-0">
                    <input type="text" id="txtSearchMutasi" placeholder="Cari berdasarkan barang, kode, staf, keterangan..." class="w-full pl-11 pr-4 py-2.5 bg-slate-100 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 focus:bg-white focus:border-blue-500 focus:outline-none dark:focus:bg-slate-950 text-sm rounded-xl text-slate-900 dark:text-slate-100 transition-colors duration-200">
                    <div class="absolute left-4 top-3.5 text-slate-400">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                <div class="flex items-center gap-3 w-full justify-end">
                    <div class="relative w-full sm:w-48">
                        <select id="filterType" class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 focus:bg-white focus:border-blue-500 focus:outline-none dark:focus:bg-slate-950 text-sm rounded-xl text-slate-700 dark:text-slate-300 transition-colors duration-200 appearance-none cursor-pointer">
                            <option value="">Semua Tipe</option>
                            <option value="IN">Masuk (IN)</option>
                            <option value="OUT">Keluar (OUT)</option>
                        </select>
                        <div class="absolute right-4 top-4 text-slate-400 pointer-events-none">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>

                    <button type="button" id="btnResetFilter" class="hidden px-4.5 py-2.5 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/60 text-slate-600 dark:text-slate-400 text-xs font-semibold rounded-xl transition duration-200 cursor-pointer">
                        Reset
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-100/80 dark:bg-slate-900/80 border-b border-slate-200 dark:border-slate-800/80 text-slate-400 dark:text-slate-500 text-xs font-bold tracking-wider uppercase">
                            <th class="px-6 py-4.5">Tanggal Input</th>
                            <th class="px-6 py-4.5">Kode</th>
                            <th class="px-6 py-4.5">Nama Barang</th>
                            <th class="px-6 py-4.5">Staf Gudang</th>
                            <th class="px-6 py-4.5 text-center">Tipe</th>
                            <th class="px-6 py-4.5 text-center">Jumlah</th>
                            <th class="px-6 py-4.5">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody id="tabelMutasi" class="divide-y divide-slate-100 dark:divide-slate-800/80 text-sm">
                    </tbody>
                </table>
            </div>

            <div id="tabelEmptyState" class="hidden flex flex-col items-center justify-center p-12 text-center text-slate-500 dark:text-slate-400">
                <svg class="w-16 h-16 text-slate-300 dark:text-slate-700 mb-4 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <h4 class="font-bold text-lg text-slate-700 dark:text-slate-300">Tidak Ada Data Mutasi</h4>
                <p class="text-sm mt-1 text-slate-400">Silakan catat mutasi barang baru atau sesuaikan filter pencarian Anda.</p>
            </div>

            <div id="tabelFooter" class="flex flex-col sm:flex-row items-center justify-between gap-4 px-6 py-4 border-t border-slate-200 dark:border-slate-800/80 bg-slate-50/50 dark:bg-slate-900/30">
                <span id="txtPaginationInfo" class="text-xs text-slate-500 dark:text-slate-400">Menampilkan 0 - 0 dari 0 transaksi</span>
                <div class="flex gap-2">
                    <button type="button" id="btnPrevPage" class="px-4 py-2 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/60 disabled:opacity-40 disabled:pointer-events-none text-slate-600 dark:text-slate-400 text-xs font-semibold rounded-lg transition duration-200 cursor-pointer">
                        Kembali
                    </button>
                    <button type="button" id="btnNextPage" class="px-4 py-2 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/60 disabled:opacity-40 disabled:pointer-events-none text-slate-600 dark:text-slate-400 text-xs font-semibold rounded-lg transition duration-200 cursor-pointer">
                        Selanjutnya
                    </button>
                </div>
            </div>
        </section>
    </main>

    <div id="modalMutasi" class="fixed inset-0 z-50 flex items-center justify-center p-4 opacity-0 pointer-events-none transition-opacity duration-300">
        <div class="fixed inset-0 bg-slate-950/50 dark:bg-slate-950/70 backdrop-blur-sm"></div>

        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl w-full max-w-md max-h-[90vh] overflow-y-auto shadow-2xl relative z-10 transform translate-y-4 transition-transform duration-300">
            <div class="flex items-center justify-between px-6 py-4.5 border-b border-slate-200 dark:border-slate-800">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Catat Mutasi Stok</h3>
                <button type="button" class="btnTutupModal p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="formMutasi" class="p-6 space-y-5" novalidate>
                <div class="space-y-1.5">
                    <label for="formBarangId" class="text-xs font-semibold text-slate-500 dark:text-slate-400">Pilih Barang <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <select id="formBarangId" name="item_id" required class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 focus:bg-white focus:border-blue-500 focus:outline-none dark:focus:bg-slate-950 text-sm rounded-lg text-slate-900 dark:text-slate-100 transition-colors duration-200 appearance-none cursor-pointer">
                            <option value="">Memuat barang...</option>
                        </select>
                        <div class="absolute right-4 top-4 text-slate-400 pointer-events-none">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label for="formType" class="text-xs font-semibold text-slate-500 dark:text-slate-400">Jenis Mutasi <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <select id="formType" name="type" required class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 focus:bg-white focus:border-blue-500 focus:outline-none dark:focus:bg-slate-950 text-sm rounded-lg text-slate-900 dark:text-slate-100 transition-colors duration-200 appearance-none cursor-pointer">
                            <option value="IN">Barang Masuk (IN)</option>
                            <option value="OUT">Barang Keluar (OUT)</option>
                        </select>
                        <div class="absolute right-4 top-4 text-slate-400 pointer-events-none">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label for="formJumlah" class="text-xs font-semibold text-slate-500 dark:text-slate-400">Jumlah Unit <span class="text-rose-500">*</span></label>
                    <input type="number" id="formJumlah" name="quantity" min="1" placeholder="Masukkan jumlah unit" required class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-950/50 border border-slate-250 dark:border-slate-800 focus:bg-white focus:border-blue-500 focus:outline-none dark:focus:bg-slate-950 text-sm rounded-lg text-slate-900 dark:text-slate-100 transition-colors duration-200">
                </div>

                <div class="space-y-1.5">
                    <label for="formKeterangan" class="text-xs font-semibold text-slate-500 dark:text-slate-400">Keterangan / Alasan</label>
                    <textarea id="formKeterangan" name="note" rows="3" placeholder="Masukkan alasan mutasi (opsional)" class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-950/50 border border-slate-250 dark:border-slate-800 focus:bg-white focus:border-blue-500 focus:outline-none dark:focus:bg-slate-950 text-sm rounded-lg text-slate-900 dark:text-slate-100 transition-colors duration-200 resize-none"></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" class="btnTutupModal px-4.5 py-2.5 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/65 text-slate-600 dark:text-slate-400 text-xs font-semibold rounded-xl transition duration-200 cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-gradient-to-tr from-blue-600 to-indigo-500 hover:from-blue-700 hover:to-indigo-600 text-white font-semibold rounded-xl text-xs transition duration-200 shadow-md shadow-blue-500/10 cursor-pointer">
                        Simpan Transaksi
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
