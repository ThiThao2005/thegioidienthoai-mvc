<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đặt lại mật khẩu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-5">

            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h3 class="fw-bold text-center mb-3">ĐẶT LẠI MẬT KHẨU</h3>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger small">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form action="/Account/processResetPassword" method="POST">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">

                    <div class="mb-3">
                        <label class="form-label">Mật khẩu mới</label>
                        <input type="password" name="new_password" class="form-control" required minlength="6">
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Nhập lại mật khẩu mới</label>
                        <input type="password" name="confirm_password" class="form-control" required minlength="6">
                    </div>

                    <button type="submit" class="btn btn-warning w-100 fw-bold">
                        Đặt lại mật khẩu
                    </button>
                </form>

            </div>

        </div>
    </div>
</div>

</body>
</html>