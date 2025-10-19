<?php
class Database {
    private $host = "localhost";
    private $user = "root";
    private $pass = "";
    private $dbname = "foothub";
    protected $conn;


    public function __construct() {
        $this->connectDB();
    }

    private function connectDB() {
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->dbname);
        if ($this->conn->connect_error) {
            die("Database Connection Failed: " . $this->conn->connect_error);
        }
    }
      public function getConnection() {
        return $this->conn;
    }
}
?>
