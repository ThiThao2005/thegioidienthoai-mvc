<?php
// Gọi SessionHelper để đồng bộ và lấy dữ liệu giỏ hàng trực tiếp từ Session
SessionHelper::start();
$cart = $_SESSION['cart'] ?? [];
$hasItems = !empty($cart);
$totalPrice = 0;

// Lấy tổng số lượng thực tế (Ví dụ: 2 iPhone + 1 Laptop = 3)
$totalQuantity = SessionHelper::getCartCount();
?>

<div class="row mt-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/project1/Product/index" class="text-decoration-none text-dark">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Giỏ hàng của bạn</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 col-md-12 mb-4"> 
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                <h4 class="fw-bold mb-4"><i class="fas fa-shopping-cart me-2"></i> Giỏ hàng (<?= $totalQuantity ?> sản phẩm)</h4>
                
                <?php if (!$hasItems): ?>
                    <div class="text-center py-5">
                        <img src="https://cdn-icons-png.flaticon.com/512/11329/11329060.png" alt="Giỏ hàng trống" style="width: 150px;" class="mb-4 opacity-75">
                        <h5 class="text-muted">Giỏ hàng của bạn đang trống trơn</h5>
                        <p class="text-secondary small">Hãy quay lại trang chủ để lựa chọn những sản phẩm công nghệ mới nhất nhé.</p>
                        <a href="/project1/Product/index" class="btn btn-warning fw-bold px-4 rounded-pill mt-2">
                            <i class="fas fa-arrow-left me-2"></i> Tiếp tục mua sắm
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table align-middle table-borderless">
                            <thead class="table-light text-secondary small">
                                <tr>
                                    <th scope="col">Sản phẩm</th>
                                    <th scope="col" class="text-center">Giá bán</th>
                                    <th scope="col" class="text-center" style="width: 140px;">Số lượng</th>
                                    <th scope="col" class="text-end">Tạm tính</th>
                                    <th scope="col" class="text-center">Xóa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cart as $id => $item): 
                                    // Bẫy lỗi: Đảm bảo lấy đúng số lượng dù cấu trúc mảng lưu thế nào
                                    $quantity = is_array($item) ? ($item['quantity'] ?? $item['Quantity'] ?? 1) : 1;
                                    $price = $item['price'] ?? $item['Price'] ?? 0;
                                    $name = $item['name'] ?? $item['Name'] ?? 'Sản phẩm không tên';
                                    $image = $item['image'] ?? $item['Image'] ?? 'default.png';

                                    $subTotal = $price * $quantity;
                                    $totalPrice += $subTotal;

                                    // Xử lý đường dẫn ảnh giống code cũ của bạn
                                    $imagePath = (strpos($image, 'public/images/') !== false || strpos($image, 'uploads/') !== false) 
                                        ? '/project1/' . $image 
                                        : '/project1/public/images/' . $image;
                                    
                                    // Chuẩn hóa ID sạch để truyền URL không lỗi
                                    $safeId = trim($id);
                                ?>
                                    <tr class="border-bottom">
                                        <td>
                                            <div class="d-flex align-items-center py-2">
                                                <img src="<?= htmlspecialchars($imagePath) ?>" 
                                                     alt="<?= htmlspecialchars($name) ?>" 
                                                     class="rounded border bg-white me-3" 
                                                     style="width: 70px; height: 70px; object-fit: contain;">
                                                <div>
                                                    <h6 class="fw-bold mb-1 text-dark"><?= htmlspecialchars($name) ?></h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center fw-semibold text-secondary">
                                            <?= number_format($price, 0, ',', '.') ?>đ
                                        </td>
                                        
                                        <td class="text-center">
                                            <div class="input-group input-group-sm justify-content-center">
                                                <a href="/project1/Product/updateCartQuantity?id=<?= $safeId ?>&action=decrease" 
                                                   class="btn btn-outline-secondary btn-minus px-2" 
                                                   data-quantity="<?= $quantity ?>"
                                                   onclick="return handleDecrease(this);">
                                                    <i class="fas fa-minus small"></i>
                                                </a>
                                                
                                                <input type="text" class="form-control text-center bg-white" value="<?= $quantity ?>" style="max-width: 45px; font-weight: 600;" readonly>
                                                
                                                <a href="/project1/Product/updateCartQuantity?id=<?= $safeId ?>&action=increase" class="btn btn-outline-secondary btn-plus px-2">
                                                    <i class="fas fa-plus small"></i>
                                                </a>
                                            </div>
                                        </td>
                                        
                                        <td class="text-end fw-bold text-danger">
                                            <?= number_format($subTotal, 0, ',', '.') ?>đ
                                        </td>
                                        
                                        <td class="text-center">
                                            <a href="/project1/Product/removeFromCart?id=<?= $safeId ?>" class="btn btn-link link-danger text-decoration-none p-0" 
                                               onclick="return confirm('Bạn có chắc muốn bỏ sản phẩm này khỏi giỏ hàng không?');">
                                                <i class="far fa-trash-alt fs-5"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <a href="/project1/Product/index" class="btn btn-outline-dark rounded-pill btn-sm px-3 fw-semibold">
                            <i class="fas fa-reply me-1"></i> Chọn thêm sản phẩm khác
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if ($hasItems): ?>
    <div class="col-lg-4 col-md-12">
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4 text-dark">Tóm tắt đơn hàng</h5>
                
                <div class="d-flex justify-content-between mb-3 text-secondary">
                    <span>Tạm tính:</span>
                    <span class="fw-semibold"><?= number_format($totalPrice, 0, ',', '.') ?>đ</span>
                </div>
                <div class="d-flex justify-content-between mb-3 text-secondary">
                    <span>Phí vận chuyển:</span>
                    <span class="text-success fw-semibold">Miễn phí</span>
                </div>
                
                <hr class="my-3 opacity-50">
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <span class="fw-bold fs-5 text-dark">Tổng tiền cộng:</span>
                    <span class="fw-extrabold fs-4 text-danger"><?= number_format($totalPrice, 0, ',', '.') ?>đ</span>
                </div>
                
                <a href="/project1/Product/checkout" class="btn btn-warning w-100 fw-bold py-3 text-uppercase shadow-sm rounded-pill d-block text-center text-dark text-decoration-none btn-checkout">
                    Tiến hành đặt hàng <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
        
        <div class="bg-white p-3 rounded-3 shadow-sm border border-opacity-10 d-flex gap-3 align-items-center">
            <i class="fas fa-shield-alt text-warning fs-2"></i>
            <div>
                <small class="fw-bold d-block text-dark">Chính sách Thế Giới Điện Thoại</small>
                <small class="text-secondary" style="font-size: 0.75rem;">Giao hàng nhanh chóng, bảo hành chính hãng toàn quốc 12 tháng.</small>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
    .btn-checkout {
        background-color: #ffd400 !important;
        border: none;
        transition: all 0.2s ease-in-out;
    }
    .btn-checkout:hover {
        background-color: #fccc00 !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(252, 204, 0, 0.4);
    }
    .btn-minus, .btn-plus {
        border-color: #ced4da !important;
        background: #f8f9fa;
        color: #495057;
    }
    .btn-minus:hover, .btn-plus:hover {
        background: #e9ecef;
        color: #000;
    }
</style>

<script>
function handleDecrease(element) {
    // Lấy số lượng hiện tại từ thuộc tính data-quantity
    const currentQty = parseInt(element.getAttribute('data-quantity')) || 1;
    
    // Nếu số lượng bằng 1 mà người dùng bấm trừ tiếp -> Hỏi xác nhận xóa hẳn
    if (currentQty <= 1) {
        return confirm('Số lượng sẽ về 0. Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng không?');
    }
    return true;
}
</script>