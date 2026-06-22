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
    <title>Dashboard - Manajemen Gudang</title>

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
                <a href="/mutasi" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-slate-200 transition-all duration-200 font-medium cursor-pointer">
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

    <main id="dashboardPage" class="flex-1 p-6 md:p-10 w-full overflow-y-auto max-w-7xl mx-auto space-y-8">

        <div>
            <h1 class="text-3xl font-extrabold tracking-tight bg-gradient-to-r from-slate-900 via-slate-800 to-slate-600 dark:from-white dark:via-slate-100 dark:to-slate-400 bg-clip-text text-transparent">Dashboard Analitik</h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1 text-sm">Selamat datang kembali! Berikut adalah ringkasan operasional logistik gudang saat ini.</p>
        </div>

        <div class="relative overflow-hidden backdrop-blur-xl bg-gradient-to-tr from-blue-600/90 to-indigo-600/90 text-white rounded-3xl p-8 border border-blue-500/20 shadow-lg shadow-blue-500/10">
            <div class="relative z-10 space-y-3 max-w-xl">
                <span class="inline-flex px-3 py-1 rounded-full bg-white/20 text-white text-[10px] font-bold tracking-wide uppercase">Sistem Berhasil Terhubung</span>
                <h2 class="text-2xl md:text-3xl font-bold">Halo, <span id="welcomeUserName">User</span>!</h2>
                <p class="text-sm text-blue-100/90 leading-relaxed">Anda berhasil masuk menggunakan **Firebase Authentication** & **Laravel Sanctum**. Sesi Anda aman dan data inventaris disinkronkan secara real-time.</p>

                <div class="pt-4 flex gap-3">
                    <a href="/katalog" class="px-5 py-2.5 bg-white text-blue-600 hover:bg-slate-50 font-bold rounded-xl text-xs transition duration-200 shadow-md">
                        Buka Katalog Barang
                    </a>
                </div>
            </div>

            <div class="absolute -right-10 -bottom-10 opacity-15 pointer-events-none scale-150 transform translate-y-6">
                <svg class="w-72 h-72 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                </svg>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="backdrop-blur-xl bg-white/80 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-6 shadow-sm space-y-4">
                <h3 class="font-bold text-base text-slate-800 dark:text-slate-200 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                    Status FR-02 [Katalog]
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">Antarmuka Katalog Barang telah selesai dikerjakan! Fitur ini mencakup CRUD, upload foto barang, manajemen rak penyimpanan, pencarian real-time, filter kategori, serta pembatasan hak akses (Staf vs Manager) dengan fallback penyimpanan LocalStorage yang cerdas.</p>
                <div class="inline-flex items-center gap-1.5 text-xs text-blue-600 dark:text-blue-400 font-semibold">
                    Sudah Siap Diuji coba &rarr;
                </div>
            </div>

            <div class="backdrop-blur-xl bg-white/80 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-6 shadow-sm space-y-4">
                <h3 class="font-bold text-base text-slate-800 dark:text-slate-200 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-slate-400"></span>
                    Fitur Lainnya (FR-03, FR-04, FR-05)
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">Fitur Mutasi Barang (IN/OUT), Otomatisasi Stok via Azure Functions, dan Grafik Analitis Tren Arus Barang (Chart.js) sedang dalam antrean pengerjaan selanjutnya bersama tim backend Anda.</p>
                <div class="inline-flex items-center gap-1.5 text-xs text-slate-400 dark:text-slate-600 font-semibold cursor-not-allowed">
                    Segera Hadir (Dalam Pengembangan)
                </div>
            </div>
        </div>
    </main>

    <script>

        document.addEventListener('DOMContentLoaded', () => {
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            if (mobileMenuBtn && sidebar && sidebarOverlay) {
                const toggleSidebar = () => {
                    const isOpen = sidebar.classList.contains('translate-x-0');
                    if (isOpen) {
                        sidebar.classList.remove('translate-x-0');
                        sidebar.classList.add('-translate-x-full');
                        sidebarOverlay.classList.remove('opacity-100');
                        sidebarOverlay.classList.add('opacity-0', 'pointer-events-none');
                    } else {
                        sidebar.classList.remove('-translate-x-full');
                        sidebar.classList.add('translate-x-0');
                        sidebarOverlay.classList.remove('opacity-0', 'pointer-events-none');
                        sidebarOverlay.classList.add('opacity-100');
                    }
                };

                mobileMenuBtn.addEventListener('click', toggleSidebar);
                sidebarOverlay.addEventListener('click', toggleSidebar);
            }
        });
    </script>
</body>

</html>