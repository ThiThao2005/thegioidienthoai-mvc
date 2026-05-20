<?php
// Helper: trả về class badge và label theo status
function statusBadge($status) {
    $map = [
        'pending'    => ['bg' => 'bg-secondary',     'icon' => 'fa-clock',           'label' => 'Chờ xác nhận'],
        'processing' => ['bg' => 'bg-primary',        'icon' => 'fa-spinner fa-spin', 'label' => 'Đang xử lý'],
        'shipped'    => ['bg' => 'bg-info text-dark', 'icon' => 'fa-truck',           'label' => 'Đang giao'],
        'done'       => ['bg' => 'bg-success',        'icon' => 'fa-check-circle',    'label' => 'Hoàn thành'],
    ];
    return $map[$status] ?? $map['pending'];
}
?>

<style>
.status-select { min-width: 160px; font-size: 0.82rem; border-radius: 20px; padding: 4px 10px; cursor: pointer; }
.btn-status-save { border-radius: 20px; font-size: 0.82rem; padding: 4px 14px; }
.badge-status { font-size: 0.78rem; border-radius: 20px; padding: 5px 12px; letter-spacing: 0.3px; }
</style>

<div class="container-fluid px-4 py-4">
    <div class="row g-4">
        <!-- Sidebar -->
        <div class="col-lg-3 col-md-4">
            <div class="card border-0 shadow-sm p-3" style="border-radius: 12px;">
                <div class="d-flex flex-column gap-2">
                    <a href="/project1/Product/dashboard" class="nav-link p-3 text-secondary fw-semibold">
                        <i class="fas fa-chart-pie me-2 text-warning"></i>Tổng quan hệ thống
                    </a>
                    <a href="/project1/Product/categories" class="nav-link p-3 text-secondary fw-semibold">
                        <i class="fas fa-tags me-2 text-success"></i>Quản lý danh mục
                    </a>
                    <a href="/project1/Product/orders" class="nav-link p-3 bg-light text-dark fw-bold active border-start border-warning border-3">
                        <i class="fas fa-shopping-bag me-2 text-danger"></i>Quản lý đơn hàng
                    </a>
                    <hr class="my-2 opacity-50">
                    <a href="/project1/Product/dashboard" class="nav-link p-3 text-dark fw-semibold">
                        <i class="fas fa-arrow-left me-2"></i>Quay lại trang chủ
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9 col-md-8">
            <h3 class="fw-bold text-dark text-uppercase mb-4">
                <i class="fas fa-shopping-bag text-warning me-2"></i>Danh sách đơn hàng hệ thống
            </h3>

            <!-- Bộ lọc trạng thái (Đã sửa lại đường dẫn đồng bộ hệ thống) -->
            <div class="mb-3 d-flex gap-2 flex-wrap align-items-center">
                <span class="text-muted small fw-semibold me-1">Lọc theo:</span>
                <?php
                $filterStatus = $_GET['filter'] ?? 'all';
                $filters = [
                    'all'        => ['label' => 'Tất cả đơn',     'class' => 'btn-dark'],
                    'pending'    => ['label' => 'Chờ xác nhận',   'class' => 'btn-secondary'],
                    'processing' => ['label' => 'Đang xử lý',     'class' => 'btn-primary'],
                    'shipped'    => ['label' => 'Đang giao',       'class' => 'btn-info'],
                    'done'       => ['label' => 'Hoàn thành',      'class' => 'btn-success'],
                ];
                foreach ($filters as $key => $f):
                    $active = ($filterStatus === $key) ? 'active fw-bold' : 'opacity-75';
                ?>
                <a href="/project1/Product/orders?filter=<?= $key ?>"
                   class="btn btn-sm <?= $f['class'] ?> rounded-pill px-3 <?= $active ?>">
                    <?= $f['label'] ?>
                </a>
                <?php endforeach; ?>
            </div>

            <div class="card border-0 shadow-sm p-4" style="border-radius: 12px;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted small">Tong <?php echo (int)($totalOrders ?? count($orders)); ?> don hang</span>
                    <span class="text-muted small">Trang <?php echo (int)($page ?? 1); ?>/<?php echo (int)($totalPages ?? 1); ?></span>
                </div>
                <div class="table-responsive bg-white rounded-3 border">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr class="small text-uppercase">
                                <th class="text-center py-3" style="width:80px;">Mã Đơn</th>
                                <th class="py-3">Khách hàng</th>
                                <th class="py-3">Số điện thoại</th>
                                <th class="py-3" style="width:140px;">Trạng thái</th>
                                <th class="py-3" style="width:220px;">Cập nhật trạng thái</th>
                                <th class="text-center py-3" style="width:120px;">Chi tiết</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Thực hiện lọc mảng đơn hàng dựa trên trạng thái đã chọn
                            $displayOrders = $orders;
                            if ($filterStatus !== 'all') {
                                $displayOrders = array_filter($orders, fn($o) => ($o->status ?? 'pending') === $filterStatus);
                            }
                            ?>
                            
                            <?php if (empty($displayOrders)): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
                                        <i class="fas fa-inbox fa-3x mb-3 d-block opacity-25" style="color: var(--bs-warning);"></i>
                                        Không tìm thấy đơn hàng nào thuộc trạng thái này.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($displayOrders as $ord):
                                    $currentStatus = $ord->status ?? 'pending';
                                    $badge = statusBadge($currentStatus);
                                ?>
                                <tr>
                                    <td class="text-center fw-bold text-secondary">#<?= $ord->id ?></td>
                                    <td>
                                        <span class="fw-bold text-dark"><?= htmlspecialchars($ord->name, ENT_QUOTES, 'UTF-8') ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border px-2 py-1"><?= htmlspecialchars($ord->phone, ENT_QUOTES, 'UTF-8') ?></span>
                                    </td>
                                    <!-- Cột hiển thị Badge hiện tại -->
                                    <td>
                                        <span class="badge badge-status <?= $badge['bg'] ?>">
                                            <i class="fas <?= $badge['icon'] ?> me-1"></i><?= $badge['label'] ?>
                                        </span>
                                    </td>
                                    <!-- Cột form xử lý chuyển đổi trạng thái -->
                                    <td>
                                        <?php if ($currentStatus !== 'done'): ?>
                                        <!-- Đã truyền kèm tham số ?filter vào action của form để giữ bộ lọc sau khi redirect -->
                                        <form method="POST" action="/project1/Product/updateOrderStatus?filter=<?= $filterStatus ?>"
                                              class="d-flex align-items-center gap-2"
                                              onsubmit="return confirm('Xác nhận cập nhật trạng thái đơn #<?= $ord->id ?>?')">
                                            
                                            <input type="hidden" name="order_id" value="<?= $ord->id ?>">
                                            <select name="status" class="form-select form-select-sm status-select">
                                                <option value="pending"    <?= $currentStatus === 'pending'    ? 'selected' : '' ?>>⏳ Chờ xác nhận</option>
                                                <option value="processing" <?= $currentStatus === 'processing' ? 'selected' : '' ?>>🔄 Đang xử lý</option>
                                                <option value="shipped"    <?= $currentStatus === 'shipped'    ? 'selected' : '' ?>>🚚 Đang giao</option>
                                                <option value="done"       <?= $currentStatus === 'done'       ? 'selected' : '' ?>>✅ Hoàn thành</option>
                                            </select>
                                            <button type="submit" class="btn btn-sm btn-warning btn-status-save fw-bold text-dark">
                                                Lưu
                                            </button>
                                        </form>
                                        <?php else: ?>
                                            <span class="text-success small fw-semibold"><i class="fas fa-lock me-1"></i>Đã đóng đơn hoàn thành</span>
                                        <?php endif; ?>
                                    </td>
                                    <!-- Nút xem chi tiết sản phẩm đơn hàng -->
                                    <td class="text-center">
                                        <a href="/project1/Product/orderDetail?id=<?= $ord->id ?>"
                                           class="btn btn-sm btn-warning fw-bold rounded-pill px-3 shadow-sm">
                                            <i class="fas fa-eye me-1"></i>Xem đơn
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if (($totalPages ?? 1) > 1): ?>
                    <nav class="mt-4">
                        <ul class="pagination justify-content-center mb-0">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?php echo $i == ($page ?? 1) ? 'active' : ''; ?>">
                                    <a class="page-link" href="/project1/Product/orders?filter=<?php echo urlencode($filterStatus); ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
