<?php
if(!defined('DB_SERVER')){
    // استخدام مسار مطلق
    $base_path = dirname(__DIR__) . DIRECTORY_SEPARATOR;
    require_once $base_path . 'initialize.php';
}

class DBConnection{

    private $host = DB_SERVER;
    private $username = DB_USERNAME;
    private $password = DB_PASSWORD;
    private $database = DB_NAME;
    
    public $conn;
    
    public function __construct(){
        try {
            if (!isset($this->conn)) {
                
                // إنشاء اتصال جديد
                $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database);
                
                // التحقق من وجود خطأ في الاتصال
                if ($this->conn->connect_error) {
                    error_log("Database connection failed: " . $this->conn->connect_error);
                    throw new Exception('فشل الاتصال بقاعدة البيانات: ' . $this->conn->connect_error);
                }
                
                // تعيين ترميز UTF-8
                if (!$this->conn->set_charset("utf8mb4")) {
                    error_log("Error setting charset utf8mb4: " . $this->conn->error);
                    // Fallback to utf8 if utf8mb4 is not supported
                    if (!$this->conn->set_charset("utf8")) {
                        error_log("Error setting charset utf8: " . $this->conn->error);
                    }
                }
                
                // تسجيل نجاح الاتصال
                error_log("Database connection successful to {$this->database} on {$this->host}");
            }
        } catch (Exception $e) {
            error_log("DBConnection constructor error: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function __destruct(){
        if($this->conn && !$this->conn->connect_errno) {
            $this->conn->close();
        }
    }
    
    public function getConnection(){
        return $this->conn;
    }
    
    // دالة لاختبار الاتصال
    public function testConnection() {
        try {
            if ($this->conn && !$this->conn->connect_error) {
                return $this->conn->ping();
            }
            return false;
        } catch (Exception $e) {
            error_log("Connection test failed: " . $e->getMessage());
            return false;
        }
    }
}
?>