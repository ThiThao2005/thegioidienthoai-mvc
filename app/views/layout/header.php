<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📱 THẾ GIỚI ĐIỆN THOẠI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
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
            
            <ul class="navbar-nav ms-auto gap-3">
                <li class="nav-item d-flex align-items-center">
                    <a class="nav-link text-dark fw-bold bg-white bg-opacity-25 rounded-pill px-3 py-2" href="/project1/Account/profile">
                        <i class="fas fa-user-circle me-1 fs-5 align-middle"></i> Tài khoản
                    </a>
                </li>
                <li class="nav-item d-flex align-items-center">
                    <a class="nav-link text-dark fw-bold bg-white bg-opacity-25 rounded-pill px-3 py-2" href="/project1/Product/cart">
                        <i class="fas fa-shopping-cart me-1 fs-5 align-middle"></i> Giỏ hàng
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.getElementById('searchForm').addEventListener('submit', function(event) {
        event.preventDefault(); 
        
        let keyword = document.getElementById('searchInput').value.trim();
        
        if (keyword !== "") {
            // Đã sửa: Đồng bộ tìm kiếm về action index
            window.location.href = "/project1/Product/index?search=" + encodeURIComponent(keyword);
        } else {
            alert("Vui lòng nhập từ khóa cần tìm!");
        }
    });
</script>