<div class="container bg-white p-4 rounded shadow-sm mt-4">
    
<div id="promoCarousel" class="carousel slide mb-5 shadow-sm rounded-4 overflow-hidden custom-carousel" data-bs-ride="carousel">
    <div class="carousel-indicators custom-indicators">
        <button type="button" data-bs-target="#promoCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
        <button type="button" data-bs-target="#promoCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
        <button type="button" data-bs-target="#promoCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
    </div>
    
    <div class="carousel-inner">
        <div class="carousel-item active" data-bs-interval="4000">
            <a href="/project1/Product/index?promotion=smartphone">
                <img src="https://cdn11.dienmaycholon.vn/filewebdmclnew/DMCL21/Picture//Tm/Tm_picture_3547/galaxy-s26-ultr_868_959.png.webp" class="d-block w-100 carousel-img" alt="Siêu sale Smartphone - Giảm đến 50%">
            </a>
        </div>
        <div class="carousel-item" data-bs-interval="4000">
            <a href="/project1/Product/index?search=iPhone">
                <img src="https://cdn11.dienmaycholon.vn/filewebdmclnew/DMCL21/Picture//Tm/Tm_picture_3454/xiaomi-redmi-a7_849_959.png.webp" class="d-block w-100 carousel-img" alt="iPhone Series - Giá tốt nhất thị trường">
            </a>
        </div>
        <div class="carousel-item" data-bs-interval="4000">
            <a href="/project1/Product/index?promotion=laptop">
                <img src="https://cdn11.dienmaycholon.vn/filewebdmclnew/DMCL21/Picture//Tm/Tm_picture_3179/banner-homepage_779_959.png.webp" class="d-block w-100 carousel-img" alt="Laptop Gaming - Quà tặng cực khủng">
            </a>
        </div>
    </div>
    
    <button class="carousel-control-prev custom-control-btn" type="button" data-bs-target="#promoCarousel" data-bs-slide="prev">
        <span class="control-icon-wrapper">
            <i class="fas fa-chevron-left"></i>
        </span>
        <span class="visually-hidden">Trước</span>
    </button>
    <button class="carousel-control-next custom-control-btn" type="button" data-bs-target="#promoCarousel" data-bs-slide="next">
        <span class="control-icon-wrapper">
            <i class="fas fa-chevron-right"></i>
        </span>
        <span class="visually-hidden">Sau</span>
    </button>
</div>

<style>
    /* Tổng thể Carousel */
    .custom-carousel {
        position: relative;
        border-radius: 16px !important; /* Bo góc mềm mại hiện đại */
    }

    /* Tối ưu ảnh: Tự động co giãn theo tỷ lệ, sử dụng hình thật không lo bị méo chữ */
    .carousel-img {
        height: 380px;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    @media (max-width: 991.98px) {
        .carousel-img {
            height: 260px; /* Màn hình Tablet */
        }
    }
    @media (max-width: 575.98px) {
        .carousel-img {
            height: 160px; /* Màn hình Điện thoại dọc */
        }
    }

    /* Custom nút mũi tên chuyển slide */
    .custom-control-btn {
        width: 6%;
        opacity: 0; /* Mặc định ẩn đi */
        transition: all 0.3s ease-in-out;
    }
    .custom-carousel:hover .custom-control-btn {
        opacity: 1; /* Hover vào mới hiện */
    }
    
    /* Vòng tròn bọc ngoài mũi tên */
    .control-icon-wrapper {
        background-color: rgba(255, 255, 255, 0.85);
        color: #333;
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        transition: all 0.2s;
    }
    .control-icon-wrapper i {
        font-size: 1.1rem;
    }
    .custom-control-btn:hover .control-icon-wrapper {
        background-color: #ffd400; /* Màu vàng thương hiệu */
        color: #000;
        transform: scale(1.1);
    }

    /* Custom indicators dưới đáy */
    .custom-indicators button {
        width: 10px !important;
        height: 10px !important;
        border-radius: 50% !important;
        margin: 0 5px !important;
        background-color: rgba(255, 255, 255, 0.6) !important;
        border: none !important;
        transition: all 0.3s ease;
    }
    .custom-indicators .active {
        width: 24px !important; /* Dấu active kéo dài thời thượng */
        border-radius: 5px !important;
        background-color: #ffd400 !important;
    }
</style>

    <?php if (isset($_SESSION['success_msg'])): ?>
        <div id="successAlert" class="alert alert-success alert-dismissible fade show fw-bold shadow-sm mb-4 border-0" role="alert" style="border-radius: 12px; background-color: #d1e7dd; color: #0f5132; transition: opacity 0.5s ease;">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2 fs-5 text-success"></i>
                <span><?php echo $_SESSION['success_msg']; ?></span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success_msg']); ?>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <h4 class="fw-bold text-uppercase m-0 text-dark">
            <i class="fas fa-mobile-alt text-warning me-2"></i>Danh sách sản phẩm
        </h4>
        
        <?php if (SessionHelper::isAdmin()): ?>
            <a href="/project1/Product/add" class="btn fw-bold px-4 shadow-sm" style="background-color: #288ad6; color: white;">
                <i class="fas fa-plus-circle me-1"></i> Thêm sản phẩm
            </a>
        <?php endif; ?>
    </div>

    <form class="row g-2 align-items-center mb-4" method="GET" action="/project1/Product/index">
        <?php if (!empty($category_id)): ?>
            <input type="hidden" name="category_id" value="<?php echo (int) $category_id; ?>">
        <?php endif; ?>
        <div class="col-md-4">
            <input type="search" name="search" class="form-control rounded-pill px-4"
                   value="<?php echo htmlspecialchars($search ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   placeholder="Nhap tu khoa san pham, mo ta hoac danh muc...">
        </div>
        <div class="col-md-2">
            <select name="brand_id" class="form-select rounded-pill">
                <option value="">Tat ca hang</option>
                <?php foreach (($brands ?? []) as $brand): ?>
                    <option value="<?php echo $brand->id; ?>" <?php echo (($filters['brand_id'] ?? '') == $brand->id) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($brand->name, ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <input type="number" name="min_price" class="form-control rounded-pill" value="<?php echo htmlspecialchars($filters['min_price'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Gia tu">
        </div>
        <div class="col-md-2">
            <input type="number" name="max_price" class="form-control rounded-pill" value="<?php echo htmlspecialchars($filters['max_price'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Gia den">
        </div>
        <div class="col-md-2">
            <select name="sort" class="form-select rounded-pill">
                <option value="newest" <?php echo (($filters['sort'] ?? '') === 'newest') ? 'selected' : ''; ?>>Moi nhat</option>
                <option value="price_asc" <?php echo (($filters['sort'] ?? '') === 'price_asc') ? 'selected' : ''; ?>>Gia tang</option>
                <option value="price_desc" <?php echo (($filters['sort'] ?? '') === 'price_desc') ? 'selected' : ''; ?>>Gia giam</option>
                <option value="rating" <?php echo (($filters['sort'] ?? '') === 'rating') ? 'selected' : ''; ?>>Danh gia cao</option>
            </select>
        </div>
        <div class="col-12 d-flex gap-2 align-items-center">
            <label class="form-check-label small me-2">
                <input class="form-check-input" type="checkbox" name="featured" value="1" <?php echo !empty($filters['featured']) ? 'checked' : ''; ?>> Chi san pham noi bat
            </label>
            <button class="btn btn-dark rounded-pill px-4 fw-semibold" type="submit">
                <i class="fas fa-search me-1"></i> Tim kiem
            </button>
            <?php if (!empty($search) || !empty($category_id)): ?>
                <a class="btn btn-outline-secondary rounded-pill px-4" href="/project1/Product/index">Xoa loc</a>
            <?php endif; ?>
        </div>
        <div class="col-12 text-muted small">
            Tim thay <?php echo (int) ($totalProducts ?? count($products)); ?> san pham<?php echo !empty($search) ? ' cho "' . htmlspecialchars($search, ENT_QUOTES, 'UTF-8') . '"' : ''; ?>.
        </div>
    </form>

    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-3">
        
        <?php if (empty($products)): ?>
            <div class="col-12 text-center py-5">
                <div class="mb-3">
                    <i class="fas fa-box-open fa-4x text-muted"></i>
                </div>
                <p class="text-muted fs-5">Chưa có sản phẩm nào thuộc danh mục này.</p>
                
                <?php if (SessionHelper::isAdmin()): ?>
                    <a href="/project1/Product/add" class="btn btn-outline-primary rounded-pill">Thêm sản phẩm ngay</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
            <div class="col product-item">
                <div class="card h-100 p-2 card-product" style="transition: all 0.3s ease; border: 1px solid #eee; border-radius: 12px; background: #fff;">
                    
                    <?php 
                        $imgName = !empty($product->image) ? $product->image : 'default.jpg';
                        $imagePath = (strpos($imgName, 'public/images/') !== false || strpos($imgName, 'uploads/') !== false) 
                            ? "/project1/" . $imgName 
                            : "/project1/public/images/" . $imgName; 
                    ?>
                    
                    <a href="/project1/Product/detail?id=<?php echo $product->id; ?>" class="text-center d-block text-decoration-none position-relative">
                        <?php if (!empty($product->sale_percent)): ?>
                            <span class="badge bg-danger position-absolute top-0 start-0 m-2">-<?php echo (int)$product->sale_percent; ?>%</span>
                        <?php elseif (!empty($product->featured)): ?>
                            <span class="badge bg-warning text-dark position-absolute top-0 start-0 m-2">Noi bat</span>
                        <?php endif; ?>
                        <img src="<?php echo htmlspecialchars($imagePath, ENT_QUOTES, 'UTF-8'); ?>" 
                             class="card-img-top" 
                             alt="Ảnh sản phẩm"
                             style="object-fit: contain; height: 160px; padding: 5px;">
                    </a>
                    
                    <div class="card-body px-1 py-2 d-flex flex-column">
                        <h6 class="card-title fw-bold mb-1" style="font-size: 0.9rem; height: 2.4rem; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; line-height: 1.2rem;">
                            <a href="/project1/Product/detail?id=<?php echo $product->id; ?>" class="text-dark text-decoration-none product-title-link">
                                <?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                        </h6>

                        <div class="mb-2">
                            <span class="badge bg-light text-secondary border small" style="font-size: 0.75rem;">
                                <i class="fas fa-tag me-1 text-warning"></i><?php echo htmlspecialchars($product->category_name ?? 'Điện thoại', ENT_QUOTES, 'UTF-8'); ?>
                            </span>
                        </div>
                        
                        <?php if (!empty($product->brand_name)): ?>
                            <div class="small fw-semibold text-dark mb-1"><?php echo htmlspecialchars($product->brand_name, ENT_QUOTES, 'UTF-8'); ?></div>
                        <?php endif; ?>
                        <p class="card-text text-muted small text-truncate mb-2" style="font-size: 0.8rem;">
                            <?php echo htmlspecialchars($product->description, ENT_QUOTES, 'UTF-8'); ?>
                        </p>

                        <div class="mt-auto mb-2">
                            <strong style="color: #d70018; font-size: 1.05rem;">
                                <?php echo number_format($product->price, 0, ',', '.'); ?>₫
                            </strong>
                            <div class="small text-warning mt-1">
                                <i class="fas fa-star"></i>
                                <?php echo number_format((float)($product->avg_rating ?? 0), 1); ?>
                                <span class="text-muted">(<?php echo (int)($product->review_count ?? 0); ?> danh gia)</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-transparent border-0 px-1 pt-0 flex-column d-flex gap-2">
                        <a href="/project1/Product/addToCart?id=<?php echo $product->id; ?>" class="btn btn-sm btn-warning w-100 fw-bold rounded-pill text-dark py-1.5" style="background-color: #ffd400; border: none;">
                            <i class="fas fa-cart-plus me-1"></i> Thêm vào giỏ
                        </a>
                        
                        <?php if (SessionHelper::isAdmin()): ?>
                            <div class="d-flex gap-1 justify-content-between border-top pt-2">
                                <a href="/project1/Product/edit?id=<?php echo $product->id; ?>" class="btn btn-sm btn-outline-secondary w-50 rounded-pill" style="font-size: 0.75rem; padding: 4px 5px;">
                                    <i class="fas fa-edit"></i> Sửa
                                </a>
                                <a href="/project1/Product/delete?id=<?php echo $product->id; ?>" class="btn btn-sm btn-outline-danger w-50 rounded-pill" style="font-size: 0.75rem; padding: 4px 5px;"
                                   onclick="return confirm('Bạn có chắc muốn xóa <?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>?');">
                                    <i class="fas fa-trash"></i> Xóa
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>

    <?php if (($totalPages ?? 1) > 1): ?>
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <?php
                    $queryBase = [];
                    if (!empty($search)) $queryBase['search'] = $search;
                    if (!empty($category_id)) $queryBase['category_id'] = $category_id;
                    for ($i = 1; $i <= $totalPages; $i++):
                        $queryBase['page'] = $i;
                ?>
                    <li class="page-item <?php echo $i == ($page ?? 1) ? 'active' : ''; ?>">
                        <a class="page-link" href="/project1/Product/index?<?php echo http_build_query($queryBase); ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<style>
    .product-title-link:hover { color: #288ad6 !important; text-decoration: underline !important; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ⏳ XỬ LÝ TỰ ĐỘNGẨN THÔNG BÁO SAU 3 GIÂY
        const successAlert = document.getElementById('successAlert');
        if (successAlert) {
            setTimeout(function() {
                // Tận dụng class ẩn của Bootstrap để làm mờ mượt mà
                successAlert.classList.remove('show');
                successAlert.classList.add('fade');
                
                // Đợi hiệu ứng mờ chạy xong (500ms) rồi xóa hẳn block khỏi giao diện
                setTimeout(function() {
                    successAlert.remove();
                }, 500);
            }, 3000); // 3000ms = 3 giây hiển thị rực rỡ
        }

        // 🔍 SCRIPT TÌM KIẾM SẢN PHẨM REALTIME
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                let keyword = this.value.toLowerCase().trim();
                let products = document.querySelectorAll('.product-item');

                products.forEach(function(product) {
                    let productName = product.querySelector('.card-title').innerText.toLowerCase();
                    if (productName.includes(keyword)) {
                        product.style.display = '';
                    } else {
                        product.style.display = 'none';
                    }
                });
            });
        }
    });
</script>
