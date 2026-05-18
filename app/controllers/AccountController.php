<?php
class AccountController {
    private $accountModel;
    private $db;

    public function __construct($db) {
        $this->db = $db;
        // Nạp Model Account để tương tác với DB
        require_once 'app/models/AccountModel.php';
        $this->accountModel = new AccountModel($this->db);
    }

    // 1. HIỂN THỊ TRANG ĐĂNG NHẬP
    public function login() {
        // Nếu đã đăng nhập rồi thì đá về trang chủ, không cho vào lại trang login nữa
        if (SessionHelper::isLoggedIn()) {
            header('Location: /project1/Product/index');
            exit();
        }
        
        // Gọi view hiển thị form Đăng nhập
        require_once 'app/views/account/login.php';
    }

    // 2. XỬ LÝ LOGIC ĐĂNG NHẬP TRUYỀN THỐNG
    public function processLogin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $password = trim($_POST['password']);

            // Tìm tài khoản theo username trong DB
            $user = $this->accountModel->getAccountByUsername($username);

            if ($user && password_verify($password, $user['password'])) {
                // Đúng mật khẩu -> Lưu thông tin vào Session để giữ trạng thái đăng nhập
                SessionHelper::login($user);
                
                // Kiểm tra quyền để điều hướng trang phù hợp
                if (SessionHelper::isAdmin()) {
                    header('Location: /project1/Admin/dashboard'); // Trang quản trị
                } else {
                    header('Location: /project1/Product/index'); // Trang chủ bán hàng
                }
                exit();
            } else {
                // Sai tài khoản hoặc mật khẩu
                $error = "Tên đăng nhập hoặc mật khẩu không chính xác!";
                require_once 'app/views/account/login.php';
            }
        }
    }

    // 3. HIỂN THỊ TRANG ĐĂNG KÝ
    public function register() {
        if (SessionHelper::isLoggedIn()) {
            header('Location: /project1/Product/index');
            exit();
        }
        require_once 'app/views/account/register.php';
    }

    // 4. XỬ LÝ LOGIC ĐĂNG KÝ TRUYỀN THỐNG
    public function processRegister() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $fullname = trim($_POST['fullname']);
            $password = trim($_POST['password']);
            $confirm_password = trim($_POST['confirm_password']);

            // Kiểm tra mật khẩu nhập lại có khớp không
            if ($password !== $confirm_password) {
                $error = "Mật khẩu nhập lại không trùng khớp!";
                require_once 'app/views/account/register.php';
                return;
            }

            // Kiểm tra username đã có ai dùng chưa
            if ($this->accountModel->checkUsernameExists($username)) {
                $error = "Tên đăng nhập này đã tồn tại trên hệ thống!";
                require_once 'app/views/account/register.php';
                return;
            }

            // Tiến hành ghi nhận vào cơ sở dữ liệu
            if ($this->accountModel->register($username, $fullname, $password)) {
                $success = "Đăng ký tài khoản thành công! Hãy đăng nhập ngay.";
                require_once 'app/views/account/login.php';
            } else {
                $error = "Đã xảy ra lỗi trong quá trình đăng ký. Vui lòng thử lại!";
                require_once 'app/views/account/register.php';
            }
        }
    }

    // ========================================================
    // ⭐ ĐÃ NÂNG CẤP: 5. XỬ LÝ ĐĂNG XUẤT (HIỂN THỊ VIEW CHỜ)
    // ========================================================
    public function logout() {
        // Chỉ gọi đến trang View thông báo đăng xuất mượt mà
        require_once 'app/views/account/logout.php';
    }

    // ⭐ ĐÃ BỔ SUNG: ACTION THỰC THI LOGIC XÓA SESSION SAU KHI VIEW LOADING CHẠY XONG
    public function executeLogout() {
        // Thực hiện xóa sạch session cũ và giỏ hàng bị lỗi
        SessionHelper::logout();
        
        // Điều hướng an toàn về trang chủ sản phẩm
        header('Location: /project1/Product/index');
        exit();
    }

    // ========================================================
    // 6. XỬ LÝ ĐĂNG NHẬP / ĐĂNG KÝ BẰNG GOOGLE (OAUTH2)
    // ========================================================
    public function googleLogin() {
        // Nhận chuỗi dữ liệu JSON (Credential Token) gửi lên từ nút bấm Google ở View
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['credential'])) {
            $credential = $_POST['credential'];

            // Tiến hành giải mã Token nhận được từ Google để lấy thông tin cá nhân
            $parts = explode('.', $credential);
            if (count($parts) === 3) {
                $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1])), true);
                
                if ($payload && isset($payload['email'])) {
                    $google_email = $payload['email']; // Dùng email làm username
                    $google_name = $payload['name'];   // Họ tên người dùng từ Google
                    
                    // Kiểm tra xem email này đã từng đăng nhập/tạo tài khoản trong hệ thống chưa
                    $user = $this->accountModel->getAccountByUsername($google_email);
                    
                    if (!$user) {
                        // Nếu chưa từng tồn tại, tự động tạo tài khoản mới với mật khẩu ngẫu nhiên
                        $random_password = bin2hex(random_bytes(8)); 
                        $this->accountModel->register($google_email, $google_name, $random_password);
                        
                        // Lấy lại thông tin tài khoản vừa tạo
                        $user = $this->accountModel->getAccountByUsername($google_email);
                    }
                    
                    // Thực hiện đăng nhập chuẩn Static vào hệ thống
                    SessionHelper::login($user);
                    
                    // Phản hồi cho Javascript xử lý chuyển hướng thành công
                    echo json_encode(['status' => 'success', 'redirect' => '/project1/Product/index']);
                    exit();
                }
            }
            echo json_encode(['status' => 'error', 'message' => 'Xác thực Google thất bại']);
            exit();
        }
    }
}