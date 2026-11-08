<?php

require_once 'app/helpers/SessionHelper.php';
require_once 'app/helpers/MailHelper.php';
require_once 'app/models/AccountModel.php';

class AccountController
{
    private $accountModel;
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
        $this->accountModel = new AccountModel($this->db);
    }

    public function login()
    {
        $this->checkRememberLogin();

        if (SessionHelper::isLoggedIn()) {
            header('Location: /Account/profile');
            exit();
        }

        require_once 'app/views/account/login.php';
    }

    public function processLogin()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /Account/login');
            exit();
        }

        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $remember = isset($_POST['remember']);

        $user = $this->accountModel->getAccountByUsername($username);

        if (!$user || !password_verify($password, $user['password'])) {
            $error = "Tên đăng nhập hoặc mật khẩu không chính xác!";
            require_once 'app/views/account/login.php';
            return;
        }

        if (($user['status'] ?? 'active') === 'locked') {
            $error = "Tài khoản của bạn đã bị khóa. Vui lòng liên hệ Admin.";
            require_once 'app/views/account/login.php';
            return;
        }

        if (isset($user['email_verified']) && (int)$user['email_verified'] === 0) {
            $error = "Tài khoản chưa xác thực Email. Vui lòng kiểm tra Email để xác thực tài khoản.";
            require_once 'app/views/account/login.php';
            return;
        }

        SessionHelper::login($user);

        if ($remember) {
            $rememberToken = bin2hex(random_bytes(32));
            $this->accountModel->saveRememberToken($user['id'], $rememberToken);

            setcookie(
                'remember_token',
                $rememberToken,
                time() + (86400 * 30),
                '/',
                '',
                false,
                true
            );
        }

        header('Location: /Account/profile');
        exit();
    }

    public function register()
    {
        if (SessionHelper::isLoggedIn()) {
            header('Location: /Account/profile');
            exit();
        }

        require_once 'app/views/account/register.php';
    }

    public function processRegister()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /Account/register');
            exit();
        }

        $username = trim($_POST['username'] ?? '');
        $fullname = trim($_POST['fullname'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $confirm_password = trim($_POST['confirm_password'] ?? '');

        if (empty($fullname)) {
            $error = "Họ và tên không được để trống!";
            require_once 'app/views/account/register.php';
            return;
        }

        if (empty($username)) {
            $error = "Tên đăng nhập không được để trống!";
            require_once 'app/views/account/register.php';
            return;
        }

        if (empty($email)) {
            $error = "Email không được để trống!";
            require_once 'app/views/account/register.php';
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Email không hợp lệ!";
            require_once 'app/views/account/register.php';
            return;
        }

        if (strlen($password) < 6) {
            $error = "Mật khẩu phải có ít nhất 6 ký tự!";
            require_once 'app/views/account/register.php';
            return;
        }

        if ($password !== $confirm_password) {
            $error = "Mật khẩu nhập lại không trùng khớp!";
            require_once 'app/views/account/register.php';
            return;
        }

        if ($this->accountModel->checkUsernameExists($username)) {
            $error = "Tên đăng nhập này đã tồn tại trên hệ thống!";
            require_once 'app/views/account/register.php';
            return;
        }

        if (method_exists($this->accountModel, 'checkEmailExists') && $this->accountModel->checkEmailExists($email)) {
            $error = "Email này đã được sử dụng!";
            require_once 'app/views/account/register.php';
            return;
        }

        $token = $this->accountModel->register(
            $username,
            $fullname,
            $email,
            $phone,
            $address,
            $password
        );

        if ($token) {
            $verifyLink = $this->getBaseUrl() . "/Account/verifyEmail?token=" . urlencode($token);

            $sent = MailHelper::sendVerifyEmail($email, $fullname, $verifyLink);

            if ($sent) {
                $success = "Đăng ký thành công! Vui lòng kiểm tra Email để xác thực tài khoản.";
            } else {
                $success = "Đăng ký thành công! Nhưng hệ thống gửi Email thất bại. Link xác thực của bạn: " . $verifyLink;
            }

            require_once 'app/views/account/login.php';
            return;
        }

        $error = "Đã xảy ra lỗi trong quá trình đăng ký. Vui lòng thử lại!";
        require_once 'app/views/account/register.php';
    }

    public function verifyEmail()
    {
        $token = $_GET['token'] ?? '';

        if (empty($token)) {
            $error = "Token xác thực không hợp lệ.";
            require_once 'app/views/account/login.php';
            return;
        }

        $user = $this->accountModel->getAccountByVerifyToken($token);

        if (!$user) {
            $error = "Link xác thực không hợp lệ hoặc tài khoản đã được xác thực.";
            require_once 'app/views/account/login.php';
            return;
        }

        if (isset($user['email_verified']) && (int)$user['email_verified'] === 1) {
            $success = "Tài khoản này đã được xác thực trước đó. Bạn có thể đăng nhập.";
            require_once 'app/views/account/login.php';
            return;
        }

        $verified = $this->accountModel->verifyEmail($token);

        if ($verified) {
            $success = "Xác thực Email thành công. Bạn có thể đăng nhập.";
        } else {
            $error = "Xác thực Email thất bại. Vui lòng thử lại.";
        }

        require_once 'app/views/account/login.php';
    }

    public function googleLogin()
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['credential'])) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Thiếu credential từ Google.'
            ]);
            exit();
        }

        $credential = $_POST['credential'];
        $parts = explode('.', $credential);

        if (count($parts) !== 3) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Token Google không hợp lệ.'
            ]);
            exit();
        }

        $payloadJson = base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1]));
        $payload = json_decode($payloadJson, true);

        if (!$payload || empty($payload['email'])) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Không lấy được email từ Google.'
            ]);
            exit();
        }

        $googleEmail = $payload['email'];
        $googleName = $payload['name'] ?? $googleEmail;
        $googlePicture = $payload['picture'] ?? null;

        $user = $this->accountModel->getAccountByUsername($googleEmail);

        if (!$user) {
            $randomPassword = bin2hex(random_bytes(8));

            $this->accountModel->register(
                $googleEmail,
                $googleName,
                $googleEmail,
                '',
                '',
                $randomPassword
            );

            $user = $this->accountModel->getAccountByUsername($googleEmail);

            if ($user && method_exists($this->accountModel, 'verifyGoogleAccount')) {
                $this->accountModel->verifyGoogleAccount($user['id']);
                $user = $this->accountModel->getAccountByUsername($googleEmail);
            }
        }

        if (!$user) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Không thể tạo hoặc lấy tài khoản Google.'
            ]);
            exit();
        }

        if (($user['status'] ?? 'active') === 'locked') {
            echo json_encode([
                'status' => 'error',
                'message' => 'Tài khoản đã bị khóa.'
            ]);
            exit();
        }

        if (method_exists($this->accountModel, 'verifyGoogleAccount')) {
            $this->accountModel->verifyGoogleAccount($user['id']);
            $user = $this->accountModel->getAccountByUsername($googleEmail);
        }

        SessionHelper::login($user);

        $_SESSION['user_id'] = $user['id'] ?? null;
        $_SESSION['username'] = $user['username'] ?? $googleEmail;
        $_SESSION['fullname'] = $user['fullname'] ?? $googleName;
        $_SESSION['email'] = $user['email'] ?? $googleEmail;
        $_SESSION['role'] = $user['role'] ?? 'user';
        $_SESSION['avatar'] = $user['avatar'] ?? $googlePicture;

        echo json_encode([
            'status' => 'success',
            'redirect' => '/Account/profile'
        ]);
        exit();
    }

    public function profile()
    {
        if (!SessionHelper::isLoggedIn()) {
            header('Location: /Account/login');
            exit();
        }

        $userId = $_SESSION['user']['id'] ?? $_SESSION['user_id'] ?? null;

        if (!$userId) {
            header('Location: /Account/login');
            exit();
        }

        $user = $this->accountModel->getAccountById($userId);

        if (!$user) {
            SessionHelper::logout();
            header('Location: /Account/login');
            exit();
        }

        require_once 'app/views/account/profile.php';
    }

    public function updateProfile()
    {
        if (!SessionHelper::isLoggedIn()) {
            header('Location: /Account/login');
            exit();
        }

        $userId = $_SESSION['user']['id'] ?? $_SESSION['user_id'] ?? null;

        $fullname = trim($_POST['fullname'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');

        $avatarName = null;

        if (!empty($_FILES['avatar']['name'])) {
            $uploadDir = 'public/uploads/avatars/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            $fileType = mime_content_type($_FILES['avatar']['tmp_name']);
            $fileSize = $_FILES['avatar']['size'];

            if (!in_array($fileType, $allowedTypes)) {
                $error = "Ảnh đại diện chỉ được dùng JPG, PNG, WEBP hoặc GIF.";
                $user = $this->accountModel->getAccountById($userId);
                require_once 'app/views/account/profile.php';
                return;
            }

            if ($fileSize > 5 * 1024 * 1024) {
                $error = "Ảnh đại diện không được vượt quá 5MB.";
                $user = $this->accountModel->getAccountById($userId);
                require_once 'app/views/account/profile.php';
                return;
            }

            $extension = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
            $avatarName = 'avatar_' . $userId . '_' . time() . '.' . $extension;

            move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadDir . $avatarName);
        }

        if ($this->accountModel->updateProfile($userId, $fullname, $email, $phone, $address, $avatarName)) {
            $_SESSION['fullname'] = $fullname;
            $_SESSION['email'] = $email;

            if (isset($_SESSION['user'])) {
                $_SESSION['user']['fullname'] = $fullname;
                $_SESSION['user']['email'] = $email;
            }

            $success = "Cập nhật hồ sơ thành công.";
        } else {
            $error = "Cập nhật hồ sơ thất bại.";
        }

        $user = $this->accountModel->getAccountById($userId);
        require_once 'app/views/account/profile.php';
    }

    public function logout()
    {
        require_once 'app/views/account/logout.php';
    }

    public function executeLogout()
    {
        if (SessionHelper::isLoggedIn()) {
            $userId = $_SESSION['user']['id'] ?? $_SESSION['user_id'] ?? null;

            if ($userId) {
                $this->accountModel->clearRememberToken($userId);
            }
        }

        setcookie('remember_token', '', time() - 3600, '/', '', false, true);

        SessionHelper::logout();

        header('Location: /Product/index');
        exit();
    }

    public function checkRememberLogin()
    {
        if (!SessionHelper::isLoggedIn() && isset($_COOKIE['remember_token'])) {
            $token = $_COOKIE['remember_token'];
            $user = $this->accountModel->getAccountByRememberToken($token);

            if ($user && ($user['status'] ?? 'active') === 'active') {
                SessionHelper::login($user);
            }
        }
    }

    public function changePassword()
    {
        if (!SessionHelper::isLoggedIn()) {
            header('Location: /Account/login');
            exit();
        }

        require_once 'app/views/account/change_password.php';
    }

    public function processChangePassword()
    {
        if (!SessionHelper::isLoggedIn()) {
            header('Location: /Account/login');
            exit();
        }

        $userId = $_SESSION['user']['id'] ?? $_SESSION['user_id'] ?? null;
        $user = $this->accountModel->getAccountById($userId);

        $currentPassword = trim($_POST['current_password'] ?? '');
        $newPassword = trim($_POST['new_password'] ?? '');
        $confirmPassword = trim($_POST['confirm_password'] ?? '');

        $account = $this->accountModel->getAccountByUsername($user['username']);

        if (!password_verify($currentPassword, $account['password'])) {
            $error = "Mật khẩu hiện tại không chính xác.";
            require_once 'app/views/account/change_password.php';
            return;
        }

        if (strlen($newPassword) < 6) {
            $error = "Mật khẩu mới phải có ít nhất 6 ký tự.";
            require_once 'app/views/account/change_password.php';
            return;
        }

        if ($newPassword !== $confirmPassword) {
            $error = "Mật khẩu nhập lại không trùng khớp.";
            require_once 'app/views/account/change_password.php';
            return;
        }

        $this->accountModel->changePassword($userId, $newPassword);

        $success = "Đổi mật khẩu thành công.";
        require_once 'app/views/account/change_password.php';
    }

    public function forgotPassword()
    {
        require_once 'app/views/account/forgot_password.php';
    }

    public function processForgotPassword()
    {
        $email = trim($_POST['email'] ?? '');
        $user = $this->accountModel->getAccountByEmail($email);

        if (!$user) {
            $error = "Email không tồn tại trong hệ thống.";
            require_once 'app/views/account/forgot_password.php';
            return;
        }

        $token = bin2hex(random_bytes(32));
        $expire = date('Y-m-d H:i:s', strtotime('+30 minutes'));

        $this->accountModel->saveResetToken($email, $token, $expire);

        $resetLink = $this->getBaseUrl() . "/Account/resetPassword?token=" . urlencode($token);
        $success = "Link đặt lại mật khẩu của bạn: " . $resetLink;

        require_once 'app/views/account/forgot_password.php';
    }

    public function resetPassword()
    {
        $token = $_GET['token'] ?? '';
        $user = $this->accountModel->getAccountByResetToken($token);

        if (!$user) {
            $error = "Link đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.";
            require_once 'app/views/account/forgot_password.php';
            return;
        }

        require_once 'app/views/account/reset_password.php';
    }

    public function processResetPassword()
    {
        $token = $_POST['token'] ?? '';
        $newPassword = trim($_POST['new_password'] ?? '');
        $confirmPassword = trim($_POST['confirm_password'] ?? '');

        $user = $this->accountModel->getAccountByResetToken($token);

        if (!$user) {
            $error = "Link đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.";
            require_once 'app/views/account/forgot_password.php';
            return;
        }

        if (strlen($newPassword) < 6) {
            $error = "Mật khẩu mới phải có ít nhất 6 ký tự.";
            require_once 'app/views/account/reset_password.php';
            return;
        }

        if ($newPassword !== $confirmPassword) {
            $error = "Mật khẩu nhập lại không trùng khớp.";
            require_once 'app/views/account/reset_password.php';
            return;
        }

        $this->accountModel->resetPassword($token, $newPassword);

        $success = "Đặt lại mật khẩu thành công. Vui lòng đăng nhập.";
        require_once 'app/views/account/login.php';
    }

    public function users()
    {
        if (!SessionHelper::isLoggedIn() || !SessionHelper::isAdmin()) {
            header('Location: /Account/login');
            exit();
        }

        $users = $this->accountModel->getAllUsers();
        require_once 'app/views/account/users.php';
    }

    public function lockUser()
    {
        if (!SessionHelper::isLoggedIn() || !SessionHelper::isAdmin()) {
            header('Location: /Account/login');
            exit();
        }

        $id = $_GET['id'] ?? null;

        if ($id) {
            $this->accountModel->updateUserStatus($id, 'locked');
        }

        header('Location: /Account/users');
        exit();
    }

    public function unlockUser()
    {
        if (!SessionHelper::isLoggedIn() || !SessionHelper::isAdmin()) {
            header('Location: /Account/login');
            exit();
        }

        $id = $_GET['id'] ?? null;

        if ($id) {
            $this->accountModel->updateUserStatus($id, 'active');
        }

        header('Location: /Account/users');
        exit();
    }

    public function changeRole()
    {
        if (!SessionHelper::isLoggedIn() || !SessionHelper::isAdmin()) {
            header('Location: /Account/login');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_POST['user_id'] ?? null;
            $role = $_POST['role'] ?? null;

            if ($userId && in_array($role, ['admin', 'user'])) {
                $this->accountModel->changeRole($userId, $role);
            }
        }

        header('Location: /Account/users');
        exit();
    }

    public function deleteUser()
    {
        if (!SessionHelper::isLoggedIn() || !SessionHelper::isAdmin()) {
            header('Location: /Account/login');
            exit();
        }

        $id = $_GET['id'] ?? null;

        if ($id) {
            $this->accountModel->deleteUser($id);
        }

        header('Location: /Account/users');
        exit();
    }

    private function getBaseUrl()
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            ? 'https'
            : 'http';

        $host = $_SERVER['HTTP_HOST'];

        return $protocol . '://' . $host;
    }
}