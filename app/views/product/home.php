<?php
function renderProductStrip($title, $products, $link) {
?>
<section class="mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold text-uppercase m-0"><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h5>
        <a href="<?php echo htmlspecialchars($link, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-sm btn-outline-dark rounded-pill">Xem tat ca</a>
    </div>
    <div class="row row-cols-2 row-cols-md-4 row-cols-lg-5 g-3">
        <?php foreach ($products as $product): ?>
            <?php
                $imgName = !empty($product->image) ? $product->image : 'default.jpg';
                $imagePath = (strpos($imgName, 'public/images/') !== false || strpos($imgName, 'uploads/') !== false) ? "/project1/" . $imgName : "/project1/public/images/" . $imgName;
                $finalPrice = $product->price * (100 - (int)($product->sale_percent ?? 0)) / 100;
            ?>
            <div class="col">
                <div class="card h-100 p-2 card-product border-0">
                    <a href="/project1/Product/detail?id=<?php echo $product->id; ?>" class="text-center text-decoration-none position-relative">
                        <?php if (!empty($product->sale_percent)): ?><span class="badge bg-danger position-absolute top-0 start-0">-<?php echo (int)$product->sale_percent; ?>%</span><?php endif; ?>
                        <img src="<?php echo htmlspecialchars($imagePath, ENT_QUOTES, 'UTF-8'); ?>" style="height:150px;object-fit:contain;" class="card-img-top">
                    </a>
                    <div class="card-body px-1 py-2">
                        <h6 class="fw-bold text-dark" style="font-size:.9rem;height:2.4rem;overflow:hidden;"><?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?></h6>
                        <div class="small text-muted"><?php echo htmlspecialchars($product->brand_name ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
                        <strong class="text-danger"><?php echo number_format($finalPrice, 0, ',', '.'); ?>d</strong>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php } ?>

<div class="container bg-white p-4 rounded shadow-sm mt-4">
    <div id="homeBanner" class="carousel slide mb-4 rounded overflow-hidden" data-bs-ride="carousel">
        <div class="carousel-inner">
            <?php foreach (($banners ?? []) as $idx => $banner): ?>
                <?php $bannerImg = !empty($banner->image) ? '/project1/public/images/' . $banner->image : '/project1/public/images/1779075308_xiaomi-17-5g-xanh-la-1-639088210870268655-750x500.jpg'; ?>
                <div class="carousel-item <?php echo $idx === 0 ? 'active' : ''; ?>">
                    <a href="<?php echo htmlspecialchars($banner->link ?: '/project1/Product/index', ENT_QUOTES, 'UTF-8'); ?>" class="d-block position-relative text-decoration-none">
                        <img src="<?php echo htmlspecialchars($bannerImg, ENT_QUOTES, 'UTF-8'); ?>" class="d-block w-100" style="height:340px;object-fit:cover;">
                        <div class="position-absolute bottom-0 start-0 end-0 p-4 text-white" style="background:linear-gradient(transparent,rgba(0,0,0,.75));">
                            <h2 class="fw-bold mb-1"><?php echo htmlspecialchars($banner->title, ENT_QUOTES, 'UTF-8'); ?></h2>
                            <p class="mb-0"><?php echo htmlspecialchars($banner->subtitle ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#homeBanner" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span></button>
        <button class="carousel-control-next" type="button" data-bs-target="#homeBanner" data-bs-slide="next"><span class="carousel-control-next-icon"></span></button>
    </div>

    <div class="p-3 mb-4 rounded bg-dark text-white d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="fw-bold mb-1"><i class="fas fa-bolt text-warning me-2"></i>Flash sale hom nay</h5>
            <span class="small text-white-50">Dung ma SALE10 cho don tu 1.000.000d</span>
        </div>
        <a href="/project1/Product/index?featured=1&sort=price_desc" class="btn btn-warning rounded-pill fw-bold text-dark">San ngay</a>
    </div>

    <div class="mb-4">
        <h5 class="fw-bold text-uppercase">Thuong hieu noi bat</h5>
        <div class="d-flex flex-wrap gap-2">
            <?php foreach (array_slice($brands ?? [], 0, 12) as $brand): ?>
                <a href="/project1/Product/index?brand_id=<?php echo $brand->id; ?>" class="btn btn-light border rounded-pill px-3 fw-semibold"><?php echo htmlspecialchars($brand->name, ENT_QUOTES, 'UTF-8'); ?></a>
            <?php endforeach; ?>
        </div>
    </div>

    <?php renderProductStrip('San pham noi bat', $featuredProducts ?? [], '/project1/Product/index?featured=1'); ?>
    <?php renderProductStrip('Dien thoai moi', $phoneProducts ?? [], '/project1/Product/index?category_id=1'); ?>
    <?php renderProductStrip('Laptop dang chu y', $laptopProducts ?? [], '/project1/Product/index?category_id=2'); ?>
    <?php renderProductStrip('Phu kien gia tot', $accessoryProducts ?? [], '/project1/Product/index?category_id=4'); ?>
</div>
