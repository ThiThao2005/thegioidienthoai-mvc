<?php
$statusSteps = [
    'pending'    => ['label' => 'Chờ xác nhận', 'icon' => 'fa-clock',         'step' => 1],
    'processing' => ['label' => 'Đang xử lý',   'icon' => 'fa-cog',           'step' => 2],
    'shipped'    => ['label' => 'Đang giao',     'icon' => 'fa-truck',         'step' => 3],
    'done'       => ['label' => 'Hoàn thành',    'icon' => 'fa-check-circle',  'step' => 4],
];
$currentStatus = $order->status ?? 'pending';
$currentStep   = $statusSteps[$currentStatus]['step'] ?? 1;

// ⭐ Nếu đơn bị hủy, đặt step = 0 để thanh tiến trình không tô màu
$isCancelled = ($currentStatus === 'cancelled');
if ($isCancelled) $currentStep = 0;

$activeFilter = $_GET['filter'] ?? 'all';

$orderDate = 'Đang cập nhật';
if (!empty($order->created_at) && $order->created_at !== '0000-00-00 00:00:00') {
    $orderDate = date('d/m/Y - H:i', strtotime($order->created_at));
}
?>

<style>
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
    background: <?= $isCancelled ? '#dc3545' : '#ffd400' ?>;
    z-index: 1;
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

.order-time-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #fff9db;
    border: 1px solid #ffe58a;
    border-radius: 20px;
    padding: 5px 14px;
    font-size: 0.82rem;
    font-weight: 600;
    color: #856404;
}

@media (max-width: 576px) {
    .step-label { font-size: 0.7rem; }
    .step-circle { width: 34px; height: 34px; font-size: 12px; top: -3px; }
    .order-steps::before, .order-steps::after { top: 15px; }
}
</style>

<div class="container py-4">
    <div class="mb-4">
        <a href="/Product/orders?filter=<?= $activeFilter ?>" class="btn btn-light border rounded-pill px-3 fw-semibold text-secondary shadow-sm">
            <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách đơn hàng
        </a>
    </div>

    <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius:12px;">
        <div class="d-flex align-items-start justify-content-between flex-wrap gap-2 mb-4">
            <div>
                <h6 class="fw-bold text-dark mb-1">
                    <i class="fas fa-route text-warning me-2"></i>Trạng thái đơn hàng #<?= $order->id ?>
                </h6>
                <span class="order-time-badge">
                    <i class="fas fa-calendar-alt"></i>
                    Đặt lúc: <?= $orderDate ?>
                </span>
            </div>
            <?php if ($isCancelled): ?>
            <span class="badge bg-danger rounded-pill px-3 py-2 fw-semibold">
                <i class="fas fa-times-circle me-1"></i>Đơn hàng đã bị hủy
            </span>
            <?php endif; ?>
        </div>

        <?php if ($isCancelled): ?>
        <!-- ⭐ Thanh tiến trình riêng cho đơn bị hủy -->
        <div class="alert alert-danger rounded-3 d-flex align-items-center gap-3 mb-3">
            <i class="fas fa-times-circle fa-2x"></i>
            <div>
                <div class="fw-bold">Đơn hàng này đã bị hủy</div>
                <div class="small opacity-75">Khách hàng đã hủy đơn trước khi được xử lý.</div>
            </div>
        </div>
        <?php else: ?>
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
        <?php endif; ?>

        <?php if (!$isCancelled && isset($_SESSION['role']) && $_SESSION['role'] === 'admin' && $currentStatus !== 'done'): ?>
        <form method="POST" action="/Product/updateOrderStatus?id=<?= $order->id ?>&filter=<?= $activeFilter ?>" class="d-flex align-items-center gap-2 mt-3 pt-3 border-top"
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
                <i class="fas fa-check-circle me-1"></i>Đơn hàng đã hoàn thành và đóng lại.
            </span>
        </div>
        <?php endif; ?>
    </div>

    <div class="row g-4">
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
                    <small class="text-muted d-block">Thời gian đặt hàng:</small>
                    <span class="fw-bold text-dark">
                        <i class="fas fa-clock text-warning me-1"></i><?= $orderDate ?>
                    </span>
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
                                        <img src="/public/images/<?= !empty($item->product_image) ? $item->product_image : 'default.jpg' ?>"
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