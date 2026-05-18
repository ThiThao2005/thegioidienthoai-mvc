<?php
// 🔥 ĐẢM BẢO SESSION LUÔN CHẠY TRƯỚC KHI NẠP BẤT KỲ FILE NÀO
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Nạp các cấu hình và Model dùng chung
require_once 'app/config/Database.php'; // Nạp file cấu hình DB
require_once 'app/models/ProductModel.php';
require_once 'app/models/CategoryModel.php'; 
require_once 'app/helpers/SessionHelper.php';

// Khởi tạo biến $db từ class Database để truyền vào Controller
$database = new Database();
$db = $database->getConnection();

$url = $_GET['url'] ?? '';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$url = explode('/', $url);

// 2. Xác định Controller
$controllerName = isset($url[0]) && $url[0] != '' ? ucfirst($url[0]) . 'Controller' : 'ProductController';

// 3. Xác định Action
$action = isset($url[1]) && $url[1] != '' ? $url[1] : 'index';
if (strpos($action, '?') !== false) {
    $action = explode('?', $action)[0];
}

// 4. Kiểm tra file Controller tồn tại
if (!file_exists('app/controllers/' . $controllerName . '.php')) {
    header("HTTP/1.0 404 Not Found");
    die('Controller không tồn tại');
}

// Nạp file controller tương ứng vào hệ thống
require_once 'app/controllers/' . $controllerName . '.php';

// =================================================================
// 🔥 HỆ THỐNG PHÂN QUYỀN TỰ ĐỘNG CHẶN URL Ở TẦNG ROUTER (INDEX.PHP)
// =================================================================

// Đảm bảo Helper nhận diện session đã start
SessionHelper::start();

// 1. Chặn toàn bộ các Controller thuộc nhóm quản trị viên (Admin, Danh mục, Thành viên)
$adminControllers = ['AdminController', 'CategoryController', 'UserController']; 
if (in_array($controllerName, $adminControllers)) {
    SessionHelper::requireAdmin(); // Nếu không phải admin, hàm này sẽ đá văng về trang Login
}

// 2. Chặn các hành động (Action) nhạy cảm, chỉnh sửa dữ liệu bên trong ProductController
if ($controllerName === 'ProductController') {
    $adminActions = ['add', 'save', 'edit', 'delete']; // Thêm, lưu, sửa, xóa chỉ dành cho Admin
    if (in_array($action, $adminActions)) {
        SessionHelper::requireAdmin(); // Khách hàng hoặc người chưa đăng nhập gõ lậu link sẽ bị chặn đứng
    }
}
// =================================================================

// Khởi tạo Controller sau khi đã qua màng lọc check quyền an toàn ở trên
$controller = new $controllerName($db); 

// 5. Kiểm tra hàm (Action) trong Controller tồn tại
if (!method_exists($controller, $action)) {
    header("HTTP/1.0 404 Not Found");
    die('Action không tồn tại');
}

// 6. XỬ LÝ THAM SỐ THÔNG MINH (Chấp nhận cả 2 dạng: /edit/14 HOẶC /edit?id=14)
$params = array_slice($url, 2); 

// Nếu mảng $params trống, tự động kiểm tra xem có tham số dạng ?id= hoặc ?category_id= hay không
if (empty($params)) {
    if (isset($_GET['id'])) {
        $params = [$_GET['id']];
    } elseif (isset($_GET['category_id'])) {
        $params = [$_GET['category_id']];
    }
}

// Gọi Action và truyền mảng tham số vào
call_user_func_array([$controller, $action], $params);
?>