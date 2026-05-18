<div class="container py-4">
    <div class="mb-4">
        <a href="/project1/Product/orders" class="btn btn-light border rounded-pill px-3 fw-semibold text-secondary">
            <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách đơn hàng
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 12px; bg-light">
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
                    <small class="text-muted d-block">Địa chỉ nhận nhận hàng:</small>
                    <span class="text-dark fw-semibold"><?= htmlspecialchars($order->address, ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm p-4" style="border-radius: 12px;">
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
                                $subtotal = $item->price * $item->quantity;
                                $total_order += $subtotal;
                            ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="/project1/public/images/<?= !empty($item->product_image) ? $item->product_image : 'default.jpg' ?>" 
                                             alt="" class="rounded border" style="width: 55px; hieght: 55px; object-fit: cover;">
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
                                <td class="text-end fw-extrabold text-danger fs-4 py-3"><?= number_format($total_order, 0, ',', '.') ?>đ</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>