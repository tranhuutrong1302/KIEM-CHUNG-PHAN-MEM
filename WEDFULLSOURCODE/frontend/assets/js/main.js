const API_BASE = '/backend/api';
let currentUser = null;
let bidModal = null;

async function apiFetch(path, options = {}) {
    options.credentials = 'include';
    options.headers = options.headers || {};
    if (options.body && typeof options.body === 'object') {
        options.headers['Content-Type'] = 'application/json';
        options.body = JSON.stringify(options.body);
    }
    const response = await fetch(`${API_BASE}/${path}`, options);
    const data = await response.json();
    return { status: response.status, data };
}

function formatCurrency(input) {
    let value = input.value.replace(/\D/g, '');
    input.value = value ? new Intl.NumberFormat('de-DE').format(value) : '';
}

function filterSelection(category) {
    const items = document.querySelectorAll('.filter-item');
    const buttons = document.querySelectorAll('.filter-btn');

    buttons.forEach(btn => btn.classList.remove('active'));
    document.querySelector(`.filter-btn[onclick="filterSelection('${category}')"]`)?.classList.add('active');

    const normalizedCategory = category === 'all' ? '' : category.replace(/ /g, '-');
    items.forEach(item => {
        const show = category === 'all' || item.classList.contains(normalizedCategory);
        item.style.display = show ? 'block' : 'none';
    });
}

function normalizeImageUrl(path) {
    if (!path) return '';
    if (path.startsWith('http')) return path;
    path = path.replace(/\\/g, '/');
    const trimmed = path.startsWith('/') ? path.slice(1) : path;
    return '/' + trimmed.split('/').map(segment => encodeURIComponent(segment)).join('/');
}

function buildCard(item) {
    const expired = item.is_expired;
    return `
        <div class="col filter-item ${item.category_name.replace(/ /g, '-')}">
            <div class="dark-card">
                <div class="card-img-wrapper">
                    <div class="cat-badge">${item.category_name}</div>
                    <img src="${normalizeImageUrl(item.image)}" class="card-img-top" alt="${item.name}">
                </div>
                <div class="card-body">
                    <h5 class="card-title" title="${item.name}">${item.name}</h5>
                    <div class="card-price">${Number(item.price).toLocaleString('de-DE')}đ</div>
                    <div class="meta-row">
                        <span class="timer" data-time="${item.end_time_js}"><i class="fa-regular fa-hourglass-half"></i> Đang tính...</span>
                        <span><i class="fa-solid fa-fire text-warning"></i> ${item.bid_count}</span>
                    </div>
                    ${expired ? `
                        <button class="btn-view-auction" style="opacity:0.5; cursor:not-allowed">ĐÃ KẾT THÚC</button>
                    ` : `
                        <button class="btn-view-auction" onclick="openBidModal('${item.id}', '${item.name.replace(/'/g, "\\'")}', ${item.next_min})">XEM CHI TIẾT & ĐẤU GIÁ</button>
                    `}
                </div>
            </div>
        </div>`;
}

function renderProducts(products) {
    const grid = document.getElementById('productGrid');
    if (!grid) return;
    grid.innerHTML = products.map(buildCard).join('');
    filterSelection('all');
}

async function loadSession() {
    const result = await apiFetch('me.php');
    if (result.status === 200 && result.data.logged_in) {
        currentUser = result.data;
    } else {
        currentUser = null;
    }
    renderAuthArea();
}

function renderAuthArea() {
    const authArea = document.getElementById('authArea');
    if (!authArea) return;
    if (currentUser) {
        authArea.innerHTML = `
            <div class="user-greeting">Xin chào, <span>${currentUser.full_name || currentUser.username}</span></div>
            <a href="my_bids.html" class="btn btn-outline-warning btn-sm">Đơn của tôi</a>
            ${currentUser.role === 'admin' ? '<a href="/backend/admin.php" class="btn btn-outline-danger btn-sm">Admin</a>' : ''}
            <button class="btn btn-sm text-secondary" onclick="logout()">Thoát</button>
        `;
    } else {
        authArea.innerHTML = `
            <a href="login.html" class="btn btn-outline-light btn-sm px-4">Đăng nhập</a>
            <a href="register.html" class="btn btn-warning btn-sm text-dark fw-bold px-4">Đăng ký</a>
        `;
    }
}

async function logout() {
    await apiFetch('logout.php', { method: 'POST' });
    window.location.reload();
}

function openBidModal(id, name, minPrice) {
    document.getElementById('mId').value = id;
    document.getElementById('mTitle').innerText = name;
    document.getElementById('mAmount').value = new Intl.NumberFormat('de-DE').format(minPrice);
    document.getElementById('mMinShow').innerText = new Intl.NumberFormat('de-DE').format(minPrice) + ' VNĐ';
    const modal = new bootstrap.Modal(document.getElementById('bidModal'));
    bidModal = modal;
    modal.show();
}

async function submitBidForm(event) {
    event.preventDefault();
    if (!currentUser) {
        alert('Vui lòng đăng nhập để đấu giá');
        return;
    }
    const productId = document.getElementById('mId').value;
    const rawAmount = document.getElementById('mAmount').value.replace(/\D/g, '');
    const bidAmount = parseInt(rawAmount, 10);
    if (!bidAmount) {
        alert('Giá đặt không hợp lệ');
        return;
    }
    const result = await apiFetch('bid.php', {
        method: 'POST',
        body: { product_id: productId, bid_amount: bidAmount }
    });
    if (result.data.success) {
        alert(result.data.message);
        bidModal?.hide();
        loadProducts();
    } else {
        alert(result.data.message || 'Đặt giá thất bại');
    }
}

function updateTimers() {
    document.querySelectorAll('.timer').forEach(el => {
        const dest = parseInt(el.dataset.time, 10);
        const now = Date.now();
        const diff = dest - now;
        if (diff <= 0) {
            el.innerHTML = 'Kết thúc';
            el.style.color = '#888';
        } else {
            const d = Math.floor(diff / (1000 * 60 * 60 * 24));
            const h = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const s = Math.floor((diff % (1000 * 60)) / 1000);
            el.innerHTML = `<i class="fa-regular fa-hourglass-half"></i> ${d}d ${h}h ${m}m ${s}s`;
        }
    });
}

window.addEventListener('DOMContentLoaded', async () => {
    document.getElementById('bidForm').addEventListener('submit', submitBidForm);
    await loadSession();
    const result = await apiFetch('products.php');
    if (result.status === 200 && Array.isArray(result.data)) {
        renderProducts(result.data);
    } else {
        document.getElementById('productGrid').innerHTML = '<div class="text-danger">Không tải được sản phẩm.</div>';
    }
    setInterval(updateTimers, 1000);
});
