<?php
session_start();
require_once 'db.php';

// ------------------- Product Class -------------------
class Product extends Database {
    public function getAllProducts($search = ""){
        if(!empty($search)){
            $stmt = $this->conn->prepare("SELECT * FROM products WHERE name LIKE ? OR category LIKE ?");
            $like = "%{$search}%";
            $stmt->bind_param("ss", $like, $like);
        } else {
            $stmt = $this->conn->prepare("SELECT * FROM products");
        }
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}

// ------------------- Cart Class -------------------
class Cart extends Database {
    private $user_id;
    public function __construct($user_id){
        parent::__construct();
        $this->user_id = $user_id;
    }

    public function addToCart($product_id){
        $stmt = $this->conn->prepare("SELECT * FROM cart WHERE user_id=? AND product_id=?");
        $stmt->bind_param("ii",$this->user_id,$product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows > 0){
            $row = $result->fetch_assoc();
            $qty = $row['quantity'] + 1;
            $update = $this->conn->prepare("UPDATE cart SET quantity=? WHERE cart_id=?");
            $update->bind_param("ii",$qty,$row['cart_id']);
            return $update->execute();
        } else {
            $insert = $this->conn->prepare("INSERT INTO cart(user_id,product_id,quantity) VALUES(?,?,1)");
            $insert->bind_param("ii",$this->user_id,$product_id);
            return $insert->execute();
        }
    }

    public function getCartCount(){
        $stmt = $this->conn->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id=?");
        $stmt->bind_param("i",$this->user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'] ?? 0;
    }
}

// ------------------- Wishlist Class -------------------
class Wishlist extends Database {
    private $user_id;
    public function __construct($user_id){
        parent::__construct();
        $this->user_id = $user_id;
    }

    public function addToWishlist($product_id){
        $stmt = $this->conn->prepare("SELECT * FROM wishlist WHERE user_id=? AND product_id=?");
        $stmt->bind_param("ii",$this->user_id,$product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows > 0){
            return false; // already in wishlist
        } else {
            $insert = $this->conn->prepare("INSERT INTO wishlist(user_id,product_id) VALUES(?,?)");
            $insert->bind_param("ii",$this->user_id,$product_id);
            return $insert->execute();
        }
    }

    public function getWishlistCount(){
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM wishlist WHERE user_id=?");
        $stmt->bind_param("i",$this->user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'] ?? 0;
    }
}

// ------------------- User Class -------------------
class User extends Database {
    public function getUserById($user_id){
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE user_id=?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result ?: null;
    }
}

// ------------------- Handle Actions -------------------
$message = "";
$cartCount = 0;
$wishlistCount = 0;
$search = $_GET['search'] ?? "";

if(isset($_SESSION['user_id'])){
    $cart = new Cart($_SESSION['user_id']);
    $wishlist = new Wishlist($_SESSION['user_id']);

    $cartCount = $cart->getCartCount();
    $wishlistCount = $wishlist->getWishlistCount();

    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        if(isset($_POST['add_cart'])){
            if($cart->addToCart($_POST['product_id'])){
                $message = "Added to cart!";
            } else {
                $message = "Failed to add to cart!";
            }
        }
        if(isset($_POST['add_wishlist'])){
            if($wishlist->addToWishlist($_POST['product_id'])){
                $message = "Added to wishlist!";
            } else {
                $message = "Already in wishlist!";
            }
        }
    }

    $userObj = new User();
    $userData = $userObj->getUserById($_SESSION['user_id']);
}

$productObj = new Product();
$products = $productObj->getAllProducts($search);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>FootHub Dashboard</title>
<style>
body { font-family:'Arial',sans-serif; background:#fff1f0; margin:0; padding:0; }
header { display:flex; justify-content:space-between; align-items:center; background:#ff3f6c; padding:15px 30px; color:#fff; border-radius:0 0 12px 12px; flex-wrap:wrap; }
header h1 { margin:0; font-size:24px; }

/* Bigger Search Bar Styling */
.search-form {
    display: flex;
    align-items: center;
    gap: 10px;
    flex: 1;
    justify-content: center;
}

.search-form input[type="text"] {
    padding: 12px 20px;
    border-radius: 30px;
    border: none;
    width: 350px;
    font-size: 16px;
    outline: none;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    transition: all 0.3s ease;
}

.search-form input[type="text"]:focus {
    width: 400px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.3);
}

.search-form button {
    background: #fff;
    color: #ff3f6c;
    padding: 12px 22px;
    border: none;
    border-radius: 30px;
    cursor: pointer;
    font-weight: bold;
    font-size: 15px;
    transition: 0.3s;
}

.search-form button:hover {
    background: #e6325a;
    color: #fff;
}

/* Navigation buttons */
header .nav-buttons { display:flex; align-items:center; gap:15px; margin-top:5px; }
header .nav-buttons a, header .nav-buttons form button {
    background:#fff; color:#ff3f6c; padding:8px 12px;
    border-radius:6px; text-decoration:none; font-weight:bold;
    cursor:pointer; transition:0.3s; border:none;
}
header .nav-buttons a:hover, header .nav-buttons form button:hover {
    background:#e6325a; color:#fff;
}

.container { width:90%; max-width:1200px; margin:30px auto; }
.message { text-align:center; color:green; font-weight:bold; margin-bottom:20px; }
.products { display:grid; grid-template-columns:repeat(auto-fit,minmax(250px,1fr)); gap:20px; }
.card { background:#fff; border-radius:12px; padding:20px; box-shadow:0 4px 15px rgba(0,0,0,0.1); }
.card img { width:100%; height:200px; object-fit:contain; border-radius:8px; }
.card h3 { color:#ff3f6c; margin:10px 0 5px; }
.card p { margin:5px 0; color:#555; }
.card form { display:flex; justify-content:space-between; margin-top:10px; }
.card form button { background:#ff3f6c; color:#fff; border:none; padding:8px 12px; border-radius:6px; cursor:pointer; font-weight:bold; }
.card form button:hover { background:#e6325a; }
</style>
</head>
<body>
<header>
    <h1>FootHub</h1>

    <form method="GET" class="search-form">
        <input type="text" name="search" placeholder="Search products..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Search</button>
    </form>

    <div class="nav-buttons">
        <?php if(isset($userData)): ?>
            <span>Hello, <?= htmlspecialchars($userData['name']) ?></span>
            <form action="logout.php" method="POST" style="display:inline;">
                <button type="submit" name="logout">Logout</button>
            </form>
            <a href="cart.php">Cart (<?= $cartCount ?>)</a>
            <a href="wishlist.php">Wishlist (<?= $wishlistCount ?>)</a>
        <?php else: ?>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </div>
</header>

<div class="container">
<?php if($message) echo "<div class='message'>$message</div>"; ?>

<?php if(!empty($search)): ?>
    <h2 style="text-align:center; color:#ff3f6c;">Search results for "<?= htmlspecialchars($search) ?>"</h2>
<?php endif; ?>

<div class="products">
<?php if(!empty($products)): ?>
    <?php foreach($products as $product){ ?>
        <div class="card">
            <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
            <h3><?= htmlspecialchars($product['name']) ?></h3>
            <p>Category: <?= htmlspecialchars($product['category']) ?></p>
            <p>Price: ₹<?= number_format($product['price'],2) ?></p>
            <?php if(isset($userData)): ?>
            <form method="POST">
                <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                <button type="submit" name="add_cart">Add to Cart</button>
                <button type="submit" name="add_wishlist">Add to Wishlist</button>
            </form>
            <?php else: ?>
                <a href="login.php" style="color:#ff3f6c; font-weight:bold;">Login to buy</a>
            <?php endif; ?>
        </div>
    <?php } ?>
<?php else: ?>
    <p style="text-align:center; color:#888;">No products found.</p>
<?php endif; ?>
</div>
</div>
</body>
</html>
