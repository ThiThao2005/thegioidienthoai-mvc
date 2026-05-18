<?php
class ProductModel
{
    private $conn;
    private $table_name = "product";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // ==========================================
    // 📂 CHỨC NĂNG QUẢN LÝ DANH MỤC (CATEGORIES)
    // ==========================================

    // 1. Lấy toàn bộ danh sách danh mục sản phẩm
    public function getAllCategories() {
        // Đã đồng bộ tên bảng là 'category' theo hàm getProducts phía dưới của bồ
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

    // 3. Cập nhật thông tin/tên danh mục sản phẩm (Đã sửa lỗi $this->db thành $this->conn)
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
public function countOrders() {
    $query = "SELECT COUNT(*) as total FROM orders";
    $stmt = $this->conn->prepare($query);
    $stmt->execute();
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
    public function getProducts($category_id = null)
    {
        $query = "SELECT p.id, p.name, p.description, p.price, p.image, c.name as category_name 
                  FROM " . $this->table_name . " p 
                  LEFT JOIN category c ON p.category_id = c.id";

        // NẾU CÓ TRUYỀN CATEGORY_ID THÌ THÊM ĐIỀU KIỆN WHERE
        if ($category_id !== null) {
            $query .= " WHERE p.category_id = :category_id";
        }

        $query .= " ORDER BY p.id DESC";

        $stmt = $this->conn->prepare($query);

        // NẾU CÓ CATEGORY_ID THÌ BIND PARAM VÀO
        if ($category_id !== null) {
            $stmt->bindParam(':category_id', $category_id);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Lấy chi tiết một sản phẩm theo ID
    public function getProductById($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    // Thêm sản phẩm mới
    public function addProduct($name, $description, $price, $category_id, $image)
    {
        $query = "INSERT INTO " . $this->table_name . " (name, description, price, category_id, image) 
                  VALUES (:name, :description, :price, :category_id, :image)";
        
        $stmt = $this->conn->prepare($query);

        // Làm sạch dữ liệu chống SQL Injection / XSS cơ bản
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

        return $stmt->execute();
    }

    // Cập nhật thông tin sản phẩm
    public function updateProduct($id, $name, $description, $price, $category_id, $image)
    {
        $query = "UPDATE " . $this->table_name . " 
                  SET name = :name, 
                      description = :description, 
                      price = :price, 
                      category_id = :category_id, 
                      image = :image 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);

        // Làm sạch dữ liệu tương tự hàm Add
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
    public function getAllOrders() {
        $query = "SELECT * FROM orders ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
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

    // 4. Xóa đơn hàng
    public function deleteOrder($order_id) {
        try {
            $this->conn->beginTransaction();

            // Xóa chi tiết đơn trước
            $query1 = "DELETE FROM order_details WHERE order_id = :order_id";
            $stmt1 = $this->conn->prepare($query1);
            $stmt1->bindParam(':order_id', $order_id);
            $stmt1->execute();

            // Xóa đơn hàng sau
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
}
?>