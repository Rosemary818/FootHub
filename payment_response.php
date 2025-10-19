<?php
session_start();
require_once 'db.php';

$db = new Database();
$conn = $db->getConnection();

// Get posted values safely
$order_id    = $_POST['order_id'] ?? null;                
$payment_id  = $_POST['razorpay_payment_id'] ?? null;     
$signature   = $_POST['razorpay_signature'] ?? null;      

$message = "";
$status_class = "";

if (!$order_id) {
    $message = "❌ Invalid order.";
    $status_class = "failed";
} else {
    if ($payment_id) {
        // ✅ Payment successful
        $update = "UPDATE orders 
                   SET payment_status = 'success', payment_method = 'Razorpay' 
                   WHERE order_id = ?";
        $stmt = $conn->prepare($update);
        $stmt->bind_param("i", $order_id);

        if ($stmt->execute()) {

            // --- Fetch all purchased items from cart ---
            $user_id = $_SESSION['user_id'] ?? 1; // fallback for demo
            $cartQuery = "SELECT product_id, quantity FROM cart WHERE user_id = ?";
            $cartStmt = $conn->prepare($cartQuery);
            $cartStmt->bind_param("i", $user_id);
            $cartStmt->execute();
            $cartResult = $cartStmt->get_result();

            while ($item = $cartResult->fetch_assoc()) {
                $product_id = $item['product_id'];
                $quantity = $item['quantity'];

                // --- Decrease stock count for each product ---
                $updateStock = "UPDATE products 
                                SET stock = stock - ? 
                                WHERE product_id = ? AND stock >= ?";
                $stockStmt = $conn->prepare($updateStock);
                $stockStmt->bind_param("iii", $quantity, $product_id, $quantity);
                $stockStmt->execute();
                $stockStmt->close();
            }

            // --- Remove all cart items after purchase ---
            $deleteCart = "DELETE FROM cart WHERE user_id = ?";
            $delStmt = $conn->prepare($deleteCart);
            $delStmt->bind_param("i", $user_id);
            $delStmt->execute();
            $delStmt->close();

            $message = "✅ Payment Successful! Your order has been placed.";
            $status_class = "success";
        } else {
            $message = "Database error: " . $stmt->error;
            $status_class = "failed";
        }
    } else {
        // ❌ Payment failed
        $update = "UPDATE orders 
                   SET payment_status = 'failed', payment_method = 'Razorpay' 
                   WHERE order_id = ?";
        $stmt = $conn->prepare($update);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();

        $message = "❌ Payment Failed!";
        $status_class = "failed";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Payment Status | FootHub</title>
<style>
body {
    font-family: Arial, sans-serif;
    background: #fff1f0;
    text-align: center;
    padding: 60px;
}
h1 {
    color: #ff3f6c;
}
.status-message {
    font-size: 22px;
    margin: 20px 0;
}
.status-message.success { color: #28a745; }
.status-message.failed { color: #dc3545; }
.btn-home {
    display: inline-block;
    margin-top: 30px;
    padding: 12px 25px;
    background: #ff3f6c;
    color: white;
    border-radius: 8px;
    font-size: 16px;
    text-decoration: none;
    transition: 0.3s;
}
.btn-home:hover { background: #e6325a; }
</style>
</head>
<body>
<h1>Payment Status</h1>
<p class="status-message <?= $status_class ?>"><?= $message ?></p>
<a href="dashboard.php" class="btn-home">Go Home</a>
</body>
</html>
