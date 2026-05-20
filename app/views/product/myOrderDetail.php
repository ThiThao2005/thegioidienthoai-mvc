<?php
$statusConfig = [
    'pending' => ['label' => 'Chờ xác nhận', 'bg' => 'bg-secondary-subtle text-secondary', 'icon' => 'fa-clock'],
    'processing' => ['label' => 'Đang xử lý', 'bg' => 'bg-primary-subtle text-primary', 'icon' => 'fa-spinner fa-spin'],
    'shipped' => ['label' => 'Đang giao', 'bg' => 'bg-info-subtle text-info-dark', 'icon' => 'fa-truck'],
    'done' => ['label' => 'Hoàn thành', 'bg' => 'bg-success-subtle text-success', 'icon' => 'fa-check-circle'],
    'cancelled' => ['label' => 'Đã hủy', 'bg' => 'bg-danger-subtle text-danger', 'icon' => 'fa-times-circle']
];

$statusKey = $order->status ?? 'pending';
$cfg = $statusConfig[$statusKey] ?? $statusConfig['pending'];
$orderDate = isset($order->created_at) ? date('d/m/Y - H:i', strtotime($order->created_at)) : 'Đang cập nhật';
?>

<style>
.detail-card { border-radius: 16px; border: 1px solid #e9ecef; }
.product-img { width: 74px; height: 74px; object-fit: contain; border-radius: 12px; border: 1px solid #eee; background: #fff; }
.badge-custom { font-size: 0.85rem; padding: 6px 14px; border-radius: 20px; font-weight: 700; }
.text-info-dark { color: #0284c7; }
.bg-info-subtle { background-color: #e0f2fe; }
.step-icon { width: 40px; height: 40px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 1.1rem; }
.review-box { background: #fff9db; border: 1px solid #ffe58a; }
.order-item:last-child { border-bottom: 0 !important; padding-bottom: 0 !important; }
</style>

<div class="container py-5" style="max-width: 980px;">
    <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
            <a href="/project1/Product/myOrders" class="btn btn-sm btn-light border rounded-pill px-3 fw-semibold text-secondary">
                <i class="fas fa-arrow-left me-1"></i> Quay lại
            </a>
            <h4 class="fw-bold text-dark mb-0">Chi tiết đơn hàng #<?= (int)$order->id ?></h4>
        </div>
        <span class="badge badge-custom <?= $cfg['bg'] ?> d-inline-flex align-items-center gap-1">
            <i class="fas <?= $cfg['icon'] ?>"></i> <?= $cfg['label'] ?>
        </span>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card detail-card border-0 shadow-sm bg-white p-4 mb-4">
                <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark"><i class="fas fa-shopping-basket text-warning me-2"></i>Sản phẩm đã đặt</h5>
                        <?php if ($statusKey === 'done'): ?>
                            <div class="text-muted small">Đơn đã hoàn thành, bạn có thể đánh giá từng sản phẩm ngay bên dưới.</div>
                        <?php endif; ?>
                    </div>
                    <?php if ($statusKey === 'done'): ?>
                        <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2">Đã nhận hàng</span>
                    <?php endif; ?>
                </div>

                <?php if (isset($_SESSION['error_msg'])): ?>
                    <div class="alert alert-danger small"><?php echo htmlspecialchars($_SESSION['error_msg'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['error_msg']); ?></div>
                <?php endif; ?>
                <?php if (isset($_SESSION['success_msg'])): ?>
                    <div class="alert alert-success small"><?php echo htmlspecialchars($_SESSION['success_msg'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['success_msg']); ?></div>
                <?php endif; ?>

                <div class="d-flex flex-column gap-3">
                    <?php
                    $subTotal = 0;
                    foreach ($details as $item):
                        $itemPrice = (float)($item->price ?? 0);
                        $itemQty = (int)($item->quantity ?? 1);
                        $itemTotal = $itemPrice * $itemQty;
                        $subTotal += $itemTotal;
                        $itemName = $item->product_name ?? $item->name ?? 'Sản phẩm không rõ tên';
                        $itemImage = $item->product_image ?? $item->image ?? 'default.jpg';
                        $productImg = (strpos($itemImage, 'public/images/') !== false || strpos($itemImage, 'uploads/') !== false)
                            ? '/project1/' . $itemImage
                            : '/project1/public/images/' . $itemImage;
                    ?>
                        <div class="order-item pb-3 border-bottom border-light">
                            <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="<?= htmlspecialchars($productImg, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($itemName, ENT_QUOTES, 'UTF-8') ?>" class="product-img">
                                    <div>
                                        <h6 class="fw-bold text-dark mb-1" style="font-size: 0.96rem;"><?= htmlspecialchars($itemName, ENT_QUOTES, 'UTF-8') ?></h6>
                                        <span class="text-muted small">Số lượng: <strong><?= $itemQty ?></strong></span>
                                        <span class="text-muted small mx-2">|</span>
                                        <span class="text-muted small">Đơn giá: <?= number_format($itemPrice, 0, ',', '.') ?>đ</span>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="fw-bold text-dark"><?= number_format($itemTotal, 0, ',', '.') ?>đ</span>
                                </div>
                            </div>

                            <?php if ($statusKey === 'done' && !empty($item->product_id)): ?>
                                <div class="review-box mt-3 p-3 rounded-3">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                                        <div>
                                            <div class="fw-bold text-dark">Đánh giá sản phẩm vừa nhận</div>
                                            <div class="text-muted small">Nếu bạn đã đánh giá trước đó, gửi lại sẽ cập nhật nội dung mới.</div>
                                        </div>
                                        <a href="/project1/Product/detail?id=<?= (int)$item->product_id ?>" class="btn btn-sm btn-outline-secondary rounded-pill">
                                            Xem sản phẩm
                                        </a>
                                    </div>
                                    <form method="POST" action="/project1/Product/saveReview" class="row g-2 align-items-end">
                                        <input type="hidden" name="product_id" value="<?= (int)$item->product_id ?>">
                                        <input type="hidden" name="redirect" value="/project1/Product/myOrderDetail?id=<?= (int)$order->id ?>">
                                        <div class="col-md-3">
                                            <label class="form-label small fw-semibold text-secondary">Số sao</label>
                                            <select name="rating" class="form-select form-select-sm" required>
                                                <option value="5">5 sao - Rất tốt</option>
                                                <option value="4">4 sao - Tốt</option>
                                                <option value="3">3 sao - Tạm ổn</option>
                                                <option value="2">2 sao - Chưa ổn</option>
                                                <option value="1">1 sao - Không hài lòng</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold text-secondary">Nhận xét</label>
                                            <input type="text" name="comment" class="form-control form-control-sm" minlength="5" placeholder="Ví dụ: máy đẹp, giao nhanh, pin ổn..." required>
                                        </div>
                                        <div class="col-md-3">
                                            <button type="submit" class="btn btn-warning btn-sm w-100 fw-bold rounded-pill text-dark">
                                                Gửi đánh giá
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

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

            <?php if ($statusKey === 'pending'): ?>
                <div class="card detail-card border-0 shadow-sm bg-white p-4">
                    <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
                        <div>
                            <h6 class="fw-bold text-dark mb-1">Bạn muốn hủy đơn?</h6>
                            <p class="text-muted small mb-0">Chỉ có thể hủy khi đơn vẫn đang chờ xác nhận.</p>
                        </div>
                        <form method="POST" action="/project1/Product/cancelOrder" onsubmit="return confirm('Bạn chắc chắn muốn hủy đơn hàng này?')">
                            <input type="hidden" name="order_id" value="<?= (int)$order->id ?>">
                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-semibold">Hủy đơn hàng</button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-lg-4">
            <div class="card detail-card border-0 shadow-sm bg-white p-4 mb-4">
                <h5 class="fw-bold mb-4 text-dark"><i class="fas fa-map-marker-alt text-warning me-2"></i>Địa chỉ nhận hàng</h5>
                <div class="mb-3">
                    <span class="text-muted small d-block mb-1">Người nhận hàng</span>
                    <span class="fw-bold text-dark"><?= htmlspecialchars($order->name ?? 'Chưa cập nhật', ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <div class="mb-3">
                    <span class="text-muted small d-block mb-1">Số điện thoại</span>
                    <span class="fw-semibold text-dark"><?= htmlspecialchars($order->phone ?? 'Chưa cập nhật', ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <div>
                    <span class="text-muted small d-block mb-1">Địa chỉ giao tới</span>
                    <span class="fw-semibold text-secondary" style="font-size: 0.9rem;"><?= htmlspecialchars($order->address ?? 'Chưa cập nhật', ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            </div>

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
