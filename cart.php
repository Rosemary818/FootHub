<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    die("Please login to view your cart.");
}

class Cart extends Database {
    private $user_id;

    public function __construct($user_id) {
        parent::__construct();
        $this->user_id = $user_id;
    }

    public function addToCart($product_id, $quantity = 1) {
        $stmt = $this->conn->prepare("SELECT * FROM cart WHERE user_id=? AND product_id=?");
        $stmt->bind_param("ii", $this->user_id, $product_id);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows > 0) {
            $stmt = $this->conn->prepare("UPDATE cart SET quantity = quantity + ? WHERE user_id=? AND product_id=?");
            $stmt->bind_param("iii", $quantity, $this->user_id, $product_id);
            return $stmt->execute();
        } else {
            $stmt = $this->conn->prepare("INSERT INTO cart(user_id, product_id, quantity) VALUES (?, ?, ?)");
            $stmt->bind_param("iii", $this->user_id, $product_id, $quantity);
            return $stmt->execute();
        }
    }

    public function updateQuantity($product_id, $action) {
        if ($action == 'increase') {
            $stmt = $this->conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id=? AND product_id=?");
        } elseif ($action == 'decrease') {
            // Decrease but not below 1
            $stmt = $this->conn->prepare("UPDATE cart SET quantity = GREATEST(quantity - 1, 1) WHERE user_id=? AND product_id=?");
        } else {
            return false;
        }
        $stmt->bind_param("ii", $this->user_id, $product_id);
        return $stmt->execute();
    }

    public function getCartItems() {
        $stmt = $this->conn->prepare("
            SELECT c.cart_id, p.product_id, p.name, p.price, p.image, c.quantity, p.stock
            FROM cart c
            JOIN products p ON c.product_id = p.product_id
            WHERE c.user_id=?
        ");
        $stmt->bind_param("i", $this->user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function removeFromCart($product_id) {
        $stmt = $this->conn->prepare("DELETE FROM cart WHERE user_id=? AND product_id=?");
        $stmt->bind_param("ii", $this->user_id, $product_id);
        return $stmt->execute();
    }
}

class Wishlist extends Database {
    private $user_id;

    public function __construct($user_id) {
        parent::__construct();
        $this->user_id = $user_id;
    }

    public function addToWishlist($product_id) {
        $stmt = $this->conn->prepare("SELECT * FROM wishlist WHERE user_id=? AND product_id=?");
        $stmt->bind_param("ii", $this->user_id, $product_id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->num_rows > 0) {
            return false;
        }

        $stmt = $this->conn->prepare("INSERT INTO wishlist(user_id, product_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $this->user_id, $product_id);
        return $stmt->execute();
    }
}

// Initialize
$cart = new Cart($_SESSION['user_id']);
$wishlist = new Wishlist($_SESSION['user_id']);
$message = "";

// ✅ Handle user actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'] ?? null;

    if (isset($_POST['remove'])) {
        if ($cart->removeFromCart($product_id)) {
            $message = "🗑️ Item removed from cart!";
        }
    }

    if (isset($_POST['add_to_wishlist'])) {
        if ($wishlist->addToWishlist($product_id)) {
            $cart->removeFromCart($product_id);
            $message = "💖 Item added to wishlist!";
        } else {
            $message = "⚠️ Item already in wishlist!";
        }
    }

    // ✅ Handle quantity updates
    if (isset($_POST['increase'])) {
        $cart->updateQuantity($product_id, 'increase');
        $message = "➕ Increased quantity!";
    }

    if (isset($_POST['decrease'])) {
        $cart->updateQuantity($product_id, 'decrease');
        $message = "➖ Decreased quantity!";
    }
}

// ✅ Fetch latest items
$items = $cart->getCartItems();
$total = 0;
foreach ($items as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Cart | FootHub</title>
<style>
body {
    font-family: 'Arial', sans-serif;
    background: #fff1f0;
    margin: 0;
    padding: 0;
}
header {
    background: #ff3f6c;
    color: white;
    padding: 15px 30px;
    border-radius: 0 0 12px 12px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
header a {
    background: white;
    color: #ff3f6c;
    padding: 8px 12px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: bold;
}
header a:hover {
    background: #e6325a;
    color: white;
}
.container {
    width: 90%;
    margin: 30px auto;
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
.cart-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 15px;
    border-bottom: 1px solid #eee;
}
.cart-item img {
    width: 80px;
    height: 80px;
    border-radius: 8px;
    object-fit: cover;
}
.cart-item-details {
    flex: 1;
    margin-left: 15px;
}
.cart-item-details h3 {
    margin: 0;
    color: #ff3f6c;
}
.cart-item-details p {
    margin: 5px 0;
    color: #555;
}
.quantity-controls {
    display: flex;
    align-items: center;
    gap: 6px;
}
.quantity-controls button {
    background: #ff3f6c;
    color: white;
    border: none;
    padding: 4px 8px;
    font-size: 16px;
    border-radius: 5px;
    cursor: pointer;
}
.quantity-controls button:hover {
    background: #e6325a;
}
.remove-btn {
    background: red;
    color: white;
    border: none;
    padding: 6px 10px;
    border-radius: 5px;
    cursor: pointer;
}
.remove-btn:hover {
    background: #c00000;
}
.wishlist-btn {
    background: #ff3f6c;
    color: white;
    border: none;
    padding: 6px 10px;
    border-radius: 5px;
    cursor: pointer;
}
.wishlist-btn:hover {
    background: #e6325a;
}
.total {
    text-align: right;
    font-weight: bold;
    margin-top: 15px;
    font-size: 18px;
}
.checkout-btn {
    display: inline-block;
    background: #ff3f6c;
    color: white;
    padding: 10px 20px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
    transition: 0.3s;
}
.checkout-btn:hover {
    background: #e6325a;
}
.empty {
    text-align: center;
    font-size: 18px;
    color: #555;
}
.message {
    text-align: center;
    font-weight: bold;
    color: green;
    margin-bottom: 15px;
}
.btn-group {
    display: flex;
    gap: 8px;
}
</style>
</head>
<body>
<header>
    <h1>🛍️ My Cart</h1>
    <a href="dashboard.php">← Back to Dashboard</a>
</header>

<div class="container">
    <?php if ($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if (empty($items)) { ?>
        <p class="empty">Your cart is empty.</p>
    <?php } else { ?>
        <?php foreach ($items as $item) { ?>
            <div class="cart-item">
                <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                <div class="cart-item-details">
                    <h3><?= htmlspecialchars($item['name']) ?></h3>
                    <p>₹<?= number_format($item['price'], 2) ?></p>
                    <div class="quantity-controls">
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                            <button type="submit" name="decrease">−</button>
                        </form>
                        <span><?= $item['quantity'] ?></span>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                            <button type="submit" name="increase">+</button>
                        </form>
                    </div>
                </div>
                <div class="btn-group">
                    <form method="POST">
                        <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                        <button type="submit" name="remove" class="remove-btn">Remove</button>
                    </form>
                    <form method="POST">
                        <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                        <button type="submit" name="add_to_wishlist" class="wishlist-btn">♡ Wishlist</button>
                    </form>
                </div>
            </div>
        <?php } ?>
        <div class="total">Total: ₹<?= number_format($total, 2) ?></div>
        <a href="checkout.php?total=<?= $total ?>" class="checkout-btn">Proceed to Checkout</a>
    <?php } ?>
</div>
</body>
</html>
