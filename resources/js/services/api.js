export class ApiClient {
    static getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    static async request(url, options = {}) {
        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': this.getCsrfToken(),
            ...(options.headers || {})
        };

        const config = {
            ...options,
            headers
        };

        if (config.body && typeof config.body === 'object') {
            config.body = JSON.stringify(config.body);
        }

        try {
            const response = await fetch(url, config);
            const data = await response.json().catch(() => ({}));

            if (!response.ok) {
                return {
                    success: false,
                    status: response.status,
                    message: data.message || 'Terjadi kesalahan pada server.',
                    errors: data.errors || null
                };
            }

            return {
                success: true,
                status: response.status,
                data
            };
        } catch (error) {
            console.error('API Request Error:', error);
            return {
                success: false,
                message: 'Gagal terhubung ke server. Periksa koneksi internet Anda.',
                errors: null
            };
        }
    }

    static post(url, body, options = {}) {
        return this.request(url, { method: 'POST', body, ...options });
    }

    static get(url, options = {}) {
        return this.request(url, { method: 'GET', ...options });
    }
}
