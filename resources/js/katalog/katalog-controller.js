import { ApiClient } from '../services/api';
import { ToastService } from '../services/toast';
import { ThemeService } from '../services/theme';

export class KatalogController {
    constructor() {
        this.state = {
            items: [],
            filteredItems: [],
            categories: ['Elektronik', 'Peralatan', 'Suku Cadang', 'Bahan Baku', 'ATK', 'Lainnya'],
            uniqueRacks: [],
            user: { 
                name: localStorage.getItem('user_name') || 'User Gudang', 
                role: localStorage.getItem('user_role') || 'staf', 
                email: '' 
            },
            currentPage: 1,
            itemsPerPage: 5,
            totalPages: 1,
            isMockMode: false, 
            activeSearch: '',
            activeCategory: '',
            activeRak: '',
            selectedItemId: null,
            selectedFile: null
        };

        this.defaultDummyItems = [
            {
                id: 1,
                kode_barang: 'BRG-EL001',
                nama_barang: 'Barcode Scanner Wireless Honeywell',
                kategori: 'Elektronik',
                deskripsi: 'Scanner barcode industri dengan koneksi bluetooth hingga jarak 10 meter.',
                stok: 15,
                limit_stok: 5,
                lokasi_rak: 'Rak A-01',
                foto_url: null
            },
            {
                id: 2,
                kode_barang: 'BRG-EL002',
                nama_barang: 'Printer Label Thermal Xprinter XP-365B',
                kategori: 'Elektronik',
                deskripsi: 'Printer thermal untuk cetak barcode dan resi logistik lebar 80mm.',
                stok: 4,
                limit_stok: 5,
                lokasi_rak: 'Rak A-02',
                foto_url: null
            },
            {
                id: 3,
                kode_barang: 'BRG-PR001',
                nama_barang: 'Pallet Plastik Heavy Duty 120x100cm',
                kategori: 'Peralatan',
                deskripsi: 'Pallet plastik tahan lama untuk beban statis hingga 4 ton.',
                stok: 50,
                limit_stok: 10,
                lokasi_rak: 'Rak B-01',
                foto_url: null
            },
            {
                id: 4,
                kode_barang: 'BRG-ATK01',
                nama_barang: 'Kertas Thermal Roll 80x80mm (Pack)',
                kategori: 'ATK',
                deskripsi: 'Kertas struk kasir dan cetak barcode berkualitas tinggi.',
                stok: 120,
                limit_stok: 20,
                lokasi_rak: 'Rak C-01',
                foto_url: null
            },
            {
                id: 5,
                kode_barang: 'BRG-SC001',
                nama_barang: 'Roda Castor PU 4 Inch Swivel (Pcs)',
                kategori: 'Suku Cadang',
                deskripsi: 'Suku cadang roda troli gudang dari polyurethane anti-bising.',
                stok: 2,
                limit_stok: 8,
                lokasi_rak: 'Rak B-04',
                foto_url: null
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

        this.btnTambahBarang = document.getElementById('btnTambahBarang');

        this.statTotalItems = document.getElementById('statTotalItems');
        this.statTotalStock = document.getElementById('statTotalStock');
        this.statLowStock = document.getElementById('statLowStock');
        this.statLowStockBg = document.getElementById('statLowStockBg');
        this.statLowStockIcon = document.getElementById('statLowStockIcon');

        this.searchBarang = document.getElementById('searchBarang');
        this.filterKategori = document.getElementById('filterKategori');
        this.filterRak = document.getElementById('filterRak');
        this.btnResetFilters = document.getElementById('btnResetFilters');

        this.tabelBarang = document.getElementById('tabelBarang');
        this.tabelEmptyState = document.getElementById('tabelEmptyState');
        this.tabelFooter = document.getElementById('tabelFooter');
        this.txtPaginationInfo = document.getElementById('txtPaginationInfo');
        this.btnPrevPage = document.getElementById('btnPrevPage');
        this.btnNextPage = document.getElementById('btnNextPage');

        this.modalBarang = document.getElementById('modalBarang');
        this.formBarang = document.getElementById('formBarang');
        this.modalTitle = document.getElementById('modalTitle');
        this.barangIdInput = document.getElementById('barangId');

        this.formKode = document.getElementById('formKode');
        this.formNama = document.getElementById('formNama');
        this.formKategori = document.getElementById('formKategori');
        this.formDeskripsi = document.getElementById('formDeskripsi');
        this.formStok = document.getElementById('formStok');
        this.formStokContainer = document.getElementById('formStokContainer');
        this.formLimitStok = document.getElementById('formLimitStok');
        this.formRak = document.getElementById('formRak');
        this.formFoto = document.getElementById('formFoto');
        this.btnSimpanBarang = document.getElementById('btnSimpanBarang');
        this.formSpinner = document.getElementById('formSpinner');

        this.formKodeError = document.getElementById('formKodeError');
        this.formNamaError = document.getElementById('formNamaError');
        this.formKategoriError = document.getElementById('formKategoriError');
        this.formStokError = document.getElementById('formStokError');
        this.formLimitStokError = document.getElementById('formLimitStokError');
        this.formRakError = document.getElementById('formRakError');

        this.fotoDropzone = document.getElementById('fotoDropzone');
        this.fotoDropzoneMessage = document.getElementById('fotoDropzoneMessage');
        this.fotoPreviewContainer = document.getElementById('fotoPreviewContainer');
        this.fotoPreview = document.getElementById('fotoPreview');
        this.btnHapusPreview = document.getElementById('btnHapusPreview');

        this.modalHapus = document.getElementById('modalHapus');
        this.txtNamaBarangHapus = document.getElementById('txtNamaBarangHapus');
        this.btnBatalHapus = document.getElementById('btnBatalHapus');
        this.btnKonfirmasiHapus = document.getElementById('btnKonfirmasiHapus');
    }

    async init() {
        ThemeService.init();
        this.initSidebarCollapse();
        this.loadUser();
        this.bindEvents();
        await this.loadInitialData();
    }

    bindEvents() {

        if (this.themeToggleBtn) {
            this.themeToggleBtn.addEventListener('click', () => {
                ThemeService.toggle();
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

        if (this.logoutBtn) {
            this.logoutBtn.addEventListener('click', () => this.handleLogout());
        }

        if (this.desktopCollapseBtn) {
            this.desktopCollapseBtn.addEventListener('click', () => this.toggleDesktopSidebar());
        }

        if (this.btnTambahBarang) {
            this.btnTambahBarang.addEventListener('click', () => this.openAddModal());
        }

        document.querySelectorAll('.btnTutupModal').forEach(btn => {
            btn.addEventListener('click', () => this.closeFormModal());
        });

        if (this.formBarang) {
            this.formBarang.addEventListener('submit', (e) => this.handleFormSubmit(e));
        }

        if (this.fotoDropzone && this.formFoto) {
            this.fotoDropzone.addEventListener('click', () => this.formFoto.click());
            this.formFoto.addEventListener('change', (e) => this.handleImageSelect(e));

            this.fotoDropzone.addEventListener('dragover', (e) => {
                e.preventDefault();
                this.fotoDropzone.classList.add('border-blue-500', 'bg-blue-500/5');
            });

            this.fotoDropzone.addEventListener('dragleave', () => {
                this.fotoDropzone.classList.remove('border-blue-500', 'bg-blue-500/5');
            });

            this.fotoDropzone.addEventListener('drop', (e) => {
                e.preventDefault();
                this.fotoDropzone.classList.remove('border-blue-500', 'bg-blue-500/5');
                if (e.dataTransfer.files.length > 0) {
                    this.formFoto.files = e.dataTransfer.files;
                    this.handleImageSelect({ target: this.formFoto });
                }
            });
        }

        if (this.btnHapusPreview) {
            this.btnHapusPreview.addEventListener('click', (e) => {
                e.stopPropagation(); 
                this.clearImagePreview();
            });
        }

        if (this.btnBatalHapus) {
            this.btnBatalHapus.addEventListener('click', () => this.closeDeleteModal());
        }

        if (this.btnKonfirmasiHapus) {
            this.btnKonfirmasiHapus.addEventListener('click', () => this.handleConfirmDelete());
        }

        if (this.searchBarang) {
            let debounceTimer;
            this.searchBarang.addEventListener('input', (e) => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    this.state.activeSearch = e.target.value.trim().toLowerCase();
                    this.state.currentPage = 1;
                    this.filterData();
                }, 300);
            });
        }

        if (this.filterKategori) {
            this.filterKategori.addEventListener('change', (e) => {
                this.state.activeCategory = e.target.value;
                this.state.currentPage = 1;
                this.filterData();
            });
        }

        if (this.filterRak) {
            this.filterRak.addEventListener('change', (e) => {
                this.state.activeRak = e.target.value;
                this.state.currentPage = 1;
                this.filterData();
            });
        }

        if (this.btnResetFilters) {
            this.btnResetFilters.addEventListener('click', () => {
                if (this.searchBarang) this.searchBarang.value = '';
                if (this.filterKategori) this.filterKategori.value = '';
                if (this.filterRak) this.filterRak.value = '';
                this.state.activeSearch = '';
                this.state.activeCategory = '';
                this.state.activeRak = '';
                this.state.currentPage = 1;
                this.filterData();
            });
        }

        if (this.btnPrevPage) {
            this.btnPrevPage.addEventListener('click', () => {
                if (this.state.currentPage > 1) {
                    this.state.currentPage--;
                    this.renderTable();
                }
            });
        }

        if (this.btnNextPage) {
            this.btnNextPage.addEventListener('click', () => {
                if (this.state.currentPage < this.state.totalPages) {
                    this.state.currentPage++;
                    this.renderTable();
                }
            });
        }
    }

    async loadUser() {
        const storedRole = localStorage.getItem('user_role') || 'staf';
        const storedName = localStorage.getItem('user_name') || (storedRole === 'staf' ? 'Ahmad Staf Gudang' : 'Hendra Manager');
        this.state.user.role = storedRole;
        this.state.user.name = storedName;

        try {

            const response = await ApiClient.get('/api/user');
            if (response.success && response.data) {
                this.state.user.name = response.data.name;
                this.state.user.email = response.data.email;
                this.state.user.role = response.data.role || storedRole;

                localStorage.setItem('user_role', this.state.user.role);
            } else {

                this.state.user.name = storedRole === 'staf' ? 'Ahmad Staf Gudang' : 'Hendra Manager';
            }
        } catch (e) {
            console.warn("Koneksi API Gagal, memuat profil dari penyimpanan lokal.");
            this.state.user.name = storedRole === 'staf' ? 'Ahmad Staf Gudang' : 'Hendra Manager';
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

        this.enforceRolePermissions();
    }

    enforceRolePermissions() {
        const isManager = this.state.user.role === 'manager';

        if (isManager) {

            if (this.btnTambahBarang) {
                this.btnTambahBarang.classList.add('hidden');
            }

            document.querySelectorAll('.action-col').forEach(el => {
                el.classList.add('hidden');
            });
        } else {
            if (this.btnTambahBarang) {
                this.btnTambahBarang.classList.remove('hidden');
            }
            document.querySelectorAll('.action-col').forEach(el => {
                el.classList.remove('hidden');
            });
        }
    }

    handleLogout() {
        localStorage.removeItem('access_token');
        localStorage.removeItem('user_role');

        ApiClient.post('/api/logout').catch(() => {});

        ToastService.success("Berhasil keluar dari sistem.");
        setTimeout(() => {
            window.location.href = '/login';
        }, 1200);
    }

    initSidebarCollapse() {
        if (!this.sidebar) return;
        const isCollapsed = localStorage.getItem('sidebar_collapsed') === 'true';
        if (isCollapsed && window.innerWidth >= 768) {
            this.sidebar.classList.remove('w-72');
            this.sidebar.classList.add('w-20');
        }
    }

    toggleDesktopSidebar() {
        if (!this.sidebar) return;
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
    }

    async loadInitialData() {
        this.showSkeletonLoading();

        try {
            const response = await ApiClient.get('/api/barang');
            if (response.success && response.data) {

                this.state.items = response.data.data || response.data; 
                this.state.isMockMode = false;
            } else if (response.status === 404 || !response.success) {

                this.activateMockMode("API Backend belum terpasang. Berpindah ke mode pengujian LocalStorage.");
            }
        } catch (error) {
            this.activateMockMode("Gagal menghubungi server. Berpindah ke mode penyimpanan offline.");
        }

        this.state.filteredItems = [...this.state.items];
        this.populateRakFilter();
        this.filterData();
    }

    activateMockMode(message) {
        console.warn(message);
        this.state.isMockMode = true;

        const localData = localStorage.getItem('gudang_cloud_items');
        if (localData) {
            try {
                this.state.items = JSON.parse(localData);
            } catch (e) {
                this.state.items = [...this.defaultDummyItems];
                localStorage.setItem('gudang_cloud_items', JSON.stringify(this.state.items));
            }
        } else {
            this.state.items = [...this.defaultDummyItems];
            localStorage.setItem('gudang_cloud_items', JSON.stringify(this.state.items));
        }

        ToastService.show("Menjalankan Katalog: Mode Demo (Penyimpanan Lokal)", "success");
    }

    populateRakFilter() {
        if (!this.filterRak) return;

        const racks = this.state.items
            .map(item => item.lokasi_rak)
            .filter((rak, index, self) => rak && self.indexOf(rak) === index)
            .sort();

        this.state.uniqueRacks = racks;

        let html = '<option value="">Semua Lokasi Rak</option>';
        racks.forEach(rak => {
            html += `<option value="${rak}">${rak}</option>`;
        });

        this.filterRak.innerHTML = html;
        if (this.state.activeRak) {
            this.filterRak.value = this.state.activeRak;
        }
    }

    filterData() {
        let filtered = [...this.state.items];

        if (this.state.activeSearch) {
            const query = this.state.activeSearch;
            filtered = filtered.filter(item => 
                (item.nama_barang && item.nama_barang.toLowerCase().includes(query)) ||
                (item.kode_barang && item.kode_barang.toLowerCase().includes(query))
            );
        }

        if (this.state.activeCategory) {
            filtered = filtered.filter(item => item.kategori === this.state.activeCategory);
        }

        if (this.state.activeRak) {
            filtered = filtered.filter(item => item.lokasi_rak === this.state.activeRak);
        }

        this.state.filteredItems = filtered;
        this.state.currentPage = 1;
        this.state.totalPages = Math.ceil(filtered.length / this.state.itemsPerPage) || 1;

        this.renderTable();
        this.updateStats();
    }

    updateStats() {
        const items = this.state.items;

        const totalItems = items.length;
        const totalStock = items.reduce((acc, curr) => acc + Number(curr.stok || 0), 0);
        const lowStockItems = items.filter(item => Number(item.stok || 0) <= Number(item.limit_stok || 10)).length;

        if (this.statTotalItems) this.statTotalItems.textContent = totalItems.toLocaleString('id-ID');
        if (this.statTotalStock) this.statTotalStock.textContent = totalStock.toLocaleString('id-ID');
        if (this.statLowStock) this.statLowStock.textContent = lowStockItems.toLocaleString('id-ID');

        if (this.statLowStockBg && this.statLowStockIcon) {
            if (lowStockItems > 0) {
                this.statLowStockBg.className = "w-12 h-12 rounded-xl bg-rose-500/10 text-rose-500 dark:text-rose-400 flex items-center justify-center shrink-0 transition-colors duration-300 animate-pulse";
                this.statLowStockIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />`;
            } else {
                this.statLowStockBg.className = "w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-500 dark:text-emerald-400 flex items-center justify-center shrink-0 transition-colors duration-300";
                this.statLowStockIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />`;
            }
        }
    }

    renderTable() {
        if (!this.tabelBarang) return;

        const isManager = this.state.user.role === 'manager';
        const start = (this.state.currentPage - 1) * this.state.itemsPerPage;
        const end = start + this.state.itemsPerPage;
        const paginatedItems = this.state.filteredItems.slice(start, end);

        if (paginatedItems.length === 0) {
            this.tabelBarang.innerHTML = '';
            if (this.tabelEmptyState) this.tabelEmptyState.classList.remove('hidden');
            if (this.tabelFooter) this.tabelFooter.classList.add('hidden');
            return;
        }

        if (this.tabelEmptyState) this.tabelEmptyState.classList.add('hidden');
        if (this.tabelFooter) this.tabelFooter.classList.remove('hidden');

        let html = '';
        paginatedItems.forEach(item => {
            const isCritical = Number(item.stok || 0) <= Number(item.limit_stok || 10);

            let stokBadge = '';
            if (Number(item.stok) === 0) {
                stokBadge = `<span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold bg-rose-500/10 text-rose-500 dark:bg-rose-500/20">Habis</span>`;
            } else if (isCritical) {
                stokBadge = `<span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-500 dark:bg-amber-500/20">Kritis</span>`;
            } else {
                stokBadge = `<span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-500 dark:bg-emerald-500/20">Normal</span>`;
            }

            let imgHtml = '';
            if (item.foto_url || (item.foto && item.foto.startsWith('data:'))) {
                imgHtml = `<img src="${item.foto_url || item.foto}" alt="${item.nama_barang}" class="w-10 h-10 rounded-lg object-cover border border-slate-200 dark:border-slate-800 transition-transform duration-300 hover:scale-150">`;
            } else {
                imgHtml = `
                    <div class="w-10 h-10 rounded-lg bg-slate-100 dark:bg-slate-800 border border-slate-250 dark:border-slate-800 flex items-center justify-center text-slate-400 dark:text-slate-600 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                `;
            }

            const actionHtml = isManager ? '' : `
                <td class="px-6 py-4 text-right whitespace-nowrap action-col">
                    <div class="flex justify-end gap-1.5">
                        <button type="button" class="btnEdit p-1.5 text-slate-400 hover:text-blue-500 dark:hover:text-blue-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition duration-150 cursor-pointer" data-id="${item.id}" title="Edit data">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </button>
                        <button type="button" class="btnHapus p-1.5 text-slate-400 hover:text-rose-500 dark:hover:text-rose-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition duration-150 cursor-pointer" data-id="${item.id}" title="Hapus data">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </td>
            `;

            html += `
                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/35 transition-colors duration-150">
                    <td class="px-6 py-4.5 whitespace-nowrap">${imgHtml}</td>
                    <td class="px-6 py-4.5 whitespace-nowrap font-bold text-slate-900 dark:text-white tracking-wide">${item.kode_barang}</td>
                    <td class="px-6 py-4.5 font-semibold text-slate-800 dark:text-slate-200">
                        <div class="flex flex-col">
                            <span>${item.nama_barang}</span>
                            <span class="text-[11px] text-slate-400 dark:text-slate-500 font-normal mt-0.5 line-clamp-1 max-w-[250px]">${item.deskripsi || '-'}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4.5 whitespace-nowrap text-slate-600 dark:text-slate-450">${item.kategori}</td>
                    <td class="px-6 py-4.5 whitespace-nowrap font-medium text-slate-600 dark:text-slate-450">${item.lokasi_rak}</td>
                    <td class="px-6 py-4.5 whitespace-nowrap text-center">
                        <div class="flex flex-col items-center gap-1">
                            <span class="font-bold ${isCritical ? 'text-rose-500 dark:text-rose-450' : 'text-slate-800 dark:text-slate-200'}">${item.stok}</span>
                            ${stokBadge}
                        </div>
                    </td>
                    <td class="px-6 py-4.5 whitespace-nowrap text-center font-medium text-slate-500 dark:text-slate-500">${item.limit_stok}</td>
                    ${actionHtml}
                </tr>
            `;
        });

        this.tabelBarang.innerHTML = html;

        document.querySelectorAll('.btnEdit').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const id = Number(e.currentTarget.getAttribute('data-id'));
                this.openEditModal(id);
            });
        });

        document.querySelectorAll('.btnHapus').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const id = Number(e.currentTarget.getAttribute('data-id'));
                this.openDeleteModal(id);
            });
        });

        const totalItemsCount = this.state.filteredItems.length;
        const displayStart = start + 1;
        const displayEnd = Math.min(end, totalItemsCount);

        if (this.txtPaginationInfo) {
            this.txtPaginationInfo.textContent = `Menampilkan ${displayStart} - ${displayEnd} dari ${totalItemsCount} barang`;
        }

        if (this.btnPrevPage) this.btnPrevPage.disabled = this.state.currentPage === 1;
        if (this.btnNextPage) this.btnNextPage.disabled = this.state.currentPage === this.state.totalPages;

        this.enforceRolePermissions();
    }

    showSkeletonLoading() {
        if (!this.tabelBarang) return;

        let html = '';
        for (let i = 0; i < this.state.itemsPerPage; i++) {
            html += `
                <tr class="animate-pulse border-b border-slate-100 dark:border-slate-800">
                    <td class="px-6 py-5.5"><div class="w-10 h-10 bg-slate-200 dark:bg-slate-800 rounded-lg"></div></td>
                    <td class="px-6 py-5.5"><div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-20"></div></td>
                    <td class="px-6 py-5.5">
                        <div class="space-y-2">
                            <div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-44"></div>
                            <div class="h-3 bg-slate-200 dark:bg-slate-800 rounded w-64"></div>
                        </div>
                    </td>
                    <td class="px-6 py-5.5"><div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-16"></div></td>
                    <td class="px-6 py-5.5"><div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-14"></div></td>
                    <td class="px-6 py-5.5"><div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-8 mx-auto"></div></td>
                    <td class="px-6 py-5.5"><div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-8 mx-auto"></div></td>
                    <td class="px-6 py-5.5 text-right action-col"><div class="h-7 bg-slate-200 dark:bg-slate-800 rounded w-14 ml-auto"></div></td>
                </tr>
            `;
        }

        this.tabelBarang.innerHTML = html;
        if (this.tabelEmptyState) this.tabelEmptyState.classList.add('hidden');
        if (this.tabelFooter) this.tabelFooter.classList.remove('hidden');
    }

    openAddModal() {
        if (this.state.user.role === 'manager') return;

        this.resetFormErrors();
        this.clearForm();

        this.state.selectedItemId = null;
        if (this.modalTitle) this.modalTitle.textContent = "Tambah Barang Baru";

        if (this.formStokContainer) this.formStokContainer.classList.remove('hidden');
        if (this.formStok) this.formStok.disabled = false;

        this.showFormModal();
    }

    openEditModal(id) {
        if (this.state.user.role === 'manager') return;

        this.resetFormErrors();
        this.clearForm();

        const item = this.state.items.find(i => i.id === id);
        if (!item) return;

        this.state.selectedItemId = id;
        if (this.modalTitle) this.modalTitle.textContent = "Ubah Data Barang";

        if (this.barangIdInput) this.barangIdInput.value = item.id;
        if (this.formKode) this.formKode.value = item.kode_barang;
        if (this.formNama) this.formNama.value = item.nama_barang;
        if (this.formKategori) this.formKategori.value = item.kategori;
        if (this.formDeskripsi) this.formDeskripsi.value = item.deskripsi || '';

        if (this.formStok) {
            this.formStok.value = item.stok;
            this.formStok.disabled = true;
        }
        if (this.formStokContainer) this.formStokContainer.classList.add('hidden');

        if (this.formLimitStok) this.formLimitStok.value = item.limit_stok;
        if (this.formRak) this.formRak.value = item.lokasi_rak;

        if (item.foto_url || (item.foto && item.foto.startsWith('data:'))) {
            this.showImagePreview(item.foto_url || item.foto);
        }

        this.showFormModal();
    }

    showFormModal() {
        if (!this.modalBarang) return;
        this.modalBarang.classList.remove('opacity-0', 'pointer-events-none');
        const dialog = this.modalBarang.querySelector('.bg-white, .dark\\:bg-slate-900');
        dialog.classList.remove('translate-y-4');
        dialog.classList.add('translate-y-0');
    }

    closeFormModal() {
        if (!this.modalBarang) return;
        this.modalBarang.classList.add('opacity-0', 'pointer-events-none');
        const dialog = this.modalBarang.querySelector('.bg-white, .dark\\:bg-slate-900');
        dialog.classList.remove('translate-y-0');
        dialog.classList.add('translate-y-4');
        this.clearForm();
    }

    clearForm() {
        if (this.formBarang) this.formBarang.reset();
        if (this.barangIdInput) this.barangIdInput.value = '';
        this.clearImagePreview();
        this.resetFormErrors();
    }

    resetFormErrors() {
        const errors = [this.formKodeError, this.formNamaError, this.formKategoriError, this.formStokError, this.formLimitStokError, this.formRakError];
        errors.forEach(err => {
            if (err) err.classList.add('hidden');
        });

        const inputs = [this.formKode, this.formNama, this.formKategori, this.formStok, this.formLimitStok, this.formRak];
        inputs.forEach(input => {
            if (input) {
                input.classList.remove('border-rose-500', 'focus:border-rose-500', 'border-emerald-500', 'focus:border-emerald-500');
            }
        });
    }

    validateForm() {
        let isValid = true;
        this.resetFormErrors();

        const setInvalid = (input, errorEl) => {
            if (input) input.classList.add('border-rose-500', 'focus:border-rose-500');
            if (errorEl) errorEl.classList.remove('hidden');
            isValid = false;
        };

        const setValid = (input) => {
            if (input) input.classList.add('border-emerald-500', 'focus:border-emerald-500');
        };

        const kodeVal = this.formKode.value.trim();
        if (!kodeVal) {
            setInvalid(this.formKode, this.formKodeError);
        } else {

            const exists = this.state.items.some(item => 
                item.kode_barang.toLowerCase() === kodeVal.toLowerCase() && 
                item.id !== this.state.selectedItemId
            );
            if (exists) {
                if (this.formKodeError) this.formKodeError.textContent = "Kode barang sudah terdaftar.";
                setInvalid(this.formKode, this.formKodeError);
            } else {
                setValid(this.formKode);
            }
        }

        if (!this.formNama.value.trim()) {
            setInvalid(this.formNama, this.formNamaError);
        } else {
            setValid(this.formNama);
        }

        if (!this.formKategori.value) {
            setInvalid(this.formKategori, this.formKategoriError);
        } else {
            setValid(this.formKategori);
        }

        if (!this.state.selectedItemId) {
            const stokVal = Number(this.formStok.value);
            if (this.formStok.value === '' || stokVal < 0 || isNaN(stokVal)) {
                setInvalid(this.formStok, this.formStokError);
            } else {
                setValid(this.formStok);
            }
        }

        const limitVal = Number(this.formLimitStok.value);
        if (this.formLimitStok.value === '' || limitVal < 0 || isNaN(limitVal)) {
            setInvalid(this.formLimitStok, this.formLimitStokError);
        } else {
            setValid(this.formLimitStok);
        }

        if (!this.formRak.value.trim()) {
            setInvalid(this.formRak, this.formRakError);
        } else {
            setValid(this.formRak);
        }

        return isValid;
    }

    handleImageSelect(e) {
        const file = e.target.files[0];
        if (!file) return;

        if (file.size > 2 * 1024 * 1024) {
            ToastService.error("Ukuran file foto maksimal adalah 2 MB.");
            this.formFoto.value = '';
            return;
        }

        if (!file.type.match('image.*')) {
            ToastService.error("Format file harus berupa gambar (PNG, JPG, JPEG, GIF).");
            this.formFoto.value = '';
            return;
        }

        this.state.selectedFile = file;

        const reader = new FileReader();
        reader.onload = (event) => {
            this.showImagePreview(event.target.result);
        };
        reader.readAsDataURL(file);
    }

    showImagePreview(src) {
        if (this.fotoPreview && this.fotoPreviewContainer && this.fotoDropzoneMessage) {
            this.fotoPreview.src = src;
            this.fotoPreviewContainer.classList.remove('hidden');
            this.fotoDropzoneMessage.classList.add('hidden');
        }
    }

    clearImagePreview() {
        this.state.selectedFile = null;
        if (this.formFoto) this.formFoto.value = '';
        if (this.fotoPreview) this.fotoPreview.src = '';
        if (this.fotoPreviewContainer) this.fotoPreviewContainer.classList.add('hidden');
        if (this.fotoDropzoneMessage) this.fotoDropzoneMessage.classList.remove('hidden');
    }

    setLoading(loading) {
        if (this.btnSimpanBarang && this.formSpinner) {
            this.btnSimpanBarang.disabled = loading;
            if (loading) {
                this.formSpinner.classList.remove('hidden');
            } else {
                this.formSpinner.classList.add('hidden');
            }
        }
    }

    async handleFormSubmit(e) {
        e.preventDefault();

        if (this.state.user.role === 'manager') return;

        const isFormValid = this.validateForm();
        if (!isFormValid) {
            ToastService.error("Mohon perbaiki isian form sebelum disimpan.");
            return;
        }

        this.setLoading(true);

        const id = this.state.selectedItemId;
        const isEdit = !!id;

        const formData = new FormData();
        formData.append('kode_barang', this.formKode.value.trim());
        formData.append('nama_barang', this.formNama.value.trim());
        formData.append('kategori', this.formKategori.value);
        formData.append('deskripsi', this.formDeskripsi.value.trim());
        formData.append('limit_stok', Number(this.formLimitStok.value));
        formData.append('lokasi_rak', this.formRak.value.trim());

        if (!isEdit) {
            formData.append('stok', Number(this.formStok.value));
        }

        if (this.state.selectedFile) {
            formData.append('foto', this.state.selectedFile);
        }

        if (isEdit) {
            formData.append('_method', 'PUT');
        }

        if (this.state.isMockMode) {

            setTimeout(() => {
                let updatedItems = [...this.state.items];

                const saveMock = () => {
                    if (isEdit) {
                        const idx = updatedItems.findIndex(i => i.id === id);
                        if (idx !== -1) {
                            const currentFoto = updatedItems[idx].foto;
                            updatedItems[idx] = {
                                ...updatedItems[idx],
                                kode_barang: this.formKode.value.trim(),
                                nama_barang: this.formNama.value.trim(),
                                kategori: this.formKategori.value,
                                deskripsi: this.formDeskripsi.value.trim(),
                                limit_stok: Number(this.formLimitStok.value),
                                lokasi_rak: this.formRak.value.trim(),
                                foto: this.state.selectedFile ? this.fotoPreview.src : currentFoto
                            };
                        }
                    } else {
                        const newId = updatedItems.length > 0 ? Math.max(...updatedItems.map(i => i.id)) + 1 : 1;
                        updatedItems.push({
                            id: newId,
                            kode_barang: this.formKode.value.trim(),
                            nama_barang: this.formNama.value.trim(),
                            kategori: this.formKategori.value,
                            deskripsi: this.formDeskripsi.value.trim(),
                            stok: Number(this.formStok.value),
                            limit_stok: Number(this.formLimitStok.value),
                            lokasi_rak: this.formRak.value.trim(),
                            foto: this.state.selectedFile ? this.fotoPreview.src : null
                        });
                    }

                    this.state.items = updatedItems;
                    localStorage.setItem('gudang_cloud_items', JSON.stringify(updatedItems));

                    ToastService.success(isEdit ? "Barang berhasil diperbarui." : "Barang baru berhasil ditambahkan.");
                    this.closeFormModal();
                    this.setLoading(false);
                    this.populateRakFilter();
                    this.filterData();
                };

                saveMock();
            }, 800);

        } else {

            const url = isEdit ? `/api/barang/${id}` : '/api/barang';

            const response = await ApiClient.post(url, formData);

            if (response.success) {
                ToastService.success(isEdit ? "Barang berhasil diperbarui." : "Barang baru berhasil ditambahkan.");
                this.closeFormModal();
                await this.loadInitialData(); 
            } else {
                ToastService.error(response.message || "Gagal menyimpan data barang.");
            }
            this.setLoading(false);
        }
    }

    openDeleteModal(id) {
        if (this.state.user.role === 'manager') return;

        const item = this.state.items.find(i => i.id === id);
        if (!item) return;

        this.state.selectedItemId = id;
        if (this.txtNamaBarangHapus) {
            this.txtNamaBarangHapus.textContent = `"${item.kode_barang} - ${item.nama_barang}"`;
        }

        if (this.modalHapus) {
            this.modalHapus.classList.remove('opacity-0', 'pointer-events-none');
            const dialog = this.modalHapus.querySelector('.bg-white, .dark\\:bg-slate-900');
            dialog.classList.remove('translate-y-4');
            dialog.classList.add('translate-y-0');
        }
    }

    closeDeleteModal() {
        if (this.modalHapus) {
            this.modalHapus.classList.add('opacity-0', 'pointer-events-none');
            const dialog = this.modalHapus.querySelector('.bg-white, .dark\\:bg-slate-900');
            dialog.classList.remove('translate-y-0');
            dialog.classList.add('translate-y-4');
        }
        this.state.selectedItemId = null;
    }

    async handleConfirmDelete() {
        const id = this.state.selectedItemId;
        if (!id || this.state.user.role === 'manager') return;

        if (this.state.isMockMode) {

            let updatedItems = this.state.items.filter(i => i.id !== id);
            this.state.items = updatedItems;
            localStorage.setItem('gudang_cloud_items', JSON.stringify(updatedItems));

            ToastService.success("Barang berhasil dihapus.");
            this.closeDeleteModal();
            this.populateRakFilter();
            this.filterData();
        } else {

            try {
                const response = await ApiClient.request(`/api/barang/${id}`, { method: 'DELETE' });
                if (response.success) {
                    ToastService.success("Barang berhasil dihapus.");
                    this.closeDeleteModal();
                    await this.loadInitialData(); 
                } else {
                    ToastService.error(response.message || "Gagal menghapus barang.");
                }
            } catch (e) {
                ToastService.error("Gagal terhubung ke server untuk menghapus barang.");
            }
        }
    }
}
