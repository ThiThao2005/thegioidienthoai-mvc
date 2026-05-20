<?php
// Cấu hình mảng ánh xạ trạng thái thân thiện với khách hàng
$statusConfig = [
    'pending'    => ['label' => 'Chờ xác nhận', 'bg' => 'bg-secondary-subtle text-secondary', 'icon' => 'fa-clock'],
    'processing' => ['label' => 'Đang xử lý',   'bg' => 'bg-primary-subtle text-primary',     'icon' => 'fa-spinner fa-spin'],
    'shipped'    => ['label' => 'Đang giao',     'bg' => 'bg-info-subtle text-info-dark',      'icon' => 'fa-truck'],
    'done'       => ['label' => 'Hoàn thành',    'bg' => 'bg-success-subtle text-success',     'icon' => 'fa-check-circle'],
    'cancelled'  => ['label' => 'Đã hủy',       'bg' => 'bg-danger-subtle text-danger',       'icon' => 'fa-times-circle']
];

// Nhận bộ lọc tab hiện tại từ URL (mặc định là 'all')
$currentTab = $_GET['tab'] ?? 'all';
?>

<style>
/* Custom style tối ưu trải nghiệm khách hàng */
.nav-pills .nav-link { color: #495057; font-weight: 600; font-size: 0.9rem; padding: 10px 20px; border-radius: 20px; transition: all 0.3s ease; }
.nav-pills .nav-link.active { background-color: #ffd400 !important; color: #000 !important; box-shadow: 0 4px 12px rgba(255, 212, 0, 0.3); }
.order-card { border-radius: 16px; transition: transform 0.2s ease, box-shadow 0.2s ease; border: 1px solid #e9ecef; }
.order-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.05) !important; }
.badge-custom { font-size: 0.8rem; padding: 6px 14px; border-radius: 20px; font-weight: 600; }
.text-info-dark { color: #0284c7; }
.bg-info-subtle { background-color: #e0f2fe; }
</style>

<div class="container py-5" style="max-width: 1000px;">
    <!-- Tiêu đề trang -->
    <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
        <h3 class="fw-bold text-dark mb-0">
            <i class="fas fa-box-open text-warning me-2"></i>Đơn hàng của tôi
        </h3>
        <span class="text-muted small fw-semibold">Quản lý và theo dõi hành trình mua sắm</span>
    </div>

    <!-- Thanh chuyển đổi Tabs Trạng thái dạng lướt ngang mượt mà -->
    <div class="mb-4 overflow-x-auto pb-2">
        <ul class="nav nav-pills flex-nowrap gap-2" style="white-space: nowrap;">
            <li class="nav-item">
                <a class="nav-link <?= $currentTab === 'all' ? 'active' : '' ?>" href="/project1/Product/myOrders?tab=all">Tất cả đơn</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentTab === 'pending' ? 'active' : '' ?>" href="/project1/Product/myOrders?tab=pending">⏳ Chờ xác nhận</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentTab === 'processing' ? 'active' : '' ?>" href="/project1/Product/myOrders?tab=processing">🔄 Đang xử lý</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentTab === 'shipped' ? 'active' : '' ?>" href="/project1/Product/myOrders?tab=shipped">🚚 Đang giao</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentTab === 'done' ? 'active' : '' ?>" href="/project1/Product/myOrders?tab=done">✅ Hoàn thành</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentTab === 'cancelled' ? 'active' : '' ?>" href="/project1/Product/myOrders?tab=cancelled">❌ Đã hủy</a>
            </li>
        </ul>
    </div>

    <!-- Danh sách hiển thị đơn hàng -->
    <div class="d-flex flex-column gap-3">
        <?php
        // Logic lọc dữ liệu đơn hàng ngay trên mảng dữ liệu Client nhận về
        $filteredOrders = $myOrders ?? [];
        if ($currentTab !== 'all') {
            $filteredOrders = array_filter($myOrders ?? [], fn($o) => ($o->status ?? 'pending') === $currentTab);
        }
        ?>

        <?php if (empty($filteredOrders)): ?>
            <!-- Trạng thái trống -->
            <div class="text-center py-5 bg-white rounded-4 border shadow-sm">
                <i class="fas fa-receipt fa-4x text-muted opacity-25 mb-3"></i>
                <h5 class="fw-bold text-secondary">Bạn chưa có đơn hàng nào ở mục này</h5>
                <p class="text-muted small">Hãy tiếp tục khám phá hệ thống và lựa chọn sản phẩm yêu thích nhé!</p>
                <a href="/project1/Product/index" class="btn btn-warning fw-bold rounded-pill px-4 py-2 mt-2 text-dark shadow-sm">
                    <i class="fas fa-shopping-cart me-2"></i>Mua sắm ngay
                </a>
            </div>
        <?php else: ?>
            <?php foreach ($filteredOrders as $order): 
                $statusKey = $order->status ?? 'pending';
                $cfg = $statusConfig[$statusKey] ?? $statusConfig['pending'];
                
                // Định dạng ngày đặt
                $orderDate = isset($order->created_at) ? date('d/m/Y - H:i', strtotime($order->created_at)) : 'Đang cập nhật';
            ?>
                <!-- Card Đơn hàng độc lập -->
                <div class="card order-card border-0 shadow-sm bg-white p-4">
                    <div class="row align-items-center g-3">
                        
                        <!-- Cột 1: Mã và Ngày -->
                        <div class="col-md-3 col-6">
                            <span class="text-muted small d-block">Mã đơn hàng</span>
                            <span class="fw-bold text-dark fs-5">#<?= $order->id ?></span>
                        </div>
                        
                        <div class="col-md-3 col-6">
                            <span class="text-muted small d-block">Ngày đặt mua</span>
                            <span class="fw-semibold text-secondary" style="font-size: 0.9rem;"><?= $orderDate ?></span>
                        </div>
                        
                        <!-- Cột 2: Tổng giá trị -->
                        <div class="col-md-3 col-6">
                            <span class="text-muted small d-block">Tổng số tiền</span>
                            <span class="fw-bold text-danger fs-5">
                                <?= number_format($order->total_price ?? 0, 0, ',', '.') ?><span class="fs-6 fw-normal text-muted"> đ</span>
                            </span>
                        </div>
                        
                        <!-- Cột 3: Trạng thái Badge -->
                        <div class="col-md-3 col-6 text-md-end">
                            <span class="badge badge-custom <?= $cfg['bg'] ?> d-inline-flex align-items-center gap-1">
                                <i class="fas <?= $cfg['icon'] ?>"></i> <?= $cfg['label'] ?>
                            </span>
                        </div>

                    </div>
                    
                    <!-- Khung hành động chân Card đơn hàng -->
                    <div class="d-flex justify-content-end gap-2 mt-3 pt-3 border-top border-light">
                        <!-- Nút xem chi tiết đơn hàng dành riêng cho Client -->
                        <a href="/project1/Product/myOrderDetail?id=<?= $order->id ?>" class="btn btn-sm btn-light border rounded-pill px-3 fw-semibold text-secondary">
                            <i class="fas fa-eye me-1"></i> Xem chi tiết
                        </a>

                        <!-- Hỗ trợ tính năng hủy đơn nhanh nếu trạng thái vẫn là Chờ xác nhận -->
                        <?php if ($statusKey === 'pending'): ?>
                            <form method="POST" action="/project1/Product/cancelOrder" onsubmit="return confirm('Bạn chắc chắn muốn hủy đơn hàng này?')">
                                <input type="hidden" name="order_id" value="<?= $order->id ?>">
                                <!-- Lưu lại vị trí tab hiện tại gửi lên Controller để điều hướng chính xác -->
                                <input type="hidden" name="current_tab" value="<?= htmlspecialchars($currentTab) ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-semibold">
                                    Hủy đơn hàng
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>