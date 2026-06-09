
export class ToastService {
    static getContainer() {
        let container = document.getElementById('toastContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toastContainer';
            container.className = 'fixed top-6 right-6 z-50 flex flex-col gap-3 max-w-sm pointer-events-none';
            document.body.appendChild(container);
        }
        return container;
    }

    static show(message, type = 'success') {
        const container = this.getContainer();
        const toast = document.createElement('div');

        const baseClass = "flex items-center gap-3 px-4 py-3 rounded-xl shadow-2xl border transition-all duration-300 transform translate-y-2 opacity-0 pointer-events-auto backdrop-blur-md";
        const typeClass = type === 'success'
            ? 'bg-emerald-950/80 text-emerald-300 border-emerald-500/20'
            : 'bg-rose-950/80 text-rose-300 border-rose-500/20';

        toast.className = `${baseClass} ${typeClass}`;

        const icon = type === 'success'
            ? `<svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>`
            : `<svg class="w-5 h-5 text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>`;

        toast.innerHTML = `${icon} <span class="text-sm font-medium leading-relaxed">${message}</span>`;
        container.appendChild(toast);

        setTimeout(() => {
            toast.classList.remove('translate-y-2', 'opacity-0');
        }, 10);

        setTimeout(() => {
            toast.classList.add('translate-y-2', 'opacity-0');
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 4000);
    }

    static success(message) {
        this.show(message, 'success');
    }

    static error(message) {
        this.show(message, 'error');
    }
}
