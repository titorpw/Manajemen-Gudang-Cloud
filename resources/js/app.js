import { LoginController } from './auth/login-controller';
import { KatalogController } from './katalog/katalog-controller';
import { ApiClient } from './services/api';
import { ThemeService } from './services/theme';
import { ToastService } from './services/toast';

async function initDashboardLayout() {
    ThemeService.init();

    const sidebar = document.getElementById('sidebar');

    if (sidebar) {
        const isCollapsed = localStorage.getItem('sidebar_collapsed') === 'true';
        if (isCollapsed && window.innerWidth >= 768) {
            sidebar.classList.remove('w-72');
            sidebar.classList.add('w-20');
        }
    }

    const desktopCollapseBtn = document.getElementById('desktopCollapseBtn');
    if (desktopCollapseBtn && sidebar) {
        desktopCollapseBtn.addEventListener('click', () => {
            const collapsed = sidebar.classList.contains('w-20');
            if (collapsed) {
                sidebar.classList.remove('w-20');
                sidebar.classList.add('w-72');
                localStorage.setItem('sidebar_collapsed', 'false');
            } else {
                sidebar.classList.remove('w-72');
                sidebar.classList.add('w-20');
                localStorage.setItem('sidebar_collapsed', 'true');
            }
        });
    }

    const themeToggle = document.getElementById('sidebarThemeToggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            ThemeService.toggle();
        });
    }

    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', () => {
            localStorage.removeItem('access_token');
            localStorage.removeItem('user_role');
            ApiClient.post('/api/logout').catch(() => {});
            
            ToastService.success("Berhasil keluar dari sistem.");
            setTimeout(() => {
                window.location.href = '/login';
            }, 1200);
        });
    }

    const userNameEl = document.getElementById('userName');
    const welcomeUserNameEl = document.getElementById('welcomeUserName');
    const userRoleBadgeEl = document.getElementById('userRoleBadge');
    const userInitialEl = document.getElementById('userInitial');

    const storedRole = localStorage.getItem('user_role') || 'staf';
    let name = storedRole === 'staf' ? 'Ahmad Staf Gudang' : 'Hendra Manager';
    let role = storedRole;

    try {
        const response = await ApiClient.get('/api/user');
        if (response.success && response.data) {
            name = response.data.name;
            role = response.data.role || storedRole;
            localStorage.setItem('user_role', role);
        }
    } catch (e) {
        console.warn("Gagal terhubung ke API, menggunakan profil offline.");
    }

    if (userNameEl) userNameEl.textContent = name;
    if (welcomeUserNameEl) welcomeUserNameEl.textContent = name;
    if (userInitialEl) userInitialEl.textContent = name.charAt(0).toUpperCase();
    if (userRoleBadgeEl) {
        userRoleBadgeEl.textContent = role === 'staf' ? 'Staf Gudang (Admin)' : 'Manager Gudang';
        if (role === 'staf') {
            userRoleBadgeEl.className = "inline-flex self-start px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wide uppercase mt-1.5 bg-blue-500/10 text-blue-500 dark:bg-blue-500/20";
        } else {
            userRoleBadgeEl.className = "inline-flex self-start px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wide uppercase mt-1.5 bg-amber-500/10 text-amber-500 dark:bg-amber-500/20";
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        const loginController = new LoginController();
        loginController.init();
    }

    const katalogPage = document.getElementById('katalogPage');
    if (katalogPage) {
        const katalogController = new KatalogController();
        katalogController.init();
    } else {
        const sidebar = document.getElementById('sidebar');
        if (sidebar) {
            initDashboardLayout();
        }
    }
});
