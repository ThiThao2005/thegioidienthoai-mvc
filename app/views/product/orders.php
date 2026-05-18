<div class="container-fluid px-4 py-4">
    <div class="row g-4">
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

        <div class="col-lg-9 col-md-8">
            <h3 class="fw-bold text-dark text-uppercase mb-4">
                <i class="fas fa-shopping-bag text-warning me-2"></i>Danh sách đơn hàng hệ thống
            </h3>

            <div class="card border-0 shadow-sm p-4" style="border-radius: 12px;">
                <div class="table-responsive bg-white rounded-3 border">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr class="small text-uppercase">
                                <th class="text-center py-3" style="width: 90px;">Mã Đơn</th>
                                <th class="py-3">Khách hàng</th>
                                <th class="py-3">Số điện thoại</th>
                                <th class="py-3">Địa chỉ giao hàng</th>
                                <th class="text-center py-3" style="width: 220px;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($orders)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Chưa có đơn hàng nào được đặt.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($orders as $ord): ?>
                                <tr>
                                    <td class="text-center fw-bold text-secondary">#<?= $ord->id ?></td>
                                    <td>
                                        <span class="fw-bold text-dark"><?= htmlspecialchars($ord->name, ENT_QUOTES, 'UTF-8') ?></span>
                                    </td>
                                    <td><span class="badge bg-light text-dark border px-2 py-1"><?= htmlspecialchars($ord->phone, ENT_QUOTES, 'UTF-8') ?></span></td>
                                    <td class="text-muted text-truncate" style="max-width: 250px;">
                                        <?= htmlspecialchars($ord->address, ENT_QUOTES, 'UTF-8') ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex gap-2 justify-content-center">
                                            <a href="/project1/Product/orderDetail?id=<?= $ord->id ?>" class="btn btn-sm btn-warning fw-bold rounded-pill px-3 shadow-sm">
                                                <i class="fas fa-eye me-1"></i>Chi tiết
                                            </a>
                                            <a href="/project1/Product/deleteOrder?id=<?= $ord->id ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="return confirm('Bạn có chắc chắn muốn xóa đơn hàng này?');">
                                                <i class="fas fa-trash me-1"></i>Xóa
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>