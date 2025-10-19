<?php
require_once 'user.php';
$user = new User();

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);

    if (!empty($email)) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $result = $user->sendPasswordReset($email); // PHPMailer method in user.php
            if ($result) {
                $message = "<p class='success'>A password reset link has been sent to your email.</p>";
            } else {
                $message = "<p class='error'>Email not found or email failed to send.</p>";
            }
        } else {
            $message = "<p class='error'>Invalid email format.</p>";
        }
    } else {
        $message = "<p class='error'>Email field is required.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>FootHub | Forgot Password</title>
<style>
    body {
        font-family: 'Arial', sans-serif;
        background: #fff1f0; /* soft peach background */
        margin: 0;
        padding: 0;
    }

    .forgot-container {
        width: 400px;
        margin: 100px auto;
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
        margin: 10px 0;
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

    .success {
        color: #28a745;
        font-weight: bold;
        text-align: center;
        margin-bottom: 10px;
    }
</style>
</head>
<body>

<div class="forgot-container">
    <h2>Forgot Password</h2>
    <?= $message ?>

    <form method="POST" action="">
        <label>Email:</label>
        <input type="email" name="email" placeholder="Enter your registered email" required>
        <button type="submit">Send Reset Link</button>
    </form>

    <p>Remembered your password? <a href="login.php">Login here</a></p>
</div>

</body>
</html>
