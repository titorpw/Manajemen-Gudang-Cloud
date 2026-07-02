import { ApiClient } from '../services/api';
import { ToastService } from '../services/toast';
import { ThemeService } from '../services/theme';

export class MutasiController {
    constructor() {
        this.state = {
            mutations: [],
            filteredMutations: [],
            items: [],
            user: { 
                name: localStorage.getItem('user_name') || 'User Gudang', 
                role: localStorage.getItem('user_role') || 'staf', 
                email: '' 
            },
            currentPage: 1,
            itemsPerPage: 8,
            totalPages: 1,
            isMockMode: false,
            activeSearch: '',
            activeType: ''
        };

        this.defaultDummyMutations = [
            {
                id: 1,
                item_id: 1,
                kode_barang: 'BRG-EL001',
                nama_barang: 'Barcode Scanner Wireless Honeywell',
                staf_gudang: 'Ahmad Staf Gudang',
                jenis_mutasi: 'IN',
                jumlah: 10,
                keterangan: 'Penerimaan barang dari supplier Honeywell Official Store',
                tanggal_input: new Date(Date.now() - 3600000 * 24).toISOString() // 1 hari lalu
            },
            {
                id: 2,
                item_id: 2,
                kode_barang: 'BRG-EL002',
                nama_barang: 'Printer Label Thermal Xprinter XP-365B',
                staf_gudang: 'Ahmad Staf Gudang',
                jenis_mutasi: 'OUT',
                jumlah: 2,
                keterangan: 'Pengeluaran untuk kebutuhan packing area kurir J&T',
                tanggal_input: new Date(Date.now() - 3600000 * 12).toISOString() // 12 jam lalu
            },
            {
                id: 3,
                item_id: 3,
                kode_barang: 'BRG-PR001',
                nama_barang: 'Pallet Plastik Heavy Duty 120x100cm',
                staf_gudang: 'Ahmad Staf Gudang',
                jenis_mutasi: 'IN',
                jumlah: 5,
                keterangan: 'Restock pallet tambahan untuk penataan Rak B-02',
                tanggal_input: new Date(Date.now() - 3600000 * 2).toISOString() // 2 jam lalu
            }
        ];

        this.initDOM();
    }

    initDOM() {
        this.userNameEl = document.getElementById('userName');
        this.userRoleBadgeEl = document.getElementById('userRoleBadge');
        this.userInitialEl = document.getElementById('userInitial');
        this.mobileMenuBtn = document.getElementById('mobileMenuBtn');
        this.sidebar = document.getElementById('sidebar');
        this.sidebarOverlay = document.getElementById('sidebarOverlay');
        this.themeToggleBtn = document.getElementById('sidebarThemeToggle');
        this.logoutBtn = document.getElementById('logoutBtn');
        this.desktopCollapseBtn = document.getElementById('desktopCollapseBtn');

        this.btnTambahMutasi = document.getElementById('btnTambahMutasi');
        this.totalMutasiCount = document.getElementById('totalMutasiCount');
        this.totalMasukCount = document.getElementById('totalMasukCount');
        this.totalKeluarCount = document.getElementById('totalKeluarCount');

        this.txtSearchMutasi = document.getElementById('txtSearchMutasi');
        this.filterType = document.getElementById('filterType');
        this.btnResetFilter = document.getElementById('btnResetFilter');

        this.tabelMutasi = document.getElementById('tabelMutasi');
        this.tabelEmptyState = document.getElementById('tabelEmptyState');
        this.tabelFooter = document.getElementById('tabelFooter');
        this.txtPaginationInfo = document.getElementById('txtPaginationInfo');
        this.btnPrevPage = document.getElementById('btnPrevPage');
        this.btnNextPage = document.getElementById('btnNextPage');

        this.modalMutasi = document.getElementById('modalMutasi');
        this.formMutasi = document.getElementById('formMutasi');
        this.formBarangId = document.getElementById('formBarangId');
        this.formType = document.getElementById('formType');
        this.formJumlah = document.getElementById('formJumlah');
        this.formKeterangan = document.getElementById('formKeterangan');
        this.btnTutupModals = document.querySelectorAll('.btnTutupModal');
    }

    async init() {
        ThemeService.init();
        this.initLayoutEvents();
        await this.loadUserProfile();
        this.initMutationEvents();
        await this.loadData();
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
                ApiClient.post('/api/logout').catch(() => {});
                ToastService.success('Berhasil keluar dari sistem.');
                setTimeout(() => {
                    window.location.href = '/login';
                }, 1200);
            });
        }
    }

    async loadUserProfile() {
        const storedRole = localStorage.getItem('user_role') || 'staf';
        const storedName = localStorage.getItem('user_name') || (storedRole === 'staf' ? 'Ahmad Staf Gudang' : 'Hendra Manager');
        this.state.user = {
            name: storedName,
            role: storedRole,
            email: ''
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
        if (this.userInitialEl) this.userInitialEl.textContent = this.state.user.name.charAt(0).toUpperCase();
        if (this.userRoleBadgeEl) {
            this.userRoleBadgeEl.textContent = this.state.user.role === 'staf' ? 'Staf Gudang (Admin)' : 'Manager Gudang';
            if (this.state.user.role === 'staf') {
                this.userRoleBadgeEl.className = "inline-flex self-start px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wide uppercase mt-1.5 bg-blue-500/10 text-blue-500 dark:bg-blue-500/20";
                if (this.btnTambahMutasi) this.btnTambahMutasi.classList.remove('hidden');
            } else {
                this.userRoleBadgeEl.className = "inline-flex self-start px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wide uppercase mt-1.5 bg-amber-500/10 text-amber-500 dark:bg-amber-500/20";
                if (this.btnTambahMutasi) this.btnTambahMutasi.classList.add('hidden');
            }
        }
    }

    initMutationEvents() {
        if (this.btnTambahMutasi) {
            this.btnTambahMutasi.addEventListener('click', () => this.bukaModalMutasi());
        }

        this.btnTutupModals.forEach(btn => {
            btn.addEventListener('click', () => this.tutupModalMutasi());
        });

        if (this.formMutasi) {
            this.formMutasi.addEventListener('submit', (e) => this.handleSimpanMutasi(e));
        }

        if (this.txtSearchMutasi) {
            let timeout = null;
            this.txtSearchMutasi.addEventListener('input', (e) => {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    this.state.activeSearch = e.target.value;
                    this.state.currentPage = 1;
                    this.applyFiltersAndRender();
                }, 300);
            });
        }

        if (this.filterType) {
            this.filterType.addEventListener('change', (e) => {
                this.state.activeType = e.target.value;
                this.state.currentPage = 1;
                this.applyFiltersAndRender();
            });
        }

        if (this.btnResetFilter) {
            this.btnResetFilter.addEventListener('click', () => {
                if (this.txtSearchMutasi) this.txtSearchMutasi.value = '';
                if (this.filterType) this.filterType.value = '';
                this.state.activeSearch = '';
                this.state.activeType = '';
                this.state.currentPage = 1;
                this.applyFiltersAndRender();
            });
        }

        if (this.btnPrevPage) {
            this.btnPrevPage.addEventListener('click', () => {
                if (this.state.currentPage > 1) {
                    this.state.currentPage--;
                    this.renderTableOnly();
                }
            });
        }

        if (this.btnNextPage) {
            this.btnNextPage.addEventListener('click', () => {
                if (this.state.currentPage < this.state.totalPages) {
                    this.state.currentPage++;
                    this.renderTableOnly();
                }
            });
        }
    }

    async loadData() {
        try {
            const response = await ApiClient.get('/api/mutasi');
            if (response.success) {
                this.state.mutations = response.data || [];
                this.state.isMockMode = false;
                console.log('Mutasi berhasil dimuat dari API backend.');
            } else {
                throw new Error('API failed');
            }
        } catch (e) {
            this.state.isMockMode = true;
            console.warn('Backend API tidak merespon. Berpindah ke Mode Fallback LocalStorage.');
            this.initLocalStorageMockData();
            ToastService.info('Menjalankan Mutasi: Mode Demo (Penyimpanan Lokal)');
        }

        await this.loadItemsList();
        this.applyFiltersAndRender();
    }

    async loadItemsList() {
        try {
            if (!this.state.isMockMode) {
                const response = await ApiClient.get('/api/barang');
                if (response.success && response.data) {
                    this.state.items = response.data;
                    this.populasiDropdownBarang();
                    return;
                }
            }
        } catch (e) {
            console.warn('Gagal memuat daftar barang dari API.');
        }

        // Fallback ke LocalStorage untuk barang
        const storedItems = localStorage.getItem('mock_items');
        if (storedItems) {
            this.state.items = JSON.parse(storedItems);
        } else {
            this.state.items = [];
        }
        this.populasiDropdownBarang();
    }

    populasiDropdownBarang() {
        if (!this.formBarangId) return;

        if (this.state.items.length === 0) {
            this.formBarangId.innerHTML = '<option value="">Belum ada barang di katalog</option>';
            return;
        }

        let html = '<option value="">-- Pilih Barang --</option>';
        this.state.items.forEach(item => {
            html += `<option value="${item.id}">${item.kode_barang} - ${item.nama_barang} (Stok: ${item.stok})</option>`;
        });
        this.formBarangId.innerHTML = html;
    }

    initLocalStorageMockData() {
        const storedMutations = localStorage.getItem('mock_mutations');
        if (storedMutations) {
            this.state.mutations = JSON.parse(storedMutations);
        } else {
            this.state.mutations = [...this.defaultDummyMutations];
            localStorage.setItem('mock_mutations', JSON.stringify(this.state.mutations));
        }
    }

    bukaModalMutasi() {
        if (this.formMutasi) this.formMutasi.reset();
        this.loadItemsList(); // Refresh stock info in dropdown
        if (this.modalMutasi) {
            this.modalMutasi.classList.remove('opacity-0', 'pointer-events-none');
            const innerModal = this.modalMutasi.querySelector('.relative.z-10');
            if (innerModal) {
                innerModal.classList.remove('translate-y-4');
                innerModal.classList.add('translate-y-0');
            }
        }
    }

    tutupModalMutasi() {
        if (this.modalMutasi) {
            this.modalMutasi.classList.add('opacity-0', 'pointer-events-none');
            const innerModal = this.modalMutasi.querySelector('.relative.z-10');
            if (innerModal) {
                innerModal.classList.remove('translate-y-0');
                innerModal.classList.add('translate-y-4');
            }
        }
    }

    async handleSimpanMutasi(e) {
        e.preventDefault();

        const itemId = parseInt(this.formBarangId.value);
        const type = this.formType.value;
        const quantity = parseInt(this.formJumlah.value);
        const note = this.formKeterangan.value;

        if (isNaN(itemId)) {
            ToastService.error('Silakan pilih barang terlebih dahulu.');
            return;
        }
        if (isNaN(quantity) || quantity < 1) {
            ToastService.error('Jumlah unit harus berupa angka minimal 1.');
            return;
        }

        if (!this.state.isMockMode) {
            try {
                const response = await ApiClient.post('/api/mutasi', {
                    item_id: itemId,
                    type: type,
                    quantity: quantity,
                    note: note
                });

                if (response.success) {
                    ToastService.success('Transaksi mutasi berhasil dicatat.');
                    this.tutupModalMutasi();
                    await this.loadData();
                } else {
                    if (response.status === 400 && response.message) {
                        ToastService.error(response.message);
                    } else if (response.errors) {
                        const errMsg = Object.values(response.errors).flat().join('\n');
                        ToastService.error(errMsg || 'Validasi gagal.');
                    } else {
                        ToastService.error(response.message || 'Gagal menyimpan mutasi.');
                    }
                }
                return;
            } catch (err) {
                console.error(err);
            }
        }

        // Mock Mode (LocalStorage fallback)
        const targetItemIndex = this.state.items.findIndex(item => item.id === itemId);
        if (targetItemIndex === -1) {
            ToastService.error('Barang tidak ditemukan di katalog.');
            return;
        }

        const targetItem = this.state.items[targetItemIndex];

        if (type === 'OUT' && targetItem.stok < quantity) {
            ToastService.error(`Transaksi ditolak. Stok barang saat ini tidak mencukupi.\nStok saat ini hanya tersedia ${targetItem.stok} unit.`);
            return;
        }

        // Update stock
        if (type === 'IN') {
            targetItem.stok += quantity;
        } else {
            targetItem.stok -= quantity;
        }

        // Save mock items
        this.state.items[targetItemIndex] = targetItem;
        localStorage.setItem('mock_items', JSON.stringify(this.state.items));

        // Create new mutation log
        const newMutation = {
            id: Date.now(),
            item_id: itemId,
            kode_barang: targetItem.kode_barang,
            nama_barang: targetItem.nama_barang,
            staf_gudang: this.state.user.name,
            jenis_mutasi: type,
            jumlah: quantity,
            keterangan: note || '-',
            tanggal_input: new Date().toISOString()
        };

        this.state.mutations.unshift(newMutation);
        localStorage.setItem('mock_mutations', JSON.stringify(this.state.mutations));

        ToastService.success('Transaksi mutasi berhasil dicatat (Lokal).');
        this.tutupModalMutasi();
        this.applyFiltersAndRender();
    }

    applyFiltersAndRender() {
        let results = [...this.state.mutations];

        if (this.state.activeSearch) {
            const query = this.state.activeSearch.toLowerCase();
            results = results.filter(m => 
                (m.nama_barang && m.nama_barang.toLowerCase().includes(query)) ||
                (m.kode_barang && m.kode_barang.toLowerCase().includes(query)) ||
                (m.staf_gudang && m.staf_gudang.toLowerCase().includes(query)) ||
                (m.keterangan && m.keterangan.toLowerCase().includes(query))
            );
        }

        if (this.state.activeType) {
            results = results.filter(m => m.jenis_mutasi === this.state.activeType);
        }

        this.state.filteredMutations = results;

        if (this.btnResetFilter) {
            if (this.state.activeSearch || this.state.activeType) {
                this.btnResetFilter.classList.remove('hidden');
            } else {
                this.btnResetFilter.classList.add('hidden');
            }
        }

        this.renderStats();
        this.renderTableOnly();
    }

    renderStats() {
        let total = this.state.mutations.length;
        let masuk = 0;
        let keluar = 0;

        this.state.mutations.forEach(m => {
            if (m.jenis_mutasi === 'IN') {
                masuk += m.jumlah;
            } else if (m.jenis_mutasi === 'OUT') {
                keluar += m.jumlah;
            }
        });

        if (this.totalMutasiCount) this.totalMutasiCount.textContent = total;
        if (this.totalMasukCount) this.totalMasukCount.textContent = masuk;
        if (this.totalKeluarCount) this.totalKeluarCount.textContent = keluar;
    }

    renderTableOnly() {
        if (!this.tabelMutasi) return;

        const totalItems = this.state.filteredMutations.length;
        this.state.totalPages = Math.ceil(totalItems / this.state.itemsPerPage) || 1;

        if (this.state.currentPage > this.state.totalPages) {
            this.state.currentPage = this.state.totalPages;
        }

        const startIdx = (this.state.currentPage - 1) * this.state.itemsPerPage;
        const endIdx = Math.min(startIdx + this.state.itemsPerPage, totalItems);
        const pagedMutations = this.state.filteredMutations.slice(startIdx, endIdx);

        if (totalItems === 0) {
            this.tabelMutasi.innerHTML = '';
            if (this.tabelEmptyState) this.tabelEmptyState.classList.remove('hidden');
            if (this.tabelFooter) this.tabelFooter.classList.add('hidden');
            return;
        }

        if (this.tabelEmptyState) this.tabelEmptyState.classList.add('hidden');
        if (this.tabelFooter) this.tabelFooter.classList.remove('hidden');

        let html = '';
        pagedMutations.forEach(m => {
            const dateObj = new Date(m.tanggal_input);
            const dateStr = dateObj.toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            }) + ' ' + dateObj.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

            const typeBadgeClass = m.jenis_mutasi === 'IN' 
                ? 'bg-emerald-500/10 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400' 
                : 'bg-rose-500/10 text-rose-600 dark:bg-rose-500/20 dark:text-rose-400';

            html += `
                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors border-b border-slate-100 dark:border-slate-800/80">
                    <td class="px-6 py-4 font-medium text-slate-500 dark:text-slate-400">${dateStr}</td>
                    <td class="px-6 py-4 font-mono text-xs font-semibold text-slate-850 dark:text-slate-300">${m.kode_barang}</td>
                    <td class="px-6 py-4 font-semibold text-slate-800 dark:text-white">${m.nama_barang}</td>
                    <td class="px-6 py-4 text-slate-650 dark:text-slate-400">${m.staf_gudang}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold ${typeBadgeClass}">
                            ${m.jenis_mutasi}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center font-bold text-slate-800 dark:text-slate-200">${m.jumlah}</td>
                    <td class="px-6 py-4 text-slate-500 dark:text-slate-400 max-w-xs truncate" title="${m.keterangan || ''}">${m.keterangan || '-'}</td>
                </tr>
            `;
        });

        this.tabelMutasi.innerHTML = html;

        if (this.txtPaginationInfo) {
            this.txtPaginationInfo.textContent = `Menampilkan ${totalItems === 0 ? 0 : startIdx + 1} - ${endIdx} dari ${totalItems} transaksi`;
        }

        if (this.btnPrevPage) this.btnPrevPage.disabled = (this.state.currentPage === 1);
        if (this.btnNextPage) this.btnNextPage.disabled = (this.state.currentPage === this.state.totalPages);
    }
}
