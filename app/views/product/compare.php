<?php
$compareCount = is_array($products ?? null) ? count($products) : 0;
$allKeys = [];
foreach (($specMap ?? []) as $specs) {
    foreach ($specs as $spec) {
        $allKeys[$spec->spec_key] = true;
    }
}
?>

<style>
.compare-shell { max-width: 1180px; }
.compare-hero { background: linear-gradient(135deg, #fff7cc, #ffffff 58%); border: 1px solid #ffe58a; border-radius: 18px; }
.compare-card { border: 1px solid #edf0f2; border-radius: 16px; transition: transform .2s ease, box-shadow .2s ease; }
.compare-card:hover { transform: translateY(-2px); box-shadow: 0 10px 28px rgba(15, 23, 42, .08); }
.compare-img { height: 138px; width: 100%; object-fit: contain; }
.spec-table th { width: 210px; background: #f8fafc; color: #475569; font-size: .88rem; }
.spec-table td { min-width: 220px; vertical-align: top; }
.sticky-spec { position: sticky; left: 0; z-index: 1; }
.winner-chip { background: #fff3bf; color: #7c5a00; border-radius: 999px; padding: 4px 10px; font-size: .74rem; font-weight: 700; }
</style>

<div class="container compare-shell py-4">
    <div class="compare-hero p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap">
            <div>
                <div class="text-uppercase small fw-bold text-warning mb-1">Trung tâm so sánh</div>
                <h3 class="fw-bold text-dark mb-1">So sánh sản phẩm</h3>
                <p class="text-muted mb-0">Đặt tối đa 4 sản phẩm cần cân nhắc vào cùng một bảng để xem giá, thương hiệu, bảo hành và cấu hình.</p>
            </div>
            <a href="/project1/Product/index" class="btn btn-dark rounded-pill px-4 fw-semibold">
                <i class="fas fa-plus me-2"></i>Chọn thêm
            </a>
        </div>
    </div>

    <?php if ($compareCount === 0): ?>
        <div class="bg-white rounded-4 border shadow-sm text-center py-5 px-3">
            <i class="fas fa-balance-scale fa-4x text-warning mb-3"></i>
            <h5 class="fw-bold text-dark">Chưa có sản phẩm nào để so sánh</h5>
            <p class="text-muted mb-4">Hãy thêm các sản phẩm bạn đang phân vân, trang này sẽ giúp nhìn khác biệt rõ hơn.</p>
            <a href="/project1/Product/index" class="btn btn-warning rounded-pill px-4 fw-bold text-dark">Đi chọn sản phẩm</a>
        </div>
    <?php else: ?>
        <?php if (isset($_SESSION['error_msg'])): ?>
            <div class="alert alert-danger small"><?php echo htmlspecialchars($_SESSION['error_msg'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['error_msg']); ?></div>
        <?php endif; ?>

        <div class="row g-3 mb-4">
            <?php foreach ($products as $product): ?>
                <?php
                $imgName = !empty($product->image) ? $product->image : 'default.jpg';
                $imagePath = (strpos($imgName, 'public/images/') !== false || strpos($imgName, 'uploads/') !== false)
                    ? "/project1/" . $imgName
                    : "/project1/public/images/" . $imgName;
                ?>
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="compare-card bg-white h-100 p-3">
                        <div class="d-flex justify-content-end">
                            <form method="POST" action="/project1/Product/toggleCompare">
                                <input type="hidden" name="product_id" value="<?= (int)$product->id ?>">
                                <button type="submit" class="btn btn-sm btn-light border rounded-circle" title="Bỏ khỏi so sánh">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                        </div>
                        <a href="/project1/Product/detail?id=<?= (int)$product->id ?>" class="text-decoration-none">
                            <img src="<?= htmlspecialchars($imagePath, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8') ?>" class="compare-img mb-3">
                            <h6 class="fw-bold text-dark mb-2" style="min-height: 42px;"><?= htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8') ?></h6>
                        </a>
                        <div class="text-muted small mb-2"><?= htmlspecialchars($product->brand_name ?? 'Đang cập nhật', ENT_QUOTES, 'UTF-8') ?></div>
                        <div class="fw-bold text-danger fs-5"><?= number_format((float)$product->price, 0, ',', '.') ?>đ</div>
                        <?php if (!empty($product->sale_percent)): ?>
                            <span class="winner-chip mt-2 d-inline-block">Giảm <?= (int)$product->sale_percent ?>%</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="bg-white rounded-4 border shadow-sm p-3 p-md-4">
            <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap mb-3">
                <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-list-check text-warning me-2"></i>Bảng thông số</h5>
                <span class="text-muted small"><?= $compareCount ?>/4 sản phẩm đang được so sánh</span>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered align-middle spec-table mb-0">
                    <tbody>
                        <tr>
                            <th class="sticky-spec">Sản phẩm</th>
                            <?php foreach ($products as $product): ?>
                                <td class="fw-bold text-dark"><?= htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8') ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <th class="sticky-spec">Thương hiệu</th>
                            <?php foreach ($products as $product): ?>
                                <td><?= htmlspecialchars($product->brand_name ?? 'Đang cập nhật', ENT_QUOTES, 'UTF-8') ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <th class="sticky-spec">Danh mục</th>
                            <?php foreach ($products as $product): ?>
                                <td><?= htmlspecialchars($product->category_name ?? 'Đang cập nhật', ENT_QUOTES, 'UTF-8') ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <th class="sticky-spec">Giá bán</th>
                            <?php foreach ($products as $product): ?>
                                <td class="fw-bold text-danger"><?= number_format((float)$product->price, 0, ',', '.') ?>đ</td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <th class="sticky-spec">Bảo hành</th>
                            <?php foreach ($products as $product): ?>
                                <td><?= (int)($product->warranty_months ?? 12) ?> tháng</td>
                            <?php endforeach; ?>
                        </tr>
                        <?php foreach (array_keys($allKeys) as $key): ?>
                            <tr>
                                <th class="sticky-spec"><?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?></th>
                                <?php foreach ($products as $product): ?>
                                    <?php
                                    $value = 'Đang cập nhật';
                                    foreach (($specMap[$product->id] ?? []) as $spec) {
                                        if ($spec->spec_key === $key && trim($spec->spec_value) !== '') {
                                            $value = $spec->spec_value;
                                            break;
                                        }
                                    }
                                    ?>
                                    <td><?= htmlspecialchars($value, ENT_QUOTES, 'UTF-8') ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>
