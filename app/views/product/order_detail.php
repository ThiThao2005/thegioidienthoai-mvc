<?php
$statusSteps = [
    'pending'    => ['label' => 'Chờ xác nhận', 'icon' => 'fa-clock',         'step' => 1],
    'processing' => ['label' => 'Đang xử lý',   'icon' => 'fa-cog',           'step' => 2],
    'shipped'    => ['label' => 'Đang giao',     'icon' => 'fa-truck',         'step' => 3],
    'done'       => ['label' => 'Hoàn thành',    'icon' => 'fa-check-circle', 'step' => 4],
];
$currentStatus = $order->status ?? 'pending';
$currentStep   = $statusSteps[$currentStatus]['step'] ?? 1;

// Lấy bộ lọc hiện tại truyền từ trang danh sách (nếu có) để xử lý nút Quay lại
$activeFilter = $_GET['filter'] ?? 'all';
?>

<style>
/* ── Thanh tiến trình trạng thái Responsive ── */
.order-steps { display: flex; align-items: flex-start; justify-content: space-between; position: relative; margin: 15px 0 25px; }
.order-steps::before {
    content: '';
    position: absolute;
    top: 20px; left: 0; right: 0;
    height: 4px;
    background: #e9ecef;
    z-index: 0;
}
.order-steps::after {
    content: '';
    position: absolute;
    top: 20px; left: 0;
    height: 4px;
    background: #ffd400;
    z-index: 1;
    /* Tính toán chính xác vị trí chạy từ tâm điểm này sang tâm điểm khác */
    width: <?= (($currentStep - 1) / 3) * 100 ?>%;
    transition: width .5s cubic-bezier(0.4, 0, 0.2, 1);
}
.step-item { flex: 1; text-align: center; position: relative; z-index: 2; }
.step-circle {
    width: 40px; height: 40px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 10px;
    font-size: 14px;
    border: 3px solid #e9ecef;
    background: #fff;
    transition: all .4s ease;
}
.step-item.done .step-circle    { background: #ffd400; border-color: #ffd400; color: #212529; }
.step-item.active .step-circle  { background: #fff; border-color: #ffd400; color: #d39e00; box-shadow: 0 0 0 5px rgba(255,212,0,.25); }
.step-item.inactive .step-circle{ background: #f8f9fa; border-color: #e9ecef; color: #6c757d; }
.step-label { font-size: 0.8rem; font-weight: 600; transition: color .3s; }
.step-item.done .step-label    { color: #212529; }
.step-item.active .step-label  { color: #d39e00; }
.step-item.inactive .step-label{ color: #6c757d; }

@media (max-width: 576px) {
    .step-label { font-size: 0.7rem; }
    .step-circle { width: 34px; height: 34px; font-size: 12px; top: -3px; }
    .order-steps::before, .order-steps::after { top: 15px; }
}
</style>

<div class="container py-4">
    <!-- Nút Quay lại thông minh: Giữ trạng thái bộ lọc của Admin trước đó -->
    <div class="mb-4">
        <a href="/project1/Product/orders?filter=<?= $activeFilter ?>" class="btn btn-light border rounded-pill px-3 fw-semibold text-secondary shadow-sm">
            <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách đơn hàng
        </a>
    </div>

    <!-- ── Thanh tiến trình ── -->
    <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius:12px;">
        <h6 class="fw-bold text-dark mb-4">
            <i class="fas fa-route text-warning me-2"></i>Trạng thái đơn hàng #<?= $order->id ?>
        </h6>
        <div class="order-steps">
            <?php foreach ($statusSteps as $key => $s):
                $stepNum = $s['step'];
                if ($stepNum < $currentStep)        $cls = 'done';
                elseif ($stepNum === $currentStep)  $cls = 'active';
                else                                $cls = 'inactive';
            ?>
            <div class="step-item <?= $cls ?>">
                <div class="step-circle">
                    <i class="fas <?= $s['icon'] ?>"></i>
                </div>
                <div class="step-label"><?= $s['label'] ?></div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Nút cập nhật trạng thái dành riêng cho Admin -->
        <?php if (function_exists('session_start') && isset($_SESSION['role']) && $_SESSION['role'] === 'admin' && $currentStatus !== 'done'): ?>
        <!-- Gửi kèm id và filter lên action của form để Controller biết đường điều hướng quay lại -->
        <form method="POST" action="/project1/Product/updateOrderStatus?id=<?= $order->id ?>&filter=<?= $activeFilter ?>" class="d-flex align-items-center gap-2 mt-3 pt-3 border-top"
              onsubmit="return confirm('Xác nhận cập nhật trạng thái cho đơn hàng này?')">
            <input type="hidden" name="order_id" value="<?= $order->id ?>">
            
            <label class="text-muted small fw-semibold me-1">Thay đổi trạng thái:</label>
            <select name="status" class="form-select form-select-sm" style="max-width:200px; border-radius:20px;">
                <option value="pending"    <?= $currentStatus==='pending'    ?'selected':'' ?>>⏳ Chờ xác nhận</option>
                <option value="processing" <?= $currentStatus==='processing' ?'selected':'' ?>>🔄 Đang xử lý</option>
                <option value="shipped"    <?= $currentStatus==='shipped'    ?'selected':'' ?>>🚚 Đang giao</option>
                <option value="done"       <?= $currentStatus==='done'       ?'selected':'' ?>>✅ Hoàn thành</option>
            </select>
            <button type="submit" class="btn btn-sm btn-warning fw-bold rounded-pill px-3 text-dark shadow-sm">
                <i class="fas fa-save me-1"></i>Lưu thay đổi
            </button>
        </form>
        <?php elseif ($currentStatus === 'done'): ?>
        <div class="mt-2 pt-3 border-top">
            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2 fw-semibold">
                <i class="fas fa-check-circle me-1"></i>Đơn hàng đã hoàn thành xuất sắc và đóng lại.
            </span>
        </div>
        <?php endif; ?>
    </div>

    <div class="row g-4">
        <!-- Thông tin giao hàng -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm p-4 h-100" style="border-radius:12px;">
                <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">
                    <i class="fas fa-user text-warning me-2"></i>Thông tin giao hàng
                </h5>
                <div class="mb-3">
                    <small class="text-muted d-block">Mã đơn hàng:</small>
                    <span class="fw-bold text-secondary">#<?= $order->id ?></span>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Tên người nhận:</small>
                    <span class="fw-bold text-dark fs-5"><?= htmlspecialchars($order->name, ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Số điện thoại liên lạc:</small>
                    <span class="fw-bold text-dark"><?= htmlspecialchars($order->phone, ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Địa chỉ nhận hàng:</small>
                    <span class="text-dark fw-semibold"><?= htmlspecialchars($order->address, ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            </div>
        </div>

        <!-- Sản phẩm trong đơn -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm p-4" style="border-radius:12px;">
                <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">
                    <i class="fas fa-shopping-basket text-warning me-2"></i>Sản phẩm trong đơn hàng
                </h5>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr class="text-muted small text-uppercase">
                                <th>Sản phẩm</th>
                                <th class="text-end">Giá bán</th>
                                <th class="text-center">Số lượng</th>
                                <th class="text-end">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $total_order = 0;
                            foreach ($details as $item):
                                $subtotal     = $item->price * $item->quantity;
                                $total_order += $subtotal;
                            ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="/project1/public/images/<?= !empty($item->product_image) ? $item->product_image : 'default.jpg' ?>"
                                             alt="" class="rounded border" style="width:55px;height:55px;object-fit:cover;">
                                        <div>
                                            <span class="fw-bold text-dark d-block"><?= htmlspecialchars($item->product_name, ENT_QUOTES, 'UTF-8') ?></span>
                                            <small class="text-muted">ID sản phẩm: #<?= $item->product_id ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end fw-semibold text-secondary"><?= number_format($item->price, 0, ',', '.') ?>đ</td>
                                <td class="text-center fw-bold"><?= $item->quantity ?></td>
                                <td class="text-end fw-bold text-dark"><?= number_format($subtotal, 0, ',', '.') ?>đ</td>
                            </tr>
                            <?php endforeach; ?>
                            <tr class="table-light">
                                <td colspan="3" class="text-end fw-bold fs-5 py-3">Tổng giá trị đơn hàng:</td>
                                <td class="text-end fw-bold text-danger fs-4 py-3"><?= number_format($total_order, 0, ',', '.') ?>đ</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>