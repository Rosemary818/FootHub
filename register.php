<?php
require_once 'user.php';
$user = new User();

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);
    $phone    = trim($_POST['phone']);
    $gender   = $_POST['gender'];

    if (!empty($name) && !empty($email) && !empty($password) && !empty($phone) && !empty($gender)) {

        // Name validation: must start with letter, contain space, no symbols/numbers at start
        if (!preg_match("/^[a-zA-Z][a-zA-Z\s]*$/", $name) || strpos($name, ' ') === false) {
            $message = "<p class='error'>Name must contain first and last name, start with a letter, and not include symbols or numbers at the start.</p>";
        }
        // Phone validation: starts with 6,7,8,9, 10 digits, not all same digits
        elseif (!preg_match("/^[6-9]\d{9}$/", $phone) || preg_match("/^(\d)\1{9}$/", $phone)) {
            $message = "<p class='error'>Phone number must start with 9,8,7,6, be 10 digits, and not all digits same.</p>";
        }
        // Email validation
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "<p class='error'>Invalid email format.</p>";
        }
        // Password length
        elseif (strlen($password) < 6) {
            $message = "<p class='error'>Password must be at least 6 characters long.</p>";
        }
        else {
            $register = $user->register($name, $email, $password, $phone, $gender);
            if ($register) {
                // Redirect to login.php after successful registration
                header("Location: login.php");
                exit();
            } else {
                $message = "<p class='error'>Error: Email already exists or registration failed.</p>";
            }
        }
    } else {
        $message = "<p class='error'>All fields are required.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>FootHub | Register</title>
<style>
    body {
        font-family: 'Arial', sans-serif;
        background: #fff1f0; /* soft peach background */
        margin: 0;
        padding: 0;
    }

    .register-container {
        width: 400px;
        margin: 80px auto;
        background: #ffffff; /* white card */
        border-radius: 12px;
        padding: 35px 30px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }

    h2 {
        text-align: center;
        color: #ff3f6c; /* pink heading */
        margin-bottom: 20px;
        font-weight: 600;
    }

    input, select {
        width: 100%;
        padding: 12px;
        margin: 10px 0;
        border-radius: 6px;
        border: 1px solid #dcdcdc; /* light gray border */
        outline: none;
        font-size: 14px;
    }

    input:focus, select:focus {
        border-color: #ff3f6c; /* pink focus */
        box-shadow: 0 0 4px rgba(255,63,108,0.3);
    }

    button {
        width: 100%;
        background: #ff3f6c;
        color: #fff;
        padding: 12px;
        margin-top: 10px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 16px;
        font-weight: bold;
        transition: background 0.3s ease;
    }

    button:hover {
        background: #e6325a;
    }

    p {
        text-align: center;
        font-size: 13px;
        color: #555555;
    }

    p a {
        color: #ff3f6c;
        font-weight: 600;
        text-decoration: none;
    }

    p a:hover {
        text-decoration: underline;
    }

    .error {
        color: #ff1a1a;
        font-size: 14px;
        text-align: center;
        margin-bottom: 10px;
    }
</style>
</head>
<body>

<div class="register-container">
    <h2>FootHub Registration</h2>
    <?= $message ?>

    <form method="POST" action="">
        <label>Full Name:</label>
        <input type="text" name="name" placeholder="Enter your name" required>

        <label>Email:</label>
        <input type="email" name="email" placeholder="Enter your email" required>

        <label>Password:</label>
        <input type="password" name="password" placeholder="Create password" required>

        <label>Phone:</label>
        <input type="text" name="phone" placeholder="Enter phone number" required>

        <label>Gender:</label>
        <select name="gender" required>
            <option value="">Select Gender</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
        </select>

        <button type="submit">Register</button>
    </form>

    <p>Already have an account? <a href="login.php">Login here</a></p>
</div>

</body>
</html>
