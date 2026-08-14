/* =========================================
   Admin Dashboard - Shared Utilities
   API Helpers, Auth Check, Toast, Sidebar
   ========================================= */

const API_BASE = '';

// ---- Auth Check ----
function checkAuth() {
    const token = localStorage.getItem('token');
    if (!token) {
        window.location.href = '/admin/login.html';
    }
}

function logout() {
    localStorage.removeItem('token');
    localStorage.removeItem('adminName');
    window.location.href = '/admin/login.html';
}

// ---- API Helpers ----
function getHeaders() {
    return {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('token')}`
    };
}

async function apiGet(url) {
    const res = await fetch(`${API_BASE}${url}`, {
        headers: getHeaders()
    });
    if (res.status === 401) { logout(); return; }
    if (!res.ok) throw new Error('API Error');
    return res.json();
}

async function apiPost(url, data) {
    const res = await fetch(`${API_BASE}${url}`, {
        method: 'POST',
        headers: getHeaders(),
        body: JSON.stringify(data)
    });
    if (res.status === 401) { logout(); return; }
    const json = await res.json();
    if (!res.ok) throw new Error(json.message || 'API Error');
    return json;
}

async function apiPut(url, data) {
    const res = await fetch(`${API_BASE}${url}`, {
        method: 'PUT',
        headers: getHeaders(),
        body: JSON.stringify(data)
    });
    if (res.status === 401) { logout(); return; }
    const json = await res.json();
    if (!res.ok) throw new Error(json.message || 'API Error');
    return json;
}

async function apiDelete(url) {
    const res = await fetch(`${API_BASE}${url}`, {
        method: 'DELETE',
        headers: getHeaders()
    });
    if (res.status === 401) { logout(); return; }
    const json = await res.json();
    if (!res.ok) throw new Error(json.message || 'API Error');
    return json;
}

// ---- Toast Notification ----
function showToast(message, type = 'success') {
    // Remove existing toast
    const existing = document.querySelector('.toast');
    if (existing) existing.remove();

    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.textContent = message;
    document.body.appendChild(toast);

    // Trigger animation
    requestAnimationFrame(() => {
        toast.classList.add('show');
    });

    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 400);
    }, 3000);
}

// ---- Sidebar Toggle (Mobile) ----
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('open');
}

// Close sidebar on clicking outside (mobile)
document.addEventListener('click', (e) => {
    const sidebar = document.getElementById('sidebar');
    const toggle = document.querySelector('.mobile-menu-toggle');
    if (sidebar && toggle && window.innerWidth <= 768) {
        if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
            sidebar.classList.remove('open');
        }
    }
});
