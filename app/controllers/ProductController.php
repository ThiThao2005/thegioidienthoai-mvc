<?php
require_once 'app/config/database.php';
require_once 'app/models/ProductModel.php';
require_once 'app/models/CategoryModel.php';
require_once 'app/helpers/SessionHelper.php'; // Đảm bảo đã nạp file Helper phân quyền

class ProductController
{
    private $productModel;
    private $categoryModel;
    private $db;

    // Sửa constructor để nhận biến $db truyền vào (hoặc tự khởi tạo nếu để trống)
    public function __construct($db = null)
    {
        // Khởi động session an toàn thông qua Helper
        SessionHelper::start();
        
        // Nếu phía ngoài (index.php) có truyền $db thì dùng, không thì tự tạo mới
        $this->db = $db ? $db : (new Database())->getConnection();
        $this->productModel = new ProductModel($this->db);
        $this->categoryModel = new CategoryModel($this->db);
    }

    // Hàm render dùng chung để giữ nguyên Header/Footer giao diện thế giới điện thoại
    private function render($viewPath, $data = [])
    {
        extract($data);
        require_once 'app/views/layout/header.php';
        require_once 'app/views/' . $viewPath . '.php';
        require_once 'app/views/layout/footer.php';
    }

    // Hiển thị danh sách sản phẩm (Có hỗ trợ lọc theo danh mục và tìm kiếm)
    // 🔓 ĐỂ MỞ: Tất cả mọi người (User, Guest) đều được phép xem danh sách
    public function index()
    {
        $category_id = isset($_GET['category_id']) ? $_GET['category_id'] : null;
        $products = $this->productModel->getProducts($category_id);
        $this->render('product/list', ['products' => $products]);
    }

    // Hiển thị form thêm sản phẩm
    public function add()
    {
        // 🛡️ BẢO MẬT TẦNG 2: Chỉ tài khoản Admin mới được vào mở Form này
        SessionHelper::requireAdmin();

        $categories = $this->categoryModel->getCategories();
        $this->render('product/add', ['categories' => $categories]);
    }

    // Xử lý lưu Thêm sản phẩm
    public function save()
    {
        // 🛡️ BẢO MẬT TẦNG 2: Chặn các Request lậu cố tình POST dữ liệu trực tiếp
        SessionHelper::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $price = $_POST['price'] ?? '';
            $category_id = $_POST['category_id'] ?? null;
            
            $imageName = 'default.jpg';

            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $uploadDir = 'public/images/';
                $imageName = time() . '_' . basename($_FILES['image']['name']);
                move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $imageName);
            }

            $result = $this->productModel->addProduct($name, $description, $price, $category_id, $imageName);

            if ($result === true) {
                header('Location: /project1/Product/index');
                exit();
            } else {
                $errors = is_array($result) ? $result : ['Lỗi không xác định khi thêm'];
                $categories = $this->categoryModel->getCategories();
                $this->render('product/add', ['errors' => $errors, 'categories' => $categories]);
            }
        }
    }

    // Sửa hàm edit để tự lấy ID từ URL xuống và bọc màng lọc bảo mật
    public function edit($id = null)
    {
        // 🛡️ BẢO MẬT TẦNG 2: Chỉ Admin mới được quyền chỉnh sửa sản phẩm
        SessionHelper::requireAdmin();

        // Nếu router không truyền tham số vào, tự động lấy từ $_GET['id'] trên URL
        if ($id === null) {
            $id = isset($_GET['id']) ? $_GET['id'] : null;
        }

        if (!$id) {
            die("Thiếu ID sản phẩm cần chỉnh sửa.");
        }

        $product = $this->productModel->getProductById($id);
        $categories = $this->categoryModel->getCategories();
        
        if ($product) {
            $this->render('product/edit', ['product' => $product, 'categories' => $categories]);
        } else {
            die("Không thấy sản phẩm.");
        }
    }

    // Sửa hàm delete tự nhận ID từ URL giống hàm edit và bọc màng lọc bảo mật
    public function delete($id = null)
    {
        // 🛡️ BẢO MẬT TẦNG 2: Chỉ Admin mới có quyền ra lệnh XÓA dữ liệu khỏi DB
        SessionHelper::requireAdmin();

        if ($id === null) {
            $id = isset($_GET['id']) ? $_GET['id'] : null;
        }

        if (!$id) {
            die("Thiếu ID sản phẩm cần xóa.");
        }

        if ($this->productModel->deleteProduct($id)) {
            header('Location: /project1/Product/index');
            exit();
        } else {
            echo "Đã xảy ra lỗi khi xóa sản phẩm.";
        }
    }

    // ==========================================
    // 🛒 CÁC CHỨC NĂNG BÀI 3: GIỎ HÀNG & ĐẶT HÀNG
    // ==========================================

    // Thêm sản phẩm vào giỏ hàng bằng SESSION
    // 🔓 ĐỂ MỞ: Cả khách vãng lai chưa đăng nhập vẫn cho phép thêm đồ vào giỏ
    public function addToCart($id = null)
    {
        if ($id === null) {
            $id = isset($_GET['id']) ? $_GET['id'] : null;
        }

        if (!$id) {
            die("Thiếu ID sản phẩm để thêm vào giỏ.");
        }

        $product = $this->productModel->getProductById($id);
        if (!$product) {
            die("Không tìm thấy sản phẩm.");
        }

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity']++;
        } else {
            $_SESSION['cart'][$id] = [
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
                'image' => $product->image
            ];
        }
        
        header('Location: /project1/Product/cart');
        exit();
    }

    // Hiển thị trang giỏ hàng
    public function cart()
    {
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        $this->render('product/cart', ['cart' => $cart]);
    }

    // Hiển thị trang điền thông tin thanh toán
    public function checkout()
    {
        // 🔒 BẢO MẬT: Bắt buộc phải ĐĂNG NHẬP mới được vào trang điền đơn vận đơn
        SessionHelper::requireLogin();

        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        if (empty($cart)) {
            header('Location: /project1/Product/cart');
            exit();
        }
        $this->render('product/checkout', ['cart' => $cart]);
    }

    // Xử lý lưu thông tin đặt hàng vào Database (Transaction)
    public function processCheckout()
    {
        // 🔒 BẢO MẬT: Chặn đứng hành vi giả mạo request đặt đơn khi chưa login
        SessionHelper::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'] ?? '';
            $phone = $_POST['phone'] ?? '';
            $address = $_POST['address'] ?? '';

            if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
                die("Giỏ hàng đang trống.");
            }

            $this->db->beginTransaction();
            try {
                $query = "INSERT INTO orders (name, phone, address) VALUES (:name, :phone, :address)";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':phone', $phone);
                $stmt->bindParam(':address', $address);
                $stmt->execute();
                
                $order_id = $this->db->lastInsertId();

                $cart = $_SESSION['cart'];
                foreach ($cart as $product_id => $item) {
                    $query = "INSERT INTO order_details (order_id, product_id, quantity, price) 
                              VALUES (:order_id, :product_id, :quantity, :price)";
                    $stmt = $this->db->prepare($query);
                    $stmt->bindParam(':order_id', $order_id);
                    $stmt->bindParam(':product_id', $product_id);
                    $stmt->bindParam(':quantity', $item['quantity']);
                    $stmt->bindParam(':price', $item['price']);
                    $stmt->execute();
                }

                unset($_SESSION['cart']);
                $this->db->commit();

                header('Location: /project1/Product/orderConfirmation');
                exit();

            } catch (Exception $e) {
                $this->db->rollBack();
                die("Đã xảy ra lỗi khi xử lý đơn hàng: " . $e->getMessage());
            }
        }
    }

// Cập nhật số lượng sản phẩm trong giỏ hàng (Đã fix lỗi đồng bộ key chữ hoa/thường)
    public function updateCartQuantity() {
        SessionHelper::start();

        $id = isset($_GET['id']) ? $_GET['id'] : null;
        $action = isset($_GET['action']) ? $_GET['action'] : '';

        if ($id && isset($_SESSION['cart'][$id])) {
            // 🚀 BẪY LOGIC: Xác định chính xác key đang được sử dụng trong Session (quantity hay Quantity)
            $key = isset($_SESSION['cart'][$id]['Quantity']) ? 'Quantity' : 'quantity';

            if ($action === 'increase') {
                $_SESSION['cart'][$id][$key] += 1;
            } elseif ($action === 'decrease') {
                $_SESSION['cart'][$id][$key] -= 1;
                
                // Nếu số lượng giảm xuống 0 hoặc nhỏ hơn thì xóa sản phẩm khỏi giỏ
                if ($_SESSION['cart'][$id][$key] <= 0) {
                    unset($_SESSION['cart'][$id]);
                }
            }
        }

        // Đẩy ngược người dùng quay lại trang giỏ hàng để cập nhật giao diện mới
        header('Location: /project1/Product/cart');
        exit();
    }

    // ⚡ CHỨC NĂNG XEM CHI TIẾT SẢN PHẨM (ĐÃ CHUẨN HÓA ĐỒNG BỘ)
    public function detail($id = null) {
        if ($id === null) {
            $id = isset($_GET['id']) ? $_GET['id'] : null;
        }

        if (!$id) {
            header('Location: /project1/Product/index');
            exit();
        }

        $product = $this->productModel->getProductById($id); 

        if (!$product) {
            die("<div style='padding:50px; text-align:center;'><h3>Sản phẩm không tồn tại hoặc đã bị xóa!</h3><a href='/project1/Product/index'>Quay lại trang chủ</a></div>");
        }

        $this->render('product/detail', ['product' => $product]);
    }

    // Xử lý Cập nhật thông tin sản phẩm
    public function update()
    {
        // 🛡️ Bảo mật: Chỉ Admin mới được lưu thay đổi
        SessionHelper::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'];
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $price = $_POST['price'] ?? '';
            $category_id = $_POST['category_id'] ?? null;
            
            // Lấy lại tên ảnh cũ phòng trường hợp khách không chọn ảnh mới
            $imageName = $_POST['existing_image'] ?? 'default.jpg';

            // Kiểm tra nếu có upload ảnh mới
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $uploadDir = 'public/images/';
                $newImageName = time() . '_' . basename($_FILES['image']['name']);
                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $newImageName)) {
                    $imageName = $newImageName; // Dùng ảnh mới
                }
            }

            // Gọi Model để cập nhật
            if ($this->productModel->updateProduct($id, $name, $description, $price, $category_id, $imageName)) {
                // Tạo một câu thông báo lưu tạm vào Session
                $_SESSION['success_msg'] = "🎉 Đã cập nhật sản phẩm thành công.";
                header('Location: /project1/Product/index');
                exit();
            } else {
                echo "Đã xảy ra lỗi khi cập nhật sản phẩm trên cơ sở dữ liệu.";
            }
        }
    }

    // Xóa sản phẩm khỏi giỏ hàng
    public function removeFromCart() {
        SessionHelper::start();

        $id = isset($_GET['id']) ? $_GET['id'] : null;

        if ($id && isset($_SESSION['cart'][$id])) {
            unset($_SESSION['cart'][$id]);
        }

        header('Location: /project1/Product/cart');
        exit();
    }

    // ==========================================
    // 👤 CHỨC NĂNG QUẢN LÝ NGƯỜI DÙNG (ADMIN)
    // ==========================================

    // 1. Trang danh sách người dùng
    public function users() {
        SessionHelper::requireAdmin();
        $users = $this->productModel->getAllUsers();
        $this->render('product/users', ['users' => $users]);
    }

    // 2. Xử lý thay đổi quyền (Role) trực tiếp
    public function changeRole() {
        SessionHelper::requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user_id = $_POST['user_id'] ?? null;
            $role = $_POST['role'] ?? 'user';
            
            if ($user_id) {
                $this->productModel->updateUserRole($user_id, $role);
            }
        }
        header('Location: /project1/Product/users');
        exit();
    }

    // 3. Xử lý xóa thành viên
    public function deleteUser($id = null) {
        SessionHelper::requireAdmin();
        if ($id === null) {
            $id = isset($_GET['id']) ? $_GET['id'] : null;
        }

        if ($id) {
            $this->productModel->deleteUser($id);
        }
        header('Location: /project1/Product/users');
        exit();
    }
    
    // ==========================================
    // 📊 CHỨC NĂNG DASHBOARD TỔNG QUAN (ADMIN)
    // ==========================================
    public function dashboard() {
        // 🛡️ Bảo mật: Ép buộc phải là Admin mới được vào xem trang này
        SessionHelper::requireAdmin();

        // 1. Lấy danh sách sản phẩm đổ vào bảng quản lý
        $products = $this->productModel->getProducts(); 

        // 2. Gọi hàm đếm số lượng thực tế trong DB từ Model để truyền sang thẻ thống kê
        $totalCategories = $this->productModel->countCategories();
        $totalOrders = $this->productModel->countOrders();
        $totalUsers = $this->productModel->countUsers();

        // 3. Dùng hàm render dùng chung của bồ để đẩy sang view dashboard + kèm theo mảng dữ liệu thực tế
        $this->render('product/dashboard', [
            'products'         => $products,
            'totalCategories'  => $totalCategories,
            'totalOrders'      => $totalOrders,
            'totalUsers'       => $totalUsers
        ]);
    }

    // ==========================================
    // 📦 CHỨC NĂNG QUẢN LÝ ĐƠN HÀNG (ADMIN)
    // ==========================================

    // 1. Trang danh sách đơn hàng
    public function orders() {
        SessionHelper::requireAdmin();
        $orders = $this->productModel->getAllOrders();
        $this->render('product/orders', ['orders' => $orders]);
    }

    // 2. Trang xem chi tiết một đơn hàng cụ thể
    public function orderDetail($id = null) {
        SessionHelper::requireAdmin();
        if ($id === null) {
            $id = isset($_GET['id']) ? $_GET['id'] : null;
        }

        if (!$id) {
            header('Location: /project1/Product/orders');
            exit();
        }

        $order = $this->productModel->getOrderById($id);
        $details = $this->productModel->getOrderDetails($id);

        if (!$order) {
            die("Đơn hàng không tồn tại.");
        }

        $this->render('product/order_detail', ['order' => $order, 'details' => $details]);
    }

    // 3. Xóa đơn hàng
    public function deleteOrder($id = null) {
        SessionHelper::requireAdmin();
        if ($id === null) {
            $id = isset($_GET['id']) ? $_GET['id'] : null;
        }

        if ($id) {
            $this->productModel->deleteOrder($id);
        }
        header('Location: /project1/Product/orders');
        exit();
    }

    // ==========================================
    // 🏷️ CHỨC NĂNG QUẢN LÝ DANH MỤC (ADMIN)
    // ==========================================

    // 1. Hiển thị danh sách danh mục
    public function categories() {
        SessionHelper::requireAdmin();
        $categories = $this->categoryModel->getCategories(); 
        $this->render('product/categories', ['categories' => $categories]);
    }

    // 2. Xử lý Thêm danh mục mới
    public function addCategory() {
        SessionHelper::requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'] ?? '';
            if (!empty($name)) {
                $this->categoryModel->addCategory($name);
            }
            header('Location: /project1/Product/categories');
            exit();
        }
    }

    // Xử lý cập nhật tên danh mục sản phẩm (POST)
    public function updateCategory() {
        SessionHelper::requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $name = $_POST['name'] ?? null;
            
            if ($id && !empty(trim($name))) {
                $this->productModel->updateCategory($id, trim($name));
            }
        }
        
        header('Location: /project1/Product/categories');
        exit();
    }

    // 3. Xử lý Xóa danh mục
    public function deleteCategory($id = null) {
        SessionHelper::requireAdmin();
        if ($id === null) {
            $id = isset($_GET['id']) ? $_GET['id'] : null;
        }
        if ($id) {
            $this->categoryModel->deleteCategory($id);
        }
        header('Location: /project1/Product/categories');
        exit();
    }

    // Hiển thị trang xác nhận đặt hàng thành công
    public function orderConfirmation()
    {
        $this->render('product/orderConfirmation');
    }
}
?>