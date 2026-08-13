<?php

require_once '../includes/db_connection.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($full_name) || empty($email) || empty($password) || empty($confirm_password)) {
        $message = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format. Please enter a valid email address.";
    } elseif ($password !== $confirm_password) {
        $message = "Passwords do not match. Please ensure both password fields are identical.";
    } else {
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            $message = "This email address is already registered. Please use a different email or log in.";
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            try {
                $sql = "INSERT INTO users (full_name, email, password_hash) VALUES (?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$full_name, $email, $password_hash]);
                $message = "Registration successful! You can now log in.";
            } catch (PDOException $e) {
                $message = "An error occurred during registration. Please try again later.";
                error_log("Registration failed: " . $e->getMessage());
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Dark Social</title>
    <link rel="stylesheet" href="../css/style.css"> 
</head>
<body>
    <div class="container">
        <div class="register-form">
            <h2>Create Your Account</h2>
            <?php if (!empty($message)): ?>
                <p class="message <?php echo (strpos($message, 'successful') !== false) ? 'success' : 'error'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </p>
            <?php endif; ?>
            <form action="register.php" method="post">
                <div class="form-group">
                    <label for="full_name">Full Name:</label>
                    <input type="text" id="full_name" name="full_name" required value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
                </div>
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
                <div class="form-group password-input-group">
                    <label for="confirm_password">Confirm Password:</label>
                    <div class="password-input-wrapper">
                        <input type="password" id="confirm_password" name="confirm_password" required>
                        <span class="password-toggle-icon">👁️</span>
                    </div>
                </div>
                <div id="passwordValidationMessage" class="message error" style="display: none;"></div>

                <button type="submit" class="btn">Register</button>
            </form>
            <p class="text-center">Already have an account? <a href="login.php">Sign In</a></p>
        </div>
    </div>
    <script src="../js/password_toggle.js"></script>
</body>
</html>