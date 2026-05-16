<div class="container pb-5 mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <ul class="mb-0 ps-3">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm" style="border-radius: 10px; border: none;">
                <div class="card-header bg-white border-bottom py-3">
                    <h4 class="fw-bold text-center m-0 text-uppercase text-success">Thêm Sản Phẩm Mới</h4>
                </div>
                
                <div class="card-body p-4">
                    <form method="POST" action="/project1/Product/save" enctype="multipart/form-data" onsubmit="return validateForm();">
                        
                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">Tên sản phẩm</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Nhập tên sản phẩm (10-100 ký tự)" required>
                        </div>

                        <div class="mb-3">
                            <label for="category_id" class="form-label fw-bold">Danh mục</label>
                            <select class="form-select" id="category_id" name="category_id" required>
                                <option value="" selected disabled>-- Chọn danh mục --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat->id; ?>">
                                        <?php echo htmlspecialchars($cat->name, ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label fw-bold">Giá (VNĐ)</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="price" name="price" step="1" placeholder="Nhập giá sản phẩm" required>
                                <span class="input-group-text">₫</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label fw-bold">Hình ảnh sản phẩm</label>
                            <input class="form-control" type="file" id="image" name="image" accept="image/*" onchange="previewImage(event)" required>
                            
                            <div class="mt-3 text-center">
                                <img id="imagePreview" src="#" alt="Ảnh xem trước" style="max-width: 100%; height: auto; max-height: 200px; display: none; border-radius: 8px; border: 1px solid #ddd; padding: 5px;">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label fw-bold">Mô tả chi tiết</label>
                            <textarea class="form-control" id="description" name="description" rows="4" placeholder="Nhập mô tả sản phẩm..." required></textarea>
                        </div>

                        <div class="d-flex justify-content-between gap-3">
                            <a href="/project1/" class="btn btn-light border flex-grow-1">
                                <i class="fas fa-arrow-left"></i> Quay lại
                            </a>
                            <button type="submit" class="btn btn-success flex-grow-1 shadow-sm">
                                <i class="fas fa-plus-circle"></i> Xác nhận thêm
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Hàm xem trước ảnh đơn giản
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function() {
            var output = document.getElementById('imagePreview');
            output.src = reader.result;
            output.style.display = 'block';
        };
        if(event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        }
    }

    // Bỏ qua validate ảnh lằng nhằng vì thẻ input file đã có thuộc tính required
    function validateForm() {
        let name = document.getElementById('name').value;
        let price = document.getElementById('price').value;
        let cat = document.getElementById('category_id').value;
        
        let errors = [];
        if (name.length < 10 || name.length > 100) errors.push('Tên sản phẩm phải có từ 10 đến 100 ký tự.');
        if (price <= 0 || isNaN(price)) errors.push('Giá phải là một số dương lớn hơn 0.');
        if (cat === "") errors.push('Vui lòng chọn một danh mục.');

        if (errors.length > 0) {
            alert(errors.join('\n'));
            return false;
        }
        return true;
    }
</script>