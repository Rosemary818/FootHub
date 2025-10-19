<?php
// Set timezone
date_default_timezone_set('Asia/Kolkata');

// Include Database
require_once __DIR__ . '/db.php';

// Include PHPMailer
require_once __DIR__ . '/PHPMailer/src/Exception.php';
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class User extends Database {

    // Register new user
    public function register($name, $email, $password, $phone, $gender) {
        $hashed = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->conn->prepare("INSERT INTO users (name, email, password, phone, gender) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $hashed, $phone, $gender);
        return $stmt->execute();
    }

    // Login user
    public function login($email, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if (password_verify($password, $row['password'])) {
                session_start();
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['user_name'] = $row['name'];
                return true;
            }
        }
        return false;
    }

    // Send password reset email
    public function sendPasswordReset($email) {
        // Check if email exists
        $stmt = $this->conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if (!$user) {
            return ['success' => false, 'message' => 'Email not found.'];
        }

        // Generate token & expiry
        $token = bin2hex(random_bytes(50));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Update DB
        $update = $this->conn->prepare("UPDATE users SET reset_token = ?, reset_expiry = ? WHERE email = ?");
        $update->bind_param("sss", $token, $expiry, $email);
        $update->execute();

        // Reset link
        $resetLink = "http://localhost/foothub/reset_password.php?token=$token";

        // PHPMailer
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'rossemariebiju';      // Your Gmail
            $mail->Password   = 'zvcq guqu obwy wkfu';         // App Password
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('no-reply@foothub.com', 'FootHub');
            $mail->addAddress($email);

            $mail->Subject = 'FootHub Password Reset';
            $mail->Body    = "Hello,\n\nClick the link below to reset your password:\n$resetLink\n\nThis link will expire in 1 hour.";

            $mail->send();
            return ['success' => true, 'message' => 'Password reset email sent successfully.'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Email failed to send: ' . $mail->ErrorInfo];
        }
    }

    // Verify token and reset password
    public function resetPassword($token, $newPassword) {
        // Check token validity
        $stmt = $this->conn->prepare("SELECT user_id, reset_expiry FROM users WHERE reset_token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if (!$user) {
            return ['success' => false, 'message' => 'Invalid token.'];
        }

        if (strtotime($user['reset_expiry']) < time()) {
            return ['success' => false, 'message' => 'Token has expired.'];
        }

        // Update password
        $hashed = password_hash($newPassword, PASSWORD_BCRYPT);
        $update = $this->conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expiry = NULL WHERE user_id = ?");
        $update->bind_param("si", $hashed, $user['user_id']);
        $update->execute();

        return ['success' => true, 'message' => 'Password reset successfully.'];
    }
    public function getUserById($user_id) {
    $stmt = $this->conn->prepare("SELECT user_id, name, email, phone, gender FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}
}
?>
