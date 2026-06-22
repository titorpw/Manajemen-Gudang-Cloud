import { ApiClient } from '../services/api';
import { ThemeService } from '../services/theme';
import { Chart } from 'chart.js/auto';

export class DashboardController {
    constructor() {
        this.state = {
            items: [],
            mutations: [],
            user: {
                name: localStorage.getItem('user_name') || 'User Gudang',
                role: localStorage.getItem('user_role') || 'staf'
            },
            isMockMode: false
        };

        this.chart = null;

        this.defaultDummyItems = [
            {
                id: 1,
                kode_barang: 'BRG-EL001',
                nama_barang: 'Barcode Scanner Wireless Honeywell',
                kategori: 'Elektronik',
                stok: 15,
                limit_stok: 5,
                lokasi_rak: 'Rak A-01'
            },
            {
                id: 2,
                kode_barang: 'BRG-EL002',
                nama_barang: 'Printer Label Thermal Xprinter XP-365B',
                kategori: 'Elektronik',
                stok: 4,
                limit_stok: 5,
                lokasi_rak: 'Rak A-02'
            },
            {
                id: 3,
                kode_barang: 'BRG-PR001',
                nama_barang: 'Pallet Plastik Heavy Duty 120x100cm',
                kategori: 'Peralatan',
                stok: 50,
                limit_stok: 10,
                lokasi_rak: 'Rak B-01'
            },
            {
                id: 4,
                kode_barang: 'BRG-ATK01',
                nama_barang: 'Kertas Thermal Roll 80x80mm (Pack)',
                kategori: 'ATK',
                stok: 120,
                limit_stok: 20,
                lokasi_rak: 'Rak C-01'
            },
            {
                id: 5,
                kode_barang: 'BRG-SC001',
                nama_barang: 'Roda Castor PU 4 Inch Swivel (Pcs)',
                kategori: 'Suku Cadang',
                stok: 2,
                limit_stok: 8,
                lokasi_rak: 'Rak B-04'
            }
        ];

        this.defaultDummyMutations = [
            {
                id: 1,
                item_id: 1,
                kode_barang: 'BRG-EL001',
                nama_barang: 'Barcode Scanner Wireless Honeywell',
                staf_gudang: 'Ahmad Staf Gudang',
                jenis_mutasi: 'IN',
                jumlah: 10,
                tanggal_input: new Date(Date.now() - 3600000 * 24).toISOString() // 1 day ago
            },
            {
                id: 2,
                item_id: 2,
                kode_barang: 'BRG-EL002',
                nama_barang: 'Printer Label Thermal Xprinter XP-365B',
                staf_gudang: 'Ahmad Staf Gudang',
                jenis_mutasi: 'OUT',
                jumlah: 2,
                tanggal_input: new Date(Date.now() - 3600000 * 12).toISOString() // 12 hours ago
            },
            {
                id: 3,
                item_id: 3,
                kode_barang: 'BRG-PR001',
                nama_barang: 'Pallet Plastik Heavy Duty 120x100cm',
                staf_gudang: 'Ahmad Staf Gudang',
                jenis_mutasi: 'IN',
                jumlah: 5,
                tanggal_input: new Date(Date.now() - 3600000 * 2).toISOString() // 2 hours ago
            }
        ];

        this.initDOM();
    }

    initDOM() {
        this.userNameEl = document.getElementById('userName');
        this.welcomeUserNameEl = document.getElementById('welcomeUserName');
        this.userRoleBadgeEl = document.getElementById('userRoleBadge');
        this.userInitialEl = document.getElementById('userInitial');
        this.mobileMenuBtn = document.getElementById('mobileMenuBtn');
        this.sidebar = document.getElementById('sidebar');
        this.sidebarOverlay = document.getElementById('sidebarOverlay');
        this.themeToggleBtn = document.getElementById('sidebarThemeToggle');
        this.logoutBtn = document.getElementById('logoutBtn');
        this.desktopCollapseBtn = document.getElementById('desktopCollapseBtn');

        this.statTotalItems = document.getElementById('statTotalItems');
        this.statTotalStock = document.getElementById('statTotalStock');
        this.statLowStock = document.getElementById('statLowStock');
        this.statLowStockIconWrapper = document.getElementById('statLowStockIconWrapper');
        this.statLowStockIcon = document.getElementById('statLowStockIcon');

        this.statTotalMutasi = document.getElementById('statTotalMutasi');
        this.statMasukMutasi = document.getElementById('statMasukMutasi');
        this.statKeluarMutasi = document.getElementById('statKeluarMutasi');

        this.lowStockWarningSection = document.getElementById('lowStockWarningSection');
        this.lowStockItemsList = document.getElementById('lowStockItemsList');
        this.analyticsChartSection = document.getElementById('analyticsChartSection');
        this.shortcutsSection = document.getElementById('shortcutsSection');

        this.dbModeEl = document.getElementById('dbMode');
    }

    async init() {
        ThemeService.init();
        this.initLayoutEvents();
        await this.loadUserProfile();
        await this.loadData();
        this.observeThemeChanges();
    }

    initLayoutEvents() {
        const isCollapsed = localStorage.getItem('sidebar_collapsed') === 'true';
        if (isCollapsed && this.sidebar && window.innerWidth >= 768) {
            this.sidebar.classList.remove('w-72');
            this.sidebar.classList.add('w-20');
        }

        if (this.desktopCollapseBtn && this.sidebar) {
            this.desktopCollapseBtn.addEventListener('click', () => {
                const collapsed = this.sidebar.classList.contains('w-20');
                if (collapsed) {
                    this.sidebar.classList.remove('w-20');
                    this.sidebar.classList.add('w-72');
                    localStorage.setItem('sidebar_collapsed', 'false');
                } else {
                    this.sidebar.classList.remove('w-72');
                    this.sidebar.classList.add('w-20');
                    localStorage.setItem('sidebar_collapsed', 'true');
                }
            });
        }

        if (this.mobileMenuBtn && this.sidebar && this.sidebarOverlay) {
            const toggleSidebar = () => {
                const isOpen = this.sidebar.classList.contains('translate-x-0');
                if (isOpen) {
                    this.sidebar.classList.remove('translate-x-0');
                    this.sidebar.classList.add('-translate-x-full');
                    this.sidebarOverlay.classList.remove('opacity-100');
                    this.sidebarOverlay.classList.add('opacity-0', 'pointer-events-none');
                } else {
                    this.sidebar.classList.remove('-translate-x-full');
                    this.sidebar.classList.add('translate-x-0');
                    this.sidebarOverlay.classList.remove('opacity-0', 'pointer-events-none');
                    this.sidebarOverlay.classList.add('opacity-100');
                }
            };
            this.mobileMenuBtn.addEventListener('click', toggleSidebar);
            this.sidebarOverlay.addEventListener('click', toggleSidebar);
        }

        if (this.themeToggleBtn) {
            this.themeToggleBtn.addEventListener('click', () => {
                ThemeService.toggle();
            });
        }

        if (this.logoutBtn) {
            this.logoutBtn.addEventListener('click', () => {
                localStorage.removeItem('access_token');
                localStorage.removeItem('user_role');
                ApiClient.post('/api/logout').catch(() => { });
                window.location.href = '/login';
            });
        }
    }

    async loadUserProfile() {
        const storedRole = localStorage.getItem('user_role') || 'staf';
        const storedName = localStorage.getItem('user_name') || (storedRole === 'staf' ? 'Ahmad Staf Gudang' : 'Hendra Manager');
        this.state.user = {
            name: storedName,
            role: storedRole
        };

        try {
            const response = await ApiClient.get('/api/user');
            if (response.success && response.data) {
                this.state.user.name = response.data.name;
                this.state.user.role = response.data.role || storedRole;
                localStorage.setItem('user_role', this.state.user.role);
            }
        } catch (e) {
            console.warn('Gagal memuat profil user dari API, menggunakan data offline.');
        }

        localStorage.setItem('user_name', this.state.user.name);
        if (this.userNameEl) this.userNameEl.textContent = this.state.user.name;
        if (this.welcomeUserNameEl) this.welcomeUserNameEl.textContent = this.state.user.name;
        if (this.userInitialEl) this.userInitialEl.textContent = this.state.user.name.charAt(0).toUpperCase();
        if (this.userRoleBadgeEl) {
            this.userRoleBadgeEl.textContent = this.state.user.role === 'staf' ? 'Staf Gudang (Admin)' : 'Manager Gudang';
            if (this.state.user.role === 'staf') {
                this.userRoleBadgeEl.className = "inline-flex self-start px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wide uppercase mt-1.5 bg-blue-500/10 text-blue-500 dark:bg-blue-500/20";
            } else {
                this.userRoleBadgeEl.className = "inline-flex self-start px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wide uppercase mt-1.5 bg-amber-500/10 text-amber-500 dark:bg-amber-500/20";
            }
        }
    }

    async loadData() {
        let barangLoaded = false;
        let mutasiLoaded = false;

        try {
            const response = await ApiClient.get('/api/barang');
            if (response.success && response.data) {
                this.state.items = response.data.data || response.data;
                barangLoaded = true;
            }
        } catch (e) {
            console.warn('Gagal mengambil data barang dari REST API.');
        }

        try {
            const response = await ApiClient.get('/api/mutasi');
            if (response.success && response.data) {
                this.state.mutations = response.data;
                mutasiLoaded = true;
            }
        } catch (e) {
            console.warn('Gagal mengambil data mutasi dari REST API.');
        }

        if (!barangLoaded || !mutasiLoaded) {
            this.state.isMockMode = true;
            if (this.dbModeEl) this.dbModeEl.textContent = 'LocalStorage Fallback';
            if (this.dbModeEl) this.dbModeEl.className = 'text-amber-500 font-semibold';

            const localItems = localStorage.getItem('gudang_cloud_items') || localStorage.getItem('mock_items');
            if (localItems) {
                this.state.items = JSON.parse(localItems);
            } else {
                this.state.items = [...this.defaultDummyItems];
                localStorage.setItem('gudang_cloud_items', JSON.stringify(this.state.items));
            }

            const localMutations = localStorage.getItem('mock_mutations');
            if (localMutations) {
                this.state.mutations = JSON.parse(localMutations);
            } else {
                this.state.mutations = [...this.defaultDummyMutations];
                localStorage.setItem('mock_mutations', JSON.stringify(this.state.mutations));
            }
        } else {
            if (this.dbModeEl) this.dbModeEl.textContent = 'REST API';
            if (this.dbModeEl) this.dbModeEl.className = 'text-blue-500 font-semibold';
        }

        this.renderStats();
        this.renderRoleLayouts();
    }

    renderStats() {
        const totalItems = this.state.items.length;
        const totalStock = this.state.items.reduce((acc, curr) => acc + Number(curr.stok || 0), 0);
        const lowStockCount = this.state.items.filter(item => Number(item.stok || 0) <= Number(item.limit_stok || 10)).length;

        const totalMutations = this.state.mutations.length;
        let incomingVolume = 0;
        let outgoingVolume = 0;

        this.state.mutations.forEach(m => {
            const qty = Number(m.jumlah || m.quantity || 0);
            const type = m.jenis_mutasi || m.type;
            if (type === 'IN') {
                incomingVolume += qty;
            } else if (type === 'OUT') {
                outgoingVolume += qty;
            }
        });

        if (this.statTotalItems) this.statTotalItems.textContent = totalItems.toLocaleString('id-ID');
        if (this.statTotalStock) this.statTotalStock.textContent = totalStock.toLocaleString('id-ID');
        if (this.statLowStock) this.statLowStock.textContent = lowStockCount.toLocaleString('id-ID');
        if (this.statTotalMutasi) this.statTotalMutasi.textContent = totalMutations.toLocaleString('id-ID');
        if (this.statMasukMutasi) this.statMasukMutasi.textContent = incomingVolume.toLocaleString('id-ID');
        if (this.statKeluarMutasi) this.statKeluarMutasi.textContent = outgoingVolume.toLocaleString('id-ID');

        if (this.statLowStockIconWrapper && this.statLowStockIcon) {
            if (lowStockCount > 0) {
                this.statLowStockIconWrapper.className = "p-1 rounded-lg bg-rose-500/10 text-rose-500 dark:bg-rose-500/20 dark:text-rose-400 shrink-0 animate-pulse";
                this.statLowStockIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />`;
            } else {
                this.statLowStockIconWrapper.className = "p-1 rounded-lg bg-emerald-500/10 text-emerald-500 dark:bg-emerald-500/20 dark:text-emerald-450 shrink-0";
                this.statLowStockIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />`;
            }
        }
    }

    renderRoleLayouts() {
        const isManager = this.state.user.role === 'manager';

        if (this.analyticsChartSection) this.analyticsChartSection.classList.remove('hidden');
        this.renderChart();

        if (isManager) {
            if (this.shortcutsSection) this.shortcutsSection.classList.add('hidden');

            this.renderLowStock();
            if (this.lowStockWarningSection) {
                const lowStockCount = this.state.items.filter(item => Number(item.stok || 0) <= Number(item.limit_stok || 10)).length;
                if (lowStockCount > 0) {
                    this.lowStockWarningSection.classList.remove('hidden');
                } else {
                    this.lowStockWarningSection.classList.add('hidden');
                }
            }
        } else {
            if (this.shortcutsSection) this.shortcutsSection.classList.remove('hidden');

            this.renderLowStock();
            if (this.lowStockWarningSection) {
                this.lowStockWarningSection.classList.remove('hidden');
            }
        }
    }

    renderLowStock() {
        if (!this.lowStockItemsList) return;

        const lowStockItems = this.state.items.filter(item => Number(item.stok || 0) <= Number(item.limit_stok || 10));

        if (lowStockItems.length === 0) {
            this.lowStockItemsList.innerHTML = `
                <tr>
                    <td colspan="5" class="py-6 text-center text-slate-400 dark:text-slate-500 font-medium">
                        Semua stok barang dalam kondisi aman.
                    </td>
                </tr>
            `;
            return;
        }

        let html = '';
        lowStockItems.forEach(item => {
            const isZero = Number(item.stok) === 0;
            const textClass = isZero ? 'text-rose-600 dark:text-rose-400 font-bold' : 'text-amber-600 dark:text-amber-400 font-bold';

            html += `
                <tr class="border-b border-slate-100/50 dark:border-slate-800/50 hover:bg-slate-50/50 dark:hover:bg-slate-900/30 transition-colors">
                    <td class="py-3 font-mono text-xs font-semibold text-slate-600 dark:text-slate-400">${item.kode_barang}</td>
                    <td class="py-3 font-semibold text-slate-850 dark:text-slate-200">${item.nama_barang}</td>
                    <td class="py-3 text-center ${textClass}">${item.stok}</td>
                    <td class="py-3 text-center text-slate-500 dark:text-slate-400">${item.limit_stok}</td>
                    <td class="py-3 text-slate-500 dark:text-slate-400">${item.lokasi_rak || '-'}</td>
                </tr>
            `;
        });

        this.lowStockItemsList.innerHTML = html;
    }

    renderChart() {
        const ctx = document.getElementById('mutationsTrendChart');
        if (!ctx) return;

        const dates = [];
        for (let i = 6; i >= 0; i--) {
            const d = new Date();
            d.setDate(d.getDate() - i);
            dates.push(d.toISOString().split('T')[0]);
        }

        const inMap = {};
        const outMap = {};
        dates.forEach(d => {
            inMap[d] = 0;
            outMap[d] = 0;
        });

        this.state.mutations.forEach(m => {
            const rawDate = m.tanggal_input || m.created_at;
            if (!rawDate) return;
            const dateStr = rawDate.split('T')[0];

            if (inMap[dateStr] !== undefined) {
                const qty = Number(m.jumlah || m.quantity || 0);
                const type = m.jenis_mutasi || m.type;
                if (type === 'IN') {
                    inMap[dateStr] += qty;
                } else if (type === 'OUT') {
                    outMap[dateStr] += qty;
                }
            }
        });

        const labels = dates.map(dateStr => {
            const d = new Date(dateStr);
            return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
        });

        const dataIn = dates.map(d => inMap[d]);
        const dataOut = dates.map(d => outMap[d]);

        const isDark = ThemeService.isDark();
        const fontColor = isDark ? '#94a3b8' : '#64748b';
        const gridColor = isDark ? '#1e293b' : '#f1f5f9';
        const emeraldColor = isDark ? '#34d399' : '#10b981';
        const roseColor = isDark ? '#fb7185' : '#f43f5e';

        if (this.chart) {
            this.chart.destroy();
        }

        this.chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Barang Masuk (IN)',
                        data: dataIn,
                        borderColor: emeraldColor,
                        backgroundColor: emeraldColor + '15',
                        tension: 0.35,
                        fill: true,
                        borderWidth: 2.5,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    },
                    {
                        label: 'Barang Keluar (OUT)',
                        data: dataOut,
                        borderColor: roseColor,
                        backgroundColor: roseColor + '15',
                        tension: 0.35,
                        fill: true,
                        borderWidth: 2.5,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            color: fontColor,
                            font: {
                                family: 'Instrument Sans',
                                size: 11,
                                weight: '500'
                            },
                            boxWidth: 12,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        backgroundColor: isDark ? '#0f172a' : '#ffffff',
                        titleColor: isDark ? '#f8fafc' : '#0f172a',
                        bodyColor: isDark ? '#cbd5e1' : '#334155',
                        borderColor: isDark ? '#334155' : '#e2e8f0',
                        borderWidth: 1,
                        padding: 10,
                        titleFont: { family: 'Instrument Sans', weight: 'bold' },
                        bodyFont: { family: 'Instrument Sans' }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: fontColor,
                            font: {
                                family: 'Instrument Sans',
                                size: 10
                            }
                        }
                    },
                    y: {
                        grid: {
                            color: gridColor
                        },
                        ticks: {
                            color: fontColor,
                            font: {
                                family: 'Instrument Sans',
                                size: 10
                            },
                            precision: 0
                        },
                        border: {
                            dash: [4, 4]
                        }
                    }
                }
            }
        });
    }

    observeThemeChanges() {
        const observer = new MutationObserver(() => {
            if (this.chart) {
                const isDark = ThemeService.isDark();
                const fontColor = isDark ? '#94a3b8' : '#64748b';
                const gridColor = isDark ? '#1e293b' : '#f1f5f9';
                const emeraldColor = isDark ? '#34d399' : '#10b981';
                const roseColor = isDark ? '#fb7185' : '#f43f5e';

                this.chart.options.scales.x.ticks.color = fontColor;
                this.chart.options.scales.y.ticks.color = fontColor;
                this.chart.options.scales.y.grid.color = gridColor;
                this.chart.options.plugins.legend.labels.color = fontColor;

                this.chart.options.plugins.tooltip.backgroundColor = isDark ? '#0f172a' : '#ffffff';
                this.chart.options.plugins.tooltip.titleColor = isDark ? '#f8fafc' : '#0f172a';
                this.chart.options.plugins.tooltip.bodyColor = isDark ? '#cbd5e1' : '#334155';
                this.chart.options.plugins.tooltip.borderColor = isDark ? '#334155' : '#e2e8f0';

                this.chart.data.datasets[0].borderColor = emeraldColor;
                this.chart.data.datasets[0].backgroundColor = emeraldColor + '15';
                this.chart.data.datasets[1].borderColor = roseColor;
                this.chart.data.datasets[1].backgroundColor = roseColor + '15';

                this.chart.update();
            }
        });

        observer.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['class']
        });
    }
}
