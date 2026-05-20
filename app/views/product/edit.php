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
                    <h4 class="fw-bold text-center m-0 text-uppercase text-dark">Sua san pham chuyen nghiep</h4>
                </div>

                <div class="card-body p-4">
                    <form method="POST" action="/project1/Product/update" enctype="multipart/form-data">
                        <input type="hidden" name="MAX_FILE_SIZE" value="5242880">
                        <input type="hidden" name="id" value="<?php echo $product->id; ?>">
                        <input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($product->image, ENT_QUOTES, 'UTF-8'); ?>">

                        <div class="row g-3">
                            <div class="col-md-7">
                                <label for="name" class="form-label fw-bold">Ten san pham</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>" required>
                            </div>
                            <div class="col-md-5">
                                <label for="category_id" class="form-label fw-bold">Danh muc</label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?php echo $cat->id; ?>" <?php echo ($cat->id == $product->category_id) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cat->name, ENT_QUOTES, 'UTF-8'); ?>
                                        </option>
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
                                        <option value="<?php echo $brand->id; ?>" <?php echo ($brand->id == ($product->brand_id ?? null)) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($brand->name, ENT_QUOTES, 'UTF-8'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="new_brand" class="form-label fw-bold">Thuong hieu moi</label>
                                <input type="text" class="form-control" id="new_brand" name="new_brand" placeholder="VD: Apple">
                            </div>
                            <div class="col-md-4">
                                <label for="price" class="form-label fw-bold">Gia goc (VND)</label>
                                <input type="number" class="form-control" id="price" name="price" step="1" min="1" value="<?php echo htmlspecialchars($product->price, ENT_QUOTES, 'UTF-8'); ?>" required>
                            </div>
                        </div>

                        <div class="row g-3 mt-1">
                            <div class="col-md-4">
                                <label for="warranty_months" class="form-label fw-bold">Bao hanh (thang)</label>
                                <input type="number" min="0" class="form-control" id="warranty_months" name="warranty_months" value="<?php echo (int)($product->warranty_months ?? 12); ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="sale_percent" class="form-label fw-bold">Giam gia (%)</label>
                                <input type="number" min="0" max="90" class="form-control" id="sale_percent" name="sale_percent" value="<?php echo (int)($product->sale_percent ?? 0); ?>">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="featured" name="featured" value="1" <?php echo !empty($product->featured) ? 'checked' : ''; ?>>
                                    <label class="form-check-label fw-bold" for="featured">San pham noi bat</label>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <label for="image" class="form-label fw-bold">Anh dai dien</label>
                                <?php
                                    $imgName = !empty($product->image) ? $product->image : 'default.jpg';
                                    $imagePath = (strpos($imgName, 'public/images/') !== false || strpos($imgName, 'uploads/') !== false)
                                        ? "/project1/" . $imgName
                                        : "/project1/public/images/" . $imgName;
                                ?>
                                <div class="mb-2 text-center bg-light p-2 rounded">
                                    <img id="imagePreview" src="<?php echo htmlspecialchars($imagePath, ENT_QUOTES, 'UTF-8'); ?>" alt="Preview" style="max-width:100%;max-height:180px;object-fit:contain;border-radius:8px;border:1px solid #ddd;padding:5px;background:#fff;">
                                </div>
                                <input class="form-control" type="file" id="image" name="image" accept="image/*" onchange="previewImage(event)">
                            </div>
                            <div class="col-md-6">
                                <label for="gallery_images" class="form-label fw-bold">Them anh phu</label>
                                <input class="form-control" type="file" id="gallery_images" name="gallery_images[]" accept="image/*" multiple>
                                <?php if (!empty($productImages)): ?>
                                    <div class="row g-2 mt-2">
                                        <?php foreach ($productImages as $img): ?>
                                            <div class="col-4">
                                                <div class="border rounded p-2 text-center">
                                                    <img src="/project1/public/images/<?php echo htmlspecialchars($img->image, ENT_QUOTES, 'UTF-8'); ?>" style="height:60px;max-width:100%;object-fit:contain;">
                                                    <label class="small d-block mt-1">
                                                        <input type="checkbox" name="delete_gallery_ids[]" value="<?php echo $img->id; ?>"> Xoa
                                                    </label>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="form-label fw-bold">Thong so ky thuat</label>
                            <?php
                                $specRows = $productSpecs ?? [];
                                for ($i = count($specRows); $i < 5; $i++) $specRows[] = (object)['spec_key' => '', 'spec_value' => ''];
                            ?>
                            <?php foreach ($specRows as $spec): ?>
                                <div class="row g-2 mb-2">
                                    <div class="col-md-4"><input class="form-control" name="spec_key[]" value="<?php echo htmlspecialchars($spec->spec_key, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Ten thong so"></div>
                                    <div class="col-md-8"><input class="form-control" name="spec_value[]" value="<?php echo htmlspecialchars($spec->spec_value, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Gia tri"></div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="mt-4">
                            <label class="form-label fw-bold">Bien the san pham</label>
                            <?php
                                $variantRows = $productVariants ?? [];
                                for ($i = count($variantRows); $i < 4; $i++) $variantRows[] = (object)['color' => '', 'ram' => '', 'storage' => '', 'price_delta' => 0, 'stock' => 0, 'sku' => ''];
                            ?>
                            <div class="table-responsive">
                                <table class="table table-sm align-middle">
                                    <thead><tr><th>Mau</th><th>RAM</th><th>Dung luong</th><th>Cong gia</th><th>Ton kho</th><th>SKU</th></tr></thead>
                                    <tbody>
                                        <?php foreach ($variantRows as $variant): ?>
                                            <tr>
                                                <td><input class="form-control form-control-sm" name="variant_color[]" value="<?php echo htmlspecialchars($variant->color ?? '', ENT_QUOTES, 'UTF-8'); ?>"></td>
                                                <td><input class="form-control form-control-sm" name="variant_ram[]" value="<?php echo htmlspecialchars($variant->ram ?? '', ENT_QUOTES, 'UTF-8'); ?>"></td>
                                                <td><input class="form-control form-control-sm" name="variant_storage[]" value="<?php echo htmlspecialchars($variant->storage ?? '', ENT_QUOTES, 'UTF-8'); ?>"></td>
                                                <td><input type="number" class="form-control form-control-sm" name="variant_price_delta[]" value="<?php echo htmlspecialchars($variant->price_delta ?? 0, ENT_QUOTES, 'UTF-8'); ?>"></td>
                                                <td><input type="number" class="form-control form-control-sm" name="variant_stock[]" value="<?php echo htmlspecialchars($variant->stock ?? 0, ENT_QUOTES, 'UTF-8'); ?>"></td>
                                                <td><input class="form-control form-control-sm" name="variant_sku[]" value="<?php echo htmlspecialchars($variant->sku ?? '', ENT_QUOTES, 'UTF-8'); ?>"></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label for="description" class="form-label fw-bold">Mo ta chi tiet</label>
                            <textarea class="form-control" id="description" name="description" rows="5" required><?php echo htmlspecialchars($product->description, ENT_QUOTES, 'UTF-8'); ?></textarea>
                        </div>

                        <div class="d-flex justify-content-between gap-3 mt-4">
                            <a href="/project1/Product/index" class="btn btn-light border flex-grow-1">Huy bo</a>
                            <button type="submit" class="btn btn-warning flex-grow-1 shadow-sm text-dark fw-bold">Luu thay doi</button>
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
        reader.onload = function() { output.src = reader.result; };
        reader.readAsDataURL(file);
    }
}
</script>
