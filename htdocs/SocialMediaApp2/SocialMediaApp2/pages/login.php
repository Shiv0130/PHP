<?php

session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

require_once '../includes/db_connection.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $message = "Email and password are required.";
    } else {
        $stmt = $pdo->prepare("SELECT user_id, email, password_hash, full_name FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['full_name'] = $user['full_name'];
            header("Location: dashboard.php");
            exit();
        } else {
            $message = "Invalid email or password. Please check your credentials.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Dark Social</title>
    <link rel="stylesheet" href="../css/style.css"> 
</head>
<body>
    <div class="container">
        <div class="login-form">
            <h2>Welcome Back</h2>
            <?php if (!empty($message)): ?>
                <p class="message error"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>
            <form action="login.php" method="post">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
                <div class="form-group password-input-group">
                    <label for="password">Password:</label>
                    <div class="password-input-wrapper">
                        <input type="password" id="password" name="password" required>
                        <span class="password-toggle-icon">👁️</span>
                    </div>
                </div>
                <button type="submit" class="btn">Login</button>
            </form>
            <p class="text-center">Don't have an account? <a href="register.php">Sign Up</a></p>
            <p class="text-center"><a href="forgot_password.php">Forgot Password?</a></p>
        </div>
    </div>
    <script src="../js/password_toggle.js"></script> 
</body>
</html>