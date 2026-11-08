<?php
/**
 * Cấu hình kết nối CSDL.
 *
 * Thứ tự ưu tiên:
 *   1. Biến môi trường DB_HOST, DB_NAME, DB_USER, DB_PASS (Docker Compose truyền vào)
 *   2. File app/config/db.local.php (dùng trên InfinityFree - KHÔNG commit lên git)
 */
class Database {
    private $host;
    private $port;
    private $db_name;
    private $username;
    private $password;
    public $conn;

    public function __construct() {
        $config = self::localConfig();

        $this->host     = getenv('DB_HOST') ?: ($config['host']     ?? 'localhost');
        $this->port     = getenv('DB_PORT') ?: ($config['port']     ?? '3306');
        $this->db_name  = getenv('DB_NAME') ?: ($config['name']     ?? 'my_store');
        $this->username = getenv('DB_USER') ?: ($config['user']     ?? 'root');
        $this->password = getenv('DB_PASS') !== false ? getenv('DB_PASS') : ($config['password'] ?? '');
    }

    /** Đọc app/config/db.local.php (nếu có). Docker có DB_HOST nên bỏ qua file này. */
    public static function localConfig() {
        static $config = null;
        if ($config === null) {
            $file = __DIR__ . '/db.local.php';
            $config = is_file($file) ? (array) require $file : [];
        }
        return $config;
    }

    public function getConnection() {
        $this->conn = null;
        try {
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->db_name};charset=utf8mb4";
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            error_log('DB connection error: ' . $exception->getMessage());
            die('Không kết nối được cơ sở dữ liệu. Kiểm tra cấu hình trong app/config/db.local.php.');
        }
        return $this->conn;
    }
}
