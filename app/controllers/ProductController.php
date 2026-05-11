<?php
require_once 'app/models/ProductModel.php';

class ProductController
{
    private $products = [];

    public function __construct()
    {
        // Kiểm tra xem session đã bắt đầu chưa để tránh lỗi Notice
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION['products'])) {
            $this->products = $_SESSION['products'];
        }
    }

    /**
     * Hàm hỗ trợ gọi View: Tự động nhúng Header và Footer
     */
    private function render($viewPath, $data = [])
    {
        extract($data); 
        
        require_once 'app/views/layout/header.php';
        require_once 'app/views/' . $viewPath . '.php';
        require_once 'app/views/layout/footer.php';
    }

    public function index()
    {
        $this->list();
    }

    public function list()
    {
        $this->render('product/list', ['products' => $this->products]);
    }

    public function add()
    {
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = trim($_POST['name']);
            $description = trim($_POST['description']);
            $price = $_POST['price'];

            // Kiểm tra dữ liệu đầu vào
            if (empty($name)) {
                $errors[] = 'Tên sản phẩm là bắt buộc.';
            } elseif (strlen($name) < 10 || strlen($name) > 100) {
                $errors[] = 'Tên sản phẩm phải có từ 10 đến 100 ký tự.';
            }

            if (!is_numeric($price) || $price <= 0) {
                $errors[] = 'Giá phải là một số dương lớn hơn 0.';
            }

            // --- PHẦN MỚI: XỬ LÝ UPLOAD HÌNH ẢNH ---
            $imageName = 'default.jpg'; // Ảnh mặc định nếu có lỗi
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $uploadDir = 'public/images/';
                
                // Tự động tạo thư mục public/images/ nếu chưa có
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                // Đổi tên file để không bị trùng (gắn thêm thời gian lúc upload)
                $imageName = time() . '_' . basename($_FILES['image']['name']);
                $targetFilePath = $uploadDir . $imageName;
                
                // Di chuyển file từ bộ nhớ tạm vào thư mục dự án
                if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
                     $errors[] = 'Không thể lưu hình ảnh, vui lòng thử lại.';
                }
            } else {
                $errors[] = 'Vui lòng chọn hình ảnh sản phẩm.';
            }
            // ----------------------------------------

            if (empty($errors)) {
                $maxId = 0;
                foreach ($this->products as $p) {
                    if ($p->getID() > $maxId) {
                        $maxId = $p->getID();
                    }
                }
                $id = $maxId + 1;
                
                // ĐÃ SỬA: Truyền thêm biến $imageName vào tham số thứ 5
                $product = new ProductModel($id, $name, $description, $price, $imageName);
                $this->products[] = $product;

                $_SESSION['products'] = $this->products;
                header('Location: /project1/Product/list');
                exit();
            }
        }
        
        $this->render('product/add', ['errors' => $errors]);
    }

    public function edit($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            foreach ($this->products as $key => $product) {
                if ($product->getID() == $id) {
                    $this->products[$key]->setName($_POST['name']);
                    $this->products[$key]->setDescription($_POST['description']);
                    $this->products[$key]->setPrice($_POST['price']);
                    // Ở trang sửa tạm thời mình giữ nguyên ảnh cũ, chưa làm chức năng đổi ảnh mới
                    break;
                }
            }
            $_SESSION['products'] = $this->products;
            header('Location: /project1/Product/list');
            exit();
        }

        foreach ($this->products as $product) {
            if ($product->getID() == $id) {
                $this->render('product/edit', ['product' => $product]);
                return;
            }
        }
        die('Product not found');
    }

    public function delete($id)
    {
        foreach ($this->products as $key => $product) {
            if ($product->getID() == $id) {
                // (Tùy chọn) Xóa luôn file ảnh trong thư mục public/images/ cho nhẹ máy
                $imagePath = 'public/images/' . $product->getImage();
                if (file_exists($imagePath) && $product->getImage() != 'default.jpg') {
                    unlink($imagePath); 
                }
                
                unset($this->products[$key]);
                break;
            }
        }
        
        $this->products = array_values($this->products);
        $_SESSION['products'] = $this->products;
        
        header('Location: /project1/Product/list');
        exit();
    }
}
?>