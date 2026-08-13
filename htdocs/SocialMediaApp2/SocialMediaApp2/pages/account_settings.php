<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../includes/db_connection.php';

$current_user_id = $_SESSION['user_id'];
$message = "";

//  Fetch user data for display 
try {
    $sql_user = "SELECT user_id, full_name, email FROM users WHERE user_id = ?";
    $stmt_user = $pdo->prepare($sql_user);
    $stmt_user->execute([$current_user_id]);
    $user_data = $stmt_user->fetch();

    if (!$user_data) {
        die("Error: User not found.");
    }
} catch (PDOException $e) {
    die("Error fetching user data: " . $e->getMessage());
}

// Handle Password Change Submission 
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];

    // 1. Verify current password
    $sql_current_pass = "SELECT password_hash FROM users WHERE user_id = ?";
    $stmt_current_pass = $pdo->prepare($sql_current_pass);
    $stmt_current_pass->execute([$current_user_id]);
    $user_pass_data = $stmt_current_pass->fetch();

    if ($user_pass_data && password_verify($current_password, $user_pass_data['password_hash'])) {
        // Current password is correct
        // 2. Validate new password
        if (empty($new_password) || empty($confirm_new_password)) {
            $message = "New password and confirmation are required.";
        } elseif ($new_password !== $confirm_new_password) {
            $message = "Passwords do not match.";
        } else {
            // 3. Hash and update password
            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $sql_update_pass = "UPDATE users SET password_hash = ? WHERE user_id = ?";
            $stmt_update_pass = $pdo->prepare($sql_update_pass);
            $stmt_update_pass->execute([$new_password_hash, $current_user_id]);
            $message = "Password changed successfully!";
            // Clear sensitive fields after successful change
            $_POST = array(); // Clear POST data to prevent re-submission issues
        }
    } else {
        $message = "Incorrect current password. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings - Dark Social</title>
    <link rel="stylesheet" href="../css/style.css"> 
    <style>
        .settings-section {
            background-color: #2c2c2c; padding: 30px; border-radius: 12px; box-shadow: 0 5px 20px rgba(0, 0, 0, 0.5); border: 1px solid #444; margin-top: 30px; max-width: 500px; /* Wider for settings */
        }
        .settings-section h2 { color: #ffffff; text-align: center; margin-bottom: 25px; }
    </style>
</head>
<body>
    <header class="dashboard-header">
        <div class="container">
            <h1>Account Settings</h1>
            <nav>
                <a href="dashboard.php">Dashboard</a> |
                <a href="profile.php?user_id=<?php echo htmlspecialchars($_SESSION['user_id']); ?>">My Profile</a> |
                <a href="logout.php">Logout</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <section class="settings-section">
            <h2>Update Password</h2>

            <?php if (!empty($message)): ?>
                <p class="message <?php echo (strpos($message, 'success') !== false) ? 'success' : 'error'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </p>
            <?php endif; ?>

            <form action="account_settings.php" method="post">
                <input type="hidden" name="change_password" value="1"> <!-- Identifier for password change -->
                <div class="form-group">
                    <label for="current_password">Current Password:</label>
                    <input type="password" id="current_password" name="current_password" required>
                    <!-- Add password toggle icon if desired -->
                </div>
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
                <button type="submit" class="btn">Change Password</button>
            </form>
        </section>
    </div>
    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Dark Social. All rights reserved.</p>
        </div>
    </footer>
    <script src="../js/password_toggle.js"></script>
</body>
</html>