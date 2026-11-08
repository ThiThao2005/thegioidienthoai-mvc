<?php
// =============================================
// FRONT CONTROLLER - index.php
// =============================================

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    require_once 'app/config/Database.php';
    require_once 'app/helpers/SessionHelper.php';

    $database = new Database();
    $db = $database->getConnection();

    // =====================================================
    // REMEMBER ME - TỰ ĐỘNG ĐĂNG NHẬP BẰNG COOKIE
    // =====================================================
    if (!SessionHelper::isLoggedIn() && isset($_COOKIE['remember_token'])) {
        require_once 'app/models/AccountModel.php';

        $accountModel = new AccountModel($db);
        $rememberToken = $_COOKIE['remember_token'];
        $rememberUser = $accountModel->getAccountByRememberToken($rememberToken);

        if ($rememberUser && ($rememberUser['status'] ?? 'active') === 'active') {
            SessionHelper::login($rememberUser);
        }
    }

    // =====================================================
    // XỬ LÝ URL ROUTING
    // =====================================================
    $url = $_GET['url'] ?? '';
    $url = rtrim($url, '/');
    $url = filter_var($url, FILTER_SANITIZE_URL);
    $url = explode('/', $url);

    $controllerName = isset($url[0]) && $url[0] !== ''
        ? ucfirst($url[0]) . 'Controller'
        : 'ProductController';

    $action = isset($url[1]) && $url[1] !== ''
        ? $url[1]
        : 'index';

    if (strpos($action, '?') !== false) {
        $action = explode('?', $action)[0];
    }

    // =====================================================
    // KIỂM TRA CONTROLLER
    // =====================================================
    $controllerFile = 'app/controllers/' . $controllerName . '.php';

    if (!file_exists($controllerFile)) {
        die("❌ Controller không tồn tại: " . htmlspecialchars($controllerName));
    }

    require_once $controllerFile;

    // =====================================================
    // PHÂN QUYỀN CONTROLLER ADMIN
    // =====================================================
    $adminControllers = [
        'AdminController',
        'CategoryController',
        'UserController'
    ];

    if (in_array($controllerName, $adminControllers)) {
        SessionHelper::requireAdmin();
    }

    // =====================================================
    // PHÂN QUYỀN ACTION ADMIN TRONG PRODUCTCONTROLLER
    // =====================================================
    if ($controllerName === 'ProductController') {
        $adminActions = [
            'add',
            'save',
            'edit',
            'delete',
            'create'
        ];

        if (in_array($action, $adminActions)) {
            SessionHelper::requireAdmin();
        }
    }

    // =====================================================
    // KHỞI TẠO CONTROLLER
    // =====================================================
    $controller = new $controllerName($db);

    if (!method_exists($controller, $action)) {
        die("❌ Action không tồn tại: " . htmlspecialchars($action));
    }

    // =====================================================
    // XỬ LÝ THAM SỐ
    // =====================================================
    $params = array_slice($url, 2);

    if (empty($params)) {
        if (isset($_GET['id'])) {
            $params = [$_GET['id']];
        } elseif (isset($_GET['category_id'])) {
            $params = [$_GET['category_id']];
        }
    }

    call_user_func_array([$controller, $action], $params);

} catch (Throwable $e) {
    echo "<h2 style='color:red'>❌ LỖI:</h2>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
    echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . " (dòng " . $e->getLine() . ")</p>";
}
?>