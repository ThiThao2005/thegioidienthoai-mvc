<div class="row justify-content-center mt-5">
    <div class="col-md-8 col-lg-6 text-center">
        <div class="card border-0 shadow-sm rounded-3 p-4 p-md-5 bg-white">
            <div class="card-body">
                <div class="success-icon-box mb-4">
                    <i class="fas fa-check-circle text-success display-1 animate-bounce"></i>
                </div>
                
                <h2 class="fw-bold text-dark mb-3">Đặt hàng thành công!</h2>
                <p class="text-secondary mb-4">
                    Cảm ơn bạn đã tin tưởng và mua sắm tại <strong>Thế Giới Điện Thoại</strong>. 
                    Đơn hàng của bạn đã được hệ thống ghi nhận và đang trong quá trình xử lý.
                </p>

                <div class="bg-light p-3 rounded-3 text-start mb-4 border-start border-warning border-3">
                    <h6 class="fw-bold text-dark mb-2"><i class="fas fa-info-circle me-1 text-warning"></i> Lưu ý giao hàng:</h6>
                    <ul class="text-secondary small mb-0 ps-3">
                        <li class="mb-1">Nhân viên tổng đài sẽ gọi điện xác nhận đơn hàng với bạn trong vòng 10 - 15 phút tới.</li>
                        <li>Bạn vui lòng giữ điện thoại để shipper có thể liên lạc khi giao hàng đến nơi.</li>
                    </ul>
                </div>

                <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                    <a href="/project1/Product/index" class="btn btn-warning fw-bold px-4 py-2.5 rounded-pill btn-back-home text-dark text-decoration-none">
                        <i class="fas fa-shopping-bag me-1"></i> Tiếp tục mua sắm
                    </a>
                </div>
            </div>
        </div>

        <p class="text-muted small mt-4">
            Nếu cần hỗ trợ gấp, vui lòng liên hệ hotline miễn phí <span class="text-danger fw-bold">1800.1060</span> (7:30 - 22:00)
        </p>
    </div>
</div>

<style>
    /* Hiệu ứng nảy nhẹ cho icon lúc vừa tải trang */
    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
    .animate-bounce {
        animation: bounce 1s ease-in-out 1;
    }

    /* Định dạng nút quay lại */
    .btn-back-home {
        background-color: #ffd400 !important;
        border: none;
        transition: all 0.2s ease-in-out;
    }
    .btn-back-home:hover {
        background-color: #fccc00 !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(252, 204, 0, 0.3);
    }
</style>