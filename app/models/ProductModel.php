<?php
class ProductModel
{
    private $conn;
    private $table_name = "product";

    public function __construct($db)
    {
        $this->conn = $db;
        $this->ensureCoreSchema();
    }

    private function columnExists($table, $column) {
        $stmt = $this->conn->prepare("SHOW COLUMNS FROM `$table` LIKE :column");
        $stmt->execute([':column' => $column]);
        return (bool) $stmt->fetch(PDO::FETCH_OBJ);
    }

    private function ensureCoreSchema() {
        try {
            if (!$this->columnExists('orders', 'status')) {
                $this->conn->exec("ALTER TABLE `orders` ADD `status` VARCHAR(20) NOT NULL DEFAULT 'pending'");
            }
            foreach ([
                'user_id' => "ALTER TABLE `orders` ADD `user_id` INT NULL AFTER `id`",
                'payment_method' => "ALTER TABLE `orders` ADD `payment_method` VARCHAR(30) NOT NULL DEFAULT 'COD'",
                'payment_status' => "ALTER TABLE `orders` ADD `payment_status` VARCHAR(30) NOT NULL DEFAULT 'unpaid'",
                'transaction_code' => "ALTER TABLE `orders` ADD `transaction_code` VARCHAR(120) NULL",
                'email' => "ALTER TABLE `orders` ADD `email` VARCHAR(160) NULL",
                'created_at' => "ALTER TABLE `orders` ADD `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP"
            ] as $column => $sql) {
                if (!$this->columnExists('orders', $column)) {
                    $this->conn->exec($sql);
                }
            }

            $this->conn->exec("CREATE TABLE IF NOT EXISTS product_reviews (
                id INT AUTO_INCREMENT PRIMARY KEY,
                product_id INT NOT NULL,
                user_id INT NOT NULL,
                rating TINYINT NOT NULL,
                comment TEXT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uniq_product_user_review (product_id, user_id),
                INDEX idx_product_reviews_product (product_id),
                CONSTRAINT fk_review_product FOREIGN KEY (product_id) REFERENCES product(id) ON DELETE CASCADE,
                CONSTRAINT fk_review_user FOREIGN KEY (user_id) REFERENCES account(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            $this->conn->exec("CREATE TABLE IF NOT EXISTS wishlists (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                product_id INT NOT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY uniq_user_product_wishlist (user_id, product_id),
                INDEX idx_wishlist_user (user_id),
                CONSTRAINT fk_wishlist_user FOREIGN KEY (user_id) REFERENCES account(id) ON DELETE CASCADE,
                CONSTRAINT fk_wishlist_product FOREIGN KEY (product_id) REFERENCES product(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            $this->conn->exec("CREATE TABLE IF NOT EXISTS brands (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(120) NOT NULL UNIQUE,
                slug VARCHAR(140) NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            foreach ([
                'brand_id' => "ALTER TABLE `product` ADD `brand_id` INT NULL",
                'warranty_months' => "ALTER TABLE `product` ADD `warranty_months` INT NOT NULL DEFAULT 12",
                'sale_percent' => "ALTER TABLE `product` ADD `sale_percent` INT NOT NULL DEFAULT 0",
                'featured' => "ALTER TABLE `product` ADD `featured` TINYINT(1) NOT NULL DEFAULT 0",
                'slug' => "ALTER TABLE `product` ADD `slug` VARCHAR(180) NULL",
                'sold_count' => "ALTER TABLE `product` ADD `sold_count` INT NOT NULL DEFAULT 0"
            ] as $column => $sql) {
                if (!$this->columnExists('product', $column)) {
                    $this->conn->exec($sql);
                }
            }

            $this->conn->exec("CREATE TABLE IF NOT EXISTS product_images (
                id INT AUTO_INCREMENT PRIMARY KEY,
                product_id INT NOT NULL,
                image VARCHAR(255) NOT NULL,
                sort_order INT NOT NULL DEFAULT 0,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_product_images_product (product_id),
                CONSTRAINT fk_product_images_product FOREIGN KEY (product_id) REFERENCES product(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            $this->conn->exec("CREATE TABLE IF NOT EXISTS product_specs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                product_id INT NOT NULL,
                spec_key VARCHAR(120) NOT NULL,
                spec_value VARCHAR(255) NOT NULL,
                sort_order INT NOT NULL DEFAULT 0,
                INDEX idx_product_specs_product (product_id),
                CONSTRAINT fk_product_specs_product FOREIGN KEY (product_id) REFERENCES product(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            $this->conn->exec("CREATE TABLE IF NOT EXISTS product_variants (
                id INT AUTO_INCREMENT PRIMARY KEY,
                product_id INT NOT NULL,
                color VARCHAR(80) NULL,
                ram VARCHAR(80) NULL,
                storage VARCHAR(80) NULL,
                price_delta DECIMAL(10,2) NOT NULL DEFAULT 0,
                stock INT NOT NULL DEFAULT 0,
                sku VARCHAR(120) NULL,
                INDEX idx_product_variants_product (product_id),
                CONSTRAINT fk_product_variants_product FOREIGN KEY (product_id) REFERENCES product(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            $this->conn->exec("CREATE TABLE IF NOT EXISTS banners (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(160) NOT NULL,
                subtitle VARCHAR(255) NULL,
                image VARCHAR(255) NULL,
                link VARCHAR(255) NULL,
                position VARCHAR(40) NOT NULL DEFAULT 'home',
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                sort_order INT NOT NULL DEFAULT 0,
                starts_at DATETIME NULL,
                ends_at DATETIME NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            $this->conn->exec("CREATE TABLE IF NOT EXISTS vouchers (
                id INT AUTO_INCREMENT PRIMARY KEY,
                code VARCHAR(60) NOT NULL UNIQUE,
                discount_type VARCHAR(20) NOT NULL DEFAULT 'percent',
                discount_value DECIMAL(10,2) NOT NULL DEFAULT 0,
                min_order DECIMAL(10,2) NOT NULL DEFAULT 0,
                usage_limit INT NOT NULL DEFAULT 0,
                used_count INT NOT NULL DEFAULT 0,
                starts_at DATETIME NULL,
                ends_at DATETIME NULL,
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            $this->conn->exec("CREATE TABLE IF NOT EXISTS product_questions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                product_id INT NOT NULL,
                user_id INT NULL,
                customer_name VARCHAR(120) NULL,
                question TEXT NOT NULL,
                answer TEXT NULL,
                is_public TINYINT(1) NOT NULL DEFAULT 1,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                answered_at DATETIME NULL,
                INDEX idx_product_questions_product (product_id),
                CONSTRAINT fk_product_questions_product FOREIGN KEY (product_id) REFERENCES product(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        } catch (Exception $e) {
            // Some hosting/database accounts do not allow ALTER/CREATE. The app still runs with existing schema.
        }
    }

    // ==========================================
    // 📂 CHỨC NĂNG QUẢN LÝ DANH MỤC (CATEGORIES)
    // ==========================================

    // 1. Lấy toàn bộ danh sách danh mục sản phẩm
    public function getAllCategories() {
        $query = "SELECT id, name FROM category ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // 2. Thêm danh mục sản phẩm mới
    public function addCategory($name) {
        $query = "INSERT INTO category (name) VALUES (:name)";
        $stmt = $this->conn->prepare($query);
        $name = htmlspecialchars(strip_tags($name));
        $stmt->bindParam(':name', $name);
        return $stmt->execute();
    }

    // 3. Cập nhật thông tin/tên danh mục sản phẩm
    public function updateCategory($id, $name) {
        $sql = "UPDATE category SET name = :name WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $name = htmlspecialchars(strip_tags($name));
        return $stmt->execute([':name' => $name, ':id' => $id]);
    }

    // 4. Xóa danh mục sản phẩm
    public function deleteCategory($id) {
        $query = "DELETE FROM category WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Hàm đếm tổng số danh mục
    public function countCategories() {
        $query = "SELECT COUNT(*) as total FROM category";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        return $row->total ?? 0;
    }

    // Hàm đếm tổng số đơn hàng
    public function countOrders($status = 'all') {
        $where = "";
        $params = [];
        if ($status !== 'all' && $status !== null && $status !== '') {
            $where = " WHERE status = :status";
            $params[':status'] = $status;
        }
        $query = "SELECT COUNT(*) as total FROM orders" . $where;
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        return $row->total ?? 0;
    }

    // Hàm đếm tổng số thành viên (tài khoản)
    public function countUsers() {
        $query = "SELECT COUNT(*) as total FROM account";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        return $row->total ?? 0;
    }

    // ==========================================
    // 🛍️ CHỨC NĂNG QUẢN LÝ SẢN PHẨM (PRODUCTS)
    // ==========================================

    // Lấy danh sách sản phẩm kèm tên danh mục (Có hỗ trợ lọc theo ID danh mục)
    public function getBrands()
    {
        $stmt = $this->conn->prepare("SELECT id, name FROM brands ORDER BY name ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function addBrandIfNotExists($name)
    {
        $name = trim($name);
        if ($name === '') return null;
        $stmt = $this->conn->prepare("INSERT IGNORE INTO brands (name, slug) VALUES (:name, :slug)");
        $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $name));
        $stmt->execute([':name' => $name, ':slug' => trim($slug, '-')]);
        $find = $this->conn->prepare("SELECT id FROM brands WHERE name = :name LIMIT 1");
        $find->execute([':name' => $name]);
        $row = $find->fetch(PDO::FETCH_OBJ);
        return $row ? (int) $row->id : null;
    }

    public function makeSlug($text)
    {
        $slug = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text) ?: $text), '-'));
        return $slug ?: ('product-' . time());
    }

    public function getActiveBanners($position = 'home')
    {
        $query = "SELECT * FROM banners
                  WHERE is_active = 1 AND position = :position
                    AND (starts_at IS NULL OR starts_at <= NOW())
                    AND (ends_at IS NULL OR ends_at >= NOW())
                  ORDER BY sort_order ASC, id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':position' => $position]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function seedDefaultMarketingData()
    {
        $count = (int)$this->conn->query("SELECT COUNT(*) FROM banners")->fetchColumn();
        if ($count === 0) {
            $stmt = $this->conn->prepare("INSERT INTO banners (title, subtitle, image, link, position, sort_order) VALUES (:title, :subtitle, :image, :link, 'home', :sort_order)");
            $rows = [
                ['Flash sale cong nghe moi', 'Giam den 30% cho dien thoai va phu kien hot', '1779075308_xiaomi-17-5g-xanh-la-1-639088210870268655-750x500.jpg', '/project1/Product/index?featured=1', 1],
                ['Laptop hoc tap lam viec', 'Nhieu mau mong nhe, bao hanh chinh hang', '1778557560_acer-swift-x-14-2024-front-angled-e1715278668968.jpg', '/project1/Product/index?category_id=2', 2],
                ['Phu kien gia tot', 'Tai nghe, chuot, sac nhanh va man hinh', '1778937883_tai-nghe-bluetooth-chup-tai-sony-wh-ch520-xanh-1-750x500.jpg', '/project1/Product/index?category_id=4', 3],
            ];
            foreach ($rows as $row) {
                $stmt->execute([':title' => $row[0], ':subtitle' => $row[1], ':image' => $row[2], ':link' => $row[3], ':sort_order' => $row[4]]);
            }
        }

        $voucherCount = (int)$this->conn->query("SELECT COUNT(*) FROM vouchers")->fetchColumn();
        if ($voucherCount === 0) {
            $stmt = $this->conn->prepare("INSERT IGNORE INTO vouchers (code, discount_type, discount_value, min_order, usage_limit, is_active) VALUES (:code, :type, :value, :min_order, :limit, 1)");
            $stmt->execute([':code' => 'SALE10', ':type' => 'percent', ':value' => 10, ':min_order' => 1000000, ':limit' => 100]);
            $stmt->execute([':code' => 'FREESHIP', ':type' => 'fixed', ':value' => 50000, ':min_order' => 500000, ':limit' => 200]);
        }
    }

    public function getVoucherByCode($code)
    {
        $stmt = $this->conn->prepare("SELECT * FROM vouchers WHERE code = :code AND is_active = 1 LIMIT 1");
        $stmt->execute([':code' => strtoupper(trim($code))]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function calculateVoucherDiscount($code, $total)
    {
        $voucher = $this->getVoucherByCode($code);
        if (!$voucher) return [0, null, 'Ma giam gia khong ton tai hoac da tat.'];
        if ($voucher->usage_limit > 0 && $voucher->used_count >= $voucher->usage_limit) return [0, null, 'Ma giam gia da het luot su dung.'];
        if ($voucher->starts_at && strtotime($voucher->starts_at) > time()) return [0, null, 'Ma giam gia chua bat dau.'];
        if ($voucher->ends_at && strtotime($voucher->ends_at) < time()) return [0, null, 'Ma giam gia da het han.'];
        if ($total < (float)$voucher->min_order) return [0, null, 'Don hang chua dat gia tri toi thieu de dung ma.'];

        $discount = $voucher->discount_type === 'fixed'
            ? (float)$voucher->discount_value
            : $total * ((float)$voucher->discount_value / 100);
        $discount = min($discount, $total);
        return [$discount, $voucher, null];
    }

    public function getProducts($category_id = null, $search = '', $limit = null, $offset = 0, $filters = [])
    {
        $query = "SELECT p.id, p.name, p.description, p.price, p.image, p.sale_percent, p.featured, p.slug, p.sold_count,
                         p.warranty_months, b.name as brand_name, c.name as category_name,
                         COALESCE((SELECT SUM(stock) FROM product_variants pv WHERE pv.product_id = p.id), 0) as total_stock,
                         COALESCE(AVG(r.rating), 0) as avg_rating,
                         COUNT(DISTINCT r.id) as review_count
                  FROM " . $this->table_name . " p 
                  LEFT JOIN category c ON p.category_id = c.id
                  LEFT JOIN brands b ON p.brand_id = b.id
                  LEFT JOIN product_reviews r ON r.product_id = p.id";

        $conditions = [];
        $params = [];
        if ($category_id !== null) {
            $conditions[] = "p.category_id = :category_id";
            $params[':category_id'] = (int) $category_id;
        }
        if (trim($search) !== '') {
            $conditions[] = "(p.name LIKE :search OR p.description LIKE :search OR c.name LIKE :search OR b.name LIKE :search)";
            $params[':search'] = '%' . trim($search) . '%';
        }
        if (!empty($filters['brand_id'])) {
            $conditions[] = "p.brand_id = :brand_id";
            $params[':brand_id'] = (int) $filters['brand_id'];
        }
        if (isset($filters['min_price']) && $filters['min_price'] !== '') {
            $conditions[] = "p.price >= :min_price";
            $params[':min_price'] = (float) $filters['min_price'];
        }
        if (isset($filters['max_price']) && $filters['max_price'] !== '') {
            $conditions[] = "p.price <= :max_price";
            $params[':max_price'] = (float) $filters['max_price'];
        }
        if (!empty($filters['featured'])) {
            $conditions[] = "p.featured = 1";
        }

        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        $query .= " GROUP BY p.id, p.name, p.description, p.price, p.image, p.sale_percent, p.featured, p.slug, p.sold_count, p.warranty_months, b.name, c.name";
        $sort = $filters['sort'] ?? 'newest';
        if ($sort === 'price_asc') {
            $query .= " ORDER BY p.price ASC";
        } elseif ($sort === 'price_desc') {
            $query .= " ORDER BY p.price DESC";
        } elseif ($sort === 'rating') {
            $query .= " ORDER BY avg_rating DESC, p.id DESC";
        } else {
            $query .= " ORDER BY p.id DESC";
        }
        if ($limit !== null) {
            $query .= " LIMIT :limit OFFSET :offset";
        }
        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        if ($limit !== null) {
            $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function countProducts($category_id = null, $search = '', $filters = []) {
        $query = "SELECT COUNT(DISTINCT p.id) as total
                  FROM " . $this->table_name . " p
                  LEFT JOIN category c ON p.category_id = c.id
                  LEFT JOIN brands b ON p.brand_id = b.id";
        $conditions = [];
        $params = [];
        if ($category_id !== null) {
            $conditions[] = "p.category_id = :category_id";
            $params[':category_id'] = (int) $category_id;
        }
        if (trim($search) !== '') {
            $conditions[] = "(p.name LIKE :search OR p.description LIKE :search OR c.name LIKE :search OR b.name LIKE :search)";
            $params[':search'] = '%' . trim($search) . '%';
        }
        if (!empty($filters['brand_id'])) {
            $conditions[] = "p.brand_id = :brand_id";
            $params[':brand_id'] = (int) $filters['brand_id'];
        }
        if (isset($filters['min_price']) && $filters['min_price'] !== '') {
            $conditions[] = "p.price >= :min_price";
            $params[':min_price'] = (float) $filters['min_price'];
        }
        if (isset($filters['max_price']) && $filters['max_price'] !== '') {
            $conditions[] = "p.price <= :max_price";
            $params[':max_price'] = (float) $filters['max_price'];
        }
        if (!empty($filters['featured'])) {
            $conditions[] = "p.featured = 1";
        }
        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }
        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        return (int) ($row->total ?? 0);
    }

    // Lấy chi tiết một sản phẩm theo ID
    public function getProductById($id)
    {
        $query = "SELECT p.id, p.name, p.description, p.price, p.category_id, p.image, p.slug, p.sold_count,
                         p.brand_id, p.warranty_months, p.sale_percent, p.featured,
                         b.name as brand_name, c.name as category_name,
                         COALESCE(AVG(r.rating), 0) as avg_rating,
                         COUNT(r.id) as review_count
                  FROM " . $this->table_name . " p
                  LEFT JOIN category c ON p.category_id = c.id
                  LEFT JOIN brands b ON p.brand_id = b.id
                  LEFT JOIN product_reviews r ON r.product_id = p.id
                  WHERE p.id = :id
                  GROUP BY p.id, p.name, p.description, p.price, p.category_id, p.image, p.slug, p.sold_count, p.brand_id,
                           p.warranty_months, p.sale_percent, p.featured, b.name, c.name";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    // Thêm sản phẩm mới
    public function addProduct($name, $description, $price, $category_id, $image, $brand_id = null, $warranty_months = 12, $sale_percent = 0, $featured = 0)
    {
        $query = "INSERT INTO " . $this->table_name . " (name, description, price, category_id, image, brand_id, warranty_months, sale_percent, featured, slug) 
                  VALUES (:name, :description, :price, :category_id, :image, :brand_id, :warranty_months, :sale_percent, :featured, :slug)";
        
        $stmt = $this->conn->prepare($query);

        $name = htmlspecialchars(strip_tags($name));
        $description = htmlspecialchars(strip_tags($description));
        $price = htmlspecialchars(strip_tags($price));
        $category_id = htmlspecialchars(strip_tags($category_id));
        $image = htmlspecialchars(strip_tags($image));

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':image', $image);
        $stmt->bindValue(':brand_id', $brand_id ?: null, PDO::PARAM_INT);
        $stmt->bindValue(':warranty_months', (int) $warranty_months, PDO::PARAM_INT);
        $stmt->bindValue(':sale_percent', (int) $sale_percent, PDO::PARAM_INT);
        $stmt->bindValue(':featured', (int) $featured, PDO::PARAM_INT);
        $stmt->bindValue(':slug', $this->makeSlug($name), PDO::PARAM_STR);

        return $stmt->execute() ? (int) $this->conn->lastInsertId() : false;
    }

    // Cập nhật thông tin sản phẩm
    public function updateProduct($id, $name, $description, $price, $category_id, $image, $brand_id = null, $warranty_months = 12, $sale_percent = 0, $featured = 0)
    {
        $query = "UPDATE " . $this->table_name . " 
                  SET name = :name, 
                      description = :description, 
                      price = :price, 
                      category_id = :category_id, 
                      image = :image,
                      brand_id = :brand_id,
                      warranty_months = :warranty_months,
                      sale_percent = :sale_percent,
                      featured = :featured,
                      slug = :slug
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);

        $name = htmlspecialchars(strip_tags($name));
        $description = htmlspecialchars(strip_tags($description));
        $price = htmlspecialchars(strip_tags($price));
        $category_id = htmlspecialchars(strip_tags($category_id));
        $image = htmlspecialchars(strip_tags($image));

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':image', $image);
        $stmt->bindValue(':brand_id', $brand_id ?: null, PDO::PARAM_INT);
        $stmt->bindValue(':warranty_months', (int) $warranty_months, PDO::PARAM_INT);
        $stmt->bindValue(':sale_percent', (int) $sale_percent, PDO::PARAM_INT);
        $stmt->bindValue(':featured', (int) $featured, PDO::PARAM_INT);
        $stmt->bindValue(':slug', $this->makeSlug($name), PDO::PARAM_STR);

        return $stmt->execute();
    }

    // Xóa sản phẩm
    public function deleteProduct($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function getProductImages($product_id) {
        $stmt = $this->conn->prepare("SELECT * FROM product_images WHERE product_id = :product_id ORDER BY sort_order ASC, id ASC");
        $stmt->bindValue(':product_id', (int) $product_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function addProductImage($product_id, $image, $sort_order = 0) {
        $stmt = $this->conn->prepare("INSERT INTO product_images (product_id, image, sort_order) VALUES (:product_id, :image, :sort_order)");
        return $stmt->execute([
            ':product_id' => (int) $product_id,
            ':image' => htmlspecialchars(strip_tags($image)),
            ':sort_order' => (int) $sort_order
        ]);
    }

    public function deleteProductImage($image_id, $product_id) {
        $stmt = $this->conn->prepare("DELETE FROM product_images WHERE id = :id AND product_id = :product_id");
        return $stmt->execute([':id' => (int) $image_id, ':product_id' => (int) $product_id]);
    }

    public function getProductSpecs($product_id) {
        $stmt = $this->conn->prepare("SELECT * FROM product_specs WHERE product_id = :product_id ORDER BY sort_order ASC, id ASC");
        $stmt->bindValue(':product_id', (int) $product_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function replaceProductSpecs($product_id, $keys, $values) {
        $this->conn->prepare("DELETE FROM product_specs WHERE product_id = :product_id")->execute([':product_id' => (int) $product_id]);
        $stmt = $this->conn->prepare("INSERT INTO product_specs (product_id, spec_key, spec_value, sort_order) VALUES (:product_id, :spec_key, :spec_value, :sort_order)");
        $order = 0;
        foreach ($keys as $idx => $key) {
            $key = trim($key);
            $value = trim($values[$idx] ?? '');
            if ($key === '' || $value === '') continue;
            $stmt->execute([
                ':product_id' => (int) $product_id,
                ':spec_key' => htmlspecialchars(strip_tags($key)),
                ':spec_value' => htmlspecialchars(strip_tags($value)),
                ':sort_order' => $order++
            ]);
        }
    }

    public function getProductVariants($product_id) {
        $stmt = $this->conn->prepare("SELECT * FROM product_variants WHERE product_id = :product_id ORDER BY id ASC");
        $stmt->bindValue(':product_id', (int) $product_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function replaceProductVariants($product_id, $colors, $rams, $storages, $priceDeltas, $stocks, $skus) {
        $this->conn->prepare("DELETE FROM product_variants WHERE product_id = :product_id")->execute([':product_id' => (int) $product_id]);
        $stmt = $this->conn->prepare("INSERT INTO product_variants (product_id, color, ram, storage, price_delta, stock, sku)
                                      VALUES (:product_id, :color, :ram, :storage, :price_delta, :stock, :sku)");
        foreach ($colors as $idx => $color) {
            $color = trim($color);
            $ram = trim($rams[$idx] ?? '');
            $storage = trim($storages[$idx] ?? '');
            $priceDelta = $priceDeltas[$idx] ?? 0;
            $stock = $stocks[$idx] ?? 0;
            $sku = trim($skus[$idx] ?? '');
            if ($color === '' && $ram === '' && $storage === '' && $sku === '') continue;
            $stmt->execute([
                ':product_id' => (int) $product_id,
                ':color' => htmlspecialchars(strip_tags($color)),
                ':ram' => htmlspecialchars(strip_tags($ram)),
                ':storage' => htmlspecialchars(strip_tags($storage)),
                ':price_delta' => is_numeric($priceDelta) ? $priceDelta : 0,
                ':stock' => max(0, (int) $stock),
                ':sku' => htmlspecialchars(strip_tags($sku))
            ]);
        }
    }

    // ==========================================
    // 👥 CHỨC NĂNG QUẢN LÝ NGƯỜI DÙNG (ADMIN)
    // ==========================================

    // 1. Lấy toàn bộ danh sách người dùng từ bảng account (Trừ mật khẩu)
    public function getAllUsers() {
        $query = "SELECT id, username, fullname, role FROM account ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // 2. Cập nhật quyền của người dùng (từ user lên admin hoặc ngược lại)
    public function updateUserRole($user_id, $role) {
        $query = "UPDATE account SET role = :role WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':id', $user_id);
        return $stmt->execute();
    }

    // 3. Xóa tài khoản người dùng
    public function deleteUser($user_id) {
        $query = "DELETE FROM account WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $user_id);
        return $stmt->execute();
    }

    // ==========================================
    // 📦 CHỨC NĂNG QUẢN LÝ ĐƠN HÀNG (ADMIN)
    // ==========================================

    // 1. Lấy toàn bộ danh sách đơn hàng mới nhất
    public function getAllOrders($status = 'all', $limit = null, $offset = 0) {
        $query = "SELECT * FROM orders";
        $params = [];
        if ($status !== 'all' && $status !== null && $status !== '') {
            $query .= " WHERE status = :status";
            $params[':status'] = $status;
        }
        $query .= " ORDER BY id DESC";
        if ($limit !== null) {
            $query .= " LIMIT :limit OFFSET :offset";
        }
        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }
        if ($limit !== null) {
            $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // 2. Lấy thông tin chi tiết của một đơn hàng
    public function getOrderById($order_id) {
        $query = "SELECT * FROM orders WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $order_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    // 3. Lấy danh sách các sản phẩm bên trong đơn hàng đó
    public function getOrderDetails($order_id) {
        $query = "SELECT od.*, p.name as product_name, p.image as product_image 
                  FROM order_details od
                  JOIN " . $this->table_name . " p ON od.product_id = p.id
                  WHERE od.order_id = :order_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $order_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // 4. Cập nhật trạng thái đơn hàng (Đã cập nhật để chấp nhận trạng thái 'cancelled')
    public function updateOrderStatus($order_id, $status) {
        $allowed = ['pending', 'processing', 'shipped', 'done', 'cancelled'];
        if (!in_array($status, $allowed)) return false;
        $current = $this->getOrderById($order_id);
        if (!$current) return false;
        $flow = ['pending' => 1, 'processing' => 2, 'shipped' => 3, 'done' => 4, 'cancelled' => 99];
        $currentStatus = $current->status ?? 'pending';
        if ($status !== 'cancelled' && ($flow[$status] ?? 0) < ($flow[$currentStatus] ?? 1)) {
            return false;
        }
        $query = "UPDATE orders SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $order_id);
        return $stmt->execute();
    }

    // 5. Xóa đơn hàng
    public function deleteOrder($order_id) {
        try {
            $this->conn->beginTransaction();

            $query1 = "DELETE FROM order_details WHERE order_id = :order_id";
            $stmt1 = $this->conn->prepare($query1);
            $stmt1->bindParam(':order_id', $order_id);
            $stmt1->execute();

            $query2 = "DELETE FROM orders WHERE id = :id";
            $stmt2 = $this->conn->prepare($query2);
            $stmt2->bindParam(':id', $order_id);
            $stmt2->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    // ===================================================
    // 🛒 CHỨC NĂNG ĐƠN HÀNG DÀNH CHO KHÁCH HÀNG (CLIENT)
    // ===================================================

    /**
     * Lấy danh sách tất cả các đơn hàng dựa trên ID người dùng đăng nhập
     */
    public function getOrdersByUserId($userId) {
        // Tự động tính tổng tiền (total_price) bằng phép tính SUM toán học từ bảng order_details
        $query = "SELECT o.*, SUM(od.quantity * od.price) as total_price 
                  FROM orders o
                  LEFT JOIN order_details od ON o.id = od.order_id
                  WHERE o.user_id = :user_id
                  GROUP BY o.id
                  ORDER BY o.created_at DESC";
                  
        // 🛠️ ĐÃ FIX: Thay đổi $this->db thành $this->conn cho đồng bộ với thuộc tính class
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_OBJ); 
    }

    /**
     * Khách hàng tự chủ động hủy đơn hàng (Chỉ cho phép khi trạng thái đang là 'pending')
     */
    public function clientCancelOrder($order_id, $user_id) {
        $query = "UPDATE orders 
                  SET status = 'cancelled' 
                  WHERE id = :id AND user_id = :user_id AND status = 'pending'";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $order_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getProductReviews($product_id) {
        $query = "SELECT r.*, a.fullname, a.username
                  FROM product_reviews r
                  JOIN account a ON a.id = r.user_id
                  WHERE r.product_id = :product_id
                  ORDER BY r.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':product_id', (int) $product_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getProductRatingSummary($product_id) {
        $query = "SELECT COALESCE(AVG(rating), 0) as avg_rating, COUNT(*) as total_reviews
                  FROM product_reviews WHERE product_id = :product_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':product_id', (int) $product_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function getProductsByIds($ids)
    {
        $ids = array_values(array_filter(array_map('intval', $ids)));
        if (empty($ids)) return [];
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $query = "SELECT p.*, b.name as brand_name, c.name as category_name
                  FROM product p
                  LEFT JOIN brands b ON b.id = p.brand_id
                  LEFT JOIN category c ON c.id = p.category_id
                  WHERE p.id IN ($placeholders)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute($ids);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function userCanReviewProduct($user_id, $product_id) {
        $query = "SELECT COUNT(*) as total
                  FROM orders o
                  JOIN order_details od ON od.order_id = o.id
                  WHERE o.user_id = :user_id AND od.product_id = :product_id AND o.status = 'done'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':user_id', (int) $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':product_id', (int) $product_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        return (int) ($row->total ?? 0) > 0;
    }

    public function addOrUpdateReview($user_id, $product_id, $rating, $comment) {
        $query = "INSERT INTO product_reviews (product_id, user_id, rating, comment)
                  VALUES (:product_id, :user_id, :rating, :comment)
                  ON DUPLICATE KEY UPDATE rating = VALUES(rating), comment = VALUES(comment), updated_at = CURRENT_TIMESTAMP";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':product_id', (int) $product_id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', (int) $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':rating', (int) $rating, PDO::PARAM_INT);
        $stmt->bindValue(':comment', htmlspecialchars(strip_tags(trim($comment))), PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function isInWishlist($user_id, $product_id) {
        $stmt = $this->conn->prepare("SELECT id FROM wishlists WHERE user_id = :user_id AND product_id = :product_id LIMIT 1");
        $stmt->execute([':user_id' => (int) $user_id, ':product_id' => (int) $product_id]);
        return (bool) $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function toggleWishlist($user_id, $product_id) {
        if ($this->isInWishlist($user_id, $product_id)) {
            $stmt = $this->conn->prepare("DELETE FROM wishlists WHERE user_id = :user_id AND product_id = :product_id");
            $stmt->execute([':user_id' => (int) $user_id, ':product_id' => (int) $product_id]);
            return false;
        }
        $stmt = $this->conn->prepare("INSERT IGNORE INTO wishlists (user_id, product_id) VALUES (:user_id, :product_id)");
        $stmt->execute([':user_id' => (int) $user_id, ':product_id' => (int) $product_id]);
        return true;
    }

    public function getWishlistByUserId($user_id) {
        $query = "SELECT p.id, p.name, p.description, p.price, p.image, c.name as category_name, w.created_at
                  FROM wishlists w
                  JOIN product p ON p.id = w.product_id
                  LEFT JOIN category c ON p.category_id = c.id
                  WHERE w.user_id = :user_id
                  ORDER BY w.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':user_id', (int) $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function addProductQuestion($product_id, $user_id, $customer_name, $question)
    {
        $stmt = $this->conn->prepare("INSERT INTO product_questions (product_id, user_id, customer_name, question) VALUES (:product_id, :user_id, :customer_name, :question)");
        return $stmt->execute([
            ':product_id' => (int)$product_id,
            ':user_id' => $user_id ? (int)$user_id : null,
            ':customer_name' => htmlspecialchars(strip_tags($customer_name)),
            ':question' => htmlspecialchars(strip_tags($question))
        ]);
    }

    public function getProductQuestions($product_id)
    {
        $stmt = $this->conn->prepare("SELECT q.*, a.fullname FROM product_questions q LEFT JOIN account a ON a.id = q.user_id WHERE q.product_id = :product_id AND q.is_public = 1 ORDER BY q.id DESC");
        $stmt->execute([':product_id' => (int)$product_id]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function answerQuestion($question_id, $answer)
    {
        $stmt = $this->conn->prepare("UPDATE product_questions SET answer = :answer, answered_at = NOW() WHERE id = :id");
        return $stmt->execute([':answer' => htmlspecialchars(strip_tags($answer)), ':id' => (int)$question_id]);
    }

    public function decrementStockForProduct($product_id, $quantity)
    {
        $variant = $this->conn->prepare("SELECT id, stock FROM product_variants WHERE product_id = :product_id AND stock >= :quantity ORDER BY stock DESC LIMIT 1");
        $variant->execute([':product_id' => (int)$product_id, ':quantity' => (int)$quantity]);
        $row = $variant->fetch(PDO::FETCH_OBJ);
        if ($row) {
            $this->conn->prepare("UPDATE product_variants SET stock = stock - :quantity WHERE id = :id")->execute([':quantity' => (int)$quantity, ':id' => (int)$row->id]);
        }
        $this->conn->prepare("UPDATE product SET sold_count = sold_count + :quantity WHERE id = :id")->execute([':quantity' => (int)$quantity, ':id' => (int)$product_id]);
    }

    public function restoreStockForOrder($order_id)
    {
        $items = $this->getOrderDetails($order_id);
        foreach ($items as $item) {
            $variant = $this->conn->prepare("SELECT id FROM product_variants WHERE product_id = :product_id ORDER BY id ASC LIMIT 1");
            $variant->execute([':product_id' => (int)$item->product_id]);
            $row = $variant->fetch(PDO::FETCH_OBJ);
            if ($row) {
                $this->conn->prepare("UPDATE product_variants SET stock = stock + :quantity WHERE id = :id")->execute([':quantity' => (int)$item->quantity, ':id' => (int)$row->id]);
            }
        }
    }

    public function getDashboardStats()
    {
        $revenue = $this->conn->query("SELECT COALESCE(SUM(od.quantity * od.price), 0) FROM order_details od JOIN orders o ON o.id = od.order_id WHERE o.status IN ('processing','shipped','done')")->fetchColumn();
        $today = $this->conn->query("SELECT COALESCE(SUM(od.quantity * od.price), 0) FROM order_details od JOIN orders o ON o.id = od.order_id WHERE DATE(o.created_at) = CURDATE()")->fetchColumn();
        $lowStock = $this->conn->query("SELECT COUNT(*) FROM product_variants WHERE stock <= 3")->fetchColumn();
        $topProducts = $this->conn->query("SELECT id, name, sold_count FROM product ORDER BY sold_count DESC, id DESC LIMIT 5")->fetchAll(PDO::FETCH_OBJ);
        return [
            'revenue' => (float)$revenue,
            'todayRevenue' => (float)$today,
            'lowStock' => (int)$lowStock,
            'topProducts' => $topProducts
        ];
    }
}
?>
