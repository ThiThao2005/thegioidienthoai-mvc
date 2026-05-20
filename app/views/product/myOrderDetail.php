<?php
// Cấu hình mảng ánh xạ trạng thái tương tự trang danh sách đơn hàng
$statusConfig = [
    'pending'    => ['label' => 'Chờ xác nhận', 'bg' => 'bg-secondary-subtle text-secondary', 'icon' => 'fa-clock'],
    'processing' => ['label' => 'Đang xử lý',   'bg' => 'bg-primary-subtle text-primary',     'icon' => 'fa-spinner fa-spin'],
    'shipped'    => ['label' => 'Đang giao',     'bg' => 'bg-info-subtle text-info-dark',      'icon' => 'fa-truck'],
    'done'       => ['label' => 'Hoàn thành',    'bg' => 'bg-success-subtle text-success',     'icon' => 'fa-check-circle'],
    'cancelled'  => ['label' => 'Đã hủy',       'bg' => 'bg-danger-subtle text-danger',       'icon' => 'fa-times-circle']
];

$statusKey = $order->status ?? 'pending';
$cfg = $statusConfig[$statusKey] ?? $statusConfig['pending'];
$orderDate = isset($order->created_at) ? date('d/m/Y - H:i', strtotime($order->created_at)) : 'Đang cập nhật';
?>

<style>
.detail-card { border-radius: 16px; border: 1px solid #e9ecef; }
.product-img { width: 70px; height: 70px; object-fit: cover; border-radius: 12px; border: 1px solid #eee; }
.badge-custom { font-size: 0.85rem; padding: 6px 14px; border-radius: 20px; font-weight: 600; }
.text-info-dark { color: #0284c7; }
.bg-info-subtle { background-color: #e0f2fe; }
.step-icon { width: 40px; height: 40px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 1.1rem; }
</style>

<div class="container py-5" style="max-width: 900px;">
    <!-- Nút quay lại và Tiêu đề -->
    <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
        <div class="d-flex align-items-center gap-3">
            <a href="/project1/Product/myOrders" class="btn btn-sm btn-light border rounded-pill px-3 fw-semibold text-secondary">
                <i class="fas fa-arrow-left me-1"></i> Quay lại
            </a>
            <h4 class="fw-bold text-dark mb-0">Chi tiết đơn hàng #<?= $order->id ?></h4>
        </div>
        <div>
            <span class="badge badge-custom <?= $cfg['bg'] ?> d-inline-flex align-items-center gap-1">
                <i class="fas <?= $cfg['icon'] ?>"></i> <?= $cfg['label'] ?>
            </span>
        </div>
    </div>

    <div class="row g-4">
        <!-- Cột trái: Thông tin sản phẩm & Thanh toán -->
        <div class="col-md-8">
            <!-- Thẻ danh sách sản phẩm -->
            <div class="card detail-card border-0 shadow-sm bg-white p-4 mb-4">
                <h5 class="fw-bold mb-4 text-dark"><i class="fas fa-shopping-basket text-warning me-2"></i>Sản phẩm đã đặt</h5>
                
                <div class="d-flex flex-column gap-3">
                    <?php 
                    $subTotal = 0;
                    foreach ($details as $item): 
                        $itemPrice = $item->price ?? 0;
                        $itemQty = $item->quantity ?? 1;
                        $itemTotal = $itemPrice * $itemQty;
                        $subTotal += $itemTotal;
                        $productImg = !empty($item->image) ? '/project1/public/images/' . $item->image : '/project1/public/images/default.jpg';
                    ?>
                        <div class="d-flex align-items-center justify-content-between pb-3 border-bottom border-light last-border-none">
                            <div class="d-flex align-items-center gap-3">
                                <img src="<?= $productImg ?>" alt="<?= htmlspecialchars($item->name ?? 'Sản phẩm') ?>" class="product-img">
                                <div>
                                    <h6 class="fw-bold text-dark mb-1 mb-0" style="font-size: 0.95rem;"><?= htmlspecialchars($item->name ?? 'Sản phẩm không rõ tên') ?></h6>
                                    <span class="text-muted small">Số lượng: <strong><?= $itemQty ?></strong></span>
                                    <span class="text-muted small mx-2">|</span>
                                    <span class="text-muted small">Đơn giá: <?= number_format($itemPrice, 0, ',', '.') ?>đ</span>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="fw-bold text-dark"><?= number_format($itemTotal, 0, ',', '.') ?>đ</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Tính tổng tiền -->
                <div class="mt-4 pt-3 border-top border-light">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Tạm tính:</span>
                        <span class="fw-semibold text-dark"><?= number_format($subTotal, 0, ',', '.') ?>đ</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Phí vận chuyển:</span>
                        <span class="fw-semibold text-success">Miễn phí</span>
                    </div>
                    <hr class="my-3 text-muted opacity-25">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-dark fs-5">Tổng số tiền:</span>
                        <span class="fw-bold text-danger fs-4"><?= number_format($order->total_price ?? $subTotal, 0, ',', '.') ?><span class="fs-6 fw-normal text-muted"> đ</span></span>
                    </div>
                </div>
            </div>
            
            <!-- Trạng thái hủy nếu có -->
            <?php if ($statusKey === 'pending'): ?>
                <div class="card detail-card border-0 shadow-sm bg-white p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="fw-bold text-dark mb-1">Bạn muốn thay đổi ý định?</h6>
                            <p class="text-muted small mb-0">Bạn có thể hủy đơn hàng này khi nó vẫn ở trạng thái chờ duyệt.</p>
                        </div>
                        <form method="POST" action="/project1/Product/cancelOrder" onsubmit="return confirm('Bạn chắc chắn muốn hủy đơn hàng này?')">
                            <input type="hidden" name="order_id" value="<?= $order->id ?>">
                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-semibold">
                                Hủy đơn hàng
                            </button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Cột phải: Thông tin giao nhận & Thời gian -->
        <div class="col-md-4">
            <!-- Card thông tin nhận hàng -->
            <div class="card detail-card border-0 shadow-sm bg-white p-4 mb-4">
                <h5 class="fw-bold mb-4 text-dark"><i class="fas fa-map-marker-alt text-warning me-2"></i>Địa chỉ nhận hàng</h5>
                
                <div class="mb-3">
                    <span class="text-muted small d-block mb-1">Người nhận hàng</span>
                    <span class="fw-bold text-dark"><?= htmlspecialchars($order->name ?? 'Chưa cập nhật') ?></span>
                </div>
                
                <div class="mb-3">
                    <span class="text-muted small d-block mb-1">Số điện thoại</span>
                    <span class="fw-semibold text-dark"><?= htmlspecialchars($order->phone ?? 'Chưa cập nhật') ?></span>
                </div>
                
                <div class="mb-0">
                    <span class="text-muted small d-block mb-1">Địa chỉ giao tới</span>
                    <span class="fw-semibold text-secondary" style="font-size: 0.9rem;"><?= htmlspecialchars($order->address ?? 'Chưa cập nhật') ?></span>
                </div>
            </div>

            <!-- Card thời gian mua sắm -->
            <div class="card detail-card border-0 shadow-sm bg-white p-4">
                <h5 class="fw-bold mb-3 text-dark"><i class="fas fa-history text-warning me-2"></i>Lịch sử mua</h5>
                <div class="d-flex align-items-center gap-3">
                    <div class="step-icon bg-warning-subtle text-warning">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block">Thời gian đặt mua</span>
                        <span class="fw-semibold text-dark" style="font-size: 0.9rem;"><?= $orderDate ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Loại bỏ đường gạch dưới cuối cùng của danh sách sản phẩm để tối ưu thẩm mỹ
document.addEventListener("DOMContentLoaded", function() {
    const borders = document.querySelectorAll('.last-border-none');
    if(borders.length > 0) {
        borders[borders.length - 1].classList.remove('border-bottom', 'pb-3');
    }
});
</script>