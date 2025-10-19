<?php
session_start();
require_once 'db.php'; // make sure this file path is correct

if (!isset($_SESSION['user_id'])) {
    die("Please login first.");
}

$user_id = $_SESSION['user_id'];
$total = isset($_GET['total']) ? (float)$_GET['total'] : 0;

if ($total <= 0) {
    die("Invalid total amount.");
}

// ✅ Create Database object and get connection
$db = new Database();
$conn = $db->getConnection();

// STEP 1: Create an order entry in database
$sql = "INSERT INTO orders (user_id, total_amount, payment_status, payment_method, date)
        VALUES ('$user_id', '$total', 'created', 'Razorpay', NOW())";

if (mysqli_query($conn, $sql)) {
    $order_id = mysqli_insert_id($conn);
} else {
    die("Database error: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout | FootHub</title>
    <style>
        body { font-family: Arial; background: #fff1f0; text-align: center; padding: 60px; }
        h1 { color: #ff3f6c; }
    </style>
</head>
<body>
    <h1>Secure Payment</h1>
    <p>Total Amount: <strong>₹<?= number_format($total, 2) ?></strong></p>

    <form action="payment_response.php" method="POST">
        <script
            src="https://checkout.razorpay.com/v1/checkout.js"
            data-key="rzp_test_1TSGXPk46TbXBv"
            data-amount="<?= $total * 100 ?>"
            data-currency="INR"
            data-name="FootHub"
            data-description="Order Payment"
            data-prefill.name="Rose"
            data-prefill.email="rose@example.com"
            data-prefill.contact="9999999999">
        </script>
        <input type="hidden" name="order_id" value="<?= $order_id ?>">
    </form>
</body>
</html>
