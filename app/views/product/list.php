<div class="container bg-white p-4 rounded shadow-sm mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <h4 class="fw-bold text-uppercase m-0">Điện thoại nổi bật</h4>
        <a href="/project1/Product/add" class="btn fw-bold px-4" style="background-color: #288ad6; color: white;">
            <i class="fas fa-plus-circle me-1"></i> Thêm sản phẩm
        </a>
    </div>

    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-3">
        
        <?php if (empty($products)): ?>
            <div class="col-12 text-center py-5">
                <p class="text-muted fs-5">Hiện tại chưa có sản phẩm nào. Hãy bấm "Thêm sản phẩm" nhé!</p>
            </div>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
            <div class="col product-item">
                <div class="card h-100 p-2" style="transition: all 0.3s ease; border: 1px solid #eee; border-radius: 8px;">
                    
                    <?php 
                        $imgName = $product->getImage();
                        if (empty($imgName)) {
                            $imgName = 'default.jpg';
                        }
                    ?>
                    <img src="/project1/public/images/<?php echo htmlspecialchars($imgName, ENT_QUOTES, 'UTF-8'); ?>" 
                         class="card-img-top" 
                         alt="Ảnh sản phẩm"
                         style="object-fit: contain; height: 200px; padding: 10px;">
                    
                    <div class="card-body px-1 py-2 d-flex flex-column">
                        <h6 class="card-title fw-bold text-dark mb-1" style="font-size: 0.95rem;">
                            <?php echo htmlspecialchars($product->getName(), ENT_QUOTES, 'UTF-8'); ?>
                        </h6>
                        <p class="card-text text-muted small text-truncate mb-2">
                            <?php echo htmlspecialchars($product->getDescription(), ENT_QUOTES, 'UTF-8'); ?>
                        </p>
                        <div class="mt-auto">
                            <strong style="color: #d70018; font-size: 1.1rem;">
                                <?php echo number_format($product->getPrice(), 0, ',', '.'); ?>₫
                            </strong>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-transparent border-0 px-1 pt-0 d-flex justify-content-between gap-2">
                        <a href="/project1/Product/edit/<?php echo $product->getID(); ?>" class="btn btn-sm btn-outline-primary w-50">
                            <i class="fas fa-edit"></i> Sửa
                        </a>
                        <a href="/project1/Product/delete/<?php echo $product->getID(); ?>" class="btn btn-sm btn-outline-danger w-50" onclick="return confirm('Bạn có chắc chắn muốn xóa <?php echo htmlspecialchars($product->getName(), ENT_QUOTES, 'UTF-8'); ?> không?');">
                            <i class="fas fa-trash"></i> Xóa
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>
</div>

<script>
    // Đợi HTML tải xong rồi mới chạy script
    document.addEventListener('DOMContentLoaded', function() {
        // Tìm ô nhập liệu trên thanh Navbar (id="searchInput" đã thêm ở header.php)
        const searchInput = document.getElementById('searchInput');
        
        // Nếu trang web có thanh tìm kiếm thì mới gắn sự kiện
        if (searchInput) {
            // Lắng nghe mỗi khi bạn gõ một phím bất kỳ
            searchInput.addEventListener('input', function() {
                // Lấy chữ bạn vừa gõ, cắt khoảng trắng 2 đầu và in thường
                let keyword = this.value.toLowerCase().trim();
                
                // Gom tất cả các khối sản phẩm lại
                let products = document.querySelectorAll('.product-item');

                // Đi xét duyệt từng khối một
                products.forEach(function(product) {
                    // Lấy cái tên của điện thoại ra
                    let productName = product.querySelector('.card-title').innerText.toLowerCase();
                    
                    // Kiểm tra xem tên điện thoại có chứa chữ bạn gõ không
                    if (productName.includes(keyword)) {
                        product.style.display = ''; // Có thì hiện bình thường
                    } else {
                        product.style.display = 'none'; // Không có thì giấu đi
                    }
                });
            });
        }
    });
</script>