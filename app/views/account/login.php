<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Thế Giới Điện Thoại</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://accounts.google.com/gsi/client" async defer></script>
</head>
<body class="bg-light">

<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-5">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h3 class="fw-bold text-dark">ĐĂNG NHẬP</h3>
                        <p class="text-secondary small">Chào mừng bạn quay trở lại với Thế Giới Điện Thoại</p>
                    </div>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger rounded-3 small py-2" role="alert">
                            <i class="fas fa-exclamation-circle me-1"></i> <?= $error ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($success)): ?>
                        <div class="alert alert-success rounded-3 small py-2" role="alert">
                            <i class="fas fa-check-circle me-1"></i> <?= $success ?>
                        </div>
                    <?php endif; ?>

                    <form action="/project1/Account/processLogin" method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label fw-semibold text-secondary small">Tên đăng nhập (Email / Tài khoản)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-secondary"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control border-start-0 py-2 ps-0" id="username" name="username" placeholder="Nhập tên tài khoản..." required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label fw-semibold text-secondary small">Mật khẩu</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-secondary"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control border-start-0 py-2 ps-0" id="password" name="password" placeholder="Nhập mật khẩu..." required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-warning w-100 fw-bold py-2 rounded-3 text-uppercase btn-login mb-3">
                            Đăng nhập
                        </button>
                    </form>

                    <div class="d-flex align-items-center my-3">
                        <hr class="flex-grow-1 my-0 opacity-25">
                        <span class="mx-3 text-secondary small">Hoặc đăng nhập bằng</span>
                        <hr class="flex-grow-1 my-0 opacity-25">
                    </div>

                    <div class="d-flex justify-content-center mb-4">
                        <div id="g_id_onload"
                             data-client_id="694098371336-ie2kni4ae1glc5cui0nam5rqp61phk5q.apps.googleusercontent.com"
                             data-context="signin"
                             data-ux_mode="popup"
                             data-callback="handleCredentialResponse"
                             data-auto_prompt="false">
                        </div>

                        <div class="g_id_signin"
                             data-type="standard"
                             data-shape="pill"
                             data-theme="outline"
                             data-text="signin_with"
                             data-size="large"
                             data-logo_alignment="left"
                             data-width="320">
                        </div>
                    </div>

                    <div class="text-center text-secondary small">
                        Bạn chưa có tài khoản? <a href="/project1/Account/register" class="text-warning fw-bold text-decoration-none">Đăng ký ngay</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function handleCredentialResponse(response) {
    const formData = new FormData();
    formData.append('credential', response.credential);

    // Gửi Token qua hàm googleLogin bằng Ajax
    fetch('/project1/Account/googleLogin', {
        method: 'POST',
        body: formData
    })
    // FIX LỖI CONSOLE: Đọc text trước khi parse JSON để nếu có lỗi PHP (thẻ <br />, < b>) thì không bị sập giao diện
    .then(res => res.text())
    .then(text => {
        try {
            const data = JSON.parse(text);
            if (data.status === 'success') {
                window.location.href = data.redirect;
            } else {
                alert('Đăng nhập bằng Google thất bại: ' + data.message);
            }
        } catch (err) {
            console.error("Dữ liệu Server trả về không phải JSON chuẩn:", text);
            alert("Đã xảy ra lỗi hệ thống từ phía Server. Hãy kiểm tra tab Network!");
        }
    })
    .catch(err => {
        console.error('Lỗi kết nối API:', err);
    });
}
</script>

<style>
    .btn-login {
        background-color: #ffd400 !important;
        border: none;
        transition: all 0.2s ease-in-out;
    }
    .btn-login:hover {
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