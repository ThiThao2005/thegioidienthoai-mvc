<div class="container pb-5 mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            
            <div class="card shadow-sm" style="border-radius: 10px; border: none;">
                <div class="card-header bg-white border-bottom py-3">
                    <h4 class="fw-bold text-center m-0 text-uppercase text-dark">
                        <i class="fas fa-edit text-warning me-2"></i>Sửa Thông Tin Sản Phẩm
                    </h4>
                </div>
                
                <div class="card-body p-4">
                    <form method="POST" action="/project1/Product/update" enctype="multipart/form-data">
                        
                        <input type="hidden" name="id" value="<?php echo $product->id; ?>">

                        <input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($product->image, ENT_QUOTES, 'UTF-8'); ?>">
                        
                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">Tên sản phẩm</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="category_id" class="form-label fw-bold">Danh mục</label>
                            <select class="form-select" id="category_id" name="category_id" required>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat->id; ?>" <?php echo ($cat->id == $product->category_id) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat->name, ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label fw-bold">Giá (VNĐ)</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="price" name="price" 
                                       value="<?php echo htmlspecialchars($product->price, ENT_QUOTES, 'UTF-8'); ?>" required>
                                <span class="input-group-text">₫</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Hình ảnh sản phẩm</label>
                            
                            <div class="mb-3 text-center bg-light p-2 rounded">
                                <?php 
                                    $imgName = !empty($product->image) ? $product->image : 'default.jpg'; 
                                    $imagePath = (strpos($imgName, 'public/images/') !== false || strpos($imgName, 'uploads/') !== false) 
                                        ? "/project1/" . $imgName 
                                        : "/project1/public/images/" . $imgName;
                                ?>
                                <img id="imagePreview" src="<?php echo htmlspecialchars($imagePath, ENT_QUOTES, 'UTF-8'); ?>" 
                                     alt="Ảnh hiện tại" style="max-width: 100%; height: auto; max-height: 180px; object-fit: contain; border-radius: 8px; border: 1px solid #ddd; padding: 5px; background: #fff;">
                            </div>

                            <input class="form-control" type="file" id="image" name="image" accept="image/*" onchange="previewImage(event)">
                            <small class="text-muted fst-italic mt-1 d-block">Bỏ trống ô này nếu bạn không muốn thay đổi hình ảnh.</small>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label fw-bold">Mô tả chi tiết</label>
                            <textarea class="form-control" id="description" name="description" rows="4" required><?php echo htmlspecialchars($product->description, ENT_QUOTES, 'UTF-8'); ?></textarea>
                        </div>

                        <div class="d-flex justify-content-between gap-3">
                            <a href="/project1/Product/index" class="btn btn-light border flex-grow-1 rounded-pill fw-semibold">
                                <i class="fas fa-arrow-left me-1"></i> Hủy bỏ
                            </a>
                            <button type="submit" class="btn btn-warning flex-grow-1 shadow-sm rounded-pill fw-bold text-dark" style="background-color: #ffd400; border: none;">
                                <i class="fas fa-save me-1"></i> Lưu thay đổi
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    // 📸 1. Script xem trước ảnh realtime + Kiểm tra dung lượng file
    function previewImage(event) {
        var input = event.target;
        var output = document.getElementById('imagePreview');
        
        if (input.files && input.files[0]) {
            var file = input.files[0];
            
            // Giới hạn file tối đa 2MB (2 * 1024 * 1024 bytes)
            var maxSize = 2 * 1024 * 1024; 
            if (file.size > maxSize) {
                alert("⚠️ Kích thước ảnh quá lớn! Vui lòng chọn ảnh nhỏ hơn 2MB để hệ thống tải lên mượt mà.");
                input.value = ""; // Xóa file đã chọn trong input
                return;
            }

            var reader = new FileReader();
            reader.onload = function() {
                output.src = reader.result;
            };
            reader.readAsDataURL(file);
        }
    }

    // ⏳ 2. Chặn đúp chuột (Double Click) khi submit form
    document.querySelector('form').addEventListener('submit', function(e) {
        var submitBtn = this.querySelector('button[type="submit"]');
        
        // Đổi trạng thái nút để người dùng không bấm liên tục khi đang upload ảnh
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Đang lưu thay đổi...';
    });
</script>