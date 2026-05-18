<div class="container-fluid px-4 py-4">
    <div class="row g-4">
        <div class="col-lg-3 col-md-4">
            <div class="card border-0 shadow-sm p-3" style="border-radius: 12px;">
                <div class="d-flex flex-column gap-2">
                    <a href="/project1/Product/dashboard" class="nav-link p-3 text-secondary fw-semibold">
                        <i class="fas fa-chart-pie me-2 text-warning"></i>Tổng quan hệ thống
                    </a>
                    
                    <a href="/project1/Product/categories" class="nav-link p-3 bg-light text-dark fw-bold active border-start border-warning border-3">
                        <i class="fas fa-tags me-2 text-success"></i>Quản lý danh mục
                    </a>
                    <a href="/project1/Product/orders" class="nav-link p-3 text-secondary fw-semibold">
                        <i class="fas fa-shopping-bag me-2 text-danger"></i>Quản lý đơn hàng
                    </a>
                    <a href="/project1/Product/users" class="nav-link p-3 text-secondary fw-semibold">
                        <i class="fas fa-users me-2 text-info"></i>Quản lý người dùng
                    </a>
                    <hr class="my-2 opacity-50">
                    <a href="/project1/Product/index" class="nav-link p-3 text-dark fw-semibold">
                        <i class="fas fa-arrow-left me-2"></i>Quay lại trang chủ
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-9 col-md-8">
            <h3 class="fw-bold text-dark text-uppercase mb-4">
                <i class="fas fa-tags text-warning me-2"></i>Quản lý danh mục sản phẩm
            </h3>

            <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 12px;">
                <h5 class="fw-bold mb-3 text-secondary">Thêm danh mục mới</h5>
                <form action="/project1/Product/addCategory" method="POST" class="row g-3 align-items-center">
                    <div class="col-md-8">
                        <input type="text" name="name" class="form-control rounded-pill px-4" placeholder="Nhập tên danh mục (Ví dụ: Tai nghe, Sạc dự phòng...)" required>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-warning fw-bold w-100 rounded-pill shadow-sm">
                            <i class="fas fa-plus-circle me-1"></i> Thêm danh mục
                        </button>
                    </div>
                </form>
            </div>

            <div class="card border-0 shadow-sm p-4" style="border-radius: 12px;">
                <h5 class="fw-bold mb-3 text-secondary">Danh sách danh mục hiện có</h5>
                <div class="table-responsive style-container bg-white rounded-3 border">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr class="small text-uppercase">
                                <th class="text-center py-3" style="width: 100px;">Mã ID</th>
                                <th class="py-3">Tên danh mục</th>
                                <th class="text-center py-3" style="width: 250px;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($categories)): ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-5">
                                        <i class="fas fa-folder-open fa-2x mb-2 d-block text-opacity-25 text-secondary"></i>
                                        Chưa có danh mục nào trong hệ thống.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($categories as $cat): ?>
                                <tr style="transition: all 0.2s ease; border-bottom: 1px solid #f1f1f1;">
                                    <td class="text-center fw-bold text-secondary py-3">
                                        <span class="badge bg-light text-dark border px-2 py-1" style="min-width: 45px;">
                                            #<?= $cat->id ?>
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <span class="fw-bold text-dark fs-6"><?= htmlspecialchars($cat->name, ENT_QUOTES, 'UTF-8') ?></span>
                                    </td>
                                    <td class="text-center py-3">
                                        <div class="d-flex gap-2 justify-content-center">
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-primary rounded-pill px-3 edit-cat-btn"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editCategoryModal"
                                                    data-id="<?= $cat->id ?>"
                                                    data-name="<?= htmlspecialchars($cat->name, ENT_QUOTES, 'UTF-8') ?>">
                                                <i class="fas fa-edit me-1"></i>Sửa
                                            </button>
                                            
                                            <a href="/project1/Product/deleteCategory?id=<?= $cat->id ?>" 
                                               class="btn btn-sm btn-outline-danger rounded-pill px-3" 
                                               onclick="return confirm('Bạn có chắc chắn muốn xóa danh mục này? Các sản phẩm thuộc danh mục này có thể bị ảnh hưởng.');">
                                                <i class="fas fa-trash me-1"></i>Xóa
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 12px;">
            <div class="modal-header bg-dark text-white" style="border-top-left-radius: 12px; border-top-right-radius: 12px;">
                <h5 class="modal-title fw-bold" id="editCategoryModalLabel">
                    <i class="fas fa-edit text-warning me-2"></i>Cập nhật tên danh mục
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/project1/Product/updateCategory" method="POST">
                <div class="modal-body p-4">
                    <input type="hidden" name="id" id="edit-cat-id">
                    
                    <div class="mb-3">
                        <label for="edit-cat-name" class="form-label fw-bold text-secondary">Tên danh mục mới</label>
                        <input type="text" name="name" id="edit-cat-name" class="form-control rounded-pill px-4" required placeholder="Nhập tên danh mục mới...">
                    </div>
                </div>
                <div class="modal-footer bg-light" style="border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
                    <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="submit" class="btn btn-warning fw-bold rounded-pill px-4 shadow-sm">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const editButtons = document.querySelectorAll(".edit-cat-btn");
    editButtons.forEach(button => {
        button.addEventListener("click", function () {
            // Lấy dữ liệu từ thuộc tính data-* của nút được ấn
            const catId = this.getAttribute("data-id");
            const catName = this.getAttribute("data-name");
            
            // Điền giá trị vào các ô input trong Modal tương ứng
            document.getElementById("edit-cat-id").value = catId;
            document.getElementById("edit-cat-name").value = catName;
        });
    });
});
</script>