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
            </div>
            
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