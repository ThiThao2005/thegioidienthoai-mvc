<?php
// Đảm bảo biến $product tồn tại và không bị rỗng
if (!isset($product)) {
    echo "<div class='container mt-5 alert alert-danger'>Không tìm thấy thông tin sản phẩm!</div>";
    return;
}

// Xử lý đường dẫn ảnh giống như trang danh sách
$imgName = !empty($product->image) ? $product->image : 'default.jpg';
$imagePath = (strpos($imgName, 'public/images/') !== false || strpos($imgName, 'uploads/') !== false) 
    ? "/project1/" . $imgName 
    : "/project1/public/images/" . $imgName; 
?>

<div class="container bg-white p-4 rounded shadow-sm mt-4">
    <div class="mb-4">
        <a href="/project1/Product/index" class="text-decoration-none text-secondary small fw-semibold">
            <i class="fas fa-arrow-left me-1"></i> Quay lại danh sách sản phẩm
        </a>
    </div>

    <div class="row g-5">
        <div class="col-12 col-md-6 col-lg-5 text-center">
            <div class="product-image-container p-3 border rounded-3 bg-light d-flex align-items-center justify-content-center" style="min-height: 380px;">
                <img src="<?php echo htmlspecialchars($imagePath, ENT_QUOTES, 'UTF-8'); ?>" 
                     class="img-fluid rounded" 
                     alt="<?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>"
                     style="max-height: 350px; object-fit: contain; transition: transform 0.3s ease;">
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-7 d-flex flex-column">
            <h2 class="fw-bold text-dark mb-2 fs-3">
                <?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>
            </h2>

            <div class="mb-3">
                <?php if (!empty($product->brand_name)): ?>
                    <span class="badge bg-dark text-white px-3 py-2 me-1" style="font-size: 0.85rem;">
                        <?php echo htmlspecialchars($product->brand_name, ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                <?php endif; ?>
                <span class="badge bg-light text-dark border px-3 py-2 me-1" style="font-size: 0.85rem;">
                    Bao hanh <?php echo (int)($product->warranty_months ?? 12); ?> thang
                </span>
                <span class="badge bg-light text-secondary border px-3 py-2" style="font-size: 0.85rem;">
                    <i class="fas fa-tag me-1 text-warning"></i> 
                    Danh mục: <?php echo htmlspecialchars($product->category_name ?? 'Điện thoại', ENT_QUOTES, 'UTF-8'); ?>
                </span>
            </div>

            <hr class="opacity-50">

            <div class="p-3 my-3 bg-light rounded-3 d-flex align-items-center">
                <span class="text-muted me-3 small">Giá bán tại cửa hàng:</span>
                <strong class="fs-2" style="color: #d70018;">
                    <?php echo number_format($product->price, 0, ',', '.'); ?>₫
                </strong>
                <span class="badge bg-danger ms-3 bg-opacity-10 text-danger fw-bold py-1.5 px-2 small border border-danger border-opacity-20" style="font-size: 0.75rem;">
                    <i class="fas fa-bolt me-1"></i> Trả góp 0%
                </span>
            </div>

            <div class="card border-warning mb-4 shadow-sm bg-warning bg-opacity-10">
                <div class="card-header bg-warning text-dark fw-bold py-2" style="font-size: 0.9rem;">
                    <i class="fas fa-gift me-2"></i> QUÀ TẶNG & KHUYẾN MÃI ĐẶC BIỆT
                </div>
                <div class="card-body py-2 px-3" style="font-size: 0.85rem;">
                    <ul class="list-unstyled m-0 lh-lg text-dark">
                        <li><i class="fas fa-check-circle text-success me-2"></i> Tặng gói bảo hành chính hãng toàn diện 12 tháng.</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i> Giảm thêm 500.000₫ khi thanh toán qua VNPAY-QR / MOMO.</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i> Trúng 1 trong 100 xe máy điện Wave khi mua trong tháng này.</li>
                    </ul>
                </div>
            </div>

            <div class="d-grid gap-2 mt-auto">
                <a href="/project1/Product/addToCart?id=<?php echo $product->id; ?>" 
                   class="btn btn-lg fw-bold rounded-pill text-dark py-3 btn-action-buy shadow-sm" 
                   style="background-color: #ffd400; border: none; transition: all 0.2s;">
                    <i class="fas fa-shopping-cart me-2 fs-5"></i> THÊM VÀO GIỎ HÀNG
                    <span class="d-block small fw-normal mt-1" style="font-size: 0.75rem; opacity: 0.8;">Giao hàng tận nơi hoặc nhận tại cửa hàng</span>
                </a>
                <?php if (SessionHelper::isLoggedIn()): ?>
                    <form method="POST" action="/project1/Product/toggleWishlist" class="m-0">
                        <input type="hidden" name="product_id" value="<?php echo $product->id; ?>">
                        <button type="submit" class="btn btn-outline-danger btn-lg fw-bold rounded-pill py-3 w-100">
                            <i class="fas fa-heart me-2"></i><?php echo !empty($isWishlisted) ? 'BO KHOI YEU THICH' : 'THEM VAO YEU THICH'; ?>
                        </button>
                    </form>
                <?php endif; ?>
            </div>

            <?php if (!empty($productVariants)): ?>
                <div class="mb-4">
                    <h6 class="fw-bold text-dark">Lua chon phien ban</h6>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($productVariants as $variant): ?>
                            <?php $variantPrice = $product->price + (float)$variant->price_delta; ?>
                            <div class="border rounded-3 px-3 py-2 bg-white">
                                <div class="fw-semibold small">
                                    <?php echo htmlspecialchars(trim(($variant->color ?? '') . ' ' . ($variant->ram ?? '') . ' ' . ($variant->storage ?? '')), ENT_QUOTES, 'UTF-8'); ?>
                                </div>
                                <div class="text-danger fw-bold small"><?php echo number_format($variantPrice, 0, ',', '.'); ?>d</div>
                                <div class="text-muted small">Ton: <?php echo (int)$variant->stock; ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if (SessionHelper::isAdmin()): ?>
                <div class="d-flex gap-2 mt-3 justify-content-end border-top pt-3">
                    <span class="text-muted small d-flex align-items-center me-auto">
                        <i class="fas fa-user-shield me-1 text-primary"></i> Chức năng Admin:
                    </span>
                    <a href="/project1/Product/edit?id=<?php echo $product->id; ?>" class="btn btn-sm btn-outline-secondary px-4 rounded-pill">
                        <i class="fas fa-edit me-1"></i> Sửa sản phẩm
                    </a>
                    <a href="/project1/Product/delete?id=<?php echo $product->id; ?>" class="btn btn-sm btn-outline-danger px-4 rounded-pill"
                       onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?');">
                        <i class="fas fa-trash me-1"></i> Xóa sản phẩm
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-12">
            <div class="border-top pt-4">
                <h5 class="fw-bold text-uppercase text-dark mb-3">
                    <i class="fas fa-file-alt text-warning me-2"></i> Đặc điểm nổi bật & Thông tin chi tiết
                </h5>
                <div class="p-4 bg-light rounded-3 text-secondary border shadow-inner" style="line-height: 1.8; font-size: 0.95rem; white-space: pre-line;">
                    <?php 
                        if (!empty($product->description)) {
                            echo htmlspecialchars($product->description, ENT_QUOTES, 'UTF-8'); 
                        } else {
                            echo "Thông tin chi tiết của sản phẩm đang được cập nhật.";
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <?php if (!empty($productSpecs)): ?>
        <div class="row mt-5">
            <div class="col-12">
                <div class="border-top pt-4">
                    <h5 class="fw-bold text-uppercase text-dark mb-3">
                        <i class="fas fa-microchip text-warning me-2"></i> Thong so ky thuat
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <tbody>
                                <?php foreach ($productSpecs as $spec): ?>
                                    <tr>
                                        <th style="width: 35%;"><?php echo htmlspecialchars($spec->spec_key, ENT_QUOTES, 'UTF-8'); ?></th>
                                        <td><?php echo htmlspecialchars($spec->spec_value, ENT_QUOTES, 'UTF-8'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <div class="row mt-5">
        <div class="col-12">
            <div class="border-top pt-4">
                <h5 class="fw-bold text-uppercase text-dark mb-3">
                    <i class="fas fa-star text-warning me-2"></i> Danh gia san pham
                </h5>

                <?php if (isset($_SESSION['error_msg'])): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['error_msg'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['error_msg']); ?></div>
                <?php endif; ?>

                <div class="d-flex align-items-center gap-3 mb-4">
                    <span class="display-6 fw-bold text-warning"><?php echo number_format((float)($ratingSummary->avg_rating ?? 0), 1); ?></span>
                    <div>
                        <div class="text-warning">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star<?php echo $i <= round((float)($ratingSummary->avg_rating ?? 0)) ? '' : '-o'; ?>"></i>
                            <?php endfor; ?>
                        </div>
                        <small class="text-muted"><?php echo (int)($ratingSummary->total_reviews ?? 0); ?> luot danh gia</small>
                    </div>
                </div>

                <?php if (!SessionHelper::isLoggedIn()): ?>
                    <div class="alert alert-light border">Dang nhap de them san pham vao wishlist va danh gia sau khi mua hang.</div>
                <?php elseif (!empty($canReview)): ?>
                    <form method="POST" action="/project1/Product/saveReview" class="bg-light border rounded-3 p-3 mb-4">
                        <input type="hidden" name="product_id" value="<?php echo $product->id; ?>">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">So sao</label>
                                <select name="rating" class="form-select" required>
                                    <option value="5">5 sao</option>
                                    <option value="4">4 sao</option>
                                    <option value="3">3 sao</option>
                                    <option value="2">2 sao</option>
                                    <option value="1">1 sao</option>
                                </select>
                            </div>
                            <div class="col-md-9">
                                <label class="form-label fw-semibold">Binh luan</label>
                                <textarea name="comment" class="form-control" rows="3" minlength="5" maxlength="1000" required></textarea>
                            </div>
                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-warning rounded-pill fw-bold px-4 text-dark">Gui danh gia</button>
                            </div>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="alert alert-light border">Ban co the danh gia sau khi don hang chua san pham nay hoan thanh.</div>
                <?php endif; ?>

                <?php if (empty($reviews)): ?>
                    <p class="text-muted">Chua co danh gia nao cho san pham nay.</p>
                <?php else: ?>
                    <?php foreach ($reviews as $review): ?>
                        <div class="border-bottom py-3">
                            <div class="d-flex justify-content-between">
                                <strong><?php echo htmlspecialchars($review->fullname ?: $review->username, ENT_QUOTES, 'UTF-8'); ?></strong>
                                <span class="text-warning"><?php echo str_repeat('*', (int)$review->rating); ?></span>
                            </div>
                            <p class="mb-1 text-secondary"><?php echo nl2br(htmlspecialchars($review->comment, ENT_QUOTES, 'UTF-8')); ?></p>
                            <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($review->created_at)); ?></small>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <?php if (!empty($productImages)): ?>
                <div class="d-flex gap-2 flex-wrap justify-content-center mt-3">
                    <img src="<?php echo htmlspecialchars($imagePath, ENT_QUOTES, 'UTF-8'); ?>" class="border rounded p-1 bg-white" style="width:70px;height:70px;object-fit:contain;">
                    <?php foreach ($productImages as $galleryImage): ?>
                        <img src="/project1/public/images/<?php echo htmlspecialchars($galleryImage->image, ENT_QUOTES, 'UTF-8'); ?>" class="border rounded p-1 bg-white" style="width:70px;height:70px;object-fit:contain;">
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .product-image-container:hover img {
        transform: scale(1.05);
    }
    .btn-action-buy:hover {
        background-color: #e6be00 !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(255, 212, 0, 0.4) !important;
    }
</style>
