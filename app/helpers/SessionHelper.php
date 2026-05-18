<?php
class SessionHelper {
    // Khởi động session an toàn
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Kiểm tra người dùng đã đăng nhập chưa
    public static function isLoggedIn() {
        self::start();
        return isset($_SESSION['user']);
    }

    // Lấy thông tin cụ thể của user từ Session (fullname, email, role,...)
    public static function getUserData($key) {
        self::start();
        if (isset($_SESSION['user'][$key])) {
            return $_SESSION['user'][$key];
        }
        return null;
    }

    // Kiểm tra xem có phải tài khoản Admin hay không
    public static function isAdmin() {
        self::start();
        // Giả định cột phân quyền trong DB của bạn tên là 'role' (giá trị 'admin' hoặc 'user')
        return self::isLoggedIn() && (self::getUserData('role') === 'admin');
    }

    // Hàm tiện ích: Bắt buộc phải là Admin, nếu không sẽ bị đẩy hướng đi nơi khác
    public static function requireAdmin() {
        if (!self::isAdmin()) {
            // Nếu không phải admin, chuyển hướng thẳng về trang đăng nhập hoặc báo lỗi
            header('Location: /project1/Account/login');
            exit();
        }
    }
    
    // Hàm tiện ích: Bắt buộc phải đăng nhập (dành cho trang Checkout, Cart)
    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            header('Location: /project1/Account/login');
            exit();
        }
    }

    // ==========================================
    // ➕ XỬ LÝ LƯU SESSION ĐĂNG NHẬP
    // ==========================================

    // Hàm xử lý lưu thông tin người dùng khi đăng nhập thành công
    public static function login($user) {
        self::start();
        // Lưu toàn bộ mảng thông tin user (id, username, role, fullname...) từ DB vào Session
        $_SESSION['user'] = $user; 
    }

    // ==========================================
    // 🛠️ ĐÃ SỬA: FIX LỖI SẬP INTERFACE DO ARRAY_SUM
    // ==========================================
    public static function getCartCount() {
        self::start();
        
        if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart']) || empty($_SESSION['cart'])) {
            return 0; // Giỏ hàng trống
        }

        $count = 0;
        foreach ($_SESSION['cart'] as $item) {
            if (is_array($item)) {
                // Trường hợp giỏ hàng là mảng đa chiều chứa object/array có trường quantity
                // Ví dụ: [['id' => 12, 'quantity' => 1]]
                $count += $item['quantity'] ?? $item['Quantity'] ?? 1;
            } else {
                // Trường hợp giỏ hàng lưu dạng phẳng: mã_sản_phẩm => số_lượng
                // Ví dụ: [12 => 1, 15 => 2]
                $count += intval($item);
            }
        }
        return $count;
    }

    // Hàm xử lý xóa sạch session khi đăng xuất
    public static function logout() {
        self::start();
        if (isset($_SESSION['user'])) {
            unset($_SESSION['user']);
        }
        // Hủy toàn bộ session trên server
        session_destroy();
    }
}