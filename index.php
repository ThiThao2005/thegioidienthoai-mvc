<?php
// 1. Nạp các cấu hình và Model dùng chung
require_once 'app/config/Database.php'; // Nạp kết nối DB
require_once 'app/models/ProductModel.php';
require_once 'app/models/CategoryModel.php'; // Nạp thêm Model Category cho Bài 2

$url = $_GET['url'] ?? '';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$url = explode('/', $url);

// 2. Xác định Controller
$controllerName = isset($url[0]) && $url[0] != '' ? ucfirst($url[0]) . 'Controller' : 'ProductController';

// 3. Xác định Action (Xử lý cắt bỏ phần Query String nếu lỡ gõ dấu ? trên URL)
$action = isset($url[1]) && $url[1] != '' ? $url[1] : 'index';
if (strpos($action, '?') !== false) {
    $action = explode('?', $action)[0];
}

// 4. Kiểm tra file Controller tồn tại
if (!file_exists('app/controllers/' . $controllerName . '.php')) {
    header("HTTP/1.0 404 Not Found");
    die('Controller không tồn tại');
}

require_once 'app/controllers/' . $controllerName . '.php';
$controller = new $controllerName();

// 5. Kiểm tra hàm (Action) trong Controller tồn tại
if (!method_exists($controller, $action)) {
    header("HTTP/1.0 404 Not Found");
    die('Action không tồn tại');
}

// 6. XỬ LÝ THAM SỐ THÔNG MINH (Chấp nhận cả 2 dạng: /edit/14 HOẶC /edit?id=14)
$params = array_slice($url, 2); // Lấy tham số dạng /Product/edit/14

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