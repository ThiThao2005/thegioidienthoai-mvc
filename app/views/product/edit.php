<div class="container pb-5 mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            
            <div class="card shadow-sm" style="border-radius: 10px; border: none;">
                <div class="card-header bg-white border-bottom py-3">
                    <h4 class="fw-bold text-center m-0 text-uppercase">Sửa Thông Tin Sản Phẩm</h4>
                </div>
                
                <div class="card-body p-4">
                    <form method="POST" action="/project1/Product/edit/<?php echo $product->getID(); ?>" enctype="multipart/form-data">
                        
                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">Tên sản phẩm</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?php echo htmlspecialchars($product->getName(), ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label fw-bold">Giá (VNĐ)</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="price" name="price" 
                                       value="<?php echo htmlspecialchars($product->getPrice(), ENT_QUOTES, 'UTF-8'); ?>" required>
                                <span class="input-group-text">₫</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label fw-bold">Hình ảnh sản phẩm</label>
                            
                            <div class="mb-3 text-center">
                                <img id="imagePreview" src="/project1/public/images/<?php echo htmlspecialchars($product->getImage(), ENT_QUOTES, 'UTF-8'); ?>" alt="Ảnh hiện tại" style="max-width: 100%; height: auto; max-height: 250px; border-radius: 8px; border: 1px solid #ddd; padding: 5px;">
                            </div>

                            <input class="form-control" type="file" id="image" name="image" accept="image/*" onchange="previewImage(event)">
                            <small class="text-muted fst-italic">Bỏ trống nếu bạn không muốn thay đổi hình ảnh.</small>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label fw-bold">Mô tả chi tiết</label>
                            <textarea class="form-control" id="description" name="description" rows="4" required><?php echo htmlspecialchars($product->getDescription(), ENT_QUOTES, 'UTF-8'); ?></textarea>
                        </div>

                        <div class="d-flex justify-content-between gap-3">
                            <a href="/project1/Product/list" class="btn btn-light border flex-grow-1">
                                <i class="fas fa-arrow-left"></i> Quay lại
                            </a>
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="fas fa-save"></i> Lưu thay đổi
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    // Script đổi ảnh xem trước khi người dùng tải ảnh mới lên
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function() {
            var output = document.getElementById('imagePreview');
            output.src = reader.result;
        };
        if(event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        }
    }
</script>