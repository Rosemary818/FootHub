<?php
require_once 'db.php';

class Product extends Database {

    public function __construct() {
        parent::__construct();
    }

    // Decrease stock by quantity purchased
    public function decreaseStock($product_id, $quantity) {
        $query = "UPDATE products SET stock = stock - ? WHERE product_id = ? AND stock >= ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iii", $quantity, $product_id, $quantity);
        $stmt->execute();
    }

    // Optional: get current stock (useful for checking availability)
    public function getStock($product_id) {
        $query = "SELECT stock FROM products WHERE product_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['stock'] ?? 0;
    }
}
?>
