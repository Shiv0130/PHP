<?php

session_start(); 

require_once '../includes/db_connection.php'; 

$message = "";
$show_form = false;
$token = '';
$user_id = null; // To store the user ID after token verification

//  Check if user is already logged in 
if (isset($_SESSION['user_id'])) {
    // If the user is already logged in, they don't need to reset their password this way.
    header("Location: dashboard.php");
    exit();
}

//  Handle token verification from GET request 
if (isset($_GET['token'])) {
    $token = trim($_GET['token']);

    try {
        // Find the user by the provided token and check if the token has expired
        $sql = "SELECT user_id, full_name FROM users WHERE password_reset_token = ? AND password_reset_expires > NOW()";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$token]);
        $user = $stmt->fetch(); // Fetches the user row if token is valid and not expired

        if ($user) {
            // Token is valid and not expired, so we show the password reset form
            $show_form = true;
            $user_id = $user['user_id']; // Store the user's ID for the password update
            $token = htmlspecialchars($token); // Store token for the form (hidden field)
        } else {
            // Token is invalid or expired
            $message = "The password reset link is invalid or has expired. Please request a new one.";
        }
    } catch (PDOException $e) {
        // Handle any database errors during token verification
        $message = "An error occurred during verification. Please try again later.";
        error_log("Password reset (token verification) error: " . $e->getMessage());
    }
} else {
    // No token was provided in the URL
    $message = "No token provided. Please request a password reset.";
}

// Handle new password submission (POST request) 
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['new_password'])) {
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];
    $received_token = trim($_POST['token']); // Get the token from the hidden form field

    // It's crucial to re-verify the token's validity before updating the password.
    try {
        // Re-query to ensure the token is still valid and not expired
        $sql_verify = "SELECT user_id FROM users WHERE password_reset_token = ? AND password_reset_expires > NOW()";
        $stmt_verify = $pdo->prepare($sql_verify);
        $stmt_verify->execute([$received_token]);
        $user_check = $stmt_verify->fetch();

        if ($user_check) {
            // Token is still valid, proceed with password update
            if (empty($new_password) || empty($confirm_new_password)) {
                $message = "New password and confirmation are required.";
                $show_form = true; // Keep form visible to allow user to re-enter
            } elseif ($new_password !== $confirm_new_password) {
                $message = "Passwords do not match. Please ensure both fields are identical.";
                $show_form = true; // Keep form visible
            } else {
                // Hash the new password securely
                $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);

                // Update the user's password in the database
                // Also, clear the reset token and expiry date to prevent reuse
                $sql_update_pass = "UPDATE users
                                    SET password_hash = ?, password_reset_token = NULL, password_reset_expires = NULL
                                    WHERE user_id = ?";
                $stmt_update_pass = $pdo->prepare($sql_update_pass);
                $stmt_update_pass->execute([$new_password_hash, $user_check['user_id']]);

                // Password reset successful
                $message = "Your password has been successfully reset. You can now log in.";
                $show_form = false; // Hide form, display success message
            }
        } else {
            // Token expired or invalid after the initial check
            $message = "The password reset link is invalid or has expired. Please request a new one.";
            $show_form = false; // Hide form
        }
    } catch (PDOException $e) {
        // Handle any database errors during password update
        $message = "An error occurred while updating your password. Please try again.";
        error_log("Password reset (update) error: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .reset-password-form { max-width: 450px; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Reusing login form styles for consistency -->
        <div class="login-form reset-password-form">
            <h2>Reset Your Password</h2>

            <?php if (!empty($message)): ?>
                <!-- Style message based on success or error -->
                <p class="message <?php echo (strpos($message, 'success') !== false || strpos($message, 'successfully') !== false) ? 'success' : 'error'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </p>
            <?php endif; ?>

            <?php if ($show_form): ?>
                <!-- The form is only displayed if the token is valid -->
                <form action="reset_password.php" method="post">
                    <!-- Hidden field to pass the token to the server for re-verification -->
                    <input type="hidden" name="token" value="<?php echo $token; ?>">
                    <div class="form-group">
                        <label for="new_password">New Password:</label>
                        <input type="password" id="new_password" name="new_password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_new_password">Confirm New Password:</label>
                        <input type="password" id="confirm_new_password" name="confirm_new_password" required>
                    </div>
                    <button type="submit" class="btn">Reset Password</button>
                </form>
            <?php endif; ?>

            <p class="text-center">Remember your password? <a href="login.php">Sign In</a></p>
        </div>
    </div>
</body>
</html>