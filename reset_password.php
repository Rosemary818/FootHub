<?php
// reset_password.php
require_once 'user.php';
$user = new User();

// Initialize message
$message = "";

// Get token from URL
if (isset($_GET['token'])) {
    $token = $_GET['token'];
} elseif (isset($_POST['token'])) {
    $token = $_POST['token'];
} else {
    die("No token provided.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = trim($_POST['password']);
    $confirm  = trim($_POST['confirm_password']);

    if (empty($password) || empty($confirm)) {
        $message = "<p class='error'>Both fields are required.</p>";
    } elseif ($password !== $confirm) {
        $message = "<p class='error'>Passwords do not match.</p>";
    } else {
        // Call User class method to reset password
        $result = $user->resetPassword($token, $password);
        if ($result['success']) {
            $message = "<p class='success'>{$result['message']}</p>";
        } else {
            $message = "<p class='error'>{$result['message']}</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>FootHub | Reset Password</title>
<style>
    body {
        font-family: 'Arial', sans-serif;
        background: #fff1f0;
        margin: 0;
        padding: 0;
    }

    .reset-container {
        width: 400px;
        margin: 100px auto;
        background: #ffffff;
        border-radius: 12px;
        padding: 35px 30px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }

    h2 {
        text-align: center;
        color: #ff3f6c;
        margin-bottom: 20px;
        font-weight: 600;
    }

    input {
        width: 100%;
        padding: 12px;
        margin: 10px 0;
        border-radius: 6px;
        border: 1px solid #dcdcdc;
        outline: none;
        font-size: 14px;
    }

    input:focus {
        border-color: #ff3f6c;
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

    .error {
        color: #ff1a1a;
        font-size: 14px;
        text-align: center;
        margin-bottom: 10px;
    }

    .success {
        color: #28a745;
        font-size: 14px;
        text-align: center;
        margin-bottom: 10px;
    }
</style>
</head>
<body>

<div class="reset-container">
    <h2>Reset Password</h2>
    <?= $message ?>

    <form method="POST" action="">
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
        <label>New Password:</label>
        <input type="password" name="password" placeholder="Enter new password" required>

        <label>Confirm Password:</label>
        <input type="password" name="confirm_password" placeholder="Confirm new password" required>

        <button type="submit">Reset Password</button>
    </form>
</div>

</body>
</html>
