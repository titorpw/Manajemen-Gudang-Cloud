import { ApiClient } from '../services/api';
import { ToastService } from '../services/toast';
import { Validator } from '../utils/validation';
import { ThemeService } from '../services/theme';
import { signInWithEmailAndPassword } from 'firebase/auth';
import { auth, isConfigured } from '../services/firebase';

export class LoginController {
    constructor(config = {}) {
        this.formId = config.formId || 'loginForm';
        this.emailId = config.emailId || 'email';
        this.passwordId = config.passwordId || 'password';
        this.rememberMeId = config.rememberMeId || 'rememberMe';

        this.state = {
            email: '',
            password: '',
            rememberMe: false,
            isSubmitting: false,
            errors: {
                email: '',
                password: ''
            }
        };

        this.initDOM();
    }

    initDOM() {
        this.form = document.getElementById(this.formId);
        this.emailInput = document.getElementById(this.emailId);
        this.passwordInput = document.getElementById(this.passwordId);
        this.rememberMeCheckbox = document.getElementById(this.rememberMeId);

        this.emailError = document.getElementById('emailError');
        this.passwordError = document.getElementById('passwordError');

        this.loginBtn = document.getElementById('loginBtn');
        this.btnText = document.getElementById('btnText');
        this.btnSpinner = document.getElementById('btnSpinner');

        this.togglePasswordBtn = document.getElementById('togglePassword');
        this.eyeIconOpen = document.getElementById('eyeIconOpen');
        this.eyeIconClosed = document.getElementById('eyeIconClosed');

        this.themeToggleBtn = document.getElementById('themeToggle');
    }

    init() {
        if (!this.form) return;

        ThemeService.init();
        this.loadSavedEmail();
        this.bindEvents();
    }

    loadSavedEmail() {
        const savedEmail = localStorage.getItem('remember_email');
        if (savedEmail) {
            this.state.email = savedEmail;
            this.state.rememberMe = true;

            if (this.emailInput) this.emailInput.value = savedEmail;
            if (this.rememberMeCheckbox) this.rememberMeCheckbox.checked = true;
        }
    }

    bindEvents() {
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));

        this.emailInput.addEventListener('input', () => this.validateEmailField());
        this.passwordInput.addEventListener('input', () => this.validatePasswordField());

        if (this.togglePasswordBtn) {
            this.togglePasswordBtn.addEventListener('click', () => this.togglePasswordVisibility());
        }

        if (this.themeToggleBtn) {
            this.themeToggleBtn.addEventListener('click', () => ThemeService.toggle());
        }
    }

    togglePasswordVisibility() {
        const isPassword = this.passwordInput.getAttribute('type') === 'password';
        this.passwordInput.setAttribute('type', isPassword ? 'text' : 'password');

        if (isPassword) {
            this.eyeIconOpen.classList.add('hidden');
            this.eyeIconClosed.classList.remove('hidden');
        } else {
            this.eyeIconOpen.classList.remove('hidden');
            this.eyeIconClosed.classList.add('hidden');
        }
    }

    validateEmailField() {
        const emailVal = this.emailInput.value.trim();
        this.state.email = emailVal;

        if (!Validator.isRequired(emailVal)) {
            this.state.errors.email = 'Alamat email tidak boleh kosong.';
            this.setFieldInvalid(this.emailInput, this.emailError, this.state.errors.email);
            return false;
        } else if (!Validator.isEmail(emailVal)) {
            this.state.errors.email = 'Format email tidak valid (contoh: nama@perusahaan.com).';
            this.setFieldInvalid(this.emailInput, this.emailError, this.state.errors.email);
            return false;
        } else {
            this.state.errors.email = '';
            this.setFieldValid(this.emailInput, this.emailError);
            return true;
        }
    }

    validatePasswordField() {
        const passVal = this.passwordInput.value;
        this.state.password = passVal;

        if (!Validator.isRequired(passVal)) {
            this.state.errors.password = 'Password tidak boleh kosong.';
            this.setFieldInvalid(this.passwordInput, this.passwordError, this.state.errors.password);
            return false;
        } else {
            this.state.errors.password = '';
            this.setFieldValid(this.passwordInput, this.passwordError);
            return true;
        }
    }

    setFieldInvalid(input, errorElement, msg) {
        input.classList.remove('border-emerald-500', 'focus:border-emerald-500', 'border-slate-800');
        input.classList.add('border-rose-500', 'focus:border-rose-500');
        if (errorElement) {
            errorElement.textContent = msg;
            errorElement.classList.remove('hidden');
        }
    }

    setFieldValid(input, errorElement) {
        input.classList.remove('border-rose-500', 'focus:border-rose-500', 'border-slate-800');
        input.classList.add('border-emerald-500', 'focus:border-emerald-500');
        if (errorElement) {
            errorElement.classList.add('hidden');
        }
    }

    setLoading(loading) {
        this.state.isSubmitting = loading;

        this.emailInput.disabled = loading;
        this.passwordInput.disabled = loading;
        if (this.rememberMeCheckbox) this.rememberMeCheckbox.disabled = loading;
        this.loginBtn.disabled = loading;

        if (loading) {
            this.btnText.innerText = "Memproses...";
            this.btnSpinner.classList.remove('hidden');
        } else {
            this.btnText.innerText = "Masuk Ke Sistem";
            this.btnSpinner.classList.add('hidden');
        }
    }

    async handleSubmit(e) {
        e.preventDefault();

        if (!isConfigured) {
            ToastService.error("Konfigurasi Firebase belum terpasang di file .env. Silakan atur VITE_FIREBASE_* terlebih dahulu.");
            return;
        }

        const isEmailValid = this.validateEmailField();
        const isPasswordValid = this.validatePasswordField();

        if (!isEmailValid || !isPasswordValid) {
            ToastService.error("Silakan perbaiki kolom yang salah sebelum masuk.");
            return;
        }

        this.setLoading(true);

        try {
            const userCredential = await signInWithEmailAndPassword(auth, this.state.email, this.state.password);
            const firebaseToken = await userCredential.user.getIdToken();
            const response = await ApiClient.post('/api/login', {
                firebase_token: firebaseToken
            });

            if (response.success) {
                const data = response.data;

                this.state.rememberMe = this.rememberMeCheckbox ? this.rememberMeCheckbox.checked : false;
                if (this.state.rememberMe) {
                    localStorage.setItem('remember_email', this.state.email);
                } else {
                    localStorage.removeItem('remember_email');
                }

                localStorage.setItem('access_token', data.access_token);
                localStorage.setItem('user_role', data.user.role);

                ToastService.success(`Login berhasil! Selamat datang, ${data.user.name}.`);

                setTimeout(() => {
                    window.location.href = '/dashboard';
                }, 1500);
            } else {
                ToastService.error(response.message);
                this.setLoading(false);
                this.passwordInput.value = '';
                this.passwordInput.focus();
                this.validatePasswordField();
            }
        } catch (firebaseError) {
            console.error('Firebase Auth Error:', firebaseError);

            let userFriendlyMsg = "Email atau password salah.";
            if (firebaseError.code === 'auth/invalid-credential' || firebaseError.code === 'auth/wrong-password' || firebaseError.code === 'auth/user-not-found') {
                userFriendlyMsg = "Kredensial salah atau akun tidak terdaftar.";
            } else if (firebaseError.code === 'auth/too-many-requests') {
                userFriendlyMsg = "Terlalu banyak percobaan login gagal. Silakan coba lagi nanti.";
            } else if (firebaseError.code === 'auth/network-request-failed') {
                userFriendlyMsg = "Gagal terhubung ke Firebase. Periksa koneksi internet Anda.";
            } else if (firebaseError.message) {
                userFriendlyMsg = firebaseError.message;
            }

            ToastService.error(userFriendlyMsg);
            this.setLoading(false);
            this.passwordInput.value = '';
            this.passwordInput.focus();
            this.validatePasswordField();
        }
    }
}
