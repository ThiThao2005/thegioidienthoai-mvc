<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin · Thế Giới Điện Thoại</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/public/css/theme.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --yellow: var(--brand);
            --yellow-dark: var(--brand-deep);
            --yellow-light: var(--brand-soft);
            --sidebar-w: 260px;
            --topbar-h: 64px;
            --radius: 12px;
            --radius-sm: 8px;
            --shadow: 0 1px 3px rgba(31,58,61,0.06), 0 4px 16px rgba(31,58,61,0.05);
            --border: 1px solid var(--line);
            --text: var(--ink);
            --white: #FFFFFF;
            --sidebar-bg: var(--ink);
        }

        body {
            font-family: 'Be Vietnam Pro', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }

        /* ── SIDEBAR ── */
        .sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-w);
            height: 100vh;
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            z-index: 100;
            overflow: hidden;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 20px 24px;
            border-bottom: 1px solid rgba(255,255,255,0.07);
        }

        .brand-icon {
            width: 36px; height: 36px;
            background: var(--yellow);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; line-height: 1;
        }

        .brand-text {
            display: flex; flex-direction: column; gap: 1px;
        }

        .brand-name {
            font-size: 13px; font-weight: 700;
            color: #fff; letter-spacing: 0.3px;
            text-transform: uppercase;
        }

        .brand-sub {
            font-size: 10px; font-weight: 500;
            color: var(--yellow);
            text-transform: uppercase; letter-spacing: 1px;
        }

        .sidebar-nav {
            flex: 1;
            padding: 16px 12px;
            display: flex; flex-direction: column; gap: 2px;
        }

        .nav-label {
            font-size: 10px; font-weight: 600;
            color: rgba(255,255,255,0.3);
            text-transform: uppercase; letter-spacing: 1.2px;
            padding: 8px 12px 6px;
            margin-top: 4px;
        }

        .nav-link {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 14px;
            border-radius: var(--radius-sm);
            color: rgba(255,255,255,0.55);
            font-size: 13.5px; font-weight: 500;
            text-decoration: none;
            transition: all 0.15s;
        }

        .nav-link i { font-size: 18px; flex-shrink: 0; }

        .nav-link:hover {
            background: rgba(255,255,255,0.07);
            color: rgba(255,255,255,0.9);
        }

        .nav-link.active {
            background: var(--yellow);
            color: var(--ink) !important;
            font-weight: 600;
        }

        .sidebar-footer {
            padding: 16px 12px;
            border-top: 1px solid rgba(255,255,255,0.07);
        }

        .user-row {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 14px;
            border-radius: var(--radius-sm);
        }

        .user-avatar {
            width: 32px; height: 32px;
            background: var(--yellow);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 700; color: var(--ink); flex-shrink: 0;
        }

        .user-name {
            font-size: 13px; font-weight: 600; color: rgba(255,255,255,0.85);
        }

        .user-role {
            font-size: 11px; color: rgba(255,255,255,0.35);
        }

        /* ── TOPBAR ── */
        .topbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-w);
            right: 0;
            height: var(--topbar-h);
            background: var(--white);
            border-bottom: var(--border);
            display: flex; align-items: center;
            padding: 0 28px;
            gap: 16px;
            z-index: 90;
        }

        .topbar-title {
            flex: 1;
            font-size: 15px; font-weight: 700;
            color: var(--text);
        }

        .topbar-title span {
            color: var(--muted);
            font-weight: 400;
        }

        .clock-badge {
            background: var(--yellow-light);
            border: 1px solid var(--yellow-dark);
            border-radius: 20px;
            padding: 6px 14px;
            font-size: 12.5px; font-weight: 600;
            color: var(--brand-strong-dark);
            display: flex; align-items: center; gap: 6px;
        }

        .btn-home {
            display: flex; align-items: center; gap: 6px;
            background: var(--yellow);
            border: none; border-radius: 20px;
            padding: 7px 16px;
            font-size: 13px; font-weight: 600;
            color: var(--ink); cursor: pointer;
            text-decoration: none;
            transition: background 0.15s;
        }

        .btn-home:hover { background: var(--yellow-dark); color: var(--ink); }

        /* ── MAIN ── */
        .main {
            margin-left: var(--sidebar-w);
            padding-top: var(--topbar-h);
            min-height: 100vh;
        }

        .main-content {
            padding: 28px;
        }

        /* ── SECTION HEADER ── */
        .section-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 13px; font-weight: 700;
            color: var(--text);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex; align-items: center; gap: 8px;
        }

        .section-title::before {
            content: '';
            display: inline-block;
            width: 3px; height: 16px;
            background: var(--yellow);
            border-radius: 2px;
        }

        /* ── STAT CARDS ── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin-bottom: 14px;
        }

        .stats-grid-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: var(--white);
            border: var(--border);
            border-radius: var(--radius);
            padding: 18px 20px;
            display: flex; align-items: center; gap: 16px;
            box-shadow: var(--shadow);
            transition: transform 0.15s, box-shadow 0.15s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(31,58,61,0.09);
        }

        .stat-icon {
            width: 44px; height: 44px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; flex-shrink: 0;
        }

        .stat-icon.blue  { background: #EBF3FF; color: #2B6CB0; }
        .stat-icon.green { background: #E8F7EE; color: #1E7E45; }
        .stat-icon.red   { background: var(--accent-soft); color: var(--danger); }
        .stat-icon.cyan  { background: #E8F8F8; color: #0E7490; }
        .stat-icon.amber { background: #FFF8E8; color: #92650A; }
        .stat-icon.dark  { background: var(--surface-2); color: var(--ink-soft); }
        .stat-icon.orange{ background: #FFF3EC; color: #C05621; }

        .stat-label {
            font-size: 11px; font-weight: 600;
            color: var(--muted);
            text-transform: uppercase; letter-spacing: 0.6px;
            margin-bottom: 4px;
        }

        .stat-value {
            font-size: 22px; font-weight: 700;
            color: var(--text);
            line-height: 1;
        }

        .stat-value.danger { color: var(--danger); }

        /* ── CONTENT CARDS ── */
        .content-card {
            background: var(--white);
            border: var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            margin-bottom: 20px;
        }

        .card-header-bar {
            display: flex; align-items: center; justify-content: space-between;
            padding: 18px 22px;
            border-bottom: var(--border);
        }

        .card-header-bar .title {
            font-size: 13.5px; font-weight: 700; color: var(--text);
        }

        /* ── TOP PRODUCTS ── */
        .top-products {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            padding: 18px 22px;
        }

        .product-rank-item {
            display: flex; align-items: center; justify-content: space-between;
            padding: 10px 14px;
            background: var(--bg);
            border-radius: var(--radius-sm);
            border: var(--border);
        }

        .product-rank-name {
            font-size: 13px; font-weight: 600; color: var(--text);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
            max-width: 160px;
        }

        .sold-badge {
            background: var(--ink);
            color: #fff;
            font-size: 11px; font-weight: 600;
            padding: 3px 10px;
            border-radius: 20px;
            white-space: nowrap; flex-shrink: 0; margin-left: 8px;
        }

        /* ── PRODUCT TABLE ── */
        .product-table {
            width: 100%;
            border-collapse: collapse;
        }

        .product-table thead tr {
            background: #F7F7F5;
            border-bottom: var(--border);
        }

        .product-table th {
            padding: 12px 16px;
            font-size: 11px; font-weight: 700;
            color: var(--muted);
            text-transform: uppercase; letter-spacing: 0.6px;
            text-align: left;
        }

        .product-table tbody tr {
            border-bottom: var(--border);
            transition: background 0.1s;
        }

        .product-table tbody tr:last-child { border-bottom: none; }
        .product-table tbody tr:hover { background: #FAFAF9; }

        .product-table td {
            padding: 14px 16px;
            font-size: 13.5px; color: var(--text);
            vertical-align: middle;
        }

        .product-img {
            width: 44px; height: 44px;
            border-radius: 8px;
            object-fit: contain;
            background: var(--bg);
            border: var(--border);
            padding: 3px;
        }

        .product-name {
            font-weight: 600; color: var(--text);
            display: block;
            max-width: 240px;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }

        .product-id {
            font-size: 11.5px; color: var(--muted); margin-top: 2px;
        }

        .category-pill {
            background: var(--bg);
            border: var(--border);
            border-radius: 20px;
            padding: 3px 10px;
            font-size: 12px; font-weight: 500;
            color: var(--text);
        }

        .price-cell {
            font-weight: 700; color: var(--danger);
            font-size: 14px;
        }

        .action-btns { display: flex; gap: 8px; }

        .btn-edit, .btn-delete {
            display: flex; align-items: center; gap: 5px;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px; font-weight: 600;
            text-decoration: none;
            border: none; cursor: pointer;
            transition: all 0.15s;
        }

        .btn-edit {
            background: var(--surface-2); color: var(--ink-soft);
        }
        .btn-edit:hover { background: #E0E0E0; color: var(--ink); }

        .btn-delete {
            background: var(--accent-soft); color: var(--danger);
        }
        .btn-delete:hover { background: #FCDEDE; color: #9B2C2C; }

        .btn-add-product {
            display: flex; align-items: center; gap: 6px;
            background: var(--yellow);
            border: none; border-radius: 8px;
            padding: 8px 16px;
            font-size: 13px; font-weight: 700;
            color: var(--ink); cursor: pointer;
            text-decoration: none;
            transition: background 0.15s;
        }
        .btn-add-product:hover { background: var(--yellow-dark); color: var(--ink); }

        /* ── EMPTY STATE ── */
        .empty-state {
            text-align: center;
            padding: 48px 24px;
            color: var(--muted);
            font-size: 14px;
        }
    </style>
</head>
<body>

<!-- ══ SIDEBAR ══ -->
<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon">📱</div>
        <div class="brand-text">
            <div class="brand-name">Thế Giới ĐT</div>
            <div class="brand-sub">Admin Panel</div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-label">Tổng quan</div>
        <a href="/Product/dashboard" class="nav-link active">
            <i class="ti ti-layout-dashboard"></i> Dashboard
        </a>

        <div class="nav-label">Quản lý</div>
        <a href="/Product/categories" class="nav-link">
            <i class="ti ti-tag"></i> Danh mục
        </a>
        <a href="/Product/orders" class="nav-link">
            <i class="ti ti-shopping-bag"></i> Đơn hàng
        </a>
        <a href="/Account/users" class="nav-link">
            <i class="ti ti-users"></i> Thành viên
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="user-row">
            <div class="user-avatar">AD</div>
            <div>
                <div class="user-name"><?= htmlspecialchars(SessionHelper::getUserData('fullname') ?? 'Admin') ?></div>
                <div class="user-role">Quản trị viên</div>
            </div>
        </div>
    </div>
</aside>

<!-- ══ TOPBAR ══ -->
<header class="topbar">
    <div class="topbar-title">
        Dashboard <span>/ Tổng quan hệ thống</span>
    </div>
    <div class="clock-badge">
        <i class="ti ti-clock" style="font-size:14px;"></i>
        <span id="liveClock">--:--:--</span>
    </div>
    <a href="/Product/index" class="btn-home">
        <i class="ti ti-home" style="font-size:15px;"></i> Trang chủ
    </a>
</header>

<!-- ══ MAIN CONTENT ══ -->
<main class="main">
    <div class="main-content">

        <!-- Stat Cards Row 1 -->
        <div class="section-header">
            <div class="section-title">Chỉ số chính</div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon blue">
                    <i class="ti ti-box"></i>
                </div>
                <div>
                    <div class="stat-label">Sản phẩm</div>
                    <div class="stat-value"><?= count($products ?? []) ?></div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="ti ti-tag"></i>
                </div>
                <div>
                    <div class="stat-label">Danh mục</div>
                    <div class="stat-value"><?= $totalCategories ?? 0 ?></div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon red">
                    <i class="ti ti-shopping-bag"></i>
                </div>
                <div>
                    <div class="stat-label">Đơn hàng</div>
                    <div class="stat-value"><?= number_format($totalOrders ?? 0, 0, ',', '.') ?></div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon cyan">
                    <i class="ti ti-users"></i>
                </div>
                <div>
                    <div class="stat-label">Thành viên</div>
                    <div class="stat-value"><?= number_format($totalUsers ?? 0, 0, ',', '.') ?></div>
                </div>
            </div>
        </div>

        <!-- Stat Cards Row 2 -->
        <div class="stats-grid-3">
            <div class="stat-card">
                <div class="stat-icon amber">
                    <i class="ti ti-currency-dollar"></i>
                </div>
                <div>
                    <div class="stat-label">Doanh thu tổng</div>
                    <div class="stat-value danger"><?= number_format($dashboardStats['revenue'] ?? 0, 0, ',', '.') ?>đ</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon dark">
                    <i class="ti ti-calendar-stats"></i>
                </div>
                <div>
                    <div class="stat-label">Doanh thu hôm nay</div>
                    <div class="stat-value"><?= number_format($dashboardStats['todayRevenue'] ?? 0, 0, ',', '.') ?>đ</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon orange">
                    <i class="ti ti-alert-triangle"></i>
                </div>
                <div>
                    <div class="stat-label">Biến thể sắp hết hàng</div>
                    <div class="stat-value danger"><?= number_format($dashboardStats['lowStock'] ?? 0, 0, ',', '.') ?></div>
                </div>
            </div>
        </div>

        <!-- Top Products -->
        <div class="content-card">
            <div class="card-header-bar">
                <div class="title">
                    <i class="ti ti-trophy" style="color:var(--brand-deep); margin-right:6px; font-size:16px; vertical-align:-2px;"></i>
                    Top sản phẩm bán chạy
                </div>
            </div>
            <div class="top-products">
                <?php foreach (($dashboardStats['topProducts'] ?? []) as $top): ?>
                    <div class="product-rank-item">
                        <span class="product-rank-name"><?= htmlspecialchars($top->name, ENT_QUOTES, 'UTF-8') ?></span>
                        <span class="sold-badge"><?= (int)$top->sold_count ?> đã bán</span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Product Table -->
        <div class="content-card">
            <div class="card-header-bar">
                <div class="title">
                    <i class="ti ti-list" style="color:var(--brand-deep); margin-right:6px; font-size:16px; vertical-align:-2px;"></i>
                    Sản phẩm đang kinh doanh
                </div>
                <a href="/Product/add" class="btn-add-product">
                    <i class="ti ti-plus" style="font-size:15px;"></i> Thêm sản phẩm
                </a>
            </div>

            <div style="overflow-x: auto;">
                <table class="product-table">
                    <thead>
                        <tr>
                            <th style="width:60px;">Ảnh</th>
                            <th>Sản phẩm</th>
                            <th style="width:140px;">Danh mục</th>
                            <th style="width:140px;">Giá bán</th>
                            <th style="width:160px; text-align:center;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($products)): ?>
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <i class="ti ti-inbox" style="font-size:32px; display:block; margin-bottom:8px;"></i>
                                        Chưa có sản phẩm nào
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($products as $product): ?>
                            <?php
                                $imgName = !empty($product->image) ? $product->image : 'default.jpg';
                                $imagePath = (strpos($imgName, 'public/images/') !== false || strpos($imgName, 'uploads/') !== false)
                                    ? "/" . $imgName
                                    : "/public/images/" . $imgName;
                            ?>
                            <tr>
                                <td>
                                    <img src="<?= htmlspecialchars($imagePath, ENT_QUOTES, 'UTF-8') ?>"
                                         class="product-img"
                                         alt="<?= htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8') ?>">
                                </td>
                                <td>
                                    <span class="product-name"><?= htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8') ?></span>
                                    <div class="product-id">ID #<?= $product->id ?></div>
                                </td>
                                <td>
                                    <span class="category-pill"><?= htmlspecialchars($product->category_name ?? 'Điện thoại', ENT_QUOTES, 'UTF-8') ?></span>
                                </td>
                                <td class="price-cell">
                                    <?= number_format($product->price, 0, ',', '.') ?>₫
                                </td>
                                <td style="text-align:center;">
                                    <div class="action-btns" style="justify-content:center;">
                                        <a href="/Product/edit?id=<?= $product->id ?>" class="btn-edit">
                                            <i class="ti ti-edit" style="font-size:13px;"></i> Sửa
                                        </a>
                                        <a href="/Product/delete?id=<?= $product->id ?>"
                                           class="btn-delete"
                                           onclick="return confirm('Bạn chắc chắn muốn xóa sản phẩm này?');">
                                            <i class="ti ti-trash" style="font-size:13px;"></i> Xóa
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
</main>

<script>
    function updateClock() {
        const now = new Date();
        const el = document.getElementById('liveClock');
        if (!el) return;
        const t = now.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false });
        const d = now.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' });
        el.textContent = `${t} — ${d}`;
    }
    setInterval(updateClock, 1000);
    updateClock();
</script>
</body>
</html>