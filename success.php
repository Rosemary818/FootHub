<?php
require 'Razorpay.php';

$payment = new RazorpayPayment();

if (isset($_POST['razorpay_payment_id'])) {
    $success = $payment->verifyPayment($_POST);
    
    if ($success) {
        echo "<h2>Payment Successful!</h2>";
        echo "Payment ID: " . $_POST['razorpay_payment_id'];
    } else {
        echo "<h2>Payment Failed or Verification Error!</h2>";
    }
} else {
    echo "<h2>No Payment Data Received</h2>";
}
