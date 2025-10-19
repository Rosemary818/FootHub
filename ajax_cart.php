<?php
session_start();
require_once 'cart.php';

$user_id = $_SESSION['user_id'] ?? 0;
if(!$user_id) exit;

$cart = new Cart($user_id);
$pid = $_POST['product_id'] ?? 0;

if($cart->addToCart($pid)) {
    echo 'Added to cart successfully!';
} else {
    echo 'Failed to add to cart.';
}
