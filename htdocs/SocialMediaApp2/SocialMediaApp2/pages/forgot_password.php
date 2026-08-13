<?php
session_start();

require_once '../includes/db_connection.php';

$message = "";
$email = "";

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

    if (empty($email)) {
        $message = "Please enter your email address.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
    } else {
        try {
            $sql = "SELECT user_id FROM users WHERE email = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user) {
                header("Location: set_new_password.php?user_id=" . urlencode($user['user_id']));
                exit();
            } else {
                $message = "If an account with this email exists, you will be directed to reset your password. (Email not found in this case.)";
            }
        } catch (PDOException $e) {
            $message = "An error occurred. Please try again later.";
            error_log("Forgot password (email check) error: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Dark Social</title>
    <link rel="stylesheet" href="../css/style.css"> 
    <style> .forgot-password-form { max-width: 450px; } </style>
</head>
<body>
    <div class="container">
        <div class="login-form forgot-password-form">
            <h2>Forgot Password?</h2>
            <p>Enter your email address to proceed with password reset.</p>
            <?php if (!empty($message)): ?>
                <p class="message <?php echo (strpos($message, 'error') !== false || strpos($message, 'An error occurred') !== false || strpos($message, 'not found') !== false) ? 'error' : 'success'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </p>
            <?php endif; ?>
            <form action="forgot_password.php" method="post">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($email); ?>">
                </div>
                <button type="submit" class="btn">Proceed to Reset</button>
            </form>
            <p class="text-center">Remember your password? <a href="login.php">Sign In</a></p>
        </div>
    </div>
</body>
</html>