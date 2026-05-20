<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📱 THẾ GIỚI ĐIỆN THOẠI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        .tgdd-bg { background-color: #ffd400; } 
        
        .card-product { transition: transform 0.2s; border: none; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .card-product:hover { transform: translateY(-5px); box-shadow: 0 4px 15px rgba(0,0,0,0.2); }
        .price-text { color: #d70018; font-weight: bold; font-size: 1.2rem; }
        
        .search-btn { background-color: #333; color: white; border: none; }
        .search-btn:hover { background-color: #000; }

        .sub-nav-bg { background-color: #fccc00; border-top: 1px solid rgba(0,0,0,0.05); }
        .category-item { transition: all 0.2s; border-radius: 8px; padding: 5px 10px; }
        .category-item:hover { background-color: rgba(0,0,0,0.05); transform: translateY(-2px); }
        .category-link { color: #333; text-decoration: none; font-size: 0.85rem; display: flex; flex-direction: column; align-items: center; gap: 5px; }
        .category-link i { font-size: 1.5rem; }
        
        .user-dropdown-btn { background-color: rgba(255, 255, 255, 0.5) !important; border: none !important; color: #333 !important; transition: all 0.2s; }
        .user-dropdown-btn:hover { background-color: rgba(255, 255, 255, 0.7) !important; }
        .dropdown-item { transition: all 0.15s ease-in-out; }
        .dropdown-item:hover { background-color: #ffd400 !important; color: #000 !important; }
        .badge-cart { font-size: 0.75rem; padding: 0.35em 0.65em; }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg tgdd-bg py-3 shadow-sm pb-2 pb-lg-3">
    <div class="container">
        <a class="navbar-brand fw-bold text-dark fs-4" href="/project1/Product/index">📱 THẾ GIỚI ĐIỆN THOẠI</a>
        
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <form id="searchForm" class="d-flex mx-auto w-50 my-2 my-lg-0">
                <input id="searchInput" class="form-control me-2 rounded-pill border-0 px-4 py-2" type="search" placeholder="Bạn tìm gì..." required>
                <button class="btn search-btn rounded-pill px-4" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </form>
            
            <ul class="navbar-nav ms-auto gap-3 align-items-lg-center">
                
                <li class="nav-item">
    <?php if (SessionHelper::isLoggedIn()): ?>
        <div class="dropdown">
            <button class="btn user-dropdown-btn fw-bold rounded-pill px-3 py-2 dropdown-toggle" 
                    type="button" 
                    id="userDropdown" 
                    data-bs-toggle="dropdown" 
                    aria-expanded="false">
                <i class="fas fa-user-circle me-1 fs-5 align-middle"></i> 
                <?= htmlspecialchars(SessionHelper::getUserData('fullname')) ?>
            </button>
            
            <ul class="dropdown-menu dropdown-menu-end border-0 shadow mt-2 rounded-3 py-2" aria-labelledby="userDropdown">
                <?php if (SessionHelper::isAdmin()): ?>
                    <li>
                        <a class="dropdown-item small py-2 fw-semibold text-primary" href="/project1/Product/dashboard">
                            <i class="fas fa-user-shield me-2"></i>Quản trị hệ thống
                        </a>
                    </li>
                    <li><hr class="dropdown-divider opacity-50"></li>
                <?php endif; ?>
                
                <li>
                    <a class="dropdown-item small py-2 fw-semibold text-dark" 
                       href="/project1/Product/myOrders"
                       onclick="checkOrderClick(event, <?= SessionHelper::isLoggedIn() ? 'true' : 'false' ?>)">
                        <i class="fas fa-box me-2 text-warning"></i>Đơn hàng của tôi
                    </a>
                </li>
                
                <li><hr class="dropdown-divider opacity-50"></li>
                
                <li>
                    <a class="dropdown-item small py-2 text-danger fw-semibold" href="/project1/Account/logout">
                        <i class="fas fa-sign-out-alt me-2"></i>Đăng xuất
                    </a>
                </li>
            </ul>
        </div>
    <?php else: ?>
        <a class="nav-link text-dark fw-bold bg-white bg-opacity-25 rounded-pill px-3 py-2" href="/project1/Account/login">
            <i class="fas fa-user-circle me-1 fs-5 align-middle"></i> Tài khoản
        </a>
    <?php endif; ?>
</li>

                <li class="nav-item d-flex align-items-center">
                    <a class="nav-link text-dark fw-bold bg-white bg-opacity-25 rounded-pill px-3 py-2 position-relative" href="/project1/Product/cart">
                        <i class="fas fa-shopping-cart me-1 fs-5 align-middle"></i> 
                        Giỏ hàng 
                        <?php 
                            $cartCount = SessionHelper::getCartCount(); 
                            if ($cartCount > 0): 
                        ?>
                            <span class="badge badge-cart bg-danger ms-1 rounded-circle"><?= $cartCount ?></span>
                        <?php else: ?>
                            <span class="badge badge-cart bg-secondary bg-opacity-50 ms-1 rounded-circle">0</span>
                        <?php endif; ?>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="sub-nav-bg mb-4 shadow-sm d-none d-lg-block"> 
    <div class="container">
        <div class="d-flex justify-content-between py-2 text-center fw-bold">
            <div class="category-item flex-fill">
                <a href="/project1/Product/index?category_id=1" class="category-link">
                    <i class="fas fa-mobile-alt"></i> Điện thoại
                </a>
            </div>
            <div class="category-item flex-fill">
                <a href="/project1/Product/index?category_id=2" class="category-link">
                    <i class="fas fa-laptop"></i> Laptop
                </a>
            </div>
            <div class="category-item flex-fill">
                <a href="/project1/Product/index?category_id=3" class="category-link">
                    <i class="fas fa-tablet-alt"></i> Tablet
                </a>
            </div>
            <div class="category-item flex-fill">
                <a href="/project1/Product/index?category_id=4" class="category-link">
                    <i class="fas fa-headphones"></i> Phụ kiện
                </a>
            </div>
            <div class="category-item flex-fill">
                <a href="/project1/Product/index?category_id=5" class="category-link">
                    <i class="fas fa-clock"></i> Thiết bị âm thanh
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container pb-5">

<script>
    // 🔍 HÀM KIỂM TRA LỖI KHI CLICK VÀO ĐƠN HÀNG
    function checkOrderClick(event, isLoggedIn) {
        if (!isLoggedIn) {
            event.preventDefault(); // Chặn chuyển trang vô ích
            alert("⚠️ LỖI: Hệ thống ghi nhận bạn CHƯA đăng nhập (Mất Session).\nVui lòng đăng nhập lại!");
            window.location.href = "/project1/Account/login";
        } else {
            // Nếu đã đăng nhập, in thông tin link ra màn hình console F12 trước khi nhảy trang
            console.log("Trạng thái: Đã đăng nhập. Đang gọi URL: " + event.currentTarget.href);
        }
    }

    // 1. Xử lý tìm kiếm sản phẩm an toàn
    document.getElementById('searchForm').addEventListener('submit', function(event) {
        event.preventDefault(); 
        let keyword = document.getElementById('searchInput').value.trim();
        
        if (keyword !== "") {
            window.location.href = "/project1/Product/index?search=" + encodeURIComponent(keyword);
        } else {
            alert("Vui lòng nhập từ khóa cần tìm!");
        }
    });

    // 🔥 2. ÉP BUỘC DROPDOWN ĐĂNG XUẤT PHẢI HIỂN THỊ KHI CLICK (FIX KẸT DROPDOWN)
    document.addEventListener("DOMContentLoaded", function() {
        const dropdownBtn = document.getElementById("userDropdown");
        const dropdownMenu = document.querySelector(".dropdown-menu"); 
        
        if (dropdownBtn && dropdownMenu) {
            dropdownBtn.addEventListener("click", function(e) {
                e.preventDefault();
                e.stopPropagation(); 
                
                dropdownMenu.classList.toggle("show");
            });
            
            document.addEventListener("click", function() {
                dropdownMenu.classList.remove("show");
            });
        }
    });
</script>