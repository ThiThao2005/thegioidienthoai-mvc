<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thế Giới Điện Thoại</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>

    <style>
        :root {
            --yellow: #ffd400;
            --yellow-dark: #f5c400;
            --black: #111;
            --red: #d70018;
            --blue: #288ad6;
            --bg: #f3f4f6;
            --white: #fff;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            background: var(--bg);
            color: #111;
            font-family: Arial, Helvetica, sans-serif;
        }

        a { text-decoration: none; }

        /* ===== TOP BAR ===== */
        .store-topbar {
            background: #111;
            color: #fff;
            font-size: .78rem;
            padding: 7px 0;
        }

        .store-topbar-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
        }

        .store-topbar-left,
        .store-topbar-right {
            display: flex;
            align-items: center;
            gap: 18px;
            flex-wrap: wrap;
        }

        .store-topbar i {
            color: var(--yellow);
            margin-right: 5px;
        }

        .store-topbar a {
            color: #fff;
            font-weight: 700;
        }

        .store-topbar a:hover {
            color: var(--yellow);
        }

        /* ===== MAIN HEADER ===== */
        .store-header {
            position: sticky;
            top: 0;
            z-index: 1030;
            background: linear-gradient(180deg, #ffd400 0%, #f8c900 100%);
            box-shadow: 0 4px 18px rgba(0,0,0,.12);
        }

        .store-mainnav {
            padding: 12px 0;
        }

        .store-main-row {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        /* ===== LOGO ===== */
        .store-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #111 !important;
            min-width: max-content;
        }

        .store-logo-icon {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            background: #111;
            color: var(--yellow);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            box-shadow: 0 8px 18px rgba(0,0,0,.22);
        }

        .store-logo-text {
            display: flex;
            flex-direction: column;
            line-height: 1.05;
        }

        .store-logo-text strong {
            font-size: 1.08rem;
            font-weight: 950;
            letter-spacing: -.4px;
        }

        .store-logo-text span {
            font-size: .68rem;
            font-weight: 800;
            color: rgba(0,0,0,.62);
        }

        /* ===== LOCATION BUTTON ===== */
        .store-location {
            background: rgba(255,255,255,.55);
            color: #111;
            border-radius: 16px;
            padding: 8px 12px;
            font-size: .78rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 7px;
            min-width: max-content;
            transition: .2s;
        }

        .store-location:hover {
            background: #fff;
            color: #111;
        }

        .store-location i {
            color: #d70018;
        }

        /* ===== SEARCH ===== */
        .store-search {
            flex: 1;
            max-width: 610px;
            display: flex;
            align-items: center;
            background: #fff;
            border-radius: 999px;
            overflow: hidden;
            box-shadow: 0 8px 22px rgba(0,0,0,.12);
            border: 2px solid rgba(255,255,255,.8);
        }

        .store-search input {
            flex: 1;
            border: none;
            outline: none;
            padding: 12px 18px;
            font-size: .92rem;
        }

        .store-search button {
            width: 54px;
            height: 46px;
            border: none;
            background: #111;
            color: var(--yellow);
            font-size: 1rem;
            transition: .2s;
        }

        .store-search button:hover {
            background: #000;
        }

        /* ===== ACTIONS ===== */
        .store-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-left: auto;
        }

        .store-action-btn {
            min-height: 42px;
            border: none;
            border-radius: 999px;
            background: rgba(255,255,255,.55);
            color: #111;
            padding: 8px 13px;
            font-size: .8rem;
            font-weight: 900;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            white-space: nowrap;
            transition: .2s;
            cursor: pointer;
        }

        .store-action-btn:hover {
            background: #fff;
            color: #111;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(0,0,0,.11);
        }

        .store-action-btn i {
            font-size: 1.05rem;
        }

        .cart-badge {
            min-width: 19px;
            height: 19px;
            border-radius: 999px;
            background: var(--red);
            color: #fff;
            font-size: .68rem;
            font-weight: 950;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 6px;
        }

        .cart-badge.empty {
            background: rgba(0,0,0,.22);
            color: #555;
        }

        /* ===== USER ===== */
        .user-dropdown {
            position: relative;
        }

        .user-avatar {
            width: 27px;
            height: 27px;
            border-radius: 50%;
            background: #111;
            color: var(--yellow);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: .76rem;
            font-weight: 950;
        }

        .user-menu {
            position: absolute;
            right: 0;
            top: calc(100% + 10px);
            width: 250px;
            background: #fff;
            border-radius: 18px;
            padding: 8px;
            box-shadow: 0 16px 42px rgba(0,0,0,.18);
            border: 1px solid rgba(0,0,0,.06);
            display: none;
            z-index: 9999;
            animation: menuFade .18s ease;
        }

        .user-menu.show {
            display: block;
        }

        @keyframes menuFade {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .user-menu-item {
            color: #222;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 12px;
            border-radius: 12px;
            font-size: .86rem;
            font-weight: 800;
            transition: .15s;
        }

        .user-menu-item:hover {
            background: #fff6cc;
            color: #111;
        }

        .user-menu-item.admin {
            color: #1565c0;
        }

        .user-menu-item.admin:hover {
            background: #e3f2fd;
        }

        .user-menu-item.danger {
            color: var(--red);
        }

        .user-menu-item.danger:hover {
            background: #fff0f0;
        }

        .user-menu-divider {
            height: 1px;
            background: #eee;
            margin: 6px 4px;
        }

        /* ===== CATEGORY NAV ===== */
        .store-catnav {
            background: #fff;
            border-top: 1px solid rgba(0,0,0,.06);
            border-bottom: 1px solid rgba(0,0,0,.07);
        }

        .store-cat-row {
            display: flex;
            align-items: center;
            gap: 10px;
            overflow-x: auto;
            padding: 9px 0;
            scrollbar-width: none;
        }

        .store-cat-row::-webkit-scrollbar {
            display: none;
        }

        .store-cat-item {
            flex: 1;
            min-width: 125px;
            color: #222;
            border-radius: 16px;
            padding: 10px 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            font-size: .8rem;
            font-weight: 900;
            transition: .2s;
        }

        .store-cat-item i {
            font-size: 1.12rem;
        }

        .store-cat-item:hover,
        .store-cat-item.active {
            background: #fff2b0;
            color: #111;
            transform: translateY(-1px);
        }

        .store-cat-item.hot {
            color: var(--red);
        }

        /* ===== MOBILE MENU ===== */
        .mobile-menu {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.52);
            z-index: 2000;
        }

        .mobile-menu.open {
            display: flex;
        }

        .mobile-panel {
            width: 310px;
            max-width: 88vw;
            height: 100%;
            background: #fff;
            overflow-y: auto;
            animation: slideIn .24s ease;
        }

        @keyframes slideIn {
            from { transform: translateX(-100%); }
            to { transform: translateX(0); }
        }

        .mobile-head {
            background: var(--yellow);
            padding: 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .mobile-close {
            width: 34px;
            height: 34px;
            border: none;
            border-radius: 50%;
            background: rgba(0,0,0,.12);
        }

        .mobile-section {
            padding: 14px;
            border-bottom: 1px solid #eee;
        }

        .mobile-title {
            font-size: .72rem;
            font-weight: 950;
            color: #999;
            text-transform: uppercase;
            letter-spacing: .08em;
            margin-bottom: 8px;
        }

        .mobile-search {
            display: flex;
        }

        .mobile-search input {
            flex: 1;
            border: 1px solid #e5e5e5;
            border-right: none;
            border-radius: 13px 0 0 13px;
            padding: 10px 12px;
            outline: none;
        }

        .mobile-search button {
            border: none;
            background: #111;
            color: var(--yellow);
            border-radius: 0 13px 13px 0;
            padding: 0 15px;
        }

        .mobile-link {
            color: #222;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 12px;
            border-radius: 12px;
            font-size: .88rem;
            font-weight: 800;
        }

        .mobile-link:hover {
            background: #fff6cc;
            color: #111;
        }

        .mobile-link i {
            width: 22px;
            text-align: center;
        }

        /* ===== CONTENT ===== */
        .tgdd-content {
            padding: 26px 0 48px;
        }

        .tgdd-flash {
            position: fixed;
            top: 90px;
            right: 20px;
            z-index: 99999;
            max-width: 330px;
        }

        .card-product {
            border: none;
            border-radius: 20px;
            box-shadow: 0 6px 18px rgba(0,0,0,.08);
            transition: .22s;
            overflow: hidden;
            background: #fff;
        }

        .card-product:hover {
            transform: translateY(-5px);
            box-shadow: 0 14px 34px rgba(0,0,0,.14);
        }

        .price-text {
            color: var(--red);
            font-weight: 950;
            font-size: 1.1rem;
        }

        @media (max-width: 1199px) {
            .store-location {
                display: none;
            }
        }

        @media (max-width: 991px) {
            .store-topbar {
                display: none;
            }

            .store-mainnav {
                padding: 10px 0;
            }

            .store-search {
                display: none;
            }

            .store-logo-icon {
                width: 39px;
                height: 39px;
            }

            .store-action-btn {
                padding: 8px 11px;
            }

            .store-catnav {
                display: none;
            }
        }

        @media (max-width: 575px) {
            .store-logo-text strong {
                font-size: .86rem;
            }

            .store-logo-text span {
                display: none;
            }

            .store-action-btn span.action-text {
                display: none;
            }
        }
    </style>
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
                <a href="/Product/index?category_id=5" class="store-cat-item">
                    <i class="fas fa-volume-high"></i> Âm thanh
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
                <span class="store-logo-icon" style="width:34px;height:34px;font-size:.95rem;">
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
                        <div style="font-size:.72rem;color:#888;">Đã đăng nhập</div>
                    </div>
                </div>

                <?php if (SessionHelper::isAdmin()): ?>
                    <a class="mobile-link" href="/Product/dashboard">
                        <i class="fas fa-user-shield" style="color:#1565c0;"></i> Quản trị hệ thống
                    </a>
                <?php endif; ?>

                <a class="mobile-link" href="/Account/profile">
                    <i class="fas fa-user"></i> Tài khoản của tôi
                </a>

                <a class="mobile-link" href="/Product/myOrders">
                    <i class="fas fa-box"></i> Đơn hàng của tôi
                </a>

                <a class="mobile-link" href="/Product/wishlist">
                    <i class="fas fa-heart" style="color:#d70018;"></i> Sản phẩm yêu thích
                </a>

                <a class="mobile-link" href="/Account/logout" style="color:#d70018;">
                    <i class="fas fa-sign-out-alt" style="color:#d70018;"></i> Đăng xuất
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
            <a class="mobile-link" href="/Product/index?category_id=5">
                <i class="fas fa-volume-high"></i> Thiết bị âm thanh
            </a>
            <a class="mobile-link" href="/Product/index?featured=1" style="color:#d70018;">
                <i class="fas fa-fire" style="color:#d70018;"></i> Hàng nổi bật
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
        success: '#2e7d32',
        warning: '#e65100',
        danger: '#d70018',
        info: '#1565c0'
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
        box-shadow:0 8px 24px rgba(0,0,0,.18);
        animation:flashIn .25s ease;
    `;

    div.innerHTML = `<i class="fas fa-${icons[type] || icons.info}"></i><span>${msg}</span>`;
    wrap.appendChild(div);

    setTimeout(() => div.remove(), 3200);
}
</script>

<style>
@keyframes flashIn {
    from {
        opacity: 0;
        transform: translateX(18px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}
</style>

<div class="tgdd-flash" id="flashWrap"></div>