<div class="container bg-white p-4 rounded shadow-sm mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <h4 class="fw-bold text-uppercase m-0 text-dark">
            <i class="fas fa-heart text-danger me-2"></i>Danh sach yeu thich
        </h4>
        <a href="/project1/Product/index" class="btn btn-outline-secondary rounded-pill px-4">Tiep tuc mua sam</a>
    </div>

    <?php if (empty($products)): ?>
        <div class="text-center py-5">
            <i class="fas fa-heart-broken fa-4x text-muted opacity-50 mb-3"></i>
            <h5 class="text-secondary">Ban chua co san pham yeu thich nao.</h5>
        </div>
    <?php else: ?>
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-3">
            <?php foreach ($products as $product): ?>
                <?php
                    $imgName = !empty($product->image) ? $product->image : 'default.jpg';
                    $imagePath = (strpos($imgName, 'public/images/') !== false || strpos($imgName, 'uploads/') !== false)
                        ? "/project1/" . $imgName
                        : "/project1/public/images/" . $imgName;
                ?>
                <div class="col">
                    <div class="card h-100 p-2 card-product">
                        <a href="/project1/Product/detail?id=<?php echo $product->id; ?>" class="text-center d-block">
                            <img src="<?php echo htmlspecialchars($imagePath, ENT_QUOTES, 'UTF-8'); ?>" class="card-img-top" style="object-fit: contain; height: 160px;">
                        </a>
                        <div class="card-body px-1 py-2">
                            <h6 class="fw-bold" style="font-size:.9rem;height:2.4rem;overflow:hidden;">
                                <?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>
                            </h6>
                            <strong class="text-danger"><?php echo number_format($product->price, 0, ',', '.'); ?>d</strong>
                        </div>
                        <div class="card-footer bg-transparent border-0 d-grid gap-2">
                            <a href="/project1/Product/addToCart?id=<?php echo $product->id; ?>" class="btn btn-sm btn-warning rounded-pill fw-bold text-dark">Them vao gio</a>
                            <form method="POST" action="/project1/Product/toggleWishlist">
                                <input type="hidden" name="product_id" value="<?php echo $product->id; ?>">
                                <button class="btn btn-sm btn-outline-danger rounded-pill w-100" type="submit">Bo yeu thich</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
