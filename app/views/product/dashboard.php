<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📊 QUẢN TRỊ HỆ THỐNG - THẾ GIỚI ĐIỆN THOẠI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .tgdd-bg { background-color: #ffd400; }
        .admin-sidebar-btn { transition: all 0.2s; border-radius: 8px; font-weight: 600; }
        .admin-sidebar-btn:hover, .admin-sidebar-btn.active { background-color: #ffd400 !important; color: #000 !important; }
        .stat-card { transition: transform 0.2s, box-shadow 0.2s; border: none; border-radius: 12px; }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .table-container { border-radius: 12px; overflow: hidden; border: 1px solid #dee2e6; }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg tgdd-bg py-3 shadow-sm mb-4">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold text-dark fs-4" href="/project1/Product/index">📱 THẾ GIỚI ĐIỆN THOẠI <span class="badge bg-dark fs-6 ms-2 text-warning">ADMIN</span></a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="adminNavbar">
            <ul class="navbar-nav ms-auto gap-3 align-items-lg-center">
                <li class="nav-item">
                    <span class="fw-bold bg-white bg-opacity-50 rounded-pill px-3 py-2 text-dark">
                        <i class="fas fa-user-shield me-1"></i> Xin chào, <?= htmlspecialchars(SessionHelper::getUserData('fullname') ?? 'Admin') ?>
                    </span>
                </li>
                <li class="nav-item">
                    <a class="btn btn-dark rounded-pill px-3 fw-semibold text-white" href="/project1/Product/index">
                        <i class="fas fa-home me-1"></i> Xem Trang Chủ
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container-fluid px-4">
    <div class="row g-4">
        
        <div class="col-lg-3 col-md-4">
            <div class="card border-0 shadow-sm p-3" style="border-radius: 12px;">
                <h6 class="text-muted text-uppercase fw-bold small px-2 mb-3">Danh mục quản lý</h6>
                <div class="d-flex flex-column gap-2">
                    <a href="/project1/Product/dashboard" class="nav-link admin-sidebar-btn active p-3 text-dark bg-light">
                        <i class="fas fa-tachometer-alt me-2 text-warning"></i>Tổng quan Dashboard
                    </a>
                    <a href="/project1/Product/categories" class="nav-link admin-sidebar-btn p-3 text-secondary">
                        <i class="fas fa-tags me-2"></i>Quản lý danh mục
                    </a>
                    <a href="/project1/Product/orders" class="nav-link admin-sidebar-btn p-3 text-secondary">
                        <i class="fas fa-shopping-basket me-2"></i>Quản lý đơn hàng
                    </a>
                    <a href="/project1/Product/users" class="nav-link admin-sidebar-btn p-3 text-secondary">
                        <i class="fas fa-users me-2"></i>Quản lý thành viên
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-9 col-md-8">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold m-0 text-dark text-uppercase fs-4">
                    <i class="fas fa-tachometer-alt text-warning me-2"></i>Bảng điều khiển tổng quan
                </h3>
                <span class="badge bg-white text-secondary border px-3 py-2 fw-semibold shadow-sm" id="liveClock"></span>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="card stat-card bg-white p-3 shadow-sm border-start border-primary border-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted small fw-bold text-uppercase mb-1">Sản phẩm</h6>
                                <h3 class="fw-bold m-0 text-dark"><?= count($products ?? []) ?></h3>
                            </div>
                            <div class="text-primary bg-primary bg-opacity-10 p-2 rounded-circle" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-box fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="card stat-card bg-white p-3 shadow-sm border-start border-success border-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted small fw-bold text-uppercase mb-1">Danh mục</h6>
                                <h3 class="fw-bold m-0 text-dark"><?= $totalCategories ?? 0 ?></h3>
                            </div>
                            <div class="text-success bg-success bg-opacity-10 p-2 rounded-circle" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-tags fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="card stat-card bg-white p-3 shadow-sm border-start border-danger border-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted small fw-bold text-uppercase mb-1">Đơn hàng</h6>
                                <h3 class="fw-bold m-0 text-dark"><?= number_format($totalOrders ?? 0, 0, ',', '.') ?></h3>
                            </div>
                            <div class="text-danger bg-danger bg-opacity-10 p-2 rounded-circle" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-shopping-basket fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="card stat-card bg-white p-3 shadow-sm border-start border-info border-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted small fw-bold text-uppercase mb-1">Thành viên</h6>
                                <h3 class="fw-bold m-0 text-dark"><?= number_format($totalUsers ?? 0, 0, ',', '.') ?></h3>
                            </div>
                            <div class="text-info bg-info bg-opacity-10 p-2 rounded-circle" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-users fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm p-4" style="border-radius: 12px;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold m-0 text-dark">
                        <i class="fas fa-list text-warning me-2"></i>Sản phẩm đang kinh doanh
                    </h5>
                    <a href="/project1/Product/add" class="btn btn-sm btn-primary fw-bold px-3 py-2 rounded-pill shadow-sm">
                        <i class="fas fa-plus-circle me-1"></i> Thêm sản phẩm mới
                    </a>
                </div>
                
                <div class="table-responsive table-container bg-white">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr class="small text-uppercase">
                                <th class="text-center py-3" style="width: 80px;">Hình ảnh</th>
                                <th class="py-3">Tên sản phẩm</th>
                                <th class="py-3" style="width: 150px;">Danh mục</th>
                                <th class="py-3" style="width: 150px;">Giá bán</th>
                                <th class="text-center py-3" style="width: 160px;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($products)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Chưa có dữ liệu sản phẩm nào hiển thị.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($products as $product): ?>
                                <tr>
                                    <td class="text-center">
                                        <?php 
                                            $imgName = !empty($product->image) ? $product->image : 'default.jpg';
                                            $imagePath = (strpos($imgName, 'public/images/') !== false || strpos($imgName, 'uploads/') !== false) 
                                                ? "/project1/" . $imgName 
                                                : "/project1/public/images/" . $imgName; 
                                        ?>
                                        <img src="<?= htmlspecialchars($imagePath, ENT_QUOTES, 'UTF-8'); ?>" class="rounded" style="width: 50px; height: 50px; object-fit: contain; background: #f8f9fa; border: 1px solid #eee; padding: 2px;">
                                    </td>
                                    <td>
                                        <span class="fw-bold text-dark d-block text-truncate" style="max-width: 250px;"><?= htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8') ?></span>
                                        <small class="text-muted">ID: #<?= $product->id ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border small fw-semibold">
                                            <?= htmlspecialchars($product->category_name ?? 'Điện thoại', ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <strong class="text-danger"><?= number_format($product->price, 0, ',', '.') ?>₫</strong>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex gap-2 justify-content-center">
                                            <a href="/project1/Product/edit?id=<?= $product->id ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                                <i class="fas fa-edit me-1"></i>Sửa
                                            </a>
                                            <a href="/project1/Product/delete?id=<?= $product->id ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="return confirm('Bạn chắc chắn muốn xóa sản phẩm này?');">
                                                <i class="fas fa-trash me-1"></i>Xóa
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div> 
    </div> 
</div> 

<script>
    function updateClock() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('vi-VN', { 
            hour: '2-digit', 
            minute: '2-digit', 
            second: '2-digit',
            hour12: false 
        });
        const dateString = now.toLocaleDateString('vi-VN', { 
            day: '2-digit', 
            month: '2-digit', 
            year: 'numeric' 
        });
        const clockEl = document.getElementById('liveClock');
        if (clockEl) {
            clockEl.innerHTML = `<i class="far fa-clock me-1 text-warning"></i> ${timeString} — ${dateString}`;
        }
    }
    setInterval(updateClock, 1000);
    updateClock();
</script>
</body>
</html>