// Auth - Login page logic
const API_BASE = '';

document.getElementById('login-form').addEventListener('submit', async (e) => {
    e.preventDefault();

    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const btn = document.getElementById('login-btn');
    const errorDiv = document.getElementById('login-error');

    btn.textContent = 'جاري تسجيل الدخول...';
    btn.disabled = true;
    errorDiv.style.display = 'none';

    try {
        const res = await fetch(`${API_BASE}/api/auth/login`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email, password })
        });

        const data = await res.json();

        if (!res.ok) {
            throw new Error(data.message || 'بيانات الدخول غير صحيحة');
        }

        localStorage.setItem('token', data.token);
        localStorage.setItem('adminName', data.name);
        window.location.href = 'index.html';
    } catch (err) {
        errorDiv.textContent = err.message;
        errorDiv.style.display = 'block';
    } finally {
        btn.textContent = 'تسجيل الدخول';
        btn.disabled = false;
    }
});

// Redirect if already logged in
if (localStorage.getItem('token')) {
    window.location.href = 'index.html';
}
