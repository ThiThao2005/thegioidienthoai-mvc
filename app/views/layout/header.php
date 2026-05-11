<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cửa hàng Điện Thoại</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        .tgdd-bg { background-color: #ffd400; } /* Màu vàng đặc trưng */
        .card-product { transition: transform 0.2s; border: none; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .card-product:hover { transform: translateY(-5px); box-shadow: 0 4px 15px rgba(0,0,0,0.2); }
        .price-text { color: #d70018; font-weight: bold; font-size: 1.2rem; }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg tgdd-bg mb-4 shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold text-dark" href="/project1/Product/list">📱 THẾ GIỚI ĐIỆN THOẠI</a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <form class="d-flex mx-auto w-50" onsubmit="return false;">
                <input id="searchInput" class="form-control me-2 rounded-pill border-0" type="search" placeholder="Bạn tìm gì...">
                <button class="btn btn-dark rounded-pill" type="button"><i class="fas fa-search"></i></button>
            </form>
            
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link text-dark fw-bold" href="#"><i class="fas fa-shopping-cart"></i> Giỏ hàng</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">