<?php
/**
 * View: Product/index.php
 * Trang chủ / danh sách sản phẩm nâng cấp
 */
?>

<div class="shop-page">

    <!-- ===== HERO BANNER ===== -->
    <section class="shop-hero mb-4">
        <div class="hero-left">
            <span class="hero-badge">Ưu đãi nổi bật 2026</span>
            <h1>Điện thoại chính hãng<br>Giá tốt mỗi ngày</h1>
            <p>Mua smartphone, laptop, phụ kiện với bảo hành minh bạch và nhiều ưu đãi hấp dẫn.</p>

            <div class="hero-actions">
                <a href="/Product/index?featured=1" class="hero-btn-primary">
                    <i class="fas fa-fire me-1"></i> Xem hàng nổi bật
                </a>
                <a href="/Product/index?search=iPhone" class="hero-btn-secondary">
                    Tìm iPhone
                </a>
            </div>
        </div>

        <div class="hero-right">
            <img src="https://cdn11.dienmaycholon.vn/filewebdmclnew/DMCL21/Picture//Tm/Tm_picture_3547/galaxy-s26-ultr_868_959.png.webp"
                 alt="Sản phẩm nổi bật">
        </div>
    </section>

    <!-- ===== MINI BANNER ===== -->
    <section class="mini-banner-grid mb-4">
        <a href="/Product/index?category_id=1" class="mini-banner">
            <i class="fas fa-mobile-alt"></i>
            <div>
                <strong>Điện thoại hot</strong>
                <span>Giảm đến 30%</span>
            </div>
        </a>

        <a href="/Product/index?category_id=2" class="mini-banner">
            <i class="fas fa-laptop"></i>
            <div>
                <strong>Laptop sinh viên</strong>
                <span>Trả góp 0%</span>
            </div>
        </a>

        <a href="/Product/index?category_id=4" class="mini-banner">
            <i class="fas fa-headphones"></i>
            <div>
                <strong>Phụ kiện chính hãng</strong>
                <span>Mua kèm giá sốc</span>
            </div>
        </a>
    </section>

    <!-- ===== THÔNG BÁO SESSION ===== -->
    <?php if (isset($_SESSION['success_msg'])): ?>
        <div id="successAlert" class="alert alert-success alert-dismissible fade show fw-bold shadow-sm border-0 mb-4 rounded-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <?php echo htmlspecialchars($_SESSION['success_msg'], ENT_QUOTES, 'UTF-8'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success_msg']); ?>
    <?php endif; ?>

    <!-- ===== HEADER TITLE ===== -->
    <div class="section-head mb-3">
        <div>
            <span class="section-kicker">Sản phẩm</span>
            <h4>Danh sách sản phẩm</h4>
        </div>

        <?php if (SessionHelper::isAdmin()): ?>
            <a href="/Product/add" class="btn-add-product">
                <i class="fas fa-plus-circle me-1"></i> Thêm sản phẩm
            </a>
        <?php endif; ?>
    </div>

    <!-- ===== FILTER BOX ===== -->
    <form class="filter-box mb-4" method="GET" action="/Product/index">
        <?php if (!empty($category_id)): ?>
            <input type="hidden" name="category_id" value="<?php echo (int)$category_id; ?>">
        <?php endif; ?>

        <div class="row g-2">
            <div class="col-lg-4 col-md-6">
                <input type="search" name="search"
                       class="form-control filter-control"
                       value="<?php echo htmlspecialchars($search ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       placeholder="Tìm tên sản phẩm, mô tả, danh mục...">
            </div>

            <div class="col-lg-2 col-md-6">
                <select name="brand_id" class="form-select filter-control">
                    <option value="">Tất cả hãng</option>
                    <?php foreach (($brands ?? []) as $brand): ?>
                        <option value="<?php echo $brand->id; ?>"
                            <?php echo (($filters['brand_id'] ?? '') == $brand->id) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($brand->name, ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-lg-2 col-md-6">
                <input type="number" name="min_price" class="form-control filter-control"
                       value="<?php echo htmlspecialchars($filters['min_price'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       placeholder="Giá từ">
            </div>

            <div class="col-lg-2 col-md-6">
                <input type="number" name="max_price" class="form-control filter-control"
                       value="<?php echo htmlspecialchars($filters['max_price'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       placeholder="Giá đến">
            </div>

            <div class="col-lg-2 col-md-6">
                <select name="sort" class="form-select filter-control">
                    <option value="newest" <?php echo (($filters['sort'] ?? '') === 'newest') ? 'selected' : ''; ?>>Mới nhất</option>
                    <option value="price_asc" <?php echo (($filters['sort'] ?? '') === 'price_asc') ? 'selected' : ''; ?>>Giá tăng</option>
                    <option value="price_desc" <?php echo (($filters['sort'] ?? '') === 'price_desc') ? 'selected' : ''; ?>>Giá giảm</option>
                    <option value="rating" <?php echo (($filters['sort'] ?? '') === 'rating') ? 'selected' : ''; ?>>Đánh giá cao</option>
                </select>
            </div>
        </div>

        <div class="filter-bottom mt-3">
            <label class="featured-check">
                <input class="form-check-input me-1" type="checkbox" name="featured" value="1"
                    <?php echo !empty($filters['featured']) ? 'checked' : ''; ?>>
                Chỉ sản phẩm nổi bật
            </label>

            <div class="filter-actions">
                <button class="btn-filter" type="submit">
                    <i class="fas fa-search me-1"></i> Lọc sản phẩm
                </button>

                <?php if (!empty($search) || !empty($category_id) || !empty($filters['brand_id']) || !empty($filters['min_price'])): ?>
                    <a class="btn-clear" href="/Product/index">
                        <i class="fas fa-times me-1"></i> Xóa lọc
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="result-count mt-2">
            Tìm thấy <strong><?php echo (int)($totalProducts ?? count($products)); ?></strong> sản phẩm
            <?php echo !empty($search) ? ' cho "<strong>' . htmlspecialchars($search, ENT_QUOTES, 'UTF-8') . '</strong>"' : ''; ?>
        </div>
    </form>

    <!-- ===== PRODUCT GRID ===== -->
    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-3">
        <?php if (empty($products)): ?>
            <div class="col-12">
                <div class="empty-box">
                    <i class="fas fa-box-open"></i>
                    <h5>Chưa có sản phẩm nào</h5>
                    <p>Vui lòng thử lại với bộ lọc khác.</p>

                    <?php if (SessionHelper::isAdmin()): ?>
                        <a href="/Product/add" class="btn-add-product mt-2">Thêm sản phẩm ngay</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
                <?php
                    $imgName = !empty($product->image) ? $product->image : 'default.jpg';
                    $imagePath = (strpos($imgName, 'public/images/') !== false || strpos($imgName, 'uploads/') !== false)
                        ? '/' . $imgName
                        : '/public/images/' . $imgName;

                    $salePercent = (int)($product->sale_percent ?? 0);
                    $originalPrice = (float)$product->price;
                    $salePrice = $salePercent > 0 ? $originalPrice * (1 - $salePercent / 100) : $originalPrice;

                    $rating = (float)($product->avg_rating ?? 0);
                    $reviewCount = (int)($product->review_count ?? 0);
                ?>

                <div class="col">
                    <div class="product-card">

                        <?php if ($salePercent > 0): ?>
                            <span class="product-badge sale">-<?php echo $salePercent; ?>%</span>
                        <?php elseif (!empty($product->featured)): ?>
                            <span class="product-badge hot">Hot</span>
                        <?php endif; ?>

                        <a href="/Product/detail?id=<?php echo $product->id; ?>" class="product-img-wrap">
                            <img src="<?php echo htmlspecialchars($imagePath, ENT_QUOTES, 'UTF-8'); ?>"
                                 alt="<?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>"
                                 loading="lazy">
                        </a>

                        <div class="product-info">
                            <div class="product-tags">
                                <span><?php echo htmlspecialchars($product->category_name ?? 'Sản phẩm', ENT_QUOTES, 'UTF-8'); ?></span>
                                <?php if (!empty($product->brand_name)): ?>
                                    <span><?php echo htmlspecialchars($product->brand_name, ENT_QUOTES, 'UTF-8'); ?></span>
                                <?php endif; ?>
                            </div>

                            <h6 class="product-name">
                                <a href="/Product/detail?id=<?php echo $product->id; ?>">
                                    <?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>
                                </a>
                            </h6>

                            <p class="product-desc">
                                <?php echo htmlspecialchars($product->description ?? 'Sản phẩm chính hãng, bảo hành đầy đủ.', ENT_QUOTES, 'UTF-8'); ?>
                            </p>

                            <div class="product-price">
                                <strong><?php echo number_format($salePrice, 0, ',', '.'); ?>₫</strong>

                                <?php if ($salePercent > 0): ?>
                                    <span><?php echo number_format($originalPrice, 0, ',', '.'); ?>₫</span>
                                <?php endif; ?>
                            </div>

                            <div class="product-meta">
                                <span class="rating">
                                    <i class="fas fa-star"></i>
                                    <?php echo number_format($rating, 1); ?>
                                </span>
                                <span><?php echo $reviewCount; ?> đánh giá</span>
                            </div>
                        </div>

                        <div class="product-actions">
                            <a href="/Product/addToCart?id=<?php echo $product->id; ?>" class="btn-cart">
                                <i class="fas fa-cart-plus me-1"></i> Thêm giỏ
                            </a>

                            <a href="/Product/detail?id=<?php echo $product->id; ?>" class="btn-detail">
                                Chi tiết
                            </a>
                        </div>

                        <?php if (SessionHelper::isAdmin()): ?>
                            <div class="admin-actions">
                                <a href="/Product/edit?id=<?php echo $product->id; ?>" class="btn-admin-edit">
                                    <i class="fas fa-edit"></i> Sửa
                                </a>

                                <a href="/Product/delete?id=<?php echo $product->id; ?>"
                                   class="btn-admin-delete"
                                   onclick="return confirmDelete(<?php echo $product->id; ?>, '<?php echo htmlspecialchars(addslashes($product->name), ENT_QUOTES, 'UTF-8'); ?>')">
                                    <i class="fas fa-trash"></i> Xóa
                                </a>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- ===== PAGINATION ===== -->
    <?php if (($totalPages ?? 1) > 1): ?>
        <nav class="mt-4">
            <ul class="pagination justify-content-center flex-wrap gap-1">
                <?php
                    $queryBase = [];

                    if (!empty($search)) $queryBase['search'] = $search;
                    if (!empty($category_id)) $queryBase['category_id'] = $category_id;
                    if (!empty($filters['brand_id'])) $queryBase['brand_id'] = $filters['brand_id'];
                    if (!empty($filters['min_price'])) $queryBase['min_price'] = $filters['min_price'];
                    if (!empty($filters['max_price'])) $queryBase['max_price'] = $filters['max_price'];
                    if (!empty($filters['sort'])) $queryBase['sort'] = $filters['sort'];
                    if (!empty($filters['featured'])) $queryBase['featured'] = 1;

                    $currentPage = (int)($page ?? 1);
                    $totalPagesInt = (int)$totalPages;

                    if ($currentPage > 1):
                        $queryBase['page'] = $currentPage - 1;
                ?>
                    <li class="page-item">
                        <a class="page-link rounded-pill" href="/Product/index?<?php echo http_build_query($queryBase); ?>">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                <?php endif; ?>

                <?php
                    $range = 3;
                    $start = max(1, $currentPage - $range);
                    $end = min($totalPagesInt, $currentPage + $range);

                    if ($start > 1):
                        $queryBase['page'] = 1;
                ?>
                    <li class="page-item">
                        <a class="page-link rounded-pill" href="/Product/index?<?php echo http_build_query($queryBase); ?>">1</a>
                    </li>
                    <?php if ($start > 2): ?>
                        <li class="page-item disabled"><span class="page-link">…</span></li>
                    <?php endif; ?>
                <?php endif; ?>

                <?php for ($i = $start; $i <= $end; $i++):
                    $queryBase['page'] = $i;
                ?>
                    <li class="page-item <?php echo $i === $currentPage ? 'active' : ''; ?>">
                        <a class="page-link rounded-pill" href="/Product/index?<?php echo http_build_query($queryBase); ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>

                <?php if ($end < $totalPagesInt):
                    if ($end < $totalPagesInt - 1): ?>
                        <li class="page-item disabled"><span class="page-link">…</span></li>
                    <?php endif;

                    $queryBase['page'] = $totalPagesInt;
                ?>
                    <li class="page-item">
                        <a class="page-link rounded-pill" href="/Product/index?<?php echo http_build_query($queryBase); ?>">
                            <?php echo $totalPagesInt; ?>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($currentPage < $totalPagesInt):
                    $queryBase['page'] = $currentPage + 1;
                ?>
                    <li class="page-item">
                        <a class="page-link rounded-pill" href="/Product/index?<?php echo http_build_query($queryBase); ?>">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>

        <p class="text-center text-muted small mt-1">
            Trang <?php echo $currentPage; ?> / <?php echo $totalPagesInt; ?>
            · <?php echo (int)($totalProducts ?? count($products)); ?> sản phẩm
        </p>
    <?php endif; ?>

</div>

<style>
.shop-page {
    background: transparent;
}

/* ===== HERO ===== */
.shop-hero {
    min-height: 360px;
    border-radius: 30px;
    padding: 46px 56px;
    background:
        radial-gradient(circle at 75% 35%, rgba(255,212,0,.95) 0, rgba(255,212,0,.25) 32%, transparent 56%),
        linear-gradient(135deg, #101010 0%, #1f1f1f 56%, #ffd400 100%);
    display: flex;
    align-items: center;
    justify-content: space-between;
    overflow: hidden;
    box-shadow: 0 18px 42px rgba(0,0,0,.16);
}

.hero-left {
    max-width: 540px;
    color: #fff;
    position: relative;
    z-index: 2;
}

.hero-badge {
    display: inline-flex;
    background: #ffd400;
    color: #111;
    border-radius: 999px;
    padding: 7px 15px;
    font-size: .78rem;
    font-weight: 900;
    margin-bottom: 14px;
}

.hero-left h1 {
    font-size: 3rem;
    line-height: 1.05;
    font-weight: 900;
    letter-spacing: -1.3px;
    margin-bottom: 14px;
}

.hero-left p {
    color: rgba(255,255,255,.78);
    font-size: 1.03rem;
    margin-bottom: 24px;
}

.hero-actions {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.hero-btn-primary,
.hero-btn-secondary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 999px;
    padding: 12px 22px;
    font-weight: 900;
    text-decoration: none;
    transition: .2s;
}

.hero-btn-primary {
    background: #fff;
    color: #111;
}

.hero-btn-secondary {
    background: rgba(255,255,255,.12);
    color: #fff;
    border: 1px solid rgba(255,255,255,.28);
}

.hero-btn-primary:hover,
.hero-btn-secondary:hover {
    transform: translateY(-2px);
}

.hero-btn-primary:hover {
    background: #ffd400;
    color: #111;
}

.hero-btn-secondary:hover {
    background: #fff;
    color: #111;
}

.hero-right img {
    max-height: 300px;
    max-width: 430px;
    object-fit: contain;
    filter: drop-shadow(0 22px 28px rgba(0,0,0,.36));
    transform: rotate(-4deg);
}

/* ===== MINI BANNER ===== */
.mini-banner-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
}

.mini-banner {
    background: #fff;
    border-radius: 22px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 14px;
    color: #111;
    text-decoration: none;
    box-shadow: 0 8px 24px rgba(0,0,0,.07);
    border: 1px solid rgba(0,0,0,.04);
    transition: .2s;
}

.mini-banner:hover {
    color: #111;
    transform: translateY(-3px);
    box-shadow: 0 14px 32px rgba(0,0,0,.12);
}

.mini-banner i {
    width: 46px;
    height: 46px;
    border-radius: 16px;
    background: #fff3b0;
    color: #111;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
}

.mini-banner strong {
    display: block;
    font-size: .98rem;
    font-weight: 900;
}

.mini-banner span {
    display: block;
    color: #d70018;
    font-size: .82rem;
    font-weight: 800;
}

/* ===== SECTION HEAD ===== */
.section-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
}

.section-kicker {
    color: #d70018;
    font-size: .76rem;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: .08em;
}

.section-head h4 {
    font-size: 1.45rem;
    font-weight: 900;
    margin: 2px 0 0;
}

.btn-add-product {
    background: #111;
    color: #ffd400;
    border-radius: 999px;
    padding: 11px 18px;
    font-weight: 900;
    text-decoration: none;
    transition: .2s;
}

.btn-add-product:hover {
    background: #ffd400;
    color: #111;
    transform: translateY(-2px);
}

/* ===== FILTER ===== */
.filter-box {
    background: #fff;
    border-radius: 24px;
    padding: 18px;
    box-shadow: 0 8px 24px rgba(0,0,0,.07);
    border: 1px solid rgba(0,0,0,.04);
}

.filter-control {
    border-radius: 999px;
    border: 1px solid #e6e6e6;
    padding: 10px 16px;
    font-size: .9rem;
}

.filter-control:focus {
    border-color: #ffd400;
    box-shadow: 0 0 0 .2rem rgba(255,212,0,.2);
}

.filter-bottom {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    flex-wrap: wrap;
}

.featured-check {
    font-size: .88rem;
    font-weight: 800;
    color: #333;
}

.filter-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.btn-filter,
.btn-clear {
    border: none;
    border-radius: 999px;
    padding: 10px 18px;
    font-weight: 900;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
}

.btn-filter {
    background: #ffd400;
    color: #111;
}

.btn-clear {
    background: #f1f1f1;
    color: #333;
}

.btn-filter:hover,
.btn-clear:hover {
    transform: translateY(-1px);
}

.result-count {
    color: #777;
    font-size: .86rem;
}

/* ===== PRODUCT CARD ===== */
.product-card {
    height: 100%;
    background: #fff;
    border-radius: 22px;
    padding: 12px;
    position: relative;
    display: flex;
    flex-direction: column;
    border: 1px solid rgba(0,0,0,.055);
    box-shadow: 0 5px 18px rgba(0,0,0,.07);
    transition: .22s;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 16px 34px rgba(0,0,0,.14);
}

.product-badge {
    position: absolute;
    top: 12px;
    left: 12px;
    z-index: 2;
    border-radius: 999px;
    padding: 5px 9px;
    font-size: .72rem;
    font-weight: 900;
}

.product-badge.sale {
    background: #d70018;
    color: #fff;
}

.product-badge.hot {
    background: #ffd400;
    color: #111;
}

.product-img-wrap {
    height: 168px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 12px 4px;
    text-decoration: none;
}

.product-img-wrap img {
    max-width: 100%;
    max-height: 150px;
    object-fit: contain;
    transition: .25s;
}

.product-card:hover .product-img-wrap img {
    transform: scale(1.04);
}

.product-info {
    display: flex;
    flex-direction: column;
    flex: 1;
}

.product-tags {
    display: flex;
    gap: 5px;
    flex-wrap: wrap;
    margin-bottom: 7px;
}

.product-tags span {
    background: #f5f5f5;
    color: #666;
    border-radius: 999px;
    padding: 4px 8px;
    font-size: .68rem;
    font-weight: 800;
}

.product-name {
    min-height: 42px;
    margin-bottom: 6px;
}

.product-name a {
    color: #111;
    text-decoration: none;
    font-size: .9rem;
    line-height: 1.25;
    font-weight: 900;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.product-name a:hover {
    color: #d70018;
}

.product-desc {
    color: #777;
    font-size: .78rem;
    line-height: 1.35;
    height: 34px;
    overflow: hidden;
    margin-bottom: 8px;
}

.product-price strong {
    color: #d70018;
    font-size: 1.08rem;
    font-weight: 900;
}

.product-price span {
    color: #999;
    text-decoration: line-through;
    font-size: .78rem;
    margin-left: 4px;
}

.product-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 6px;
    margin-top: 5px;
    font-size: .76rem;
    color: #888;
}

.product-meta .rating {
    color: #f5a400;
    font-weight: 900;
}

.product-actions {
    display: grid;
    grid-template-columns: 1.2fr .8fr;
    gap: 7px;
    margin-top: 12px;
}

.btn-cart,
.btn-detail {
    border-radius: 999px;
    padding: 9px 8px;
    font-size: .8rem;
    font-weight: 900;
    text-align: center;
    text-decoration: none;
    transition: .2s;
}

.btn-cart {
    background: #ffd400;
    color: #111;
}

.btn-detail {
    background: #f1f1f1;
    color: #333;
}

.btn-cart:hover,
.btn-detail:hover {
    transform: translateY(-1px);
    color: #111;
}

.admin-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 7px;
    border-top: 1px solid #eee;
    padding-top: 9px;
    margin-top: 9px;
}

.btn-admin-edit,
.btn-admin-delete {
    border-radius: 999px;
    padding: 7px;
    font-size: .75rem;
    font-weight: 900;
    text-align: center;
    text-decoration: none;
}

.btn-admin-edit {
    background: #e3f2fd;
    color: #1565c0;
}

.btn-admin-delete {
    background: #fff0f0;
    color: #d70018;
}

.empty-box {
    background: #fff;
    border-radius: 24px;
    padding: 60px 20px;
    text-align: center;
    box-shadow: 0 8px 24px rgba(0,0,0,.07);
}

.empty-box i {
    font-size: 4rem;
    color: #ccc;
    margin-bottom: 14px;
}

.empty-box h5 {
    font-weight: 900;
}

/* ===== PAGINATION ===== */
.page-link {
    color: #111;
    border: none;
    box-shadow: 0 4px 12px rgba(0,0,0,.08);
}

.page-item.active .page-link {
    background: #ffd400;
    color: #111;
    font-weight: 900;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 991px) {
    .shop-hero {
        padding: 34px;
    }

    .hero-left h1 {
        font-size: 2.35rem;
    }

    .hero-right img {
        max-height: 240px;
    }

    .mini-banner-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 767px) {
    .shop-hero {
        flex-direction: column;
        text-align: center;
        padding: 30px 22px;
    }

    .hero-left h1 {
        font-size: 2rem;
    }

    .hero-actions {
        justify-content: center;
    }

    .hero-right img {
        margin-top: 20px;
        max-height: 210px;
    }

    .section-head {
        align-items: flex-start;
        flex-direction: column;
    }

    .filter-bottom {
        align-items: flex-start;
        flex-direction: column;
    }

    .product-img-wrap {
        height: 145px;
    }

    .product-img-wrap img {
        max-height: 128px;
    }

    .product-actions {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const successAlert = document.getElementById('successAlert');

    if (successAlert) {
        setTimeout(function () {
            successAlert.classList.remove('show');
            setTimeout(function () {
                successAlert.remove();
            }, 500);
        }, 3000);
    }
});

function confirmDelete(id, name) {
    return confirm('Xóa sản phẩm "' + name + '"?\nHành động này không thể hoàn tác.');
}
</script>