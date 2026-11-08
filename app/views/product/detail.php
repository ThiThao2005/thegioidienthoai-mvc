<?php
/**
 * View: Product/detail.php  (Nâng cấp)
 *
 * Biến cần truyền từ controller:
 *   $product         — object sản phẩm
 *   $productImages   — array ảnh gallery
 *   $productVariants — array biến thể
 *   $productSpecs    — array thông số kỹ thuật
 *   $productQuestions— array hỏi đáp
 *   $reviews         — array đánh giá
 *   $ratingSummary   — object { avg_rating, total_reviews }
 *   $isWishlisted    — bool
 *   $canReview       — bool
 */

if (!isset($product)) {
    echo "<div class='container mt-5 alert alert-danger'>Không tìm thấy thông tin sản phẩm!</div>";
    return;
}

$imgName   = !empty($product->image) ? $product->image : 'default.jpg';
$imagePath = (strpos($imgName, 'public/images/') !== false || strpos($imgName, 'uploads/') !== false)
    ? '/' . $imgName
    : '/public/images/' . $imgName;

$avgRating   = (float)($ratingSummary->avg_rating ?? 0);
$totalReview = (int)($ratingSummary->total_reviews ?? 0);

// Xây dựng mảng tất cả ảnh (ảnh chính + gallery)
$allImages   = [['src' => $imagePath, 'alt' => $product->name]];
if (!empty($productImages)) {
    foreach ($productImages as $gi) {
        $allImages[] = ['src' => '/public/images/' . htmlspecialchars($gi->image, ENT_QUOTES, 'UTF-8'), 'alt' => $product->name];
    }
}

// Tính giá sau giảm
$salePercent = (int)($product->sale_percent ?? 0);
$priceOrig   = (float)$product->price;
$priceFinal  = $salePercent > 0 ? round($priceOrig * (1 - $salePercent / 100)) : $priceOrig;
$priceSaved  = $priceOrig - $priceFinal;
?>

<!————— CSS —————>
<style>
/* ===== RESET & BASE ===== */
.pd-wrap * { box-sizing: border-box; }
.pd-wrap { font-family: 'Segoe UI', system-ui, sans-serif; color: #1a1a1a; }

/* ===== BREADCRUMB ===== */
.pd-breadcrumb { font-size: .82rem; color: #888; margin-bottom: 1.5rem; }
.pd-breadcrumb a { color: #555; text-decoration: none; }
.pd-breadcrumb a:hover { color: #d70018; }
.pd-breadcrumb span { margin: 0 .4rem; }

/* ===== GRID LAYOUT ===== */
.pd-main-grid {
    display: grid;
    grid-template-columns: minmax(0,1fr) minmax(0,1fr);
    gap: 2.5rem;
    align-items: start;
}
@media (max-width: 768px) {
    .pd-main-grid { grid-template-columns: 1fr; gap: 1.5rem; }
}

/* ===== GALLERY ===== */
.pd-gallery { position: sticky; top: 80px; }
.pd-gallery-main {
    background: #f8f8f8;
    border-radius: 16px;
    overflow: hidden;
    position: relative;
    aspect-ratio: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 12px;
    border: 1px solid #eee;
    cursor: zoom-in;
}
.pd-gallery-main img {
    max-width: 100%; max-height: 100%;
    object-fit: contain;
    transition: transform .4s ease;
    padding: 1rem;
}
.pd-gallery-main:hover img { transform: scale(1.06); }

/* Nút prev/next gallery */
.pd-gallery-btn {
    position: absolute; top: 50%; transform: translateY(-50%);
    background: rgba(255,255,255,.92); border: 1px solid #ddd;
    border-radius: 50%; width: 36px; height: 36px;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; font-size: 1rem; color: #333;
    transition: all .2s; z-index: 2; line-height: 1;
}
.pd-gallery-btn:hover { background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,.12); }
.pd-gallery-btn.prev { left: 10px; }
.pd-gallery-btn.next { right: 10px; }

/* Badge giảm giá trên gallery */
.pd-discount-badge {
    position: absolute; top: 12px; left: 12px;
    background: #d70018; color: #fff;
    font-size: .75rem; font-weight: 700;
    padding: 4px 10px; border-radius: 20px; z-index: 2;
}

/* Thumbnails */
.pd-thumbs {
    display: flex; gap: 8px; overflow-x: auto;
    scrollbar-width: none; padding-bottom: 4px;
}
.pd-thumbs::-webkit-scrollbar { display: none; }
.pd-thumb {
    flex: 0 0 60px; height: 60px;
    border: 2px solid transparent;
    border-radius: 10px; overflow: hidden;
    background: #f5f5f5; cursor: pointer;
    transition: all .2s;
    display: flex; align-items: center; justify-content: center;
}
.pd-thumb img { max-width: 100%; max-height: 100%; object-fit: contain; padding: 4px; }
.pd-thumb.active { border-color: #d70018; }
.pd-thumb:hover { border-color: #aaa; }

/* ===== PRODUCT INFO PANEL ===== */
.pd-info { display: flex; flex-direction: column; gap: 1rem; }

.pd-badges { display: flex; flex-wrap: wrap; gap: 6px; }
.pd-badge {
    font-size: .75rem; font-weight: 600; padding: 4px 12px;
    border-radius: 20px; display: inline-flex; align-items: center; gap: 5px;
}
.pd-badge-brand  { background: #1a1a1a; color: #fff; }
.pd-badge-cat    { background: #f0f0f0; color: #555; }
.pd-badge-warr   { background: #e8f5e9; color: #2e7d32; }
.pd-badge-feat   { background: #fff8e1; color: #e65100; }

.pd-title { font-size: 1.5rem; font-weight: 700; line-height: 1.35; margin: 0; color: #111; }

/* Rating tóm tắt */
.pd-rating-row { display: flex; align-items: center; gap: 10px; }
.pd-stars-display { color: #f5a623; font-size: 1rem; letter-spacing: 2px; }
.pd-rating-avg { font-size: 1.1rem; font-weight: 700; color: #f5a623; }
.pd-rating-count { font-size: .82rem; color: #888; }

/* Giá */
.pd-price-block {
    background: linear-gradient(135deg, #fff5f5 0%, #fff9e6 100%);
    border: 1px solid #fdd; border-radius: 14px; padding: 1rem 1.25rem;
}
.pd-price-main { font-size: 2rem; font-weight: 800; color: #d70018; line-height: 1; }
.pd-price-orig { font-size: .9rem; color: #aaa; text-decoration: line-through; margin-left: 8px; }
.pd-price-saved { font-size: .78rem; color: #2e7d32; font-weight: 600; margin-top: 4px; }
.pd-installment {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: .75rem; font-weight: 700;
    background: #fff; border: 1.5px solid #d70018; color: #d70018;
    padding: 3px 10px; border-radius: 20px; margin-top: 8px;
}

/* Khuyến mãi */
.pd-promo {
    border: 1.5px solid #ffd400; border-radius: 14px; overflow: hidden;
}
.pd-promo-header {
    background: #ffd400; color: #111;
    font-size: .8rem; font-weight: 700;
    padding: 8px 14px; display: flex; align-items: center; gap: 6px;
}
.pd-promo-body { background: #fffdf0; padding: 10px 14px; }
.pd-promo-item {
    font-size: .82rem; color: #333;
    padding: 3px 0; display: flex; align-items: flex-start; gap: 8px; line-height: 1.5;
}
.pd-promo-item i { color: #2e7d32; font-size: 1rem; flex-shrink: 0; margin-top: 1px; }

/* Biến thể */
.pd-variants-block {}
.pd-variants-label { font-size: .82rem; font-weight: 700; color: #555; text-transform: uppercase; letter-spacing: .05em; margin-bottom: 8px; }
.pd-variant-grid { display: flex; flex-wrap: wrap; gap: 8px; }
.pd-variant-btn {
    border: 1.5px solid #ddd; border-radius: 10px;
    padding: 8px 12px; background: #fff; cursor: pointer;
    transition: all .2s; text-align: left; min-width: 100px;
}
.pd-variant-btn:hover { border-color: #aaa; }
.pd-variant-btn.active { border-color: #d70018; background: #fff5f5; }
.pd-variant-name { font-size: .78rem; font-weight: 600; color: #222; display: block; }
.pd-variant-price { font-size: .78rem; color: #d70018; font-weight: 700; display: block; margin-top: 2px; }
.pd-variant-stock { font-size: .7rem; color: #888; display: block; }

/* Actions */
.pd-actions { display: flex; flex-direction: column; gap: 10px; }
.pd-btn {
    display: flex; align-items: center; justify-content: center; gap: 8px;
    border: none; cursor: pointer; font-weight: 700;
    border-radius: 50px; font-size: .95rem; padding: 14px 20px;
    transition: all .25s; text-decoration: none; width: 100%;
}
.pd-btn-primary {
    background: #ffd400; color: #111;
    box-shadow: 0 4px 16px rgba(255,212,0,.3);
}
.pd-btn-primary:hover {
    background: #e6be00; transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255,212,0,.4); color: #111;
}
.pd-btn-primary .pd-btn-sub { font-size: .72rem; font-weight: 400; opacity: .75; display: block; margin-top: 2px; }
.pd-btn-secondary {
    background: #fff; border: 1.5px solid #ddd; color: #333;
}
.pd-btn-secondary:hover { border-color: #aaa; background: #fafafa; color: #111; }
.pd-btn-wishlist { border-color: #e91e63; color: #e91e63; }
.pd-btn-wishlist:hover { background: #fce4ec; border-color: #e91e63; color: #e91e63; }
.pd-btn-compare  { border-color: #333; color: #333; }
.pd-btn-compare:hover  { background: #f5f5f5; }

/* Admin bar */
.pd-admin-bar {
    display: flex; gap: 8px; align-items: center; flex-wrap: wrap;
    border-top: 1px solid #eee; padding-top: .75rem; margin-top: .5rem;
}
.pd-admin-label { font-size: .75rem; color: #888; margin-right: auto; display: flex; align-items: center; gap: 5px; }

/* ===== TABS ===== */
.pd-tabs { margin-top: 2.5rem; }
.pd-tab-nav {
    display: flex; border-bottom: 2px solid #eee;
    overflow-x: auto; gap: 0; scrollbar-width: none;
}
.pd-tab-nav::-webkit-scrollbar { display: none; }
.pd-tab-btn {
    padding: 12px 20px; font-size: .875rem; font-weight: 600; color: #888;
    border: none; background: none; cursor: pointer; white-space: nowrap;
    border-bottom: 2.5px solid transparent; margin-bottom: -2px;
    transition: all .2s;
}
.pd-tab-btn.active { color: #d70018; border-bottom-color: #d70018; }
.pd-tab-btn:hover:not(.active) { color: #333; }
.pd-tab-content { display: none; padding: 1.5rem 0; }
.pd-tab-content.active { display: block; }

/* ===== DESCRIPTION ===== */
.pd-desc-block {
    background: #fafafa; border: 1px solid #eee; border-radius: 14px;
    padding: 1.5rem; font-size: .93rem; color: #444;
    line-height: 1.85; white-space: pre-line;
}

/* ===== SPECS TABLE ===== */
.pd-specs-table { width: 100%; border-collapse: collapse; font-size: .88rem; }
.pd-specs-table tr:nth-child(even) td,
.pd-specs-table tr:nth-child(even) th { background: #f9f9f9; }
.pd-specs-table th {
    width: 32%; padding: 10px 14px; color: #555; font-weight: 600;
    text-align: left; border-bottom: 1px solid #eee;
}
.pd-specs-table td {
    padding: 10px 14px; color: #222;
    border-bottom: 1px solid #eee;
}

/* ===== Q&A ===== */
.pd-qa-form { background: #f9f9f9; border: 1px solid #eee; border-radius: 14px; padding: 1.25rem; margin-bottom: 1.5rem; }
.pd-qa-form .form-control { border-radius: 10px; border: 1px solid #ddd; font-size: .875rem; }
.pd-qa-form .form-control:focus { border-color: #ffd400; box-shadow: 0 0 0 3px rgba(255,212,0,.15); outline: none; }
.pd-qa-item { border-bottom: 1px solid #f0f0f0; padding: 1rem 0; }
.pd-qa-item:last-child { border-bottom: none; }
.pd-qa-user { font-weight: 700; font-size: .88rem; color: #222; }
.pd-qa-text { color: #555; font-size: .88rem; margin: 4px 0 0; line-height: 1.6; }
.pd-qa-answer {
    background: #fffdf0; border-left: 3px solid #ffd400;
    border-radius: 0 8px 8px 0; padding: 8px 12px;
    font-size: .85rem; color: #333; margin-top: 8px;
}
.pd-qa-answer strong { color: #e65100; }
.pd-qa-answer-form { display: flex; gap: 8px; margin-top: 8px; }
.pd-qa-answer-form input { border-radius: 8px; border: 1px solid #ddd; font-size: .82rem; flex: 1; padding: 6px 10px; }

/* ===== REVIEWS ===== */
.pd-review-summary {
    display: flex; align-items: center; gap: 1.5rem;
    background: #fafafa; border: 1px solid #eee; border-radius: 14px;
    padding: 1.25rem 1.5rem; margin-bottom: 1.5rem; flex-wrap: wrap;
}
.pd-review-big { font-size: 3rem; font-weight: 800; color: #f5a623; line-height: 1; }
.pd-review-stars-big { font-size: 1.4rem; color: #f5a623; letter-spacing: 4px; }
.pd-review-count { font-size: .82rem; color: #888; margin-top: 4px; }

/* Progress bars phân phối sao */
.pd-star-bars { flex: 1; min-width: 160px; }
.pd-star-bar-row { display: flex; align-items: center; gap: 8px; margin-bottom: 4px; font-size: .75rem; }
.pd-star-bar-row span { min-width: 30px; color: #888; }
.pd-star-bar-track { flex: 1; height: 6px; background: #eee; border-radius: 3px; overflow: hidden; }
.pd-star-bar-fill  { height: 100%; background: #f5a623; border-radius: 3px; transition: width .6s; }

/* Interactive star rating */
.pd-star-rating { display: flex; gap: 4px; cursor: pointer; }
.pd-star-rating .star {
    font-size: 1.6rem; color: #ddd;
    transition: color .1s, transform .1s;
    user-select: none;
}
.pd-star-rating .star.lit    { color: #f5a623; }
.pd-star-rating .star:hover  { transform: scale(1.2); }

.pd-review-form { background: #f9f9f9; border: 1px solid #eee; border-radius: 14px; padding: 1.25rem; margin-bottom: 1.5rem; }
.pd-review-form .form-control { border-radius: 10px; border: 1px solid #ddd; font-size: .875rem; }
.pd-review-form .form-control:focus { border-color: #ffd400; box-shadow: 0 0 0 3px rgba(255,212,0,.15); outline: none; }

.pd-review-item { border-bottom: 1px solid #f0f0f0; padding: 1rem 0; }
.pd-review-item:last-child { border-bottom: none; }
.pd-review-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px; }
.pd-review-author { font-weight: 700; font-size: .9rem; }
.pd-review-date { font-size: .75rem; color: #aaa; }
.pd-review-stars-sm { color: #f5a623; font-size: .82rem; letter-spacing: 1px; }
.pd-review-comment { font-size: .875rem; color: #555; line-height: 1.65; margin-top: 4px; }

/* ===== EMPTY STATE ===== */
.pd-empty { text-align: center; padding: 2rem; color: #aaa; font-size: .9rem; }
.pd-empty i { font-size: 2rem; display: block; margin-bottom: .5rem; }

/* ===== BUTTONS chung ===== */
.btn-pd {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 18px; border-radius: 50px; border: none;
    font-size: .82rem; font-weight: 700; cursor: pointer; transition: all .2s;
}
.btn-pd-yellow { background: #ffd400; color: #111; }
.btn-pd-yellow:hover { background: #e6be00; }
.btn-pd-dark { background: #1a1a1a; color: #fff; }
.btn-pd-dark:hover { background: #333; }
.btn-pd-outline { background: #fff; border: 1.5px solid #ddd; color: #333; }
.btn-pd-outline:hover { border-color: #aaa; }
.btn-pd-danger { background: #fff; border: 1.5px solid #d70018; color: #d70018; }
.btn-pd-danger:hover { background: #fff5f5; }

/* Toast */
.pd-toast {
    position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%) translateY(20px);
    background: #222; color: #fff; padding: 10px 22px; border-radius: 30px;
    font-size: .85rem; font-weight: 600; opacity: 0;
    transition: all .35s; z-index: 9999; pointer-events: none; white-space: nowrap;
}
.pd-toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }
</style>

<!————— MARKUP —————>
<div class="pd-wrap container mt-4 pb-5">

    <!-- Breadcrumb -->
    <div class="pd-breadcrumb">
        <a href="/">Trang chủ</a><span>›</span>
        <a href="/Product/index">Sản phẩm</a><span>›</span>
        <?php if (!empty($product->category_name)): ?>
            <a href="/Product/index?category=<?php echo urlencode($product->category_name); ?>">
                <?php echo htmlspecialchars($product->category_name, ENT_QUOTES, 'UTF-8'); ?>
            </a><span>›</span>
        <?php endif; ?>
        <span style="color:#333;"><?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?></span>
    </div>

    <!-- Main grid -->
    <div class="pd-main-grid">

        <!-- CỘT TRÁI: Gallery -->
        <div class="pd-gallery">
            <div class="pd-gallery-main" id="pdGalleryMain">
                <?php if ($salePercent > 0): ?>
                    <div class="pd-discount-badge">-<?php echo $salePercent; ?>%</div>
                <?php endif; ?>
                <button class="pd-gallery-btn prev" onclick="galleryNav(-1)" aria-label="Ảnh trước" <?php echo count($allImages) <= 1 ? 'style="display:none"' : ''; ?>>&#8249;</button>
                <img id="pdMainImg"
                     src="<?php echo htmlspecialchars($allImages[0]['src'], ENT_QUOTES, 'UTF-8'); ?>"
                     alt="<?php echo htmlspecialchars($allImages[0]['alt'], ENT_QUOTES, 'UTF-8'); ?>">
                <button class="pd-gallery-btn next" onclick="galleryNav(1)" aria-label="Ảnh kế tiếp" <?php echo count($allImages) <= 1 ? 'style="display:none"' : ''; ?>>&#8250;</button>
            </div>

            <?php if (count($allImages) > 1): ?>
            <div class="pd-thumbs" id="pdThumbs">
                <?php foreach ($allImages as $idx => $img): ?>
                    <div class="pd-thumb <?php echo $idx === 0 ? 'active' : ''; ?>"
                         onclick="gallerySet(<?php echo $idx; ?>)" data-idx="<?php echo $idx; ?>">
                        <img src="<?php echo htmlspecialchars($img['src'], ENT_QUOTES, 'UTF-8'); ?>"
                             alt="<?php echo htmlspecialchars($img['alt'], ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- CỘT PHẢI: Thông tin sản phẩm -->
        <div class="pd-info">

            <!-- Badges -->
            <div class="pd-badges">
                <?php if (!empty($product->brand_name)): ?>
                    <span class="pd-badge pd-badge-brand">
                        <i class="fas fa-crown" style="font-size:.7rem;"></i>
                        <?php echo htmlspecialchars($product->brand_name, ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                <?php endif; ?>
                <?php if (!empty($product->category_name)): ?>
                    <span class="pd-badge pd-badge-cat">
                        <i class="fas fa-tag" style="font-size:.7rem;"></i>
                        <?php echo htmlspecialchars($product->category_name, ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                <?php endif; ?>
                <span class="pd-badge pd-badge-warr">
                    <i class="fas fa-shield-alt" style="font-size:.7rem;"></i>
                    Bảo hành <?php echo (int)($product->warranty_months ?? 12); ?> tháng
                </span>
                <?php if (!empty($product->featured)): ?>
                    <span class="pd-badge pd-badge-feat">
                        <i class="fas fa-star" style="font-size:.7rem;"></i> Nổi bật
                    </span>
                <?php endif; ?>
            </div>

            <!-- Tên -->
            <h1 class="pd-title"><?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?></h1>

            <!-- Rating tóm tắt -->
            <div class="pd-rating-row">
                <span class="pd-rating-avg"><?php echo number_format($avgRating, 1); ?></span>
                <span class="pd-stars-display">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <?php echo $i <= round($avgRating) ? '★' : '☆'; ?>
                    <?php endfor; ?>
                </span>
                <a href="#tab-reviews" class="pd-rating-count" onclick="openTab('reviews')" style="text-decoration:none; color:#888;">
                    (<?php echo $totalReview; ?> đánh giá)
                </a>
            </div>

            <!-- Giá -->
            <div class="pd-price-block">
                <div style="display:flex; align-items:baseline; flex-wrap:wrap; gap:4px;">
                    <span class="pd-price-main" id="pdPriceDisplay">
                        <?php echo number_format($priceFinal, 0, ',', '.'); ?>₫
                    </span>
                    <?php if ($salePercent > 0): ?>
                        <span class="pd-price-orig">
                            <?php echo number_format($priceOrig, 0, ',', '.'); ?>₫
                        </span>
                    <?php endif; ?>
                </div>
                <?php if ($priceSaved > 0): ?>
                    <div class="pd-price-saved">
                        <i class="fas fa-piggy-bank"></i>
                        Tiết kiệm <?php echo number_format($priceSaved, 0, ',', '.'); ?>₫
                    </div>
                <?php endif; ?>
                <div>
                    <span class="pd-installment">
                        <i class="fas fa-bolt" style="font-size:.72rem;"></i> Trả góp 0%
                    </span>
                </div>
            </div>

            <!-- Khuyến mãi -->
            <div class="pd-promo">
                <div class="pd-promo-header">
                    <i class="fas fa-gift"></i> Quà tặng &amp; khuyến mãi đặc biệt
                </div>
                <div class="pd-promo-body">
                    <div class="pd-promo-item">
                        <i class="fas fa-check-circle"></i>
                        Tặng gói bảo hành chính hãng toàn diện 12 tháng.
                    </div>
                    <div class="pd-promo-item">
                        <i class="fas fa-check-circle"></i>
                        Giảm thêm 500.000₫ khi thanh toán qua VNPAY-QR / MoMo.
                    </div>
                    <div class="pd-promo-item">
                        <i class="fas fa-check-circle"></i>
                        Trúng 1 trong 100 xe máy điện Wave khi mua trong tháng này.
                    </div>
                </div>
            </div>

            <!-- Biến thể -->
            <?php if (!empty($productVariants)): ?>
            <div class="pd-variants-block">
                <div class="pd-variants-label">Chọn phiên bản</div>
                <div class="pd-variant-grid" id="pdVariantGrid">
                    <?php foreach ($productVariants as $idx => $variant): ?>
                        <?php
                            $variantPrice = $priceOrig + (float)($variant->price_delta ?? 0);
                            $label = trim(($variant->color ?? '') . ' ' . ($variant->ram ?? '') . ' ' . ($variant->storage ?? ''));
                        ?>
                        <button type="button"
                                class="pd-variant-btn <?php echo $idx === 0 ? 'active' : ''; ?>"
                                data-price="<?php echo $variantPrice; ?>"
                                data-sale="<?php echo $salePercent; ?>"
                                onclick="selectVariant(this)">
                            <span class="pd-variant-name"><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></span>
                            <span class="pd-variant-price"><?php echo number_format($variantPrice * (1 - $salePercent/100), 0, ',', '.'); ?>₫</span>
                            <?php if (isset($variant->stock)): ?>
                                <span class="pd-variant-stock">
                                    <?php echo (int)$variant->stock > 0
                                        ? 'Còn ' . (int)$variant->stock . ' máy'
                                        : '⚠ Hết hàng'; ?>
                                </span>
                            <?php endif; ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Nút hành động -->
            <div class="pd-actions">
                <a href="/Product/addToCart?id=<?php echo $product->id; ?>"
                   class="pd-btn pd-btn-primary">
                    <i class="fas fa-shopping-cart fs-5"></i>
                    <span>
                        Thêm vào giỏ hàng
                        <span class="pd-btn-sub">Giao tận nơi hoặc nhận tại cửa hàng</span>
                    </span>
                </a>

                <div style="display:flex; gap:8px;">
                    <?php if (SessionHelper::isLoggedIn()): ?>
                        <form method="POST" action="/Product/toggleWishlist" style="flex:1; margin:0;">
                            <input type="hidden" name="product_id" value="<?php echo $product->id; ?>">
                            <button type="submit"
                                    class="pd-btn pd-btn-secondary pd-btn-wishlist"
                                    style="width:100%;">
                                <i class="fas fa-heart"></i>
                                <?php echo !empty($isWishlisted) ? 'Bỏ yêu thích' : 'Yêu thích'; ?>
                            </button>
                        </form>
                    <?php endif; ?>

                    <form method="POST" action="/Product/toggleCompare" style="flex:1; margin:0;">
                        <input type="hidden" name="product_id" value="<?php echo $product->id; ?>">
                        <button type="submit"
                                class="pd-btn pd-btn-secondary pd-btn-compare"
                                style="width:100%;">
                            <i class="fas fa-balance-scale"></i> So sánh
                        </button>
                    </form>
                </div>
            </div>

            <!-- Admin actions -->
            <?php if (SessionHelper::isAdmin()): ?>
                <div class="pd-admin-bar">
                    <span class="pd-admin-label">
                        <i class="fas fa-user-shield text-primary"></i> Chức năng quản trị:
                    </span>
                    <a href="/Product/edit?id=<?php echo $product->id; ?>"
                       class="btn-pd btn-pd-outline">
                        <i class="fas fa-edit"></i> Chỉnh sửa
                    </a>
                    <a href="/Product/delete?id=<?php echo $product->id; ?>"
                       class="btn-pd btn-pd-danger"
                       onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?');">
                        <i class="fas fa-trash"></i> Xóa
                    </a>
                </div>
            <?php endif; ?>

        </div><!-- /.pd-info -->
    </div><!-- /.pd-main-grid -->

    <!-- ==================== TABS ==================== -->
    <div class="pd-tabs">
        <div class="pd-tab-nav" role="tablist">
            <button class="pd-tab-btn active" onclick="openTab('desc')"    id="tab-btn-desc"     role="tab">
                <i class="fas fa-file-alt me-1 text-warning"></i> Mô tả
            </button>
            <?php if (!empty($productSpecs)): ?>
            <button class="pd-tab-btn" onclick="openTab('specs')"   id="tab-btn-specs"    role="tab">
                <i class="fas fa-microchip me-1 text-warning"></i> Thông số kỹ thuật
            </button>
            <?php endif; ?>
            <button class="pd-tab-btn" onclick="openTab('reviews')" id="tab-btn-reviews"  role="tab">
                <i class="fas fa-star me-1 text-warning"></i>
                Đánh giá <?php echo $totalReview > 0 ? "($totalReview)" : ''; ?>
            </button>
            <button class="pd-tab-btn" onclick="openTab('qa')"      id="tab-btn-qa"       role="tab">
                <i class="fas fa-comments me-1 text-warning"></i>
                Hỏi đáp <?php echo !empty($productQuestions) ? '(' . count($productQuestions) . ')' : ''; ?>
            </button>
        </div>

        <!-- Tab: Mô tả -->
        <div class="pd-tab-content active" id="tab-desc">
            <div class="pd-desc-block">
                <?php if (!empty($product->description)): ?>
                    <?php echo htmlspecialchars($product->description, ENT_QUOTES, 'UTF-8'); ?>
                <?php else: ?>
                    <span style="color:#aaa;">Thông tin chi tiết của sản phẩm đang được cập nhật.</span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Tab: Thông số kỹ thuật -->
        <?php if (!empty($productSpecs)): ?>
        <div class="pd-tab-content" id="tab-specs">
            <div class="table-responsive">
                <table class="pd-specs-table">
                    <tbody>
                        <?php foreach ($productSpecs as $spec): ?>
                            <tr>
                                <th><?php echo htmlspecialchars($spec->spec_key, ENT_QUOTES, 'UTF-8'); ?></th>
                                <td><?php echo htmlspecialchars($spec->spec_value, ENT_QUOTES, 'UTF-8'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Tab: Đánh giá -->
        <div class="pd-tab-content" id="tab-reviews">

            <!-- Rating tổng quan -->
            <div class="pd-review-summary">
                <div style="text-align:center;">
                    <div class="pd-review-big"><?php echo number_format($avgRating, 1); ?></div>
                    <div class="pd-review-stars-big">
                        <?php for ($i = 1; $i <= 5; $i++) echo $i <= round($avgRating) ? '★' : '☆'; ?>
                    </div>
                    <div class="pd-review-count"><?php echo $totalReview; ?> đánh giá</div>
                </div>

                <!-- Thanh phân phối sao (demo; nếu có data thực thì thay) -->
                <div class="pd-star-bars">
                    <?php for ($star = 5; $star >= 1; $star--): ?>
                        <div class="pd-star-bar-row">
                            <span><?php echo $star; ?> ★</span>
                            <div class="pd-star-bar-track">
                                <div class="pd-star-bar-fill" style="width:<?php echo ($totalReview > 0 && !empty($ratingDistribution[$star])) ? round($ratingDistribution[$star] / $totalReview * 100) : 0; ?>%"></div>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- Form viết đánh giá -->
            <?php if (!SessionHelper::isLoggedIn()): ?>
                <div class="alert alert-light border rounded-3 text-center" style="font-size:.88rem;">
                    <i class="fas fa-lock text-muted me-1"></i>
                    <a href="/User/login" style="color:#d70018; font-weight:700;">Đăng nhập</a>
                    để thêm sản phẩm vào danh sách yêu thích và đánh giá sau khi mua hàng.
                </div>
            <?php elseif (!empty($canReview)): ?>
                <div class="pd-review-form">
                    <h6 class="fw-bold mb-3" style="font-size:.88rem; color:#444;">✍️ Viết đánh giá của bạn</h6>
                    <form method="POST" action="/Product/saveReview">
                        <input type="hidden" name="product_id" value="<?php echo $product->id; ?>">

                        <!-- Interactive star picker -->
                        <div style="margin-bottom:12px;">
                            <label style="font-size:.8rem; color:#888; display:block; margin-bottom:6px;">Chọn số sao</label>
                            <div class="pd-star-rating" id="starPicker">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <span class="star <?php echo $i <= 5 ? 'lit' : ''; ?>"
                                          data-value="<?php echo $i; ?>"
                                          onclick="setRating(<?php echo $i; ?>)"
                                          onmouseenter="hoverRating(<?php echo $i; ?>)"
                                          onmouseleave="resetRatingHover()">★</span>
                                <?php endfor; ?>
                            </div>
                            <input type="hidden" name="rating" id="ratingInput" value="5">
                        </div>

                        <div style="margin-bottom:10px;">
                            <label style="font-size:.8rem; color:#888; display:block; margin-bottom:5px;">Bình luận</label>
                            <textarea name="comment" class="form-control" rows="3"
                                      minlength="5" maxlength="1000" required
                                      placeholder="Chia sẻ trải nghiệm của bạn về sản phẩm..."></textarea>
                        </div>

                        <div style="text-align:right;">
                            <button type="submit" class="btn-pd btn-pd-yellow">
                                <i class="fas fa-paper-plane"></i> Gửi đánh giá
                            </button>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <div class="alert alert-light border rounded-3 text-center" style="font-size:.88rem;">
                    <i class="fas fa-info-circle text-muted me-1"></i>
                    Bạn có thể đánh giá sau khi đơn hàng chứa sản phẩm này hoàn thành.
                </div>
            <?php endif; ?>

            <!-- Danh sách review -->
            <?php if (empty($reviews)): ?>
                <div class="pd-empty">
                    <i class="fas fa-star" style="color:#ddd;"></i>
                    Chưa có đánh giá nào cho sản phẩm này.
                </div>
            <?php else: ?>
                <?php foreach ($reviews as $review): ?>
                    <div class="pd-review-item">
                        <div class="pd-review-header">
                            <span class="pd-review-author">
                                <?php echo htmlspecialchars($review->fullname ?: $review->username, ENT_QUOTES, 'UTF-8'); ?>
                            </span>
                            <span class="pd-review-date">
                                <?php echo date('d/m/Y H:i', strtotime($review->created_at)); ?>
                            </span>
                        </div>
                        <div class="pd-review-stars-sm">
                            <?php for ($i = 1; $i <= 5; $i++) echo $i <= (int)$review->rating ? '★' : '☆'; ?>
                        </div>
                        <div class="pd-review-comment">
                            <?php echo nl2br(htmlspecialchars($review->comment, ENT_QUOTES, 'UTF-8')); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Tab: Hỏi đáp -->
        <div class="pd-tab-content" id="tab-qa">

            <!-- Form đặt câu hỏi -->
            <div class="pd-qa-form">
                <h6 class="fw-bold mb-3" style="font-size:.88rem; color:#444;">💬 Đặt câu hỏi về sản phẩm</h6>
                <form method="POST" action="/Product/askQuestion">
                    <input type="hidden" name="product_id" value="<?php echo $product->id; ?>">
                    <?php if (!SessionHelper::isLoggedIn()): ?>
                        <input type="text" name="customer_name" class="form-control mb-2"
                               placeholder="Tên của bạn" required>
                    <?php endif; ?>
                    <textarea name="question" class="form-control mb-2" rows="2"
                              minlength="5" required
                              placeholder="Nhập câu hỏi về sản phẩm..."></textarea>
                    <button class="btn-pd btn-pd-yellow" type="submit">
                        <i class="fas fa-paper-plane"></i> Gửi câu hỏi
                    </button>
                </form>
            </div>

            <!-- Danh sách hỏi đáp -->
            <?php if (empty($productQuestions)): ?>
                <div class="pd-empty">
                    <i class="fas fa-comments" style="color:#ddd;"></i>
                    Chưa có câu hỏi nào.
                </div>
            <?php else: ?>
                <?php foreach ($productQuestions as $q): ?>
                    <div class="pd-qa-item">
                        <div class="pd-qa-user">
                            <i class="fas fa-user-circle text-secondary me-1" style="font-size:.9rem;"></i>
                            <?php echo htmlspecialchars($q->fullname ?: $q->customer_name ?: 'Khách hàng', ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                        <div class="pd-qa-text">
                            <i class="fas fa-question-circle text-warning me-1" style="font-size:.8rem;"></i>
                            <?php echo htmlspecialchars($q->question, ENT_QUOTES, 'UTF-8'); ?>
                        </div>

                        <?php if (!empty($q->answer)): ?>
                            <div class="pd-qa-answer">
                                <strong>Cửa hàng:</strong>
                                <?php echo htmlspecialchars($q->answer, ENT_QUOTES, 'UTF-8'); ?>
                            </div>
                        <?php elseif (SessionHelper::isAdmin()): ?>
                            <form method="POST" action="/Product/answerQuestion" class="pd-qa-answer-form">
                                <input type="hidden" name="question_id" value="<?php echo $q->id; ?>">
                                <input type="hidden" name="product_id"  value="<?php echo $product->id; ?>">
                                <input type="text" name="answer"
                                       placeholder="Trả lời câu hỏi..." required>
                                <button class="btn-pd btn-pd-dark" type="submit">Trả lời</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div><!-- /.pd-tabs -->

</div><!-- /.pd-wrap -->

<!-- Toast thông báo -->
<div class="pd-toast" id="pdToast"></div>

<!-- ==================== SCRIPTS ==================== -->
<script>
/* ----- Gallery ----- */
const allImages = <?php echo json_encode(array_map(fn($i) => $i['src'], $allImages)); ?>;
let currentImg = 0;

function gallerySet(idx) {
    currentImg = (idx + allImages.length) % allImages.length;
    document.getElementById('pdMainImg').src = allImages[currentImg];
    document.querySelectorAll('.pd-thumb').forEach((t, i) => {
        t.classList.toggle('active', i === currentImg);
    });
}
function galleryNav(dir) { gallerySet(currentImg + dir); }

/* Click vào ảnh chính → zoom modal nhẹ (dùng window.open) */
document.getElementById('pdMainImg').addEventListener('click', function () {
    const w = window.open('', '_blank', 'width=800,height=700');
    w.document.write(`<style>body{margin:0;background:#111;display:flex;align-items:center;justify-content:center;min-height:100vh;}</style>
                      <img src="${this.src}" style="max-width:100%;max-height:100vh;object-fit:contain;">`);
    w.document.close();
});

/* ----- Variant selector ----- */
function selectVariant(btn) {
    document.querySelectorAll('.pd-variant-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    const rawPrice  = parseFloat(btn.dataset.price);
    const sale      = parseFloat(btn.dataset.sale) || 0;
    const finalPrc  = Math.round(rawPrice * (1 - sale / 100));

    document.getElementById('pdPriceDisplay').textContent =
        finalPrc.toLocaleString('vi-VN') + '₫';
}

/* ----- Tabs ----- */
function openTab(name) {
    document.querySelectorAll('.pd-tab-content').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.pd-tab-btn').forEach(el => el.classList.remove('active'));

    const content = document.getElementById('tab-' + name);
    const btn     = document.getElementById('tab-btn-' + name);
    if (content) content.classList.add('active');
    if (btn)     btn.classList.add('active');

    content?.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

/* Click vào "(N đánh giá)" → mở tab reviews */
document.querySelectorAll('[href="#tab-reviews"]').forEach(el => {
    el.addEventListener('click', e => { e.preventDefault(); openTab('reviews'); });
});

/* ----- Interactive star rating ----- */
let selectedRating = 5;
function setRating(val) {
    selectedRating = val;
    document.getElementById('ratingInput').value = val;
    updateStars(val);
}
function hoverRating(val) { updateStars(val); }
function resetRatingHover() { updateStars(selectedRating); }
function updateStars(val) {
    document.querySelectorAll('#starPicker .star').forEach((s, i) => {
        s.classList.toggle('lit', i < val);
    });
}

/* ----- Toast helper ----- */
function showToast(msg) {
    const t = document.getElementById('pdToast');
    t.textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 2800);
}

/* Chặn spam click add-to-cart với feedback */
document.querySelector('.pd-btn-primary')?.addEventListener('click', function (e) {
    showToast('✓ Đã thêm vào giỏ hàng!');
});
</script>