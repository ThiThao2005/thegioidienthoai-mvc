<?php
require_once 'app/config/database.php';
require_once 'app/models/ProductModel.php';
require_once 'app/models/CategoryModel.php';
require_once 'app/helpers/SessionHelper.php'; 

class ProductController
{
    private $productModel;
    private $categoryModel;
    private $db;

    public function __construct($db = null)
    {
        SessionHelper::start();
        
        $this->db = $db ? $db : (new Database())->getConnection();
        $this->productModel = new ProductModel($this->db);
        $this->categoryModel = new CategoryModel($this->db);
    }

    // Hàm render dùng chung cho giao diện
    private function render($viewPath, $data = [])
    {
        extract($data);
        require_once 'app/views/layout/header.php';
        require_once 'app/views/' . $viewPath . '.php';
        require_once 'app/views/layout/footer.php';
    }

    private function cleanText($value)
    {
        return trim((string) $value);
    }

    private function validateProductInput($name, $description, $price, $category_id)
    {
        $errors = [];
        if (strlen(trim($name)) < 3 || strlen(trim($name)) > 150) {
            $errors[] = 'Ten san pham phai tu 3 den 150 ky tu.';
        }
        if (strlen(trim($description)) < 10) {
            $errors[] = 'Mo ta san pham phai co it nhat 10 ky tu.';
        }
        if (!is_numeric($price) || (float) $price <= 0) {
            $errors[] = 'Gia san pham phai la so duong.';
        }
        if (!filter_var($category_id, FILTER_VALIDATE_INT)) {
            $errors[] = 'Vui long chon danh muc hop le.';
        }
        return $errors;
    }

    private function validateImageUpload($file, $required = false)
    {
        if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return $required ? ['Vui long chon hinh anh san pham.'] : [];
        }
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['Tai anh len khong thanh cong. Ma loi upload: ' . $file['error'] . '.'];
        }
        if ($file['size'] > 5 * 1024 * 1024) {
            return ['Anh san pham khong duoc vuot qua 5MB.'];
        }
        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $mime = mime_content_type($file['tmp_name']);
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        if (!in_array($mime, $allowed, true) && !in_array($extension, $allowedExtensions, true)) {
            return ['Chi chap nhan anh JPG, PNG, WEBP hoac GIF.'];
        }
        return [];
    }

    private function uploadImage($file)
    {
        $uploadDir = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR;
        if (!is_dir($uploadDir) || !is_writable($uploadDir)) {
            return null;
        }
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $safeName = time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
        return move_uploaded_file($file['tmp_name'], $uploadDir . $safeName) ? $safeName : null;
    }

    private function saveProductExtras($productId)
    {
        $this->productModel->replaceProductSpecs(
            $productId,
            $_POST['spec_key'] ?? [],
            $_POST['spec_value'] ?? []
        );

        $this->productModel->replaceProductVariants(
            $productId,
            $_POST['variant_color'] ?? [],
            $_POST['variant_ram'] ?? [],
            $_POST['variant_storage'] ?? [],
            $_POST['variant_price_delta'] ?? [],
            $_POST['variant_stock'] ?? [],
            $_POST['variant_sku'] ?? []
        );

        if (!empty($_POST['delete_gallery_ids']) && is_array($_POST['delete_gallery_ids'])) {
            foreach ($_POST['delete_gallery_ids'] as $imageId) {
                $this->productModel->deleteProductImage((int) $imageId, $productId);
            }
        }

        if (!empty($_FILES['gallery_images']['name']) && is_array($_FILES['gallery_images']['name'])) {
            foreach ($_FILES['gallery_images']['name'] as $index => $name) {
                if ($_FILES['gallery_images']['error'][$index] === UPLOAD_ERR_NO_FILE) continue;
                $file = [
                    'name' => $_FILES['gallery_images']['name'][$index],
                    'type' => $_FILES['gallery_images']['type'][$index],
                    'tmp_name' => $_FILES['gallery_images']['tmp_name'][$index],
                    'error' => $_FILES['gallery_images']['error'][$index],
                    'size' => $_FILES['gallery_images']['size'][$index],
                ];
                if (empty($this->validateImageUpload($file, false))) {
                    $uploaded = $this->uploadImage($file);
                    if ($uploaded !== null) {
                        $this->productModel->addProductImage($productId, $uploaded, $index);
                    }
                }
            }
        }
    }

    private function sendOrderEmail($to, $orderId, $name, $total)
    {
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        $subject = "Xac nhan don hang #$orderId";
        $body = "Xin chao $name,\n\nDon hang #$orderId cua ban da duoc ghi nhan.\nTong tien: " . number_format($total, 0, ',', '.') . " VND.\nChung toi se lien he va cap nhat trang thai don hang som nhat.";

        if (class_exists('\\PHPMailer\\PHPMailer\\PHPMailer')) {
            try {
                $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
                $mail->isMail();
                $mail->CharSet = 'UTF-8';
                $mail->setFrom('no-reply@localhost', 'The Gioi Dien Thoai');
                $mail->addAddress($to, $name);
                $mail->Subject = $subject;
                $mail->Body = $body;
                return $mail->send();
            } catch (Exception $e) {
                return false;
            }
        }
        return @mail($to, $subject, $body);
    }

    // Hiển thị danh sách sản phẩm
    public function index()
    {
        $category_id = isset($_GET['category_id']) && $_GET['category_id'] !== '' ? (int) $_GET['category_id'] : null;
        $search = trim($_GET['search'] ?? '');
        $filters = [
            'brand_id' => $_GET['brand_id'] ?? '',
            'min_price' => $_GET['min_price'] ?? '',
            'max_price' => $_GET['max_price'] ?? '',
            'featured' => $_GET['featured'] ?? '',
            'sort' => $_GET['sort'] ?? 'newest'
        ];
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 10;
        $totalProducts = $this->productModel->countProducts($category_id, $search, $filters);
        $totalPages = max(1, (int) ceil($totalProducts / $perPage));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $perPage;
        $products = $this->productModel->getProducts($category_id, $search, $perPage, $offset, $filters);
        $brands = $this->productModel->getBrands();
        $this->render('product/list', [
            'products' => $products,
            'search' => $search,
            'category_id' => $category_id,
            'brands' => $brands,
            'filters' => $filters,
            'page' => $page,
            'totalPages' => $totalPages,
            'totalProducts' => $totalProducts
        ]);
    }

    public function home()
    {
        $this->productModel->seedDefaultMarketingData();
        $banners = $this->productModel->getActiveBanners('home');
        $featuredProducts = $this->productModel->getProducts(null, '', 10, 0, ['featured' => 1]);
        $phoneProducts = $this->productModel->getProducts(1, '', 10, 0, ['sort' => 'newest']);
        $laptopProducts = $this->productModel->getProducts(2, '', 10, 0, ['sort' => 'newest']);
        $accessoryProducts = $this->productModel->getProducts(4, '', 10, 0, ['sort' => 'newest']);
        $brands = $this->productModel->getBrands();
        $this->render('product/home', [
            'banners' => $banners,
            'featuredProducts' => $featuredProducts,
            'phoneProducts' => $phoneProducts,
            'laptopProducts' => $laptopProducts,
            'accessoryProducts' => $accessoryProducts,
            'brands' => $brands
        ]);
    }

    // Hiển thị form thêm sản phẩm
    public function add()
    {
        SessionHelper::requireAdmin();

        $categories = $this->categoryModel->getCategories();
        $brands = $this->productModel->getBrands();
        $this->render('product/add', ['categories' => $categories, 'brands' => $brands]);
    }

    // Xử lý lưu Thêm sản phẩm
    public function save()
    {
        SessionHelper::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $this->cleanText($_POST['name'] ?? '');
            $description = $this->cleanText($_POST['description'] ?? '');
            $price = $this->cleanText($_POST['price'] ?? '');
            $category_id = $_POST['category_id'] ?? null;
            $brand_id = $_POST['brand_id'] ?? null;
            $new_brand = $this->cleanText($_POST['new_brand'] ?? '');
            $warranty_months = max(0, (int) ($_POST['warranty_months'] ?? 12));
            $sale_percent = min(90, max(0, (int) ($_POST['sale_percent'] ?? 0)));
            $featured = isset($_POST['featured']) ? 1 : 0;
            if ($new_brand !== '') {
                $brand_id = $this->productModel->addBrandIfNotExists($new_brand);
            }
            $errors = array_merge(
                $this->validateProductInput($name, $description, $price, $category_id),
                $this->validateImageUpload($_FILES['image'] ?? null, true)
            );

            $imageName = 'default.jpg';
            if (empty($errors)) {
                $uploaded = $this->uploadImage($_FILES['image']);
                if ($uploaded === null) {
                    $errors[] = 'Khong the luu hinh anh san pham.';
                } else {
                    $imageName = $uploaded;
                }
            }

            if (!empty($errors)) {
                $categories = $this->categoryModel->getCategories();
                $brands = $this->productModel->getBrands();
                $this->render('product/add', ['errors' => $errors, 'categories' => $categories, 'brands' => $brands]);
                return;
            }

            $result = $this->productModel->addProduct($name, $description, $price, $category_id, $imageName, $brand_id, $warranty_months, $sale_percent, $featured);

            if ($result) {
                $this->saveProductExtras((int) $result);
                header('Location: /project1/Product/index');
                exit();
            } else {
                $errors = is_array($result) ? $result : ['Lỗi không xác định khi thêm'];
                $categories = $this->categoryModel->getCategories();
                $brands = $this->productModel->getBrands();
                $this->render('product/add', ['errors' => $errors, 'categories' => $categories, 'brands' => $brands]);
            }
        }
    }

    // Chỉnh sửa sản phẩm
    public function edit($id = null)
    {
        SessionHelper::requireAdmin();

        if ($id === null) {
            $id = isset($_GET['id']) ? $_GET['id'] : null;
        }

        if (!$id) {
            die("Thiếu ID sản phẩm cần chỉnh sửa.");
        }

        $product = $this->productModel->getProductById($id);
        $categories = $this->categoryModel->getCategories();
        $brands = $this->productModel->getBrands();
        $productImages = $this->productModel->getProductImages($id);
        $productSpecs = $this->productModel->getProductSpecs($id);
        $productVariants = $this->productModel->getProductVariants($id);
        
        if ($product) {
            $this->render('product/edit', [
                'product' => $product,
                'categories' => $categories,
                'brands' => $brands,
                'productImages' => $productImages,
                'productSpecs' => $productSpecs,
                'productVariants' => $productVariants
            ]);
        } else {
            die("Không thấy sản phẩm.");
        }
    }

    // Xử lý cập nhật thông tin sản phẩm
    public function update()
    {
        SessionHelper::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'];
            $name = $this->cleanText($_POST['name'] ?? '');
            $description = $this->cleanText($_POST['description'] ?? '');
            $price = $this->cleanText($_POST['price'] ?? '');
            $category_id = $_POST['category_id'] ?? null;
            $brand_id = $_POST['brand_id'] ?? null;
            $new_brand = $this->cleanText($_POST['new_brand'] ?? '');
            $warranty_months = max(0, (int) ($_POST['warranty_months'] ?? 12));
            $sale_percent = min(90, max(0, (int) ($_POST['sale_percent'] ?? 0)));
            $featured = isset($_POST['featured']) ? 1 : 0;
            if ($new_brand !== '') {
                $brand_id = $this->productModel->addBrandIfNotExists($new_brand);
            }
            
            $imageName = $_POST['existing_image'] ?? 'default.jpg';
            $oldImage = $imageName; 

            $errors = array_merge(
                $this->validateProductInput($name, $description, $price, $category_id),
                $this->validateImageUpload($_FILES['image'] ?? null, false)
            );
            if (!empty($errors)) {
                $product = $this->productModel->getProductById($id);
                $categories = $this->categoryModel->getCategories();
                $brands = $this->productModel->getBrands();
                $this->render('product/edit', [
                    'errors' => $errors,
                    'product' => $product,
                    'categories' => $categories,
                    'brands' => $brands,
                    'productImages' => $this->productModel->getProductImages($id),
                    'productSpecs' => $this->productModel->getProductSpecs($id),
                    'productVariants' => $this->productModel->getProductVariants($id)
                ]);
                return;
            }

            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $uploadDir = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR;
                $newImageName = $this->uploadImage($_FILES['image']);
                if ($newImageName !== null) {
                    $imageName = $newImageName;
                    
                    if ($oldImage !== 'default.jpg' && file_exists($uploadDir . $oldImage)) {
                        unlink($uploadDir . $oldImage);
                    }
                } else {
                    $product = $this->productModel->getProductById($id);
                    $categories = $this->categoryModel->getCategories();
                    $brands = $this->productModel->getBrands();
                    $this->render('product/edit', [
                        'errors' => ['Khong the luu anh moi. Hay kiem tra thu muc public/images co quyen ghi va anh khong qua 5MB.'],
                        'product' => $product,
                        'categories' => $categories,
                        'brands' => $brands,
                        'productImages' => $this->productModel->getProductImages($id),
                        'productSpecs' => $this->productModel->getProductSpecs($id),
                        'productVariants' => $this->productModel->getProductVariants($id)
                    ]);
                    return;
                }
            }

            if ($this->productModel->updateProduct($id, $name, $description, $price, $category_id, $imageName, $brand_id, $warranty_months, $sale_percent, $featured)) {
                $this->saveProductExtras((int) $id);
                $_SESSION['success_msg'] = "🎉 Đã cập nhật sản phẩm thành công.";
                header('Location: /project1/Product/index');
                exit();
            } else {
                echo "Đã xảy ra lỗi khi cập nhật sản phẩm trên cơ sở dữ liệu.";
            }
        }
    }

    // Xóa sản phẩm
    public function delete($id = null)
    {
        SessionHelper::requireAdmin();

        if ($id === null) {
            $id = isset($_GET['id']) ? $_GET['id'] : null;
        }

        if (!$id) {
            die("Thiếu ID sản phẩm cần xóa.");
        }

        $product = $this->productModel->getProductById($id);

        if ($this->productModel->deleteProduct($id)) {
            if ($product && $product->image !== 'default.jpg' && file_exists('public/images/' . $product->image)) {
                unlink('public/images/' . $product->image);
            }
            header('Location: /project1/Product/index');
            exit();
        } else {
            echo "Đã xảy ra lỗi khi xóa sản phẩm.";
        }
    }

    // Chức năng xem chi tiết sản phẩm
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

        $userId = SessionHelper::isLoggedIn() ? SessionHelper::getUserData('id') : null;
        $reviews = $this->productModel->getProductReviews($id);
        $ratingSummary = $this->productModel->getProductRatingSummary($id);
        $canReview = $userId ? $this->productModel->userCanReviewProduct($userId, $id) : false;
        $isWishlisted = $userId ? $this->productModel->isInWishlist($userId, $id) : false;
        $productImages = $this->productModel->getProductImages($id);
        $productSpecs = $this->productModel->getProductSpecs($id);
        $productVariants = $this->productModel->getProductVariants($id);
        $productQuestions = $this->productModel->getProductQuestions($id);

        $this->render('product/detail', [
            'product' => $product,
            'reviews' => $reviews,
            'ratingSummary' => $ratingSummary,
            'canReview' => $canReview,
            'isWishlisted' => $isWishlisted,
            'productImages' => $productImages,
            'productSpecs' => $productSpecs,
            'productVariants' => $productVariants,
            'productQuestions' => $productQuestions
        ]);
    }

    public function askQuestion()
    {
        $product_id = (int)($_POST['product_id'] ?? 0);
        $question = trim($_POST['question'] ?? '');
        $name = SessionHelper::isLoggedIn() ? SessionHelper::getUserData('fullname') : trim($_POST['customer_name'] ?? 'Khach hang');
        $userId = SessionHelper::isLoggedIn() ? SessionHelper::getUserData('id') : null;
        if ($product_id > 0 && strlen($question) >= 5) {
            $this->productModel->addProductQuestion($product_id, $userId, $name, $question);
            $_SESSION['success_msg'] = 'Cau hoi cua ban da duoc ghi nhan.';
        } else {
            $_SESSION['error_msg'] = 'Vui long nhap cau hoi ro hon.';
        }
        header('Location: /project1/Product/detail?id=' . $product_id);
        exit();
    }

    public function answerQuestion()
    {
        SessionHelper::requireAdmin();
        $questionId = (int)($_POST['question_id'] ?? 0);
        $productId = (int)($_POST['product_id'] ?? 0);
        $answer = trim($_POST['answer'] ?? '');
        if ($questionId > 0 && strlen($answer) >= 2) {
            $this->productModel->answerQuestion($questionId, $answer);
        }
        header('Location: /project1/Product/detail?id=' . $productId);
        exit();
    }

    public function saveReview()
    {
        SessionHelper::requireLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /project1/Product/index');
            exit();
        }

        $product_id = (int) ($_POST['product_id'] ?? 0);
        $rating = (int) ($_POST['rating'] ?? 0);
        $comment = trim($_POST['comment'] ?? '');
        $user_id = SessionHelper::getUserData('id');
        $redirect = $_POST['redirect'] ?? ('/project1/Product/detail?id=' . $product_id);
        if (!is_string($redirect) || strpos($redirect, '/project1/') !== 0) {
            $redirect = '/project1/Product/detail?id=' . $product_id;
        }

        if ($product_id <= 0 || $rating < 1 || $rating > 5 || strlen($comment) < 5) {
            $_SESSION['error_msg'] = 'Danh gia can co so sao tu 1-5 va binh luan toi thieu 5 ky tu.';
            header('Location: ' . $redirect);
            exit();
        }

        if (!$this->productModel->userCanReviewProduct($user_id, $product_id)) {
            $_SESSION['error_msg'] = 'Chi khach hang da mua va hoan thanh don moi duoc danh gia san pham.';
            header('Location: ' . $redirect);
            exit();
        }

        $this->productModel->addOrUpdateReview($user_id, $product_id, $rating, $comment);
        $_SESSION['success_msg'] = 'Cam on ban da danh gia san pham.';
        header('Location: ' . $redirect);
        exit();
    }

    public function toggleWishlist()
    {
        SessionHelper::requireLogin();
        $product_id = (int) ($_POST['product_id'] ?? $_GET['id'] ?? 0);
        if ($product_id > 0) {
            $added = $this->productModel->toggleWishlist(SessionHelper::getUserData('id'), $product_id);
            $_SESSION['success_msg'] = $added ? 'Da them vao danh sach yeu thich.' : 'Da bo khoi danh sach yeu thich.';
        }
        $back = $_SERVER['HTTP_REFERER'] ?? '/project1/Product/detail?id=' . $product_id;
        header('Location: ' . $back);
        exit();
    }

    public function toggleCompare()
    {
        $product_id = (int)($_POST['product_id'] ?? $_GET['id'] ?? 0);
        if (!isset($_SESSION['compare'])) $_SESSION['compare'] = [];
        if ($product_id > 0) {
            if (in_array($product_id, $_SESSION['compare'])) {
                $_SESSION['compare'] = array_values(array_diff($_SESSION['compare'], [$product_id]));
            } elseif (count($_SESSION['compare']) < 4) {
                $_SESSION['compare'][] = $product_id;
            } else {
                $_SESSION['error_msg'] = 'Chi co the so sanh toi da 4 san pham.';
            }
        }
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/project1/Product/compare'));
        exit();
    }

    public function compare()
    {
        $ids = $_SESSION['compare'] ?? [];
        $products = $this->productModel->getProductsByIds($ids);
        $specMap = [];
        foreach ($products as $product) {
            $specMap[$product->id] = $this->productModel->getProductSpecs($product->id);
        }
        $this->render('product/compare', ['products' => $products, 'specMap' => $specMap]);
    }

    public function wishlist()
    {
        SessionHelper::requireLogin();
        $products = $this->productModel->getWishlistByUserId(SessionHelper::getUserData('id'));
        $this->render('product/wishlist', ['products' => $products]);
    }

    // ==========================================
    // 🛒 GIỎ HÀNG & ĐẶT HÀNG
    // ==========================================

    // Thêm sản phẩm vào giỏ hàng
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

    // Cập nhật số lượng sản phẩm trong giỏ hàng
    public function updateCartQuantity() {
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        $action = isset($_GET['action']) ? $_GET['action'] : '';

        if ($id && isset($_SESSION['cart'][$id])) {
            if (isset($_SESSION['cart'][$id]['Quantity'])) {
                $_SESSION['cart'][$id]['quantity'] = $_SESSION['cart'][$id]['Quantity'];
                unset($_SESSION['cart'][$id]['Quantity']);
            }

            if ($action === 'increase') {
                $_SESSION['cart'][$id]['quantity'] += 1;
            } elseif ($action === 'decrease') {
                $_SESSION['cart'][$id]['quantity'] -= 1;
                
                if ($_SESSION['cart'][$id]['quantity'] <= 0) {
                    unset($_SESSION['cart'][$id]);
                }
            }
        }

        header('Location: /project1/Product/cart');
        exit();
    }

    // Xóa sản phẩm khỏi giỏ hàng
    public function removeFromCart() {
        $id = isset($_GET['id']) ? $_GET['id'] : null;

        if ($id && isset($_SESSION['cart'][$id])) {
            unset($_SESSION['cart'][$id]);
        }

        header('Location: /project1/Product/cart');
        exit();
    }

    public function applyVoucher()
    {
        $code = strtoupper(trim($_POST['voucher_code'] ?? ''));
        $cart = $_SESSION['cart'] ?? [];
        $total = 0;
        foreach ($cart as $item) {
            $total += ((float)($item['price'] ?? 0)) * ((int)($item['quantity'] ?? 1));
        }
        [$discount, $voucher, $error] = $this->productModel->calculateVoucherDiscount($code, $total);
        if ($error) {
            unset($_SESSION['voucher']);
            $_SESSION['error_msg'] = $error;
        } else {
            $_SESSION['voucher'] = [
                'code' => $voucher->code,
                'discount' => $discount
            ];
            $_SESSION['success_msg'] = 'Da ap dung ma ' . $voucher->code . '.';
        }
        header('Location: /project1/Product/cart');
        exit();
    }

    // Hiển thị trang thanh toán
    public function checkout()
    {
        SessionHelper::requireLogin();

        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        if (empty($cart)) {
            header('Location: /project1/Product/cart');
            exit();
        }
        $this->render('product/checkout', ['cart' => $cart]);
    }

    // Xử lý lưu thông tin đặt hàng
    public function processCheckout()
    {
        SessionHelper::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $this->cleanText($_POST['name'] ?? '');
            $phone = preg_replace('/\D+/', '', $_POST['phone'] ?? '');
            $address = $this->cleanText($_POST['address'] ?? '');
            $email = $this->cleanText($_POST['email'] ?? '');
            $payment_method = strtoupper($this->cleanText($_POST['payment_method'] ?? 'COD'));
            $transaction_code = $this->cleanText($_POST['transaction_code'] ?? '');
            $allowedPayments = ['COD', 'MOMO', 'VNPAY'];
            
            // 🛠️ ĐÃ SỬA: Lấy ID người dùng thông qua SessionHelper cho đồng bộ
            $user_id = SessionHelper::getUserData('id'); 

            if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
                die("Giỏ hàng đang trống.");
            }

            $errors = [];
            if (strlen($name) < 2 || strlen($name) > 120) {
                $errors[] = 'Ten nguoi nhan khong hop le.';
            }
            if (!preg_match('/^(0|84)[0-9]{9,10}$/', $phone)) {
                $errors[] = 'So dien thoai khong hop le.';
            }
            if (strlen($address) < 10 || strlen($address) > 255) {
                $errors[] = 'Dia chi giao hang phai day du hon.';
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Email xac nhan don hang khong hop le.';
            }
            if (!in_array($payment_method, $allowedPayments, true)) {
                $errors[] = 'Phuong thuc thanh toan khong hop le.';
            }
            if (in_array($payment_method, ['MOMO', 'VNPAY'], true) && strlen($transaction_code) < 4) {
                $errors[] = 'Vui long nhap ma giao dich sau khi thanh toan QR.';
            }
            if (!empty($errors)) {
                $this->render('product/checkout', ['cart' => $_SESSION['cart'], 'errors' => $errors]);
                return;
            }

            $this->db->beginTransaction();
            try {
                $payment_status = $payment_method === 'COD' ? 'unpaid' : 'pending_verify';
                $query = "INSERT INTO orders (user_id, name, phone, address, email, payment_method, payment_status, transaction_code, status)
                          VALUES (:user_id, :name, :phone, :address, :email, :payment_method, :payment_status, :transaction_code, 'pending')";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':phone', $phone);
                $stmt->bindParam(':address', $address);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':payment_method', $payment_method);
                $stmt->bindParam(':payment_status', $payment_status);
                $stmt->bindParam(':transaction_code', $transaction_code);
                $stmt->execute();
                
                $order_id = $this->db->lastInsertId();

                $queryDetail = "INSERT INTO order_details (order_id, product_id, quantity, price) 
                                VALUES (:order_id, :product_id, :quantity, :price)";
                $stmtDetail = $this->db->prepare($queryDetail);

                $cart = $_SESSION['cart'];
                $total = 0;
                foreach ($cart as $product_id => $item) {
                    $qty = $item['quantity'] ?? 1; 
                    $total += ((float) $item['price']) * ((int) $qty);

                    $stmtDetail->bindParam(':order_id', $order_id);
                    $stmtDetail->bindParam(':product_id', $product_id);
                    $stmtDetail->bindParam(':quantity', $qty);
                    $stmtDetail->bindParam(':price', $item['price']);
                    $stmtDetail->execute();
                    $this->productModel->decrementStockForProduct($product_id, $qty);
                }

                unset($_SESSION['cart']);
                unset($_SESSION['voucher']);
                $this->db->commit();
                $this->sendOrderEmail($email, $order_id, $name, $total);
                $_SESSION['success_msg'] = 'Don hang #' . $order_id . ' da duoc tao. Email xac nhan se duoc gui neu server da cau hinh mail.';

                header('Location: /project1/Product/orderConfirmation');
                exit();

            } catch (Exception $e) {
                $this->db->rollBack();
                die("Đã xảy ra lỗi khi xử lý đơn hàng: " . $e->getMessage());
            }
        }
    }

    // Hiển thị trang xác nhận đặt hàng thành công
    public function orderConfirmation()
    {
        $this->render('product/orderConfirmation');
    }

    // ==========================================
    // 👤 QUẢN LÝ NGƯỜI DÙNG (ADMIN)
    // ==========================================

    public function users() {
        SessionHelper::requireAdmin();
        $users = $this->productModel->getAllUsers();
        $this->render('product/users', ['users' => $users]);
    }

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
    // 📊 DASHBOARD TỔNG QUAN (ADMIN)
    // ==========================================
    public function dashboard() {
        SessionHelper::requireAdmin();

        $products = $this->productModel->getProducts(); 
        $totalCategories = $this->productModel->countCategories();
        $totalOrders = $this->productModel->countOrders();
        $totalUsers = $this->productModel->countUsers();
        $dashboardStats = $this->productModel->getDashboardStats();

        $this->render('product/dashboard', [
            'products'         => $products,
            'totalCategories'  => $totalCategories,
            'totalOrders'      => $totalOrders,
            'totalUsers'       => $totalUsers,
            'dashboardStats'   => $dashboardStats
        ]);
    }

    // ==========================================
    // 📦 QUẢN LÝ ĐƠN HÀNG (ADMIN)
    // ==========================================

    public function orders() {
        SessionHelper::requireAdmin();
        $filter = $_GET['filter'] ?? 'all';
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 10;
        $totalOrders = $this->productModel->countOrders($filter);
        $totalPages = max(1, (int) ceil($totalOrders / $perPage));
        $page = min($page, $totalPages);
        $orders = $this->productModel->getAllOrders($filter, $perPage, ($page - 1) * $perPage);
        $this->render('product/orders', [
            'orders' => $orders,
            'filterStatus' => $filter,
            'page' => $page,
            'totalPages' => $totalPages,
            'totalOrders' => $totalOrders
        ]);
    }

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

    public function deleteOrder($id = null) {
        SessionHelper::requireAdmin();
        if ($id === null) {
            $id = isset($_GET['id']) ? $_GET['id'] : null;
        }

        if ($id) {
            $this->productModel->deleteOrder($id);
        }
        header('Location: /project1/Product/orders?filter=' . urlencode($_GET['filter'] ?? 'all'));
        exit();
    }

    public function updateOrderStatus() {
        SessionHelper::requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $order_id = $_POST['order_id'] ?? null;
            $status   = $_POST['status']   ?? null;
            if ($order_id && $status) {
                $this->productModel->updateOrderStatus($order_id, $status);
            }
        }
        $filter = $_GET['filter'] ?? $_POST['filter'] ?? 'all';
        $backId = $_GET['id'] ?? null;
        $target = $backId ? '/project1/Product/orderDetail?id=' . (int) $backId . '&filter=' . urlencode($filter) : '/project1/Product/orders?filter=' . urlencode($filter);
        header('Location: ' . $target);
        exit();
    }

    // ===================================================
    // 🛒 ĐƠN HÀNG DÀNH CHO KHÁCH HÀNG (CLIENT)
    // ===================================================

    // Trang hiển thị lịch sử đơn hàng của tôi
    public function myOrders() {
        // 🛠️ ĐÃ SỬA: Thay thế việc kiểm tra thủ công bằng hàm requireLogin() của Helper
        SessionHelper::requireLogin();

        // 🛠️ ĐÃ SỬA: Lấy chuẩn dữ liệu ID từ cấu trúc mảng lồng qua Helper
        $userId = SessionHelper::getUserData('id');
        $myOrders = $this->productModel->getOrdersByUserId($userId);

        $this->render('product/myOrders', ['myOrders' => $myOrders]); 
    }

    // Xử lý khách hàng hủy đơn hàng nhanh từ trang cá nhân
    public function cancelOrder() {
        // 🛠️ ĐÃ SỬA: Yêu cầu đăng nhập thông qua SessionHelper
        SessionHelper::requireLogin();

        $currentTab = 'all';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $order_id = $_POST['order_id'] ?? null;
            
            // 🛠️ ĐÃ SỬA: Lấy chuẩn ID người dùng từ Helper
            $user_id = SessionHelper::getUserData('id');
            $currentTab = $_POST['current_tab'] ?? 'all'; 

            if ($order_id) {
                $order = $this->productModel->getOrderById($order_id);
                $this->productModel->clientCancelOrder($order_id, $user_id);
                if ($order && ($order->status ?? '') === 'pending') {
                    $this->productModel->restoreStockForOrder($order_id);
                }
            }
        }
        
        header('Location: /project1/Product/myOrders?tab=' . $currentTab);
        exit();
    }

    // Trang hiển thị chi tiết đơn hàng của khách hàng
    public function myOrderDetail($id = null) {
        // 🛠️ ĐÃ SỬA: Yêu cầu đăng nhập thông qua SessionHelper
        SessionHelper::requireLogin();

        if ($id === null) {
            $id = isset($_GET['id']) ? $_GET['id'] : null;
        }
        if (!$id) {
            header('Location: /project1/Product/myOrders');
            exit();
        }
        
        $order = $this->productModel->getOrderById($id);
        $details = $this->productModel->getOrderDetails($id);

        if (!$order) {
            die("Đơn hàng không tồn tại.");
        }

        // 🛠️ ĐÃ SỬA: Thay thế $_SESSION['user_id'] bằng SessionHelper::getUserData('id') để tăng cường bảo mật đúng cấu trúc
        $currentUserId = SessionHelper::getUserData('id');
        if ($order->user_id != $currentUserId) {
            die("Bạn không có quyền truy cập vào dữ liệu đơn hàng này!");
        }

        $this->render('product/myOrderDetail', ['order' => $order, 'details' => $details]);
    }

    // ==========================================
    // 🏷️ QUẢN LÝ DANH MỤC (ADMIN)
    // ==========================================

    public function categories() {
        SessionHelper::requireAdmin();
        $categories = $this->categoryModel->getCategories(); 
        $this->render('product/categories', ['categories' => $categories]);
    }

    public function addCategory() {
        SessionHelper::requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'] ?? '';
            if (!empty(trim($name))) {
                $this->categoryModel->addCategory(trim($name));
            }
            header('Location: /project1/Product/categories');
            exit();
        }
    }

    public function updateCategory() {
        SessionHelper::requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $name = $_POST['name'] ?? null;
            
            if ($id && !empty(trim($name))) {
                $this->categoryModel->updateCategory($id, trim($name));
            }
        }
        
        header('Location: /project1/Product/categories');
        exit();
    }

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
}
?>
