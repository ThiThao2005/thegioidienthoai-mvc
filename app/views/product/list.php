<div class="container bg-white p-4 rounded shadow-sm mt-4">
    
    <div id="promoCarousel" class="carousel slide mb-5 shadow-sm rounded overflow-hidden" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#promoCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#promoCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#promoCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>
        
        <div class="carousel-inner">
            <div class="carousel-item active" data-bs-interval="3000">
                <img src="https://placehold.co/1200x350/ffd400/333?text=SIEU+SALE+SMARTPHONE+-+GIAM+DEN+50%25" class="d-block w-100" alt="Khuyến mãi 1" style="object-fit: cover; max-height: 350px;">
            </div>
            <div class="carousel-item" data-bs-interval="3000">
                <img src="https://placehold.co/1200x350/288ad6/fff?text=IPHONE+15+PRO+MAX+-+GIA+TOT+NHAT+THI+TRUONG" class="d-block w-100" alt="Khuyến mãi 2" style="object-fit: cover; max-height: 350px;">
            </div>
            <div class="carousel-item" data-bs-interval="3000">
                <img src="https://placehold.co/1200x350/d70018/fff?text=LAPTOP+GAMING+-+QUA+TANG+CUC+KHUNG" class="d-block w-100" alt="Khuyến mãi 3" style="object-fit: cover; max-height: 350px;">
            </div>
        </div>
        
        <button class="carousel-control-prev" type="button" data-bs-target="#promoCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Trước</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#promoCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Sau</span>
        </button>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <h4 class="fw-bold text-uppercase m-0 text-dark">
            <i class="fas fa-mobile-alt text-warning me-2"></i>Danh sách sản phẩm
        </h4>
        <a href="/project1/Product/add" class="btn fw-bold px-4 shadow-sm" style="background-color: #288ad6; color: white;">
            <i class="fas fa-plus-circle me-1"></i> Thêm sản phẩm
        </a>
    </div>

    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-3">
        
        <?php if (empty($products)): ?>
            <div class="col-12 text-center py-5">
                <div class="mb-3">
                    <i class="fas fa-box-open fa-4x text-muted"></i>
                </div>
                <p class="text-muted fs-5">Chưa có sản phẩm nào thuộc danh mục này.</p>
                <a href="/project1/Product/add" class="btn btn-outline-primary rounded-pill">Thêm sản phẩm ngay</a>
            </div>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
            <div class="col product-item">
                <div class="card h-100 p-2 card-product" style="transition: all 0.3s ease; border: 1px solid #eee; border-radius: 12px; background: #fff;">
                    
                    <?php 
                        $imgName = !empty($product->image) ? $product->image : 'default.jpg';
                        // Nếu ảnh lưu dạng path hoàn chỉnh hoặc tên file đơn thuần
                        $imagePath = (strpos($imgName, 'public/images/') !== false || strpos($imgName, 'uploads/') !== false) 
                            ? "/project1/" . $imgName 
                            : "/project1/public/images/" . $imgName; 
                    ?>
                    <img src="<?php echo htmlspecialchars($imagePath, ENT_QUOTES, 'UTF-8'); ?>" 
                         class="card-img-top" 
                         alt="Ảnh sản phẩm"
                         style="object-fit: contain; height: 160px; padding: 5px;">
                    
                    <div class="card-body px-1 py-2 d-flex flex-column">
                        <h6 class="card-title fw-bold text-dark mb-1" style="font-size: 0.9rem; height: 2.4rem; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; line-height: 1.2rem;">
                            <?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>
                        </h6>

                        <div class="mb-2">
                            <span class="badge bg-light text-secondary border small" style="font-size: 0.75rem;">
                                <i class="fas fa-tag me-1 text-warning"></i><?php echo htmlspecialchars($product->category_name ?? 'Điện thoại', ENT_QUOTES, 'UTF-8'); ?>
                            </span>
                        </div>
                        
                        <p class="card-text text-muted small text-truncate mb-2" style="font-size: 0.8rem;">
                            <?php echo htmlspecialchars($product->description, ENT_QUOTES, 'UTF-8'); ?>
                        </p>

                        <div class="mt-auto mb-2">
                            <strong style="color: #d70018; font-size: 1.05rem;">
                                <?php echo number_format($product->price, 0, ',', '.'); ?>₫
                            </strong>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-transparent border-0 px-1 pt-0 flex-column d-flex gap-2">
                        <a href="/project1/Product/addToCart?id=<?php echo $product->id; ?>" class="btn btn-sm btn-warning w-100 fw-bold rounded-pill text-dark py-1.5" style="background-color: #ffd400; border: none;">
                            <i class="fas fa-cart-plus me-1"></i> Thêm vào giỏ
                        </a>
                        
                        <div class="d-flex gap-1 justify-content-between">
                            <a href="/project1/Product/edit?id=<?php echo $product->id; ?>" class="btn btn-sm btn-outline-secondary w-50 rounded-pill" style="font-size: 0.75rem; padding: 2px 5px;">
                                <i class="fas fa-edit"></i> Sửa
                            </a>
                            <a href="/project1/Product/delete?id=<?php echo $product->id; ?>" class="btn btn-sm btn-outline-danger w-50 rounded-pill" style="font-size: 0.75rem; padding: 2px 5px;"
                               onclick="return confirm('Bạn có chắc muốn xóa <?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>?');">
                                <i class="fas fa-trash"></i> Xóa
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
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