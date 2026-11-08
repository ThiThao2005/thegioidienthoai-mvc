<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thế Giới Điện Thoại</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/public/css/theme.css?v=<?php echo @filemtime(__DIR__ . '/../../../public/css/theme.css') ?: 1; ?>">
    <meta name="theme-color" content="#1f3a3d">
</head>

<body>

<?php
    $cartCount = 0;
    if (class_exists('SessionHelper')) {
        $cartCount = SessionHelper::getCartCount();
    }

    $fullname = '';
    $initials = 'T';

    if (class_exists('SessionHelper') && SessionHelper::isLoggedIn()) {
        $fullname = SessionHelper::getUserData('fullname') ?: 'Tài khoản';
        $initials = mb_strtoupper(mb_substr($fullname, 0, 1, 'UTF-8'), 'UTF-8');
    }
?>

<div class="store-topbar">
    <div class="container">
        <div class="store-topbar-inner">
            <div class="store-topbar-left">
                <span><i class="fas fa-shield-alt"></i> Hàng chính hãng 100%</span>
                <span><i class="fas fa-sync-alt"></i> Đổi trả 7 ngày</span>
                <span><i class="fas fa-truck-fast"></i> Giao nhanh toàn quốc</span>
            </div>

            <div class="store-topbar-right">
                <a href="tel:19001234"><i class="fas fa-phone"></i> 1900 1234</a>
                <a href="/Product/myOrders"><i class="fas fa-receipt"></i> Tra cứu đơn hàng</a>
            </div>
        </div>
    </div>
</div>

<header class="store-header">
    <div class="store-mainnav">
        <div class="container">
            <div class="store-main-row">

                <button class="store-action-btn d-lg-none"
                        onclick="openMobileMenu()"
                        type="button">
                    <i class="fas fa-bars"></i>
                </button>

                <a href="/Product/index" class="store-logo">
                    <span class="store-logo-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </span>
                    <span class="store-logo-text">
                        <strong>THẾ GIỚI ĐIỆN THOẠI</strong>
                        <span>Phone • Laptop • Phụ kiện</span>
                    </span>
                </a>

                <a href="#" class="store-location d-none d-xl-flex">
                    <i class="fas fa-location-dot"></i>
                    <span>Hồ Chí Minh</span>
                </a>

                <form id="searchForm" class="store-search" novalidate>
                    <input id="searchInput"
                           type="search"
                           placeholder="Bạn cần tìm iPhone, Samsung, laptop...?"
                           autocomplete="off">
                    <button type="submit" aria-label="Tìm kiếm">
                        <i class="fas fa-search"></i>
                    </button>
                </form>

                <div class="store-actions">
                    <a class="store-action-btn d-none d-md-inline-flex" href="/Product/compare">
                        <i class="fas fa-scale-balanced"></i>
                        <span class="action-text">So sánh</span>
                    </a>

                    <a class="store-action-btn d-none d-md-inline-flex" href="/Product/myOrders">
                        <i class="fas fa-receipt"></i>
                        <span class="action-text">Đơn hàng</span>
                    </a>

                    <a class="store-action-btn" href="/Product/cart">
                        <i class="fas fa-cart-shopping"></i>
                        <span class="action-text">Giỏ hàng</span>
                        <span class="cart-badge <?php echo $cartCount > 0 ? '' : 'empty'; ?>">
                            <?php echo $cartCount; ?>
                        </span>
                    </a>

                    <?php if (SessionHelper::isLoggedIn()): ?>
                        <div class="user-dropdown">
                            <button class="store-action-btn"
                                    id="userDropdownBtn"
                                    onclick="toggleDropdown(event)"
                                    type="button">
                                <span class="user-avatar">
                                    <?php echo htmlspecialchars($initials, ENT_QUOTES, 'UTF-8'); ?>
                                </span>
                                
                                <i class="fas fa-chevron-down" style="font-size:.7rem;"></i>
                            </button>

                            <div class="user-menu" id="userDropdownMenu">
                                <?php if (SessionHelper::isAdmin()): ?>
                                    <a class="user-menu-item admin" href="/Product/dashboard">
                                        <i class="fas fa-user-shield"></i> Quản trị hệ thống
                                    </a>
                                    <div class="user-menu-divider"></div>
                                <?php endif; ?>

                                <a class="user-menu-item" href="/Account/profile">
                                    <i class="fas fa-user"></i> Tài khoản của tôi
                                </a>
                                <a class="user-menu-item" href="/Product/myOrders">
                                    <i class="fas fa-box"></i> Đơn hàng của tôi
                                </a>
                                <a class="user-menu-item" href="/Product/wishlist">
                                    <i class="fas fa-heart"></i> Sản phẩm yêu thích
                                </a>
                                <a class="user-menu-item" href="/Account/changePassword">
                                    <i class="fas fa-key"></i> Đổi mật khẩu
                                </a>


                      
                                <div class="user-menu-divider"></div>

                                <a class="user-menu-item danger" href="/Account/logout">
                                    <i class="fas fa-sign-out-alt"></i> Đăng xuất
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a class="store-action-btn" href="/Account/login">
                            <i class="fas fa-user-circle"></i>
                            <span class="action-text">Đăng nhập</span>
                        </a>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>

    <div class="store-catnav">
        <div class="container">
            <div class="store-cat-row">
                <a href="/Product/index?category_id=1" class="store-cat-item">
                    <i class="fas fa-mobile-screen-button"></i> Điện thoại
                </a>
                <a href="/Product/index?category_id=2" class="store-cat-item">
                    <i class="fas fa-laptop"></i> Laptop
                </a>
                <a href="/Product/index?category_id=3" class="store-cat-item">
                    <i class="fas fa-tablet-screen-button"></i> Máy tính bảng
                </a>
                <a href="/Product/index?category_id=4" class="store-cat-item">
                    <i class="fas fa-headphones"></i> Phụ kiện
                </a>
                <a href="/Product/index?featured=1" class="store-cat-item hot">
                    <i class="fas fa-fire"></i> Hàng nổi bật
                </a>
            </div>
        </div>
    </div>
</header>

<div class="mobile-menu" id="mobileMenu" onclick="closeMobileMenuOutside(event)">
    <div class="mobile-panel">
        <div class="mobile-head">
            <a href="/Product/index" class="store-logo">
                <span class="store-logo-icon" style="width:36px;height:36px;font-size:.95rem;border-radius:11px;">
                    <i class="fas fa-mobile-alt"></i>
                </span>
                <span class="store-logo-text">
                    <strong>THẾ GIỚI ĐIỆN THOẠI</strong>
                </span>
            </a>

            <button class="mobile-close" onclick="closeMobileMenu()" type="button">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="mobile-section">
            <form id="searchFormMobile" class="mobile-search" novalidate>
                <input id="searchInputMobile" type="search" placeholder="Tìm sản phẩm...">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
        </div>

        <div class="mobile-section">
            <div class="mobile-title">Tài khoản</div>

            <?php if (SessionHelper::isLoggedIn()): ?>
                <div style="display:flex;align-items:center;gap:10px;padding:8px 12px;margin-bottom:6px;">
                    <span class="user-avatar" style="width:36px;height:36px;font-size:.9rem;">
                        <?php echo htmlspecialchars($initials, ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                    <div>
                        <div style="font-size:.9rem;font-weight:900;">
                            <?php echo htmlspecialchars($fullname, ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                        <div style="font-size:.74rem;color:var(--muted);">Đã đăng nhập</div>
                    </div>
                </div>

                <?php if (SessionHelper::isAdmin()): ?>
                    <a class="mobile-link" href="/Product/dashboard">
                        <i class="fas fa-user-shield"></i> Quản trị hệ thống
                    </a>
                <?php endif; ?>

                <a class="mobile-link" href="/Account/profile">
                    <i class="fas fa-user"></i> Tài khoản của tôi
                </a>

                <a class="mobile-link" href="/Product/myOrders">
                    <i class="fas fa-box"></i> Đơn hàng của tôi
                </a>

                <a class="mobile-link" href="/Product/wishlist">
                    <i class="fas fa-heart" style="color:var(--accent);"></i> Sản phẩm yêu thích
                </a>

                <a class="mobile-link danger" href="/Account/logout">
                    <i class="fas fa-sign-out-alt"></i> Đăng xuất
                </a>
            <?php else: ?>
                <a class="mobile-link" href="/Account/login">
                    <i class="fas fa-sign-in-alt"></i> Đăng nhập
                </a>
                <a class="mobile-link" href="/Account/register">
                    <i class="fas fa-user-plus"></i> Đăng ký tài khoản
                </a>
            <?php endif; ?>
        </div>

        <div class="mobile-section">
            <div class="mobile-title">Danh mục sản phẩm</div>

            <a class="mobile-link" href="/Product/index?category_id=1">
                <i class="fas fa-mobile-screen-button"></i> Điện thoại
            </a>
            <a class="mobile-link" href="/Product/index?category_id=2">
                <i class="fas fa-laptop"></i> Laptop
            </a>
            <a class="mobile-link" href="/Product/index?category_id=3">
                <i class="fas fa-tablet-screen-button"></i> Máy tính bảng
            </a>
            <a class="mobile-link" href="/Product/index?category_id=4">
                <i class="fas fa-headphones"></i> Phụ kiện
            </a>
            <a class="mobile-link danger" href="/Product/index?featured=1">
                <i class="fas fa-fire"></i> Hàng nổi bật
            </a>
        </div>

        <div class="mobile-section">
            <div class="mobile-title">Tiện ích</div>

            <a class="mobile-link" href="/Product/cart">
                <i class="fas fa-cart-shopping"></i>
                Giỏ hàng
                <span class="cart-badge ms-auto <?php echo $cartCount > 0 ? '' : 'empty'; ?>">
                    <?php echo $cartCount; ?>
                </span>
            </a>

            <a class="mobile-link" href="/Product/compare">
                <i class="fas fa-scale-balanced"></i> So sánh sản phẩm
            </a>
        </div>
    </div>
</div>

<div class="tgdd-content">
    <div class="container">

<script>
// Ảnh bị lỗi/không tồn tại -> hiện ảnh thay thế thay vì icon ảnh vỡ
document.addEventListener('error', function (e) {
    const img = e.target;
    if (!(img instanceof HTMLImageElement) || img.dataset.fallback) return;
    img.dataset.fallback = '1';
    img.src = 'data:image/svg+xml;charset=utf-8,' + encodeURIComponent(
        '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200"><rect width="200" height="200" fill="#eaf6f0"/>' +
        '<rect x="70" y="40" width="60" height="110" rx="12" fill="none" stroke="#7cc6ab" stroke-width="6"/>' +
        '<circle cx="100" cy="132" r="5" fill="#7cc6ab"/><circle cx="138" cy="44" r="8" fill="#ee8a6f"/></svg>');
}, true);

function toggleDropdown(e) {
    e.stopPropagation();

    const menu = document.getElementById('userDropdownMenu');
    const btn = document.getElementById('userDropdownBtn');

    if (!menu) return;

    const isOpen = menu.classList.contains('show');
    closeAllDropdowns();

    if (!isOpen) {
        menu.classList.add('show');
        if (btn) btn.setAttribute('aria-expanded', 'true');
    }
}

function closeAllDropdowns() {
    document.querySelectorAll('.user-menu').forEach(menu => {
        menu.classList.remove('show');
    });

    const btn = document.getElementById('userDropdownBtn');
    if (btn) btn.setAttribute('aria-expanded', 'false');
}

document.addEventListener('click', closeAllDropdowns);

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeAllDropdowns();
        closeMobileMenu();
    }
});

function openMobileMenu() {
    const menu = document.getElementById('mobileMenu');
    if (!menu) return;

    menu.classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeMobileMenu() {
    const menu = document.getElementById('mobileMenu');
    if (!menu) return;

    menu.classList.remove('open');
    document.body.style.overflow = '';
}

function closeMobileMenuOutside(e) {
    const menu = document.getElementById('mobileMenu');

    if (e.target === menu) {
        closeMobileMenu();
    }
}

function handleSearch(keyword) {
    keyword = keyword.trim();

    if (!keyword) {
        showFlash('warning', 'Vui lòng nhập từ khóa cần tìm!');
        return false;
    }

    window.location.href = '/Product/index?search=' + encodeURIComponent(keyword);
    return false;
}

const searchForm = document.getElementById('searchForm');
if (searchForm) {
    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        handleSearch(document.getElementById('searchInput').value);
    });
}

const searchFormMobile = document.getElementById('searchFormMobile');
if (searchFormMobile) {
    searchFormMobile.addEventListener('submit', function(e) {
        e.preventDefault();
        handleSearch(document.getElementById('searchInputMobile').value);
        closeMobileMenu();
    });
}

(function highlightActiveCategory() {
    const params = new URLSearchParams(window.location.search);
    const catId = params.get('category_id');
    const featured = params.get('featured');

    document.querySelectorAll('.store-cat-item').forEach(link => {
        if (catId && link.href.includes('category_id=' + catId)) {
            link.classList.add('active');
        }

        if (featured === '1' && link.href.includes('featured=1')) {
            link.classList.add('active');
        }
    });
})();

function showFlash(type, msg) {
    let wrap = document.getElementById('flashWrap');

    if (!wrap) {
        wrap = document.createElement('div');
        wrap.id = 'flashWrap';
        wrap.className = 'tgdd-flash';
        document.body.appendChild(wrap);
    }

    const colors = {
        success: '#2f8f5b',
        warning: '#c98a1b',
        danger: '#c8493a',
        info: '#2f7d6d'
    };

    const icons = {
        success: 'check-circle',
        warning: 'exclamation-triangle',
        danger: 'times-circle',
        info: 'info-circle'
    };

    const div = document.createElement('div');
    div.style.cssText = `
        background:${colors[type] || colors.info};
        color:#fff;
        border-radius:14px;
        padding:12px 16px;
        margin-bottom:8px;
        font-size:.86rem;
        font-weight:700;
        display:flex;
        align-items:center;
        gap:8px;
        box-shadow:0 10px 30px rgba(31,58,61,.22);
        animation:flashIn .25s ease;
    `;

    div.innerHTML = `<i class="fas fa-${icons[type] || icons.info}"></i><span>${msg}</span>`;
    wrap.appendChild(div);

    setTimeout(() => div.remove(), 3200);
}
</script>


<div class="tgdd-flash" id="flashWrap"></div>