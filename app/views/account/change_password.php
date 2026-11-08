<?php require_once 'app/views/layout/header.php'; ?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h3 class="fw-bold text-center mb-3">ĐỔI MẬT KHẨU</h3>

                <?php if (isset($success)): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form action="/Account/processChangePassword" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu hiện tại</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mật khẩu mới</label>
                        <input type="password" name="new_password" class="form-control" required minlength="6">
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Nhập lại mật khẩu mới</label>
                        <input type="password" name="confirm_password" class="form-control" required minlength="6">
                    </div>

                    <button type="submit" class="btn btn-warning w-100 fw-bold">
                        Cập nhật mật khẩu
                    </button>
                </form>

            </div>

        </div>
    </div>
</div>

<?php require_once 'app/views/layout/footer.php'; ?>