<?php
session_start();
require_once 'db.php';

class Wishlist extends Database {
    private $user_id;

    public function __construct($user_id){
        parent::__construct();
        $this->user_id = $user_id;
    }

    public function getItems(){
        $stmt = $this->conn->prepare("
            SELECT w.wishlist_id, p.product_id, p.name, p.category, p.price, p.image
            FROM wishlist w
            JOIN products p ON w.product_id = p.product_id
            WHERE w.user_id = ?
        ");
        $stmt->bind_param("i", $this->user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function removeItem($wishlist_id){
        $stmt = $this->conn->prepare("DELETE FROM wishlist WHERE wishlist_id = ?");
        $stmt->bind_param("i", $wishlist_id);
        return $stmt->execute();
    }

    // ✅ Move item to cart (then remove from wishlist)
    public function moveToCart($product_id){
        // Check if product already exists in cart
        $check = $this->conn->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
        $check->bind_param("ii", $this->user_id, $product_id);
        $check->execute();
        $exists = $check->get_result()->num_rows > 0;

        if (!$exists) {
            $stmt = $this->conn->prepare("INSERT INTO cart (user_id, product_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $this->user_id, $product_id);
            $stmt->execute();
        }

        // Remove from wishlist
        $delete = $this->conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
        $delete->bind_param("ii", $this->user_id, $product_id);
        $delete->execute();
    }
}

if(!isset($_SESSION['user_id'])){
    die("Please login to view wishlist.");
}

$wishlist = new Wishlist($_SESSION['user_id']);
$message = "";

// Handle remove
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove_item'])){
    $wishlist->removeItem($_POST['wishlist_id']);
    $message = "Item removed from wishlist!";
}

// ✅ Handle move to cart — redirect to cart.php after success
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['move_to_cart'])){
    $wishlist->moveToCart($_POST['product_id']);
    header("Location: cart.php"); // redirect to cart page
    exit();
}

$items = $wishlist->getItems();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>FootHub Wishlist</title>
<style>
body { font-family:'Arial',sans-serif; background:#fff1f0; margin:0; padding:0; }
.container { width:90%; max-width:1200px; margin:50px auto; }
h1 { text-align:center; color:#ff3f6c; margin-bottom:20px; }
.message { text-align:center; font-weight:bold; color:green; margin-bottom:20px; }

.back-btn {
    display:inline-block;
    background:#ff3f6c;
    color:white;
    padding:10px 18px;
    border-radius:6px;
    text-decoration:none;
    font-weight:bold;
}
.back-btn:hover {
    background:#e6325a;
}

.products { display:grid; grid-template-columns:repeat(auto-fit,minmax(250px,1fr)); gap:20px; }
.card { background:#fff; border-radius:12px; padding:20px; box-shadow:0 4px 15px rgba(0,0,0,0.1); }
.card img { width:100%; height:200px; object-fit:contain; border-radius:8px; }
.card h3 { color:#ff3f6c; margin:10px 0 5px; }
.card p { margin:5px 0; color:#555; }
.card form { display:flex; justify-content:center; gap:8px; margin-top:10px; }
.card form button {
    background:#ff3f6c; color:#fff; border:none;
    padding:8px 12px; border-radius:6px; cursor:pointer; font-weight:bold;
}
.card form button:hover { background:#e6325a; }
.card .cart-btn { background:#28a745; }
.card .cart-btn:hover { background:#218838; }
</style>
</head>
<body>
<div class="container">
<h1>My Wishlist</h1>

<div style="text-align:center; margin-bottom:20px;">
    <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>
</div>

<?php if($message) echo "<div class='message'>$message</div>"; ?>

<div class="products">
<?php if(!empty($items)) {
    foreach($items as $item){ ?>
    <div class="card">
        <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
        <h3><?= htmlspecialchars($item['name']) ?></h3>
        <p>Category: <?= htmlspecialchars($item['category']) ?></p>
        <p>Price: ₹<?= number_format($item['price'],2) ?></p>
        <form method="POST">
            <input type="hidden" name="wishlist_id" value="<?= $item['wishlist_id'] ?>">
            <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
            <button type="submit" name="remove_item">❌ Remove</button>
            <button type="submit" name="move_to_cart" class="cart-btn">🛒 Move to Cart</button>
        </form>
    </div>
<?php } } else { ?>
<p>Your wishlist is empty.</p>
<?php } ?>
</div>
</div>
</body>
</html>
