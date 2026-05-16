<?php
require_once 'app/config/database.php';
require_once 'app/models/ProductModel.php';
require_once 'app/models/CategoryModel.php';

class ProductController
{
    private $productModel;
    private $categoryModel;
    private $db;

    public function __construct()
    {
        // Khởi động session để làm việc với Giỏ hàng (Bắt buộc phải có)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $this->db = (new Database())->getConnection();
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
    public function index()
    {
        $category_id = isset($_GET['category_id']) ? $_GET['category_id'] : null;
        $products = $this->productModel->getProducts($category_id);
        $this->render('product/list', ['products' => $products]);
    }

    // Hiển thị form thêm sản phẩm
    public function add()
    {
        $categories = $this->categoryModel->getCategories();
        $this->render('product/add', ['categories' => $categories]);
    }

    // Xử lý lưu Thêm sản phẩm
    public function save()
    {
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

    // Xử lý form Sửa sản phẩm
    public function edit($id)
    {
        $product = $this->productModel->getProductById($id);
        $categories = $this->categoryModel->getCategories();
        
        if ($product) {
            $this->render('product/edit', ['product' => $product, 'categories' => $categories]);
        } else {
            die("Không thấy sản phẩm.");
        }
    }

    // Xử lý xóa sản phẩm
    public function delete($id)
    {
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

    // 1. Thêm sản phẩm vào giỏ hàng bằng SESSION
    public function addToCart($id)
    {
        $product = $this->productModel->getProductById($id);
        if (!$product) {
            die("Không tìm thấy sản phẩm.");
        }

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Nếu sản phẩm đã có trong giỏ thì tăng số lượng, chưa có thì thêm mới
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
        
        // Chuyển hướng thẳng đến trang xem giỏ hàng
        header('Location: /project1/Product/cart');
        exit();
    }

    // 2. Hiển thị trang giỏ hàng
    public function cart()
    {
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        $this->render('product/cart', ['cart' => $cart]);
    }

    // 3. Hiển thị trang điền thông tin thanh toán
    public function checkout()
    {
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        if (empty($cart)) {
            header('Location: /project1/Product/cart');
            exit();
        }
        $this->render('product/checkout', ['cart' => $cart]);
    }

    // 4. Xử lý lưu thông tin đặt hàng vào Database (Transaction)
    public function processCheckout()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'] ?? '';
            $phone = $_POST['phone'] ?? '';
            $address = $_POST['address'] ?? '';

            if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
                die("Giỏ hàng đang trống.");
            }

            // Bắt đầu Transaction (giao dịch an toàn dữ liệu)
            $this->db->beginTransaction();
            try {
                // Bước A: Lưu vào bảng orders
                $query = "INSERT INTO orders (name, phone, address) VALUES (:name, :phone, :address)";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':phone', $phone);
                $stmt->bindParam(':address', $address);
                $stmt->execute();
                
                // Lấy ra ID của đơn hàng vừa chèn
                $order_id = $this->db->lastInsertId();

                // Bước B: Duyệt giỏ hàng và lưu vào bảng order_details
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

                // Bước C: Xóa giỏ hàng sau khi đặt thành công và commit
                unset($_SESSION['cart']);
                $this->db->commit();

                // Chuyển hướng sang trang thông báo thành công
                header('Location: /project1/Product/orderConfirmation');
                exit();

            } catch (Exception $e) {
                // Hoàn tác lại nếu có bất kỳ lỗi nào xảy ra trong quá trình lưu
                $this->db->rollBack();
                die("Đã xảy ra lỗi khi xử lý đơn hàng: " . $e->getMessage());
            }
        }
    }
    public function updateCartQuantity() {
    if (!isset($_SESSION)) {
        session_start();
    }

    $id = isset($_GET['id']) ? $_GET['id'] : null;
    $action = isset($_GET['action']) ? $_GET['action'] : '';

    if ($id && isset($_SESSION['cart'][$id])) {
        if ($action === 'increase') {
            $_SESSION['cart'][$id]['quantity'] += 1;
        } elseif ($action === 'decrease') {
            $_SESSION['cart'][$id]['quantity'] -= 1;
            // Nếu giảm xuống 0 hoặc nhỏ hơn thì tự động xóa sản phẩm đó
            if ($_SESSION['cart'][$id]['quantity'] <= 0) {
                unset($_SESSION['cart'][$id]);
            }
        }
    }

    // Chuyển hướng quay ngược lại trang giỏ hàng sau khi tính toán xong
    header('Location: /project1/Product/cart');
    exit();
}

public function removeFromCart() {
    // 1. Khởi động session nếu chưa có
    if (!isset($_SESSION)) {
        session_start();
    }

    // 2. Lấy ID sản phẩm cần xóa từ URL qua $_GET
    $id = isset($_GET['id']) ? $_GET['id'] : null;

    // 3. Kiểm tra xem sản phẩm có tồn tại trong giỏ hàng không, nếu có thì hủy nó đi
    if ($id && isset($_SESSION['cart'][$id])) {
        unset($_SESSION['cart'][$id]);
    }

    // 4. Xóa xong chuyển hướng quay trở lại trang giỏ hàng ngay lập tức
    header('Location: /project1/Product/cart');
    exit();
}

    // 5. Hiển thị trang xác nhận đặt hàng thành công
    public function orderConfirmation()
    {
        $this->render('product/orderConfirmation');
    }
}
?>