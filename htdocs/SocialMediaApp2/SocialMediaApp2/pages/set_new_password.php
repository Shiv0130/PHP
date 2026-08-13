<?php

session_start();

require_once '../includes/db_connection.php';

$message = "";
$user_id = null;

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

if (isset($_GET['user_id']) && is_numeric($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']);
    try {
        $sql_check = "SELECT user_id FROM users WHERE user_id = ?";
        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->execute([$user_id]);
        if (!$stmt_check->fetch()) {
            $message = "Invalid user ID provided for password reset.";
            $user_id = null;
        }
    } catch (PDOException $e) {
        $message = "An error occurred during verification. Please try again later.";
        error_log("Password reset (user verification) error: " . $e->getMessage());
        $user_id = null;
    }
} else {
    $message = "Invalid request. Please start the password reset process from the 'Forgot Password' page.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && $user_id !== null) {
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];

    if (empty($new_password) || empty($confirm_new_password)) {
        $message = "New password and confirmation are required.";
    } elseif ($new_password !== $confirm_new_password) {
        $message = "Passwords do not match. Please ensure both fields are identical.";
    } else {
        try {
            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $sql_update_pass = "UPDATE users SET password_hash = ? WHERE user_id = ?";
            $stmt_update_pass = $pdo->prepare($sql_update_pass);
            $stmt_update_pass->execute([$new_password_hash, $user_id]);
            $message = "Your password has been successfully reset. You can now log in.";
            header("Location: login.php?reset_success=1");
            exit();
        } catch (PDOException $e) {
            $message = "An error occurred while updating your password. Please try again.";
            error_log("Password reset (update) error: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set New Password - Dark Social</title>
    <link rel="stylesheet" href="../css/style.css"> <!-- Correct CSS link -->
    <style> .set-password-form { max-width: 450px; } </style>
</head>
<body>
    <div class="container">
        <div class="login-form set-password-form">
            <h2>Set New Password</h2>
            <?php if (!empty($message)): ?>
                <p class="message <?php echo (strpos($message, 'success') !== false || strpos($message, 'successfully') !== false) ? 'success' : 'error'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </p>
            <?php endif; ?>
            <?php if ($user_id !== null && empty($message)): ?>
                <form action="set_new_password.php?user_id=<?php echo urlencode($user_id); ?>" method="post">
                    <div class="form-group password-input-group">
                        <label for="new_password">New Password:</label>
                        <div class="password-input-wrapper">
                            <input type="password" id="new_password" name="new_password" required>
                            <span class="password-toggle-icon">👁️</span>
                        </div>
                    </div>
                    <div class="form-group password-input-group">
                        <label for="confirm_new_password">Confirm New Password:</label>
                        <div class="password-input-wrapper">
                            <input type="password" id="confirm_new_password" name="confirm_new_password" required>
                            <span class="password-toggle-icon">👁️</span>
                        </div>
                    </div>
                    <button type="submit" class="btn">Reset Password</button>
                </form>
            <?php elseif ($user_id === null && empty($message)): ?>
                <p class="message error">Could not retrieve user information. Please start over.</p>
            <?php endif; ?>
            <p class="text-center">Remember your password? <a href="login.php">Sign In</a></p>
        </div>
    </div>
    <script src="../js/password_toggle.js"></script>
</body>
</html>