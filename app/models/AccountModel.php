<?php
class AccountModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // 1. Đăng ký tài khoản mới
    public function register($username, $fullname, $password) {
        // Mã hóa mật khẩu bằng hàm password_hash để đảm bảo bảo mật an toàn
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $query = "INSERT INTO account (username, fullname, password, role) VALUES (:username, :fullname, :password, 'user')";
        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':fullname', $fullname);
        $stmt->bindParam(':password', $hashed_password);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // 2. Kiểm tra tài khoản tồn tại (dùng khi Đăng ký để tránh trùng tên)
    public function checkUsernameExists($username) {
        $query = "SELECT id FROM account WHERE username = :username LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return true; // Đã tồn tại
        }
        return false; // Chưa tồn tại
    }

    // 3. Lấy thông tin tài khoản bằng Username (dùng khi Đăng nhập)
    public function getAccountByUsername($username) {
        $query = "SELECT * FROM account WHERE username = :username LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}