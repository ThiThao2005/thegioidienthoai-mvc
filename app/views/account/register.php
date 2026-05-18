<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản - Thế Giới Điện Thoại</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-5">
            <div class="card border-0 shadow-sm rounded-4 p-4 my-5">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h3 class="fw-bold text-dark">ĐĂNG KÝ TÀI KHOẢN</h3>
                        <p class="text-secondary small">Tạo tài khoản để trải nghiệm mua sắm tuyệt vời nhất</p>
                    </div>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger rounded-3 small py-2" role="alert">
                            <i class="fas fa-exclamation-circle me-1"></i> <?= $error ?>
                        </div>
                    <?php endif; ?>

                    <form action="/project1/Account/processRegister" method="POST">
                        
                        <div class="mb-3">
                            <label for="fullname" class="form-label fw-semibold text-secondary small">Họ và tên <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-secondary"><i class="fas fa-id-card"></i></span>
                                <input type="text" class="form-control border-start-0 py-2 ps-0" id="fullname" name="fullname" placeholder="Ví dụ: Nguyễn Văn A" required value="<?= isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : '' ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="username" class="form-label fw-semibold text-secondary small">Tên đăng nhập (Tài khoản) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-secondary"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control border-start-0 py-2 ps-0" id="username" name="username" placeholder="Nhập tên tài khoản mong muốn..." required value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold text-secondary small">Mật khẩu <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-secondary"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control border-start-0 py-2 ps-0" id="password" name="password" placeholder="Nhập mật khẩu an toàn..." required minlength="6">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="confirm_password" class="form-label fw-semibold text-secondary small">Nhập lại mật khẩu <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-secondary"><i class="fas fa-check-double"></i></span>
                                <input type="password" class="form-control border-start-0 py-2 ps-0" id="confirm_password" name="confirm_password" placeholder="Xác nhận lại mật khẩu..." required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-warning w-100 fw-bold py-2 rounded-3 text-uppercase btn-register mb-3">
                            Đăng ký ngay
                        </button>
                    </form>

                    <div class="text-center text-secondary small">
                        Bạn đã có tài khoản rồi? <a href="/project1/Account/login" class="text-warning fw-bold text-decoration-none">Đăng nhập ngay</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .btn-register {
        background-color: #ffd400 !important;
        border: none;
        transition: all 0.2s ease-in-out;
    }
    .btn-register:hover {
        background-color: #fccc00 !important;
        transform: translateY(-1px);
    }
    .input-group:focus-within .input-group-text,
    .input-group:focus-within .form-control {
        border-color: #ffd400 !important;
        box-shadow: none;
    }
</style>

</body>
</html>