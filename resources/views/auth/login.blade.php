<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Manajemen Gudang Cloud</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; background: #f0f2f5; }
        .login-card { background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.1); width: 100%; max-width: 350px; text-align: center; }
        h2 { color: #1a73e8; margin-bottom: 1.5rem; }
        input { width: 100%; padding: 12px; margin: 8px 0; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; font-size: 14px; }
        button { width: 100%; padding: 12px; background: #1a73e8; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; font-weight: bold; margin-top: 10px; transition: background 0.3s; }
        button:hover { background: #1557b0; }
        #message { margin-top: 15px; font-size: 14px; min-height: 20px; }
        .error { color: #d93025; }
        .success { color: #188038; }
    </style>
</head>
<body>
    <div class="login-card">
        <h2>Gudang Cloud</h2>
        <p style="color: #666; font-size: 14px;">Silakan masuk ke akun Anda</p>

        <form id="loginForm">
            <input type="email" id="email" placeholder="Email" required>
            <input type="password" id="password" placeholder="Password" required>
            <button type="submit" id="loginBtn">Log In</button>
        </form>

        <div id="message"></div>
    </div>

    <script>
        const loginForm = document.getElementById('loginForm');
        const messageDiv = document.getElementById('message');
        const loginBtn = document.getElementById('loginBtn');

        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            messageDiv.innerText = "Memproses...";
            messageDiv.className = "";
            loginBtn.disabled = true;

            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            try {
                const response = await fetch('/api/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ email, password })
                });

                const data = await response.json();

                if (response.ok) {
                    messageDiv.innerText = "Login Berhasil! Mengalihkan...";
                    messageDiv.className = "success";

                    localStorage.setItem('access_token', data.access_token);
                    localStorage.setItem('user_role', data.user.role);

                    console.log("Token disimpan:", data.access_token);

                    setTimeout(() => {
                        alert("Selamat Datang, " + data.user.name + " (" + data.user.role + ")");
                        // window.location.href = '/dashboard'; // Nanti diarahkan ke sini
                    }, 1500);

                } else {
                    messageDiv.innerText = data.message || "Email atau password salah.";
                    messageDiv.className = "error";
                }
            } catch (error) {
                messageDiv.innerText = "Terjadi kesalahan koneksi.";
                messageDiv.className = "error";
            } finally {
                loginBtn.disabled = false;
            }
        });
    </script>
</body>
</html>
