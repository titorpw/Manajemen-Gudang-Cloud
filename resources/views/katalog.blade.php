<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Katalog Barang - Manajemen Gudang</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
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

<body class="bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-slate-100 min-h-screen md:h-screen md:overflow-hidden flex flex-col md:flex-row relative overflow-x-hidden selection:bg-blue-500/30 selection:text-blue-200 transition-colors duration-300">

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
                <div class="flex items-center justify-between px-4 py-3 rounded-xl text-slate-400 dark:text-slate-600 font-medium cursor-not-allowed select-none">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                        </svg>
                        <span class="link-text">Dashboard</span>
                    </div>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-500 tracking-wide uppercase scale-90 link-badge">Soon</span>
                </div>
                <a href="/katalog" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 transition-all duration-200 font-semibold shadow-sm shadow-blue-500/5">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <span class="link-text">Katalog Barang</span>
                </a>
                <div class="flex items-center justify-between px-4 py-3 rounded-xl text-slate-400 dark:text-slate-600 font-medium cursor-not-allowed select-none">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>
                        <span class="link-text">Mutasi Stok</span>
                    </div>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-500 tracking-wide uppercase scale-90 link-badge">Soon</span>
                </div>
            </nav>
        </div>

        <div class="flex flex-col gap-5 border-t border-slate-200 dark:border-slate-800 pt-5">

            <div class="flex items-center gap-3 px-2 user-profile-container">
                <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-slate-200 to-slate-300 dark:from-slate-800 dark:to-slate-700 flex items-center justify-center shadow-sm shrink-0">
                    <span id="userInitial" class="font-bold text-sm text-slate-700 dark:text-slate-300">U</span>
                </div>
                <div class="flex flex-col min-w-0 flex-1 user-info-text">
                    <span id="userName" class="font-bold text-sm text-slate-800 dark:text-slate-200 truncate">Nama User</span>
                    <span id="userRoleBadge" class="inline-flex self-start px-2 py-0.5 rounded text-[10px] font-bold tracking-wide uppercase mt-1">Staf</span>
                </div>
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

    <main id="katalogPage" class="flex-1 p-6 md:p-10 w-full overflow-y-auto max-w-7xl mx-auto space-y-8">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight bg-gradient-to-r from-slate-900 via-slate-800 to-slate-600 dark:from-white dark:via-slate-100 dark:to-slate-400 bg-clip-text text-transparent">Katalog Barang</h1>
                <p class="text-slate-500 dark:text-slate-400 mt-1 text-sm">Kelola data barang, lokasi rak penyimpanan, dan stok gudang.</p>
            </div>
            <button type="button" id="btnTambahBarang" class="inline-flex items-center gap-2 px-5 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-semibold rounded-xl text-sm shadow-md shadow-blue-500/15 hover:shadow-blue-500/25 active:scale-98 transition-all duration-200 cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Barang Baru
            </button>
        </div>

        <section class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div class="backdrop-blur-xl bg-white/80 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-6 shadow-sm flex items-center gap-4 transition-all duration-300 hover:shadow-md">
                <div class="w-12 h-12 rounded-xl bg-blue-500/10 text-blue-500 dark:text-blue-400 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <div>
                    <span class="text-xs font-semibold text-slate-400 dark:text-slate-500 tracking-wider uppercase">Total Jenis Barang</span>
                    <h3 id="statTotalItems" class="text-2xl font-bold text-slate-800 dark:text-slate-200 mt-1">0</h3>
                </div>
            </div>

            <div class="backdrop-blur-xl bg-white/80 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-6 shadow-sm flex items-center gap-4 transition-all duration-300 hover:shadow-md">
                <div class="w-12 h-12 rounded-xl bg-indigo-500/10 text-indigo-500 dark:text-indigo-400 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <div>
                    <span class="text-xs font-semibold text-slate-400 dark:text-slate-500 tracking-wider uppercase">Total Stok Unit</span>
                    <h3 id="statTotalStock" class="text-2xl font-bold text-slate-800 dark:text-slate-200 mt-1">0</h3>
                </div>
            </div>

            <div class="backdrop-blur-xl bg-white/80 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-6 shadow-sm flex items-center gap-4 transition-all duration-300 hover:shadow-md">
                <div id="statLowStockBg" class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-500 dark:text-emerald-400 flex items-center justify-center shrink-0 transition-colors duration-300">
                    <svg id="statLowStockIcon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <span class="text-xs font-semibold text-slate-400 dark:text-slate-500 tracking-wider uppercase">Stok Menipis (Kritis)</span>
                    <h3 id="statLowStock" class="text-2xl font-bold text-slate-800 dark:text-slate-200 mt-1">0</h3>
                </div>
            </div>
        </section>

        <section class="backdrop-blur-xl bg-white/80 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-6 shadow-sm flex flex-col md:flex-row gap-4">

            <div class="flex-1 relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" id="searchBarang" placeholder="Cari berdasarkan nama atau kode barang..." class="w-full pl-11 pr-4 py-3 bg-slate-100/50 hover:bg-slate-100/80 border border-slate-200/85 hover:border-slate-300 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:border-blue-500 focus:outline-none dark:bg-slate-950/30 dark:hover:bg-slate-950/50 dark:border-slate-800 dark:hover:border-slate-700 dark:focus:bg-slate-950 dark:text-slate-100 dark:placeholder-slate-600 transition-all duration-200">
            </div>

            <div class="w-full md:w-56">
                <select id="filterKategori" class="w-full px-4 py-3 bg-slate-100/50 hover:bg-slate-100/80 border border-slate-200/85 hover:border-slate-300 rounded-xl text-sm text-slate-700 dark:text-slate-300 focus:bg-white focus:border-blue-500 focus:outline-none dark:bg-slate-950/30 dark:hover:bg-slate-950/50 dark:border-slate-800 dark:hover:border-slate-700 dark:focus:bg-slate-950 transition-all duration-200">
                    <option value="">Semua Kategori</option>
                    <option value="Elektronik">Elektronik</option>
                    <option value="Peralatan">Peralatan</option>
                    <option value="Suku Cadang">Suku Cadang</option>
                    <option value="Bahan Baku">Bahan Baku</option>
                    <option value="ATK">ATK</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>

            <div class="w-full md:w-48">
                <select id="filterRak" class="w-full px-4 py-3 bg-slate-100/50 hover:bg-slate-100/80 border border-slate-200/85 hover:border-slate-300 rounded-xl text-sm text-slate-700 dark:text-slate-300 focus:bg-white focus:border-blue-500 focus:outline-none dark:bg-slate-950/30 dark:hover:bg-slate-950/50 dark:border-slate-800 dark:hover:border-slate-700 dark:focus:bg-slate-950 transition-all duration-200">
                    <option value="">Semua Lokasi Rak</option>

                </select>
            </div>

            <button type="button" id="btnResetFilters" class="px-5 py-3 border border-slate-200 dark:border-slate-800 text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/50 rounded-xl text-sm transition-colors duration-200 font-semibold cursor-pointer">
                Reset
            </button>
        </section>

        <section class="backdrop-blur-xl bg-white/80 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden transition-all duration-300">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left">
                    <thead>
                        <tr class="bg-slate-100/80 dark:bg-slate-900/80 border-b border-slate-200 dark:border-slate-800 text-slate-400 dark:text-slate-500 text-xs font-bold tracking-wider uppercase">
                            <th class="px-6 py-4.5">Foto</th>
                            <th class="px-6 py-4.5">Kode</th>
                            <th class="px-6 py-4.5">Nama Barang</th>
                            <th class="px-6 py-4.5">Kategori</th>
                            <th class="px-6 py-4.5">Lokasi Rak</th>
                            <th class="px-6 py-4.5 text-center">Stok</th>
                            <th class="px-6 py-4.5 text-center">Limit</th>
                            <th class="px-6 py-4.5 text-right action-col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tabelBarang" class="divide-y divide-slate-100 dark:divide-slate-800/80 text-sm">

                    </tbody>
                </table>
            </div>

            <div id="tabelEmptyState" class="hidden flex-col items-center justify-center p-12 text-center text-slate-500 dark:text-slate-400">
                <svg class="w-16 h-16 text-slate-300 dark:text-slate-700 mb-4 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <h4 class="font-bold text-lg text-slate-700 dark:text-slate-300">Tidak Ada Data Barang</h4>
                <p class="text-sm mt-1 text-slate-400">Silakan tambahkan data barang baru atau sesuaikan filter pencarian Anda.</p>
            </div>

            <div id="tabelFooter" class="flex flex-col sm:flex-row items-center justify-between gap-4 px-6 py-4 border-t border-slate-200 dark:border-slate-800/80 bg-slate-50/50 dark:bg-slate-900/30">
                <span id="txtPaginationInfo" class="text-xs text-slate-500 dark:text-slate-400">Menampilkan 0 - 0 dari 0 barang</span>
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

    <div id="modalBarang" class="fixed inset-0 z-50 flex items-center justify-center p-4 opacity-0 pointer-events-none transition-opacity duration-300">
        <div class="fixed inset-0 bg-slate-950/50 dark:bg-slate-950/70 backdrop-blur-sm"></div>

        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl w-full max-w-xl max-h-[90vh] overflow-y-auto shadow-2xl relative z-10 transform translate-y-4 transition-transform duration-300">

            <div class="flex items-center justify-between px-6 py-4.5 border-b border-slate-200 dark:border-slate-800">
                <h3 id="modalTitle" class="text-lg font-bold text-slate-900 dark:text-white">Tambah Barang Baru</h3>
                <button type="button" class="btnTutupModal p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="formBarang" class="p-6 space-y-5" novalidate>
                <input type="hidden" id="barangId" name="id">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <div class="space-y-1.5">
                        <label for="formKode" class="text-xs font-semibold text-slate-500 dark:text-slate-400">Kode Barang (SKU) <span class="text-rose-500">*</span></label>
                        <input type="text" id="formKode" name="kode_barang" placeholder="BRG-XXXXX" required class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-950/50 border border-slate-250 dark:border-slate-800 focus:bg-white focus:border-blue-500 focus:outline-none dark:focus:bg-slate-950 text-sm rounded-lg text-slate-900 dark:text-slate-100 transition-colors duration-200">
                        <span id="formKodeError" class="text-[10px] text-rose-500 hidden mt-1">Kode barang harus diisi.</span>
                    </div>

                    <div class="space-y-1.5">
                        <label for="formNama" class="text-xs font-semibold text-slate-500 dark:text-slate-400">Nama Barang <span class="text-rose-500">*</span></label>
                        <input type="text" id="formNama" name="nama_barang" placeholder="Masukkan nama barang" required class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-950/50 border border-slate-250 dark:border-slate-800 focus:bg-white focus:border-blue-500 focus:outline-none dark:focus:bg-slate-950 text-sm rounded-lg text-slate-900 dark:text-slate-100 transition-colors duration-200">
                        <span id="formNamaError" class="text-[10px] text-rose-500 hidden mt-1">Nama barang harus diisi.</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <div class="space-y-1.5">
                        <label for="formKategori" class="text-xs font-semibold text-slate-500 dark:text-slate-400">Kategori <span class="text-rose-500">*</span></label>
                        <select id="formKategori" name="kategori" required class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-950/50 border border-slate-250 dark:border-slate-800 focus:bg-white focus:border-blue-500 focus:outline-none dark:focus:bg-slate-950 text-sm rounded-lg text-slate-700 dark:text-slate-300 transition-colors duration-200">
                            <option value="">Pilih Kategori</option>
                            <option value="Elektronik">Elektronik</option>
                            <option value="Peralatan">Peralatan</option>
                            <option value="Suku Cadang">Suku Cadang</option>
                            <option value="Bahan Baku">Bahan Baku</option>
                            <option value="ATK">ATK</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                        <span id="formKategoriError" class="text-[10px] text-rose-500 hidden mt-1">Pilih kategori barang.</span>
                    </div>

                    <div class="space-y-1.5">
                        <label for="formRak" class="text-xs font-semibold text-slate-500 dark:text-slate-400">Lokasi Rak <span class="text-rose-500">*</span></label>
                        <input type="text" id="formRak" name="lokasi_rak" placeholder="Contoh: A-01" required class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-950/50 border border-slate-250 dark:border-slate-800 focus:bg-white focus:border-blue-500 focus:outline-none dark:focus:bg-slate-950 text-sm rounded-lg text-slate-900 dark:text-slate-100 transition-colors duration-200">
                        <span id="formRakError" class="text-[10px] text-rose-500 hidden mt-1">Lokasi rak harus diisi.</span>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label for="formDeskripsi" class="text-xs font-semibold text-slate-500 dark:text-slate-400">Deskripsi Barang</label>
                    <textarea id="formDeskripsi" name="deskripsi" placeholder="Jelaskan spesifikasi detail barang di sini..." rows="3" class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-950/50 border border-slate-250 dark:border-slate-800 focus:bg-white focus:border-blue-500 focus:outline-none dark:focus:bg-slate-950 text-sm rounded-lg text-slate-900 dark:text-slate-100 transition-colors duration-200 resize-none"></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <div class="space-y-1.5" id="formStokContainer">
                        <label for="formStok" class="text-xs font-semibold text-slate-500 dark:text-slate-400">Stok Awal <span class="text-rose-500">*</span></label>
                        <input type="number" id="formStok" name="stok" placeholder="0" min="0" required class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-950/50 border border-slate-250 dark:border-slate-800 focus:bg-white focus:border-blue-500 focus:outline-none dark:focus:bg-slate-950 text-sm rounded-lg text-slate-900 dark:text-slate-100 transition-colors duration-200">
                        <span id="formStokError" class="text-[10px] text-rose-500 hidden mt-1">Stok awal harus angka minimal 0.</span>
                    </div>

                    <div class="space-y-1.5">
                        <label for="formLimitStok" class="text-xs font-semibold text-slate-500 dark:text-slate-400">Batas Limit Stok <span class="text-rose-500">*</span></label>
                        <input type="number" id="formLimitStok" name="limit_stok" placeholder="10" min="0" required class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-950/50 border border-slate-250 dark:border-slate-800 focus:bg-white focus:border-blue-500 focus:outline-none dark:focus:bg-slate-950 text-sm rounded-lg text-slate-900 dark:text-slate-100 transition-colors duration-200">
                        <span id="formLimitStokError" class="text-[10px] text-rose-500 hidden mt-1">Batas stok harus angka minimal 0.</span>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Foto Barang (Unggah ke Storage)</label>

                    <div id="fotoDropzone" class="border-2 border-dashed border-slate-250 dark:border-slate-800 rounded-2xl p-6 text-center hover:bg-slate-50/50 dark:hover:bg-slate-900/30 hover:border-blue-500 dark:hover:border-blue-500/50 cursor-pointer transition-all duration-200">
                        <input type="file" id="formFoto" name="foto" accept="image/*" class="hidden">

                        <div id="fotoDropzoneMessage" class="flex flex-col items-center justify-center space-y-2">
                            <svg class="w-8 h-8 text-slate-400 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="text-xs font-medium text-slate-600 dark:text-slate-400">Seret foto barang di sini, atau klik untuk memilih file</span>
                            <span class="text-[10px] text-slate-400">Format yang didukung: JPG, PNG, JPEG, GIF (Maksimal 2 MB)</span>
                        </div>

                        <div id="fotoPreviewContainer" class="hidden flex-col items-center justify-center space-y-3 relative group">
                            <img id="fotoPreview" src="" alt="Pratinjau Foto" class="max-h-40 rounded-lg shadow-sm border border-slate-200 dark:border-slate-800 object-contain">
                            <button type="button" id="btnHapusPreview" class="absolute -top-2 -right-2 p-1 rounded-full bg-rose-600 text-white hover:bg-rose-500 shadow-md transition-colors cursor-pointer" title="Hapus foto">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-2 border-t border-slate-200 dark:border-slate-800 pt-5">
                    <button type="button" class="btnTutupModal px-5 py-2.5 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/60 rounded-xl text-xs font-semibold text-slate-500 hover:text-slate-700 dark:text-slate-400 transition duration-200 cursor-pointer">
                        Batalkan
                    </button>
                    <button type="submit" id="btnSimpanBarang" class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-semibold rounded-xl text-xs shadow-md shadow-blue-500/15 hover:shadow-blue-500/25 transition duration-200 cursor-pointer flex items-center justify-center gap-2">
                        <svg id="formSpinner" class="w-4 h-4 text-white animate-spin hidden" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="modalHapus" class="fixed inset-0 z-50 flex items-center justify-center p-4 opacity-0 pointer-events-none transition-opacity duration-300">
        <div class="fixed inset-0 bg-slate-950/50 dark:bg-slate-950/70 backdrop-blur-sm"></div>

        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl w-full max-w-sm overflow-hidden shadow-2xl relative z-10 transform translate-y-4 transition-transform duration-300 p-6 space-y-5">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-full bg-rose-500/10 text-rose-500 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="space-y-1">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Hapus Barang?</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">Apakah Anda yakin ingin menghapus barang <strong id="txtNamaBarangHapus" class="text-slate-800 dark:text-slate-200"></strong> ini? Tindakan ini tidak dapat dibatalkan.</p>
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" id="btnBatalHapus" class="px-4.5 py-2.5 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/60 rounded-xl text-xs font-semibold text-slate-500 hover:text-slate-700 dark:text-slate-400 transition duration-200 cursor-pointer">
                    Batalkan
                </button>
                <button type="button" id="btnKonfirmasiHapus" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-500 text-white font-semibold rounded-xl text-xs shadow-md shadow-rose-500/15 hover:shadow-rose-500/25 transition duration-200 cursor-pointer">
                    Ya, Hapus Data
                </button>
            </div>
        </div>
    </div>
</body>

</html>
