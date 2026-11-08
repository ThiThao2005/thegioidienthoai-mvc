<?php
/**
 * View: Product/edit.php
 * Form chỉnh sửa sản phẩm — Admin only
 *
 * Biến cần truyền từ controller:
 *   $product        — object sản phẩm
 *   $categories     — array danh mục
 *   $brands         — array thương hiệu
 *   $productImages  — array ảnh phụ (gallery)
 *   $productVariants— array biến thể
 *   $productSpecs   — array thông số kỹ thuật
 *   $errors         — array lỗi (tuỳ chọn)
 */
?>

<div class="container pb-5 mt-4">
<div class="row justify-content-center">
<div class="col-lg-10">

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <strong><i class="fas fa-exclamation-circle me-2"></i>Có lỗi xảy ra:</strong>
            <ul class="mb-0 mt-2 ps-3">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0" style="border-radius:12px;">

        <!-- Header -->
        <div class="card-header bg-white border-bottom py-3 d-flex align-items-center gap-2">
            <a href="/Product/index" class="btn btn-sm btn-light border me-1" title="Quay lại danh sách">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h4 class="fw-bold m-0 text-dark" style="font-size:1.05rem;">
                <i class="fas fa-edit text-warning me-2"></i>
                Chỉnh sửa sản phẩm
                <span class="badge bg-warning text-dark ms-2" style="font-size:.7rem; vertical-align:middle;">
                    ID #<?php echo $product->id; ?>
                </span>
            </h4>
        </div>

        <div class="card-body p-4">
        <form method="POST" action="/Product/update" enctype="multipart/form-data" id="editForm" novalidate>
            <input type="hidden" name="MAX_FILE_SIZE" value="5242880">
            <input type="hidden" name="id" value="<?php echo $product->id; ?>">
            <input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($product->image, ENT_QUOTES, 'UTF-8'); ?>">

            <!-- ==================== THÔNG TIN CƠ BẢN ==================== -->
            <div class="section-block mb-4">
                <h6 class="section-label"><i class="fas fa-info-circle text-primary me-2"></i>Thông tin cơ bản</h6>

                <div class="row g-3">
                    <div class="col-md-7">
                        <label for="name" class="form-label fw-semibold">Tên sản phẩm <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name"
                               value="<?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>"
                               placeholder="VD: iPhone 15 Pro Max 256GB" required>
                        <div class="invalid-feedback">Vui lòng nhập tên sản phẩm.</div>
                    </div>
                    <div class="col-md-5">
                        <label for="category_id" class="form-label fw-semibold">Danh mục <span class="text-danger">*</span></label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat->id; ?>"
                                    <?php echo ($cat->id == $product->category_id) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat->name, ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mt-0">
                    <div class="col-md-4">
                        <label for="brand_id" class="form-label fw-semibold">Thương hiệu</label>
                        <select class="form-select" id="brand_id" name="brand_id">
                            <option value="">-- Chọn thương hiệu --</option>
                            <?php foreach (($brands ?? []) as $brand): ?>
                                <option value="<?php echo $brand->id; ?>"
                                    <?php echo ($brand->id == ($product->brand_id ?? null)) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($brand->name, ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="new_brand" class="form-label fw-semibold">Hoặc thêm thương hiệu mới</label>
                        <input type="text" class="form-control" id="new_brand" name="new_brand" placeholder="VD: Xiaomi, Oppo...">
                        <div class="form-text">Để trống nếu đã chọn ở trên.</div>
                    </div>
                    <div class="col-md-4">
                        <label for="warranty_months" class="form-label fw-semibold">Bảo hành (tháng)</label>
                        <input type="number" min="0" class="form-control" id="warranty_months" name="warranty_months"
                               value="<?php echo (int)($product->warranty_months ?? 12); ?>">
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <!-- ==================== GIÁ & KHUYẾN MÃI ==================== -->
            <div class="section-block mb-4">
                <h6 class="section-label"><i class="fas fa-tag text-warning me-2"></i>Giá & khuyến mãi</h6>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="price" class="form-label fw-semibold">Giá gốc (VND) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="price" name="price"
                               step="1000" min="1"
                               value="<?php echo htmlspecialchars($product->price, ENT_QUOTES, 'UTF-8'); ?>"
                               required>
                        <div class="invalid-feedback">Vui lòng nhập giá hợp lệ.</div>
                    </div>
                    <div class="col-md-4">
                        <label for="sale_percent" class="form-label fw-semibold">Giảm giá (%)</label>
                        <input type="number" min="0" max="90" class="form-control" id="sale_percent" name="sale_percent"
                               value="<?php echo (int)($product->sale_percent ?? 0); ?>">
                        <div class="form-text">Tối đa 90%</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Giá sau giảm</label>
                        <div id="sale-price-display" class="alert alert-success py-2 px-3 mb-0" style="font-size:.95rem; border-radius:8px;">
                            <strong id="sale-price-value">—</strong>
                            <div id="sale-save-text" class="small text-success mt-1"></div>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="featured" name="featured" value="1"
                            <?php echo !empty($product->featured) ? 'checked' : ''; ?>>
                        <label class="form-check-label fw-semibold" for="featured">
                            <i class="fas fa-star text-warning me-1"></i>Sản phẩm nổi bật
                        </label>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <!-- ==================== HÌNH ẢNH ==================== -->
            <div class="section-block mb-4">
                <h6 class="section-label"><i class="fas fa-images text-info me-2"></i>Hình ảnh</h6>

                <div class="row g-3">

                    <!-- Ảnh đại diện -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Ảnh đại diện</label>
                        <?php
                            $imgName   = !empty($product->image) ? $product->image : 'default.jpg';
                            $imagePath = (strpos($imgName, 'public/images/') !== false || strpos($imgName, 'uploads/') !== false)
                                ? '/' . $imgName
                                : '/public/images/' . $imgName;
                        ?>
                        <!-- Vùng drag & drop -->
                        <div id="imgDropZone"
                             class="img-drop-zone text-center rounded mb-2 p-2"
                             onclick="document.getElementById('image').click()"
                             ondragover="handleDragOver(event)"
                             ondragleave="handleDragLeave(event)"
                             ondrop="handleDrop(event)">
                            <img id="imagePreview"
                                 src="<?php echo htmlspecialchars($imagePath, ENT_QUOTES, 'UTF-8'); ?>"
                                 alt="Preview ảnh sản phẩm"
                                 style="max-width:100%; max-height:190px; object-fit:contain; border-radius:8px;">
                            <div class="mt-2 text-muted small">
                                <i class="fas fa-cloud-upload-alt me-1"></i>Click hoặc kéo thả ảnh vào đây
                            </div>
                        </div>
                        <input class="form-control" type="file" id="image" name="image"
                               accept="image/*" onchange="previewMainImage(event)" style="display:none">
                        <div class="form-text">JPG, PNG, WEBP — Tối đa 5MB</div>
                    </div>

                    <!-- Gallery ảnh phụ -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Ảnh phụ (gallery)</label>
                        <label for="gallery_images" class="img-drop-zone w-100 d-flex align-items-center justify-content-center gap-2"
                               style="min-height:80px; cursor:pointer;">
                            <i class="fas fa-plus-circle text-muted fs-4"></i>
                            <span class="text-muted small">Chọn nhiều ảnh</span>
                        </label>
                        <input class="form-control" type="file" id="gallery_images" name="gallery_images[]"
                               accept="image/*" multiple style="display:none"
                               onchange="previewGallery(event)">

                        <!-- Ảnh gallery hiện có -->
                        <?php if (!empty($productImages)): ?>
                            <div class="gallery-existing mt-2">
                                <p class="text-muted small mb-2">Ảnh hiện có — tích vào ô để xóa:</p>
                                <div class="row g-2">
                                    <?php foreach ($productImages as $img): ?>
                                        <div class="col-4">
                                            <div class="border rounded p-1 text-center position-relative gallery-thumb">
                                                <img src="/public/images/<?php echo htmlspecialchars($img->image, ENT_QUOTES, 'UTF-8'); ?>"
                                                     style="height:56px; max-width:100%; object-fit:contain;"
                                                     alt="Gallery image">
                                                <label class="gallery-del-label" title="Xóa ảnh này">
                                                    <input type="checkbox" name="delete_gallery_ids[]"
                                                           value="<?php echo $img->id; ?>"
                                                           onchange="toggleDeleteMark(this)">
                                                    <span class="del-text small">Xóa</span>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Preview ảnh mới được chọn -->
                        <div class="row g-2 mt-1" id="newGalleryPreview"></div>
                    </div>

                </div>
            </div>

            <hr class="my-4">

            <!-- ==================== THÔNG SỐ KỸ THUẬT ==================== -->
            <div class="section-block mb-4">
                <h6 class="section-label"><i class="fas fa-list-ul text-secondary me-2"></i>Thông số kỹ thuật</h6>

                <div id="specsList">
                    <?php
                        $specRows = $productSpecs ?? [];
                        // Đảm bảo tối thiểu 3 dòng trống nếu chưa có gì
                        if (empty($specRows)) {
                            for ($i = 0; $i < 3; $i++) {
                                $specRows[] = (object)['spec_key' => '', 'spec_value' => ''];
                            }
                        }
                    ?>
                    <?php foreach ($specRows as $spec): ?>
                        <div class="spec-row row g-2 mb-2 align-items-center">
                            <div class="col-md-4">
                                <input class="form-control form-control-sm" name="spec_key[]"
                                       value="<?php echo htmlspecialchars($spec->spec_key, ENT_QUOTES, 'UTF-8'); ?>"
                                       placeholder="Tên thông số (VD: CPU)">
                            </div>
                            <div class="col-md-7">
                                <input class="form-control form-control-sm" name="spec_value[]"
                                       value="<?php echo htmlspecialchars($spec->spec_value, ENT_QUOTES, 'UTF-8'); ?>"
                                       placeholder="Giá trị (VD: Apple A17 Pro)">
                            </div>
                            <div class="col-md-1 text-center">
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-circle"
                                        onclick="removeSpecRow(this)" title="Xóa dòng" style="width:30px; height:30px; padding:0;">
                                    <i class="fas fa-times" style="font-size:.75rem;"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <button type="button" class="btn btn-sm btn-outline-secondary mt-1" onclick="addSpecRow()">
                    <i class="fas fa-plus me-1"></i>Thêm thông số
                </button>
            </div>

            <hr class="my-4">

            <!-- ==================== BIẾN THỂ SẢN PHẨM ==================== -->
            <div class="section-block mb-4">
                <h6 class="section-label"><i class="fas fa-layer-group text-success me-2"></i>Biến thể sản phẩm</h6>
                <p class="text-muted small mb-3">Mỗi dòng là một phiên bản (màu sắc, dung lượng, RAM, ...). Cộng giá là số tiền cộng thêm so với giá gốc.</p>

                <div class="table-responsive">
                    <table class="table table-sm align-middle" id="variantTable">
                        <thead class="table-light">
                            <tr>
                                <th>Màu sắc</th>
                                <th>RAM</th>
                                <th>Dung lượng</th>
                                <th>Cộng giá (₫)</th>
                                <th>Tồn kho</th>
                                <th>SKU</th>
                                <th style="width:40px;"></th>
                            </tr>
                        </thead>
                        <tbody id="variantBody">
                            <?php
                                $variantRows = $productVariants ?? [];
                                if (empty($variantRows)) {
                                    $variantRows[] = (object)['color'=>'','ram'=>'','storage'=>'','price_delta'=>0,'stock'=>0,'sku'=>''];
                                }
                            ?>
                            <?php foreach ($variantRows as $variant): ?>
                                <tr class="variant-row">
                                    <td><input class="form-control form-control-sm" name="variant_color[]"   value="<?php echo htmlspecialchars($variant->color   ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Đen Titan"></td>
                                    <td><input class="form-control form-control-sm" name="variant_ram[]"     value="<?php echo htmlspecialchars($variant->ram     ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="8GB"></td>
                                    <td><input class="form-control form-control-sm" name="variant_storage[]" value="<?php echo htmlspecialchars($variant->storage  ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="256GB"></td>
                                    <td><input type="number" class="form-control form-control-sm variant-delta" name="variant_price_delta[]" value="<?php echo (int)($variant->price_delta ?? 0); ?>" placeholder="0"></td>
                                    <td><input type="number" class="form-control form-control-sm" name="variant_stock[]" value="<?php echo (int)($variant->stock ?? 0); ?>" min="0" placeholder="0"></td>
                                    <td><input class="form-control form-control-sm" name="variant_sku[]"     value="<?php echo htmlspecialchars($variant->sku     ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="SKU-001"></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-circle p-0"
                                                onclick="removeVariantRow(this)" title="Xóa biến thể"
                                                style="width:28px; height:28px;">
                                            <i class="fas fa-trash" style="font-size:.7rem;"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <button type="button" class="btn btn-sm btn-outline-success" onclick="addVariantRow()">
                    <i class="fas fa-plus me-1"></i>Thêm biến thể
                </button>
            </div>

            <hr class="my-4">

            <!-- ==================== MÔ TẢ CHI TIẾT ==================== -->
            <div class="section-block mb-4">
                <h6 class="section-label"><i class="fas fa-align-left text-muted me-2"></i>Mô tả chi tiết</h6>
                <textarea class="form-control" id="description" name="description"
                          rows="6" required
                          placeholder="Nhập mô tả chi tiết sản phẩm..."
                          ><?php echo htmlspecialchars($product->description, ENT_QUOTES, 'UTF-8'); ?></textarea>
                <div class="d-flex justify-content-between mt-1">
                    <div class="invalid-feedback d-block" id="desc-error" style="display:none!important;"></div>
                    <small class="text-muted ms-auto" id="charCount">0 ký tự</small>
                </div>
            </div>

            <!-- ==================== ACTIONS ==================== -->
            <div class="d-flex justify-content-between gap-3 mt-2">
                <a href="/Product/index" class="btn btn-light border flex-grow-1">
                    <i class="fas fa-times me-1"></i>Hủy bỏ
                </a>
                <button type="submit" class="btn btn-warning flex-grow-1 fw-bold shadow-sm text-dark" id="submitBtn">
                    <i class="fas fa-save me-1"></i>Lưu thay đổi
                </button>
            </div>

        </form>
        </div><!-- /.card-body -->
    </div><!-- /.card -->
</div>
</div>
</div>

<!-- ==================== STYLES ==================== -->
<style>
.section-label {
    font-size: .8rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: var(--muted);
    border-left: 3px solid var(--brand);
    padding-left: 10px;
    margin-bottom: 1rem;
}

/* Vùng kéo thả ảnh */
.img-drop-zone {
    border: 2px dashed #ccc;
    border-radius: 10px;
    background: var(--surface-2);
    cursor: pointer;
    transition: all .2s ease;
    padding: .75rem;
}
.img-drop-zone:hover,
.img-drop-zone.drag-over {
    border-color: var(--brand-strong);
    background: #eaf4ff;
}

/* Gallery thumb với nút xóa */
.gallery-thumb { transition: all .2s; }
.gallery-thumb.marked-delete { opacity: .4; }
.gallery-del-label {
    display: block;
    margin-top: 4px;
    cursor: pointer;
    font-size: .72rem;
    color: var(--danger);
}
.gallery-del-label input { margin-right: 3px; }

/* Variant table */
#variantTable th { font-size: .78rem; font-weight: 600; }

/* Submit loading state */
#submitBtn.loading { pointer-events: none; opacity: .8; }
</style>

<!-- ==================== SCRIPTS ==================== -->
<script>
/* ---------- Tính giá sau giảm realtime ---------- */
function formatVND(num) {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND', maximumFractionDigits: 0 }).format(num);
}
function updateSalePreview() {
    const price = parseFloat(document.getElementById('price').value) || 0;
    const sale  = Math.min(90, Math.max(0, parseFloat(document.getElementById('sale_percent').value) || 0));
    const after = Math.round(price * (1 - sale / 100));
    const saved = price - after;

    document.getElementById('sale-price-value').textContent = formatVND(after);
    document.getElementById('sale-save-text').textContent   = saved > 0 ? 'Tiết kiệm ' + formatVND(saved) : 'Không giảm';
}
document.getElementById('price').addEventListener('input', updateSalePreview);
document.getElementById('sale_percent').addEventListener('input', updateSalePreview);
updateSalePreview();

/* ---------- Preview ảnh đại diện ---------- */
function previewMainImage(event) {
    const file = event.target.files[0];
    if (!file) return;
    if (file.size > 5 * 1024 * 1024) {
        alert('Ảnh phải nhỏ hơn 5MB.');
        event.target.value = '';
        return;
    }
    const reader = new FileReader();
    reader.onload = e => document.getElementById('imagePreview').src = e.target.result;
    reader.readAsDataURL(file);
}

/* ---------- Drag & Drop ảnh đại diện ---------- */
function handleDragOver(e) {
    e.preventDefault();
    document.getElementById('imgDropZone').classList.add('drag-over');
}
function handleDragLeave(e) {
    document.getElementById('imgDropZone').classList.remove('drag-over');
}
function handleDrop(e) {
    e.preventDefault();
    document.getElementById('imgDropZone').classList.remove('drag-over');
    const file = e.dataTransfer.files[0];
    if (!file || !file.type.startsWith('image/')) return;
    if (file.size > 5 * 1024 * 1024) { alert('Ảnh phải nhỏ hơn 5MB.'); return; }
    const dt = new DataTransfer();
    dt.items.add(file);
    document.getElementById('image').files = dt.files;
    const reader = new FileReader();
    reader.onload = ev => document.getElementById('imagePreview').src = ev.target.result;
    reader.readAsDataURL(file);
}

/* ---------- Preview gallery ảnh mới ---------- */
function previewGallery(event) {
    const preview = document.getElementById('newGalleryPreview');
    Array.from(event.target.files).forEach(file => {
        if (!file.type.startsWith('image/') || file.size > 5 * 1024 * 1024) return;
        const reader = new FileReader();
        reader.onload = e => {
            const col = document.createElement('div');
            col.className = 'col-4';
            col.innerHTML = `
                <div class="border rounded p-1 text-center position-relative gallery-thumb">
                    <img src="${e.target.result}" style="height:56px; max-width:100%; object-fit:contain;">
                    <button type="button" class="btn btn-sm btn-danger rounded-circle position-absolute top-0 end-0"
                            style="width:18px; height:18px; padding:0; font-size:.6rem; transform:translate(40%,-40%);"
                            onclick="this.closest('.col-4').remove()" title="Bỏ ảnh này">
                        <i class="fas fa-times"></i>
                    </button>
                    <div class="small text-muted" style="font-size:.65rem;">Mới</div>
                </div>`;
            preview.appendChild(col);
        };
        reader.readAsDataURL(file);
    });
}

/* ---------- Đánh dấu xóa gallery hiện có ---------- */
function toggleDeleteMark(checkbox) {
    const thumb = checkbox.closest('.gallery-thumb');
    if (checkbox.checked) {
        thumb.classList.add('marked-delete');
        thumb.querySelector('.del-text').textContent = '✓ Sẽ xóa';
    } else {
        thumb.classList.remove('marked-delete');
        thumb.querySelector('.del-text').textContent = 'Xóa';
    }
}

/* ---------- Thông số kỹ thuật: thêm / xóa dòng ---------- */
function addSpecRow() {
    const list = document.getElementById('specsList');
    const row  = document.createElement('div');
    row.className = 'spec-row row g-2 mb-2 align-items-center';
    row.innerHTML = `
        <div class="col-md-4"><input class="form-control form-control-sm" name="spec_key[]"   placeholder="Tên thông số"></div>
        <div class="col-md-7"><input class="form-control form-control-sm" name="spec_value[]" placeholder="Giá trị"></div>
        <div class="col-md-1 text-center">
            <button type="button" class="btn btn-sm btn-outline-danger rounded-circle"
                    onclick="removeSpecRow(this)" title="Xóa dòng" style="width:30px; height:30px; padding:0;">
                <i class="fas fa-times" style="font-size:.75rem;"></i>
            </button>
        </div>`;
    list.appendChild(row);
    row.querySelector('input').focus();
}
function removeSpecRow(btn) {
    const allRows = document.querySelectorAll('#specsList .spec-row');
    if (allRows.length <= 1) { alert('Cần ít nhất 1 thông số.'); return; }
    btn.closest('.spec-row').remove();
}

/* ---------- Biến thể: thêm / xóa dòng ---------- */
function addVariantRow() {
    const tbody = document.getElementById('variantBody');
    const tr    = document.createElement('tr');
    tr.className = 'variant-row';
    tr.innerHTML = `
        <td><input class="form-control form-control-sm" name="variant_color[]"       placeholder="Màu sắc"></td>
        <td><input class="form-control form-control-sm" name="variant_ram[]"         placeholder="RAM"></td>
        <td><input class="form-control form-control-sm" name="variant_storage[]"     placeholder="Dung lượng"></td>
        <td><input type="number" class="form-control form-control-sm variant-delta"  name="variant_price_delta[]" value="0" placeholder="0"></td>
        <td><input type="number" class="form-control form-control-sm"                name="variant_stock[]" value="0" min="0" placeholder="0"></td>
        <td><input class="form-control form-control-sm"                              name="variant_sku[]" placeholder="SKU-xxx"></td>
        <td>
            <button type="button" class="btn btn-sm btn-outline-danger rounded-circle p-0"
                    onclick="removeVariantRow(this)" title="Xóa biến thể" style="width:28px; height:28px;">
                <i class="fas fa-trash" style="font-size:.7rem;"></i>
            </button>
        </td>`;
    tbody.appendChild(tr);
    tr.querySelector('input').focus();
}
function removeVariantRow(btn) {
    const rows = document.querySelectorAll('#variantBody .variant-row');
    if (rows.length <= 1) { alert('Cần ít nhất 1 biến thể.'); return; }
    btn.closest('tr').remove();
}

/* ---------- Đếm ký tự mô tả ---------- */
const descEl = document.getElementById('description');
const charCountEl = document.getElementById('charCount');
function updateCharCount() {
    charCountEl.textContent = descEl.value.length + ' ký tự';
}
descEl.addEventListener('input', updateCharCount);
updateCharCount();

/* ---------- Client-side validation trước khi submit ---------- */
document.getElementById('editForm').addEventListener('submit', function (e) {
    let valid = true;

    // Tên sản phẩm
    const nameEl = document.getElementById('name');
    if (!nameEl.value.trim()) {
        nameEl.classList.add('is-invalid');
        valid = false;
    } else {
        nameEl.classList.remove('is-invalid');
    }

    // Giá
    const priceEl = document.getElementById('price');
    if (!priceEl.value || parseFloat(priceEl.value) < 1) {
        priceEl.classList.add('is-invalid');
        valid = false;
    } else {
        priceEl.classList.remove('is-invalid');
    }

    // Mô tả
    if (!descEl.value.trim()) {
        descEl.classList.add('is-invalid');
        valid = false;
    } else {
        descEl.classList.remove('is-invalid');
    }

    if (!valid) {
        e.preventDefault();
        // Cuộn lên field đầu tiên bị lỗi
        const firstInvalid = document.querySelector('.is-invalid');
        if (firstInvalid) firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
    }

    // Trạng thái loading trên nút
    const btn = document.getElementById('submitBtn');
    btn.classList.add('loading');
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang lưu...';
});

// Xóa trạng thái invalid khi người dùng bắt đầu gõ lại
['name', 'price', 'description'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.addEventListener('input', () => el.classList.remove('is-invalid'));
});
</script>