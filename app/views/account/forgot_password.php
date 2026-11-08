<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quên mật khẩu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/public/css/theme.css">
</head>
<body class="bg-light">

<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-5">

            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h3 class="fw-bold text-center mb-3">QUÊN MẬT KHẨU</h3>
                <p class="text-secondary small text-center">
                    Nhập email tài khoản để nhận link đặt lại mật khẩu.
                </p>

                <?php if (isset($success)): ?>
                    <div class="alert alert-success small">
                        <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger small">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form action="/Account/processForgotPassword" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Email tài khoản</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-warning w-100 fw-bold">
                        Gửi yêu cầu
                    </button>
                </form>

                <div class="text-center mt-3">
                    <a href="/Account/login" class="text-decoration-none">Quay lại đăng nhập</a>
                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>