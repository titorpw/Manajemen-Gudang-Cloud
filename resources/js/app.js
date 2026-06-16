import { LoginController } from './auth/login-controller';

document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        const loginController = new LoginController();
        loginController.init();
    }
});
