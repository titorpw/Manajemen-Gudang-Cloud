
export class ThemeService {
    static THEME_KEY = 'gudang_cloud_theme';

    static init() {
        const savedTheme = localStorage.getItem(this.THEME_KEY);
        const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

        if (savedTheme === 'dark' || (!savedTheme && systemPrefersDark)) {
            this.setDark(true);
        } else {
            this.setDark(false);
        }
    }

    static toggle() {
        const isDark = document.documentElement.classList.contains('dark');
        const newDark = !isDark;
        this.setDark(newDark);
        return newDark;
    }

    static setDark(dark) {
        if (dark) {
            document.documentElement.classList.add('dark');
            localStorage.setItem(this.THEME_KEY, 'dark');
        } else {
            document.documentElement.classList.remove('dark');
            localStorage.setItem(this.THEME_KEY, 'light');
        }
    }

    static isDark() {
        return document.documentElement.classList.contains('dark');
    }
}
