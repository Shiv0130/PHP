<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../includes/db_connection.php';

$current_user_id = $_SESSION['user_id'];
$message = "";
$user_data = [];

$allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
$max_file_size = 5 * 1024 * 1024;
$upload_directory = '../uploads/';

try {
    $sql_user = "SELECT user_id, full_name, email, profile_picture FROM users WHERE user_id = ?";
    $stmt_user = $pdo->prepare($sql_user);
    $stmt_user->execute([$current_user_id]);
    $user_data = $stmt_user->fetch();

    if (!$user_data) {
        die("Error: User not found.");
    }
} catch (PDOException $e) {
    die("Error fetching user data: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $new_profile_picture_path = $user_data['profile_picture']; 

    if (!empty($full_name)) {
        try {
            $sql_update_name = "UPDATE users SET full_name = ? WHERE user_id = ?";
            $stmt_update_name = $pdo->prepare($sql_update_name);
            $stmt_update_name->execute([$full_name, $current_user_id]);
            $_SESSION['full_name'] = $full_name; // Update session variable too
            $message .= "Profile name updated successfully. ";
        } catch (PDOException $e) {
            $message .= "Error updating name: " . $e->getMessage() . " ";
            error_log("Error updating profile name: " . $e->getMessage());
        }
    }

    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['profile_image'];
        if ($file['error'] === UPLOAD_ERR_OK) {
            $file_name = basename($file['name']);
            $file_tmp_path = $file['tmp_name'];
            $file_size = $file['size'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if ($file_size > $max_file_size) {
                $message .= "Image file is too large. Maximum size is 5MB. ";
            } elseif (!in_array($file_ext, $allowed_extensions)) {
                $message .= "Invalid image file type. Only JPG, JPEG, PNG, GIF are allowed. ";
            } else {
                $new_file_name = uniqid('profile_', true) . '.' . $file_ext;
                $destination_path = $upload_directory . $new_file_name;

                if (move_uploaded_file($file_tmp_path, $destination_path)) {
                    if (!empty($user_data['profile_picture']) && file_exists('../' . $user_data['profile_picture'])) {
                         unlink('../' . $user_data['profile_picture']);
                    }
                    $new_profile_picture_path = 'uploads/' . $new_file_name;
                    try {
                        $sql_update_pic = "UPDATE users SET profile_picture = ? WHERE user_id = ?";
                        $stmt_update_pic = $pdo->prepare($sql_update_pic);
                        $stmt_update_pic->execute([$new_profile_picture_path, $current_user_id]);
                        $message .= "Profile picture updated successfully. ";
                    } catch (PDOException $e) {
                        $message .= "Error updating profile picture in DB: " . $e->getMessage() . " ";
                        error_log("Error updating profile picture in DB: " . $e->getMessage());
                    }
                } else {
                    $message .= "Error moving uploaded file. ";
                }
            }
        } else {
            $message .= "Image upload error code: " . $file['error'] . ". ";
            error_log("Image upload error code: " . $file['error']);
        }
    }

    try {
        $sql_user_refresh = "SELECT user_id, full_name, email, profile_picture FROM users WHERE user_id = ?";
        $stmt_user_refresh = $pdo->prepare($sql_user_refresh);
        $stmt_user_refresh->execute([$current_user_id]);
        $user_data = $stmt_user_refresh->fetch();
    } catch (PDOException $e) {
        error_log("Error re-fetching user data after update: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile </title>
    <link rel="stylesheet" href="../css/style.css"> <!-- Correct CSS link -->
    <style>
        .edit-profile-section {
            background-color: #ffffffff; padding: 30px; border-radius: 12px; box-shadow: 0 5px 20px rgba(0, 0, 0, 0.5); border: 1px solid #444; margin-top: 30px;
        }
        .edit-profile-section h2 { color: #ffffff; text-align: center; margin-bottom: 25px; }
        .current-profile-pic-display { text-align: center; margin-bottom: 25px; }
    </style>
</head>
<body>
    <header class="dashboard-header">
        <div class="container">
            <h1>Edit Profile</h1>
            <nav>
                <a href="dashboard.php">Dashboard</a> |
                <a href="profile.php?user_id=<?php echo htmlspecialchars($_SESSION['user_id']); ?>">My Profile</a> |
                <a href="logout.php">Logout</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <section class="edit-profile-section">
            <h2>Update Your Information</h2>
            <?php if (!empty($message)): ?>
                <p class="message <?php echo (strpos($message, 'Error') !== false || strpos($message, 'error') !== false || strpos($message, 'Failed') !== false) ? 'error' : 'success'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </p>
            <?php endif; ?>
            <div class="current-profile-pic-display">
                <img src="<?php echo htmlspecialchars($user_data['profile_picture'] ? '../' . $user_data['profile_picture'] : '../images/default_profile.png'); ?>" alt="Current Profile Picture" class="profile-pic-large">
            </div>
            <form action="edit_profile.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="full_name">Full Name:</label>
                    <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user_data['full_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email (cannot be changed):</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="profile_image">New Profile Picture (Optional):</label>
                    <input type="file" id="profile_image" name="profile_image" accept=".jpg,.jpeg,.png,.gif">
                    <div class="image-preview">
                        <img id="profileImagePreview" src="#" alt="Image Preview" style="display: none; max-width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 2px solid #8a2be2;">
                    </div>
                    <small>Max file size: 5MB. Allowed types: JPG, JPEG, PNG, GIF.</small>
                </div>
                <button type="submit" class="btn">Save Changes</button>
            </form>
        </section>
    </div>
    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Dark Social. All rights reserved.</p>
        </div>
    </footer>
    <script src="../js/image_preview.js"></script>
</body>
</html>