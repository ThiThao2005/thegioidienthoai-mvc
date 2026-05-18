<?php
// Gọi SessionHelper để kiểm tra trạng thái nếu cần
SessionHelper::start();
?>

<div class="row justify-content-center mt-5">
    <div class="col-md-6 col-sm-12">
        <div class="card border-0 shadow-sm rounded-3 text-center p-5 bg-white">
            <div class="card-body">
                <div class="mb-4 text-warning">
                    <i class="fas fa-sign-out-alt fa-4x animate__animated animate__pulse animate__infinite"></i>
                </div>
                
                <h3 class="fw-bold mb-3 text-dark">Đang đăng xuất...</h3>
                <p class="text-secondary mb-4">
                    Hệ thống đang tiến hành xóa phiên làm việc và bảo mật tài khoản của bạn.
                </p>

                <div class="spinner-border text-warning mb-4" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>

                <div class="pt-2">
                    <small class="text-muted d-block">
                        Nếu trình duyệt không tự chuyển hướng, vui lòng nhấn vào nút bên dưới:
                    </small>
                    <a href="/project1/Account/executeLogout" class="btn btn-warning fw-bold px-4 rounded-pill mt-3 text-dark btn-logout-manual">
                        Xác nhận Đăng xuất ngay
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .btn-logout-manual {
        background-color: #ffd400 !important;
        border: none;
        transition: all 0.2s ease-in-out;
    }
    .btn-logout-manual:hover {
        background-color: #fccc00 !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(252, 204, 0, 0.3);
    }
</style>

<script>
    setTimeout(function() {
        window.location.href = '/project1/Account/executeLogout';
    }, 1500);
</script>