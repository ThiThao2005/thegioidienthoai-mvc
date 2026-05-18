<?php
class CategoryModel
{
    private $conn;
    // Tên bảng chuẩn trong DB của bồ là số ít
    private $table_name = "category"; 

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Thêm danh mục mới vào database
    public function addCategory($name) {
        // Sửa từ categories thành category (Dùng luôn biến $this->table_name cho chuẩn)
        $query = "INSERT INTO " . $this->table_name . " (name) VALUES (:name)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        return $stmt->execute();
    }

    // Xóa danh mục theo ID
    public function deleteCategory($id) {
        // Sửa từ categories thành category (Dùng luôn biến $this->table_name cho chuẩn)
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function getCategories()
    {
        $query = "SELECT id, name FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $result;
    }
}
?>