<?php
SessionHelper::start();
$cart = $_SESSION['cart'] ?? [];
$hasItems = !empty($cart);
$totalPrice = 0;
$totalQuantity = SessionHelper::getCartCount();
$voucher = $_SESSION['voucher'] ?? null;
$discount = (float)($voucher['discount'] ?? 0);
?>

<style>
.cart-shell { max-width: 1180px; }
.cart-hero { background: #ffd400; border-radius: 18px; }
.cart-card { border: 1px solid #edf0f2; border-radius: 16px; }
.cart-item { border: 1px solid #edf0f2; border-radius: 14px; transition: box-shadow .2s ease, transform .2s ease; }
.cart-item:hover { box-shadow: 0 10px 28px rgba(15, 23, 42, .07); transform: translateY(-1px); }
.cart-img { width: 88px; height: 88px; object-fit: contain; border-radius: 12px; background: #fff; border: 1px solid #eef2f5; }
.qty-control .btn { width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center; }
.qty-control input { width: 48px; height: 34px; }
.summary-card { position: sticky; top: 18px; }
.checkout-btn { background: #ffd400; border: 0; }
.checkout-btn:hover { background: #f6c900; }
.empty-cart-icon { width: 96px; height: 96px; border-radius: 50%; background: #fff7cc; display: inline-flex; align-items: center; justify-content: center; }
</style>

<div class="container cart-shell py-4">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="/project1/Product/index" class="text-decoration-none text-dark">Trang chủ</a></li>
            <li class="breadcrumb-item active" aria-current="page">Giỏ hàng</li>
        </ol>
    </nav>

    <div class="cart-hero p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap">
            <div>
                <div class="text-uppercase small fw-bold text-dark opacity-75 mb-1">Giỏ hàng của bạn</div>
                <h3 class="fw-bold text-dark mb-1"><?= $totalQuantity ?> sản phẩm đang chờ thanh toán</h3>
                <p class="mb-0 text-dark opacity-75">Kiểm tra số lượng, áp mã giảm giá và hoàn tất đặt hàng trong một màn hình.</p>
            </div>
            <a href="/project1/Product/index" class="btn btn-dark rounded-pill px-4 fw-semibold">
                <i class="fas fa-plus me-2"></i>Chọn thêm
            </a>
        </div>
    </div>

    <?php if (!$hasItems): ?>
        <div class="cart-card bg-white text-center py-5 px-3 shadow-sm">
            <div class="empty-cart-icon mb-3">
                <i class="fas fa-shopping-cart fa-3x text-warning"></i>
            </div>
            <h5 class="fw-bold text-dark">Giỏ hàng đang trống</h5>
            <p class="text-muted mb-4">Hãy thêm điện thoại, laptop hoặc phụ kiện bạn thích rồi quay lại đây thanh toán.</p>
            <a href="/project1/Product/index" class="btn btn-warning rounded-pill px-4 fw-bold text-dark">Mua sắm ngay</a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="cart-card bg-white shadow-sm p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold text-dark mb-0"><i class="fas fa-shopping-bag text-warning me-2"></i>Sản phẩm trong giỏ</h5>
                        <span class="text-muted small"><?= $totalQuantity ?> sản phẩm</span>
                    </div>

                    <div class="d-flex flex-column gap-3">
                        <?php foreach ($cart as $id => $item): ?>
                            <?php
                            $quantity = is_array($item) ? (int)($item['quantity'] ?? $item['Quantity'] ?? 1) : 1;
                            $price = (float)($item['price'] ?? $item['Price'] ?? 0);
                            $name = $item['name'] ?? $item['Name'] ?? 'Sản phẩm không tên';
                            $image = $item['image'] ?? $item['Image'] ?? 'default.png';
                            $subTotal = $price * $quantity;
                            $totalPrice += $subTotal;
                            $imagePath = (strpos($image, 'public/images/') !== false || strpos($image, 'uploads/') !== false)
                                ? '/project1/' . $image
                                : '/project1/public/images/' . $image;
                            $safeId = urlencode((string)$id);
                            ?>
                            <div class="cart-item bg-white p-3">
                                <div class="row g-3 align-items-center">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="<?= htmlspecialchars($imagePath, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>" class="cart-img">
                                            <div>
                                                <h6 class="fw-bold text-dark mb-1"><?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?></h6>
                                                <div class="text-muted small">Đơn giá: <?= number_format($price, 0, ',', '.') ?>đ</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="qty-control d-flex align-items-center justify-content-md-center">
                                            <a href="/project1/Product/updateCartQuantity?id=<?= $safeId ?>&action=decrease"
                                               class="btn btn-outline-secondary btn-sm rounded-start-pill"
                                               data-quantity="<?= $quantity ?>"
                                               onclick="return handleDecrease(this);">
                                                <i class="fas fa-minus small"></i>
                                            </a>
                                            <input type="text" class="form-control form-control-sm text-center bg-white rounded-0 fw-bold" value="<?= $quantity ?>" readonly>
                                            <a href="/project1/Product/updateCartQuantity?id=<?= $safeId ?>&action=increase" class="btn btn-outline-secondary btn-sm rounded-end-pill">
                                                <i class="fas fa-plus small"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3 text-end">
                                        <div class="fw-bold text-danger fs-5"><?= number_format($subTotal, 0, ',', '.') ?>đ</div>
                                        <a href="/project1/Product/removeFromCart?id=<?= $safeId ?>"
                                           class="btn btn-link link-danger text-decoration-none p-0 small fw-semibold"
                                           onclick="return confirm('Bạn có chắc muốn bỏ sản phẩm này khỏi giỏ hàng?');">
                                            <i class="far fa-trash-alt me-1"></i>Xóa
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="cart-card summary-card bg-white shadow-sm p-4 mb-3">
                    <h5 class="fw-bold text-dark mb-3">Tóm tắt đơn hàng</h5>

                    <?php if (isset($_SESSION['error_msg'])): ?>
                        <div class="alert alert-danger small"><?php echo htmlspecialchars($_SESSION['error_msg'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['error_msg']); ?></div>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['success_msg'])): ?>
                        <div class="alert alert-success small"><?php echo htmlspecialchars($_SESSION['success_msg'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['success_msg']); ?></div>
                    <?php endif; ?>

                    <form method="POST" action="/project1/Product/applyVoucher" class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Mã giảm giá</label>
                        <div class="input-group">
                            <input type="text" name="voucher_code" class="form-control" placeholder="Nhập SALE10 hoặc FREESHIP" value="<?php echo htmlspecialchars($voucher['code'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <button class="btn btn-dark fw-semibold" type="submit">Áp dụng</button>
                        </div>
                    </form>

                    <div class="d-flex justify-content-between mb-3 text-secondary">
                        <span>Tạm tính</span>
                        <span class="fw-semibold"><?= number_format($totalPrice, 0, ',', '.') ?>đ</span>
                    </div>
                    <?php if ($discount > 0): ?>
                        <div class="d-flex justify-content-between mb-3 text-success">
                            <span>Giảm giá <?= htmlspecialchars($voucher['code'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                            <span class="fw-semibold">-<?= number_format($discount, 0, ',', '.') ?>đ</span>
                        </div>
                    <?php endif; ?>
                    <div class="d-flex justify-content-between mb-3 text-secondary">
                        <span>Phí vận chuyển</span>
                        <span class="fw-semibold text-success">Miễn phí</span>
                    </div>

                    <hr class="my-3">

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="fw-bold fs-5 text-dark">Tổng thanh toán</span>
                        <span class="fw-bold fs-3 text-danger"><?= number_format(max(0, $totalPrice - $discount), 0, ',', '.') ?>đ</span>
                    </div>

                    <a href="/project1/Product/checkout" class="checkout-btn btn w-100 fw-bold py-3 rounded-pill d-block text-center text-dark text-decoration-none">
                        Đặt hàng ngay <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>

                <div class="cart-card bg-white p-3 shadow-sm d-flex gap-3 align-items-start">
                    <i class="fas fa-shield-alt text-warning fs-3 mt-1"></i>
                    <div>
                        <div class="fw-bold text-dark">Yên tâm mua sắm</div>
                        <div class="text-muted small">Giao hàng nhanh, đổi trả theo chính sách và bảo hành chính hãng.</div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function handleDecrease(element) {
    const currentQty = parseInt(element.getAttribute('data-quantity')) || 1;
    if (currentQty <= 1) {
        return confirm('Số lượng sẽ về 0. Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?');
    }
    return true;
}
</script>
