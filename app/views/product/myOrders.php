<?php
$statusConfig = [
    'pending' => ['label' => 'Chờ xác nhận', 'bg' => 'bg-secondary-subtle text-secondary', 'icon' => 'fa-clock'],
    'processing' => ['label' => 'Đang xử lý', 'bg' => 'bg-primary-subtle text-primary', 'icon' => 'fa-spinner fa-spin'],
    'shipped' => ['label' => 'Đang giao', 'bg' => 'bg-info-subtle text-info-dark', 'icon' => 'fa-truck'],
    'done' => ['label' => 'Hoàn thành', 'bg' => 'bg-success-subtle text-success', 'icon' => 'fa-check-circle'],
    'cancelled' => ['label' => 'Đã hủy', 'bg' => 'bg-danger-subtle text-danger', 'icon' => 'fa-times-circle']
];

$currentTab = $_GET['tab'] ?? 'all';
$filteredOrders = $myOrders ?? [];
if ($currentTab !== 'all') {
    $filteredOrders = array_filter($myOrders ?? [], fn($o) => ($o->status ?? 'pending') === $currentTab);
}
?>

<style>
.nav-pills .nav-link { color: #495057; font-weight: 700; font-size: .9rem; padding: 10px 18px; border-radius: 999px; transition: all .2s ease; }
.nav-pills .nav-link.active { background-color: #ffd400 !important; color: #000 !important; box-shadow: 0 6px 18px rgba(255, 212, 0, .28); }
.order-card { border-radius: 16px; transition: transform .2s ease, box-shadow .2s ease; border: 1px solid #edf0f2; }
.order-card:hover { transform: translateY(-2px); box-shadow: 0 10px 28px rgba(15,23,42,.07) !important; }
.badge-custom { font-size: .8rem; padding: 6px 14px; border-radius: 20px; font-weight: 700; }
.text-info-dark { color: #0284c7; }
.bg-info-subtle { background-color: #e0f2fe; }
</style>

<div class="container py-5" style="max-width: 1040px;">
    <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom flex-wrap gap-3">
        <div>
            <h3 class="fw-bold text-dark mb-1"><i class="fas fa-box-open text-warning me-2"></i>Đơn hàng của tôi</h3>
            <div class="text-muted small">Theo dõi đơn hàng và đánh giá sản phẩm sau khi đã nhận.</div>
        </div>
        <a href="/project1/Product/index" class="btn btn-dark rounded-pill px-4 fw-semibold">Tiếp tục mua sắm</a>
    </div>

    <div class="mb-4 overflow-x-auto pb-2">
        <ul class="nav nav-pills flex-nowrap gap-2" style="white-space: nowrap;">
            <li class="nav-item"><a class="nav-link <?= $currentTab === 'all' ? 'active' : '' ?>" href="/project1/Product/myOrders?tab=all">Tất cả đơn</a></li>
            <li class="nav-item"><a class="nav-link <?= $currentTab === 'pending' ? 'active' : '' ?>" href="/project1/Product/myOrders?tab=pending">Chờ xác nhận</a></li>
            <li class="nav-item"><a class="nav-link <?= $currentTab === 'processing' ? 'active' : '' ?>" href="/project1/Product/myOrders?tab=processing">Đang xử lý</a></li>
            <li class="nav-item"><a class="nav-link <?= $currentTab === 'shipped' ? 'active' : '' ?>" href="/project1/Product/myOrders?tab=shipped">Đang giao</a></li>
            <li class="nav-item"><a class="nav-link <?= $currentTab === 'done' ? 'active' : '' ?>" href="/project1/Product/myOrders?tab=done">Hoàn thành</a></li>
            <li class="nav-item"><a class="nav-link <?= $currentTab === 'cancelled' ? 'active' : '' ?>" href="/project1/Product/myOrders?tab=cancelled">Đã hủy</a></li>
        </ul>
    </div>

    <div class="d-flex flex-column gap-3">
        <?php if (empty($filteredOrders)): ?>
            <div class="text-center py-5 bg-white rounded-4 border shadow-sm">
                <i class="fas fa-receipt fa-4x text-muted opacity-25 mb-3"></i>
                <h5 class="fw-bold text-secondary">Chưa có đơn hàng nào ở mục này</h5>
                <p class="text-muted small">Hãy tiếp tục lựa chọn sản phẩm yêu thích.</p>
                <a href="/project1/Product/index" class="btn btn-warning fw-bold rounded-pill px-4 py-2 mt-2 text-dark">
                    <i class="fas fa-shopping-cart me-2"></i>Mua sắm ngay
                </a>
            </div>
        <?php else: ?>
            <?php foreach ($filteredOrders as $order): ?>
                <?php
                $statusKey = $order->status ?? 'pending';
                $cfg = $statusConfig[$statusKey] ?? $statusConfig['pending'];
                $orderDate = isset($order->created_at) ? date('d/m/Y - H:i', strtotime($order->created_at)) : 'Đang cập nhật';
                ?>
                <div class="card order-card border-0 shadow-sm bg-white p-4">
                    <div class="row align-items-center g-3">
                        <div class="col-md-3 col-6">
                            <span class="text-muted small d-block">Mã đơn hàng</span>
                            <span class="fw-bold text-dark fs-5">#<?= (int)$order->id ?></span>
                        </div>
                        <div class="col-md-3 col-6">
                            <span class="text-muted small d-block">Ngày đặt mua</span>
                            <span class="fw-semibold text-secondary" style="font-size: .9rem;"><?= $orderDate ?></span>
                        </div>
                        <div class="col-md-3 col-6">
                            <span class="text-muted small d-block">Tổng số tiền</span>
                            <span class="fw-bold text-danger fs-5"><?= number_format($order->total_price ?? 0, 0, ',', '.') ?><span class="fs-6 fw-normal text-muted"> đ</span></span>
                        </div>
                        <div class="col-md-3 col-6 text-md-end">
                            <span class="badge badge-custom <?= $cfg['bg'] ?> d-inline-flex align-items-center gap-1">
                                <i class="fas <?= $cfg['icon'] ?>"></i> <?= $cfg['label'] ?>
                            </span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-3 pt-3 border-top border-light flex-wrap">
                        <?php if ($statusKey === 'done'): ?>
                            <a href="/project1/Product/myOrderDetail?id=<?= (int)$order->id ?>" class="btn btn-sm btn-warning rounded-pill px-3 fw-bold text-dark">
                                <i class="fas fa-star me-1"></i>Đánh giá sản phẩm
                            </a>
                        <?php endif; ?>
                        <a href="/project1/Product/myOrderDetail?id=<?= (int)$order->id ?>" class="btn btn-sm btn-light border rounded-pill px-3 fw-semibold text-secondary">
                            <i class="fas fa-eye me-1"></i> Xem chi tiết
                        </a>
                        <?php if ($statusKey === 'pending'): ?>
                            <form method="POST" action="/project1/Product/cancelOrder" onsubmit="return confirm('Bạn chắc chắn muốn hủy đơn hàng này?')">
                                <input type="hidden" name="order_id" value="<?= (int)$order->id ?>">
                                <input type="hidden" name="current_tab" value="<?= htmlspecialchars($currentTab, ENT_QUOTES, 'UTF-8') ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-semibold">Hủy đơn hàng</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
