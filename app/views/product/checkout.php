<?php
$totalPrice = 0;
?>

<div class="row mt-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/project1/Product/index" class="text-decoration-none text-dark">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="/project1/Product/cart" class="text-decoration-none text-dark">Giỏ hàng</a></li>
                <li class="breadcrumb-item active" aria-current="page">Thanh toán đơn hàng</li>
            </ol>
        </nav>
    </div>
</div>

<form action="/project1/Product/processCheckout" method="POST">
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <div class="row">
        <div class="col-lg-7 col-md-12 mb-4">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-4 text-dark"><i class="fas fa-map-marker-alt text-warning me-2"></i> Thông tin giao hàng</h4>
                    
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold text-secondary">Họ và tên người nhận <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-3 py-2" id="name" name="name" placeholder="Ví dụ: Nguyễn Văn A" required>
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label fw-semibold text-secondary">Số điện thoại <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control rounded-3 py-2" id="phone" name="phone" placeholder="Ví dụ: 0912345678" pattern="[0-9]{10}" title="Vui lòng nhập đúng số điện thoại 10 chữ số" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold text-secondary">Email nhan xac nhan <span class="text-danger">*</span></label>
                        <input type="email" class="form-control rounded-3 py-2" id="email" name="email" placeholder="vidu@email.com" required>
                    </div>

                    <div class="mb-4">
                        <label for="address" class="form-label fw-semibold text-secondary">Địa chỉ nhận hàng (Số nhà, tên đường, phường/xã...) <span class="text-danger">*</span></label>
                        <textarea class="form-control rounded-3 py-2" id="address" name="address" rows="3" placeholder="Nhập đầy đủ địa chỉ để shipper giao tận nơi" required></textarea>
                    </div>

                    <h5 class="fw-bold mb-3 text-dark"><i class="fas fa-credit-card text-secondary me-2"></i> Phương thức thanh toán</h5>
                    <div class="d-flex flex-column gap-2">
                        <div class="p-3 border rounded-3 bg-light d-flex align-items-center gap-3 payment-method-option">
                            <input type="radio" id="cod" name="payment_method" value="COD" checked class="form-check-input ms-1">
                            <label for="cod" class="form-check-label fw-semibold m-0 text-dark w-100 cursor-pointer">
                                <i class="fas fa-money-bill-wave text-success me-1"></i> Thanh toán khi nhận hàng (COD)
                            </label>
                        </div>
                        
                        <div class="p-3 border rounded-3 bg-light d-flex align-items-center gap-3 payment-method-option">
                            <input type="radio" id="momo" name="payment_method" value="MOMO" class="form-check-input ms-1">
                            <label for="momo" class="form-check-label fw-semibold m-0 text-dark w-100 cursor-pointer">
                                <span class="badge bg-danger p-1 me-1" style="background-color: #a50064 !important; font-size: 0.65rem;">MoMo</span> 
                                Chuyển khoản qua Mã QR MoMo (VietQR)
                            </label>
                        </div>

                        <div class="p-3 border rounded-3 bg-light d-flex align-items-center gap-3 payment-method-option">
                            <input type="radio" id="vnpay" name="payment_method" value="VNPAY" class="form-check-input ms-1">
                            <label for="vnpay" class="form-check-label fw-semibold m-0 text-dark w-100 cursor-pointer">
                                <span class="badge bg-primary p-1 me-1" style="font-size: 0.65rem;">VNPay</span>
                                Thanh toan qua VNPay QR
                            </label>
                        </div>

                        <div id="momo-qr-container" class="border rounded-3 p-4 text-center bg-white shadow-sm mt-2 d-none">
                            <p class="text-secondary small mb-2">Vui lòng mở ứng dụng <strong>MoMo</strong> hoặc <strong>Ngân hàng</strong> để quét mã VietQR dưới đây:</p>
                            
                            <img src="/project1/public/images/momo_qr.png" alt="Mã QR MoMo Nguyễn Thị Thảo" class="img-fluid border rounded p-2 mb-3 shadow-sm" style="max-width: 250px;">
                            
                            <div class="bg-light p-3 rounded-3 text-start mx-auto" style="max-width: 320px;">
                                <div class="d-flex justify-content-between mb-1 small text-secondary">
                                    <span>Chủ tài khoản:</span>
                                    <strong class="text-dark">NGUYỄN THỊ THẢO</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-1 small text-secondary">
                                    <span>Hình thức:</span>
                                    <strong class="text-dark">MoMo / VietQR Napas247</strong>
                                </div>
                                <div class="d-flex justify-content-between small text-secondary">
                                    <span>Nội dung CK:</span>
                                    <strong class="text-danger" id="transfer-content">Thanh toan DH</strong>
                                </div>
                            </div>
                            <span class="badge bg-warning text-dark mt-3 px-3 py-2 rounded-pill small fw-semibold">
                                <i class="fas fa-info-circle me-1"></i> Bạn hãy chụp lại biên lai sau khi chuyển khoản thành công nhé!
                            </span>
                        </div>

                        <div id="vnpay-qr-container" class="border rounded-3 p-4 text-center bg-white shadow-sm mt-2 d-none">
                            <p class="text-secondary small mb-2">Quet QR VNPay cua cua hang, sau do nhap ma giao dich de admin doi soat.</p>
                            <div class="display-6 text-primary mb-2"><i class="fas fa-qrcode"></i></div>
                            <span class="badge bg-primary-subtle text-primary border rounded-pill px-3 py-2">VNPay sandbox/manual verify</span>
                        </div>

                        <div id="transaction-code-wrap" class="mt-2">
                            <label for="transaction_code" class="form-label fw-semibold text-secondary">Ma giao dich / Noi dung chuyen khoan</label>
                            <input type="text" class="form-control rounded-3 py-2" id="transaction_code" name="transaction_code" placeholder="VD: MOMO123456 hoac VNPAY987654">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5 col-md-12">
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3 text-dark">Đơn hàng của bạn</h5>
                    <hr class="opacity-25">

                    <div class="checkout-product-list mb-3" style="max-height: 240px; overflow-y: auto;">
                        <?php foreach ($cart as $id => $item): 
                            $subTotal = $item['price'] * $item['quantity'];
                            $totalPrice += $subTotal;
                            $imagePath = (strpos($item['image'], 'public/images/') !== false || strpos($item['image'], 'uploads/') !== false) 
                                ? '/project1/' . $item['image'] 
                                : '/project1/public/images/' . $item['image'];
                        ?>
                            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-opacity-10">
                                <div class="d-flex align-items-center">
                                    <div class="position-relative me-3">
                                        <img src="<?= htmlspecialchars($imagePath) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="rounded border bg-white" style="width: 50px; height: 50px; object-fit: contain;">
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-secondary text-white" style="font-size: 0.7rem;">
                                            <?= $item['quantity'] ?>
                                        </span>
                                    </div>
                                    <div>
                                        <small class="fw-bold d-block text-dark text-truncate" style="max-width: 180px;"><?= htmlspecialchars($item['name']) ?></small>
                                        <small class="text-secondary"><?= number_format($item['price'], 0, ',', '.') ?>đ</small>
                                    </div>
                                </div>
                                <span class="fw-semibold text-dark"><?= number_format($subTotal, 0, ',', '.') ?>đ</span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="d-flex justify-content-between mb-2 text-secondary small">
                        <span>Tạm tính:</span>
                        <span><?= number_format($totalPrice, 0, ',', '.') ?>đ</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 text-secondary small">
                        <span>Phí vận chuyển:</span>
                        <span class="text-success">Miễn phí</span>
                    </div>
                    
                    <hr class="my-3 opacity-25">
                    
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="fw-bold text-dark">Tổng tiền thanh toán:</span>
                        <span class="fw-extrabold fs-4 text-danger"><?= number_format($totalPrice, 0, ',', '.') ?>đ</span>
                    </div>

                    <button type="submit" class="btn btn-warning w-full fw-bold py-3 text-uppercase shadow-sm rounded-pill btn-order d-block text-center text-dark">
                        <i class="fas fa-check-circle me-1"></i> Xác nhận đặt hàng
                    </button>
                    
                    <a href="/project1/Product/cart" class="btn btn-link text-decoration-none text-secondary text-center d-block w-full mt-3 small">
                        <i class="fas fa-chevron-left me-1"></i> Quay lại sửa giỏ hàng
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
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const codRadio = document.getElementById('cod');
    const momoRadio = document.getElementById('momo');
    const vnpayRadio = document.getElementById('vnpay');
    const qrContainer = document.getElementById('momo-qr-container');
    const vnpayQrContainer = document.getElementById('vnpay-qr-container');
    const transactionWrap = document.getElementById('transaction-code-wrap');
    const transactionInput = document.getElementById('transaction_code');
    const transferContent = document.getElementById('transfer-content');
    const phoneInput = document.getElementById('phone');

    // Tự động sinh nội dung chuyển khoản bằng Số điện thoại khách hàng cho dễ quản lý đơn
    function updateTransferContent() {
        const phone = phoneInput.value.trim() || 'KH';
        transferContent.innerText = 'CHUYEN KHOAN DH ' + phone;
    }

    phoneInput.addEventListener('input', updateTransferContent);

    // Lắng nghe sự kiện thay đổi phương thức thanh toán
    function handlePaymentMethodChange() {
        if (momoRadio.checked) {
            qrContainer.classList.remove('d-none'); // Hiện QR Code
            vnpayQrContainer.classList.add('d-none');
            transactionWrap.classList.remove('d-none');
            transactionInput.required = true;
            updateTransferContent();
        } else {
            qrContainer.classList.add('d-none');    // Ẩn QR Code
        }
    }

    codRadio.addEventListener('change', handlePaymentMethodChange);
    momoRadio.addEventListener('change', handlePaymentMethodChange);
});
</script>

<style>
    .cursor-pointer {
        cursor: pointer;
    }
    .btn-order {
        background-color: #ffd400 !important;
        border: none;
        transition: all 0.2s ease-in-out;
    }
    .btn-order:hover {
        background-color: #fccc00 !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(252, 204, 0, 0.4);
    }
    .checkout-product-list::-webkit-scrollbar {
        width: 4px;
    }
    .checkout-product-list::-webkit-scrollbar-thumb {
        background: #ddd;
        border-radius: 4px;
    }
    .payment-method-option {
        transition: border-color 0.2s;
    }
    .payment-method-option:hover {
        border-color: #ffd400 !important;
    }
</style>
