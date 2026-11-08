<?php

require_once 'app/helpers/SessionHelper.php';
require_once 'app/helpers/MailHelper.php';
require_once 'app/helpers/UploadHelper.php';
require_once 'app/models/AccountModel.php';

class AccountController
{
    // Client ID Google (khớp với data-client_id trong views/account/login.php)
    const GOOGLE_CLIENT_ID = '694098371336-ie2kni4ae1glc5cui0nam5rqp61phk5q.apps.googleusercontent.com';

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
                $this->isHttps(),
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

        $payload = $this->verifyGoogleIdToken($_POST['credential']);

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

        $user = $this->accountModel->getAccountByEmail($googleEmail)
            ?: $this->accountModel->getAccountByUsername($googleEmail);

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
            $user = $this->accountModel->getAccountById($user['id']) ?: $user;
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

        if ($fullname === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Vui lòng nhập họ tên và email hợp lệ.";
            $user = $this->accountModel->getAccountById($userId);
            require_once 'app/views/account/profile.php';
            return;
        }

        $sameEmail = $this->accountModel->getAccountByEmail($email);
        if ($sameEmail && (int) $sameEmail['id'] !== (int) $userId) {
            $error = "Email này đã được tài khoản khác sử dụng.";
            $user = $this->accountModel->getAccountById($userId);
            require_once 'app/views/account/profile.php';
            return;
        }

        $avatarName = null;

        if (!empty($_FILES['avatar']['name'])) {
            $uploadDir = 'public/uploads/avatars/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $extension = UploadHelper::imageExtension($_FILES['avatar']['tmp_name'] ?? '');
            $fileSize = $_FILES['avatar']['size'];

            if ($extension === null) {
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

            $avatarName = 'avatar_' . $userId . '_' . time() . '.' . $extension;

            move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadDir . $avatarName);
        }

        if ($this->accountModel->updateProfile($userId, $fullname, $email, $phone, $address, $avatarName)) {
            $_SESSION['fullname'] = $fullname;
            $_SESSION['email'] = $email;

            if (isset($_SESSION['user'])) {
                $_SESSION['user']['fullname'] = $fullname;
                $_SESSION['user']['email'] = $email;
                if ($avatarName) {
                    $_SESSION['user']['avatar'] = $avatarName;
                }
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
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /Account/forgotPassword');
            exit();
        }

        $email = trim($_POST['email'] ?? '');
        $user = filter_var($email, FILTER_VALIDATE_EMAIL) ? $this->accountModel->getAccountByEmail($email) : null;

        // Luôn trả cùng một thông báo để không lộ email nào có tài khoản
        $success = "Nếu email này có tài khoản, chúng tôi đã gửi link đặt lại mật khẩu (hiệu lực 30 phút). Hãy kiểm tra hộp thư và mục Spam.";

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $this->accountModel->saveResetToken($email, $token);
            $resetLink = $this->getBaseUrl() . "/Account/resetPassword?token=" . urlencode($token);

            $sent = MailHelper::sendResetPasswordEmail($email, $user['fullname'] ?? '', $resetLink);
            if (!$sent && Database::isDebug()) {
                // Chỉ khi chạy trên máy (Docker) mới hiện link để test
                $success = "[Chế độ dev] Gửi email thất bại. Link đặt lại mật khẩu: " . $resetLink;
            }
        }

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

    private function isHttps()
    {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    }

    private function getBaseUrl()
    {
        // Ưu tiên địa chỉ cấu hình sẵn (tránh giả mạo header Host trong link gửi qua email)
        $configured = getenv('APP_URL') ?: (Database::localConfig()['app_url'] ?? '');
        if (!empty($configured)) {
            return rtrim($configured, '/');
        }

        $protocol = $this->isHttps() ? 'https' : 'http';
        $host = preg_replace('/[^A-Za-z0-9.\-:]/', '', $_SERVER['HTTP_HOST'] ?? 'localhost');

        return $protocol . '://' . $host;
    }

    /** Xác minh Google ID token với máy chủ Google; trả về payload nếu hợp lệ. */
    private function verifyGoogleIdToken($idToken)
    {
        if (!is_string($idToken) || substr_count($idToken, '.') !== 2) {
            return null;
        }

        $ch = curl_init('https://oauth2.googleapis.com/tokeninfo?id_token=' . urlencode($idToken));
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$response) {
            return null;
        }

        $payload = json_decode($response, true);
        $clientId = Database::localConfig()['google_client_id'] ?? self::GOOGLE_CLIENT_ID;

        if (!is_array($payload)
            || ($payload['aud'] ?? '') !== $clientId
            || !in_array($payload['iss'] ?? '', ['accounts.google.com', 'https://accounts.google.com'], true)
            || (int) ($payload['exp'] ?? 0) < time()
            || !filter_var($payload['email_verified'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
            return null;
        }

        return $payload;
    }
}