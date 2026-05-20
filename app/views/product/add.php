<div class="container pb-5 mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
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

            <div class="card shadow-sm border-0" style="border-radius: 10px;">
                <div class="card-header bg-white border-bottom py-3">
                    <h4 class="fw-bold text-center m-0 text-uppercase text-success">Them san pham chuyen nghiep</h4>
                </div>

                <div class="card-body p-4">
                    <form method="POST" action="/project1/Product/save" enctype="multipart/form-data">
                        <input type="hidden" name="MAX_FILE_SIZE" value="5242880">

                        <div class="row g-3">
                            <div class="col-md-7">
                                <label for="name" class="form-label fw-bold">Ten san pham</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="col-md-5">
                                <label for="category_id" class="form-label fw-bold">Danh muc</label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <option value="" selected disabled>-- Chon danh muc --</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?php echo $cat->id; ?>"><?php echo htmlspecialchars($cat->name, ENT_QUOTES, 'UTF-8'); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mt-1">
                            <div class="col-md-4">
                                <label for="brand_id" class="form-label fw-bold">Thuong hieu</label>
                                <select class="form-select" id="brand_id" name="brand_id">
                                    <option value="">-- Chon thuong hieu --</option>
                                    <?php foreach (($brands ?? []) as $brand): ?>
                                        <option value="<?php echo $brand->id; ?>"><?php echo htmlspecialchars($brand->name, ENT_QUOTES, 'UTF-8'); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="new_brand" class="form-label fw-bold">Thuong hieu moi</label>
                                <input type="text" class="form-control" id="new_brand" name="new_brand" placeholder="VD: Apple">
                            </div>
                            <div class="col-md-4">
                                <label for="price" class="form-label fw-bold">Gia goc (VND)</label>
                                <input type="number" class="form-control" id="price" name="price" step="1" min="1" required>
                            </div>
                        </div>

                        <div class="row g-3 mt-1">
                            <div class="col-md-4">
                                <label for="warranty_months" class="form-label fw-bold">Bao hanh (thang)</label>
                                <input type="number" min="0" class="form-control" id="warranty_months" name="warranty_months" value="12">
                            </div>
                            <div class="col-md-4">
                                <label for="sale_percent" class="form-label fw-bold">Giam gia (%)</label>
                                <input type="number" min="0" max="90" class="form-control" id="sale_percent" name="sale_percent" value="0">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="featured" name="featured" value="1">
                                    <label class="form-check-label fw-bold" for="featured">San pham noi bat</label>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <label for="image" class="form-label fw-bold">Anh dai dien</label>
                                <input class="form-control" type="file" id="image" name="image" accept="image/*" onchange="previewImage(event)" required>
                                <div class="mt-3 text-center">
                                    <img id="imagePreview" src="#" alt="Preview" style="max-width:100%;max-height:200px;display:none;border-radius:8px;border:1px solid #ddd;padding:5px;">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="gallery_images" class="form-label fw-bold">Anh phu / gallery</label>
                                <input class="form-control" type="file" id="gallery_images" name="gallery_images[]" accept="image/*" multiple>
                                <small class="text-muted">Co the chon nhieu anh.</small>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="form-label fw-bold">Thong so ky thuat</label>
                            <?php for ($i = 0; $i < 5; $i++): ?>
                                <div class="row g-2 mb-2">
                                    <div class="col-md-4"><input class="form-control" name="spec_key[]" placeholder="Ten thong so"></div>
                                    <div class="col-md-8"><input class="form-control" name="spec_value[]" placeholder="Gia tri"></div>
                                </div>
                            <?php endfor; ?>
                        </div>

                        <div class="mt-4">
                            <label class="form-label fw-bold">Bien the san pham</label>
                            <div class="table-responsive">
                                <table class="table table-sm align-middle">
                                    <thead><tr><th>Mau</th><th>RAM</th><th>Dung luong</th><th>Cong gia</th><th>Ton kho</th><th>SKU</th></tr></thead>
                                    <tbody>
                                        <?php for ($i = 0; $i < 4; $i++): ?>
                                            <tr>
                                                <td><input class="form-control form-control-sm" name="variant_color[]" placeholder="Den"></td>
                                                <td><input class="form-control form-control-sm" name="variant_ram[]" placeholder="8GB"></td>
                                                <td><input class="form-control form-control-sm" name="variant_storage[]" placeholder="256GB"></td>
                                                <td><input type="number" class="form-control form-control-sm" name="variant_price_delta[]" value="0"></td>
                                                <td><input type="number" class="form-control form-control-sm" name="variant_stock[]" value="0"></td>
                                                <td><input class="form-control form-control-sm" name="variant_sku[]" placeholder="SKU"></td>
                                            </tr>
                                        <?php endfor; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label for="description" class="form-label fw-bold">Mo ta chi tiet</label>
                            <textarea class="form-control" id="description" name="description" rows="5" required></textarea>
                        </div>

                        <div class="d-flex justify-content-between gap-3 mt-4">
                            <a href="/project1/Product/index" class="btn btn-light border flex-grow-1">Quay lai</a>
                            <button type="submit" class="btn btn-success flex-grow-1 shadow-sm">Them san pham</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function previewImage(event) {
    const input = event.target;
    const output = document.getElementById('imagePreview');
    if (input.files && input.files[0]) {
        const file = input.files[0];
        if (file.size > 5 * 1024 * 1024) {
            alert('Anh phai nho hon 5MB.');
            input.value = '';
            return;
        }
        const reader = new FileReader();
        reader.onload = function() {
            output.src = reader.result;
            output.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
}
</script>
