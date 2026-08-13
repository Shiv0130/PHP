<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../includes/db_connection.php';

$message = "";
$post_content = "";
$image_path = null;

$allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
$max_file_size = 5 * 1024 * 1024;
$upload_directory = '../uploads/';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $post_content = trim($_POST['content']);
    $user_id = $_SESSION['user_id'];

    if (empty($post_content)) {
        $message = "Post content cannot be empty.";
    } else {
        if (isset($_FILES['post_image']) && $_FILES['post_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $file = $_FILES['post_image'];
            if ($file['error'] === UPLOAD_ERR_OK) {
                $file_name = basename($file['name']);
                $file_tmp_path = $file['tmp_name'];
                $file_size = $file['size'];
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                if ($file_size > $max_file_size) {
                    $message = "Image file is too large. Maximum size is 5MB.";
                } elseif (!in_array($file_ext, $allowed_extensions)) {
                    $message = "Invalid image file type. Only JPG, JPEG, PNG, GIF are allowed.";
                } else {
                    $new_file_name = uniqid('post_', true) . '.' . $file_ext;
                    if (move_uploaded_file($file_tmp_path, $upload_directory . $new_file_name)) {
                        $image_path = 'uploads/' . $new_file_name;
                    } else {
                        $message = "Error uploading image.";
                        $image_path = null;
                        error_log("Error moving uploaded file for post.");
                    }
                }
            } else {
                $message = "Error during image upload.";
                error_log("Image upload error code: " . $file['error']);
            }
        }

        if (empty($message) || $image_path !== null) { // Proceed if no critical errors or if image was handled
            try {
                $sql = "INSERT INTO posts (user_id, content, image_path) VALUES (?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$user_id, $post_content, $image_path]);
                $message = "Your post has been created successfully!";
                $post_content = "";
                $image_path = null;
            } catch (PDOException $e) {
                $message = "Error creating post. Please try again later.";
                error_log("Error creating post: " . $e->getMessage());
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
    <title>Create New Post - Dark Social</title>
    <link rel="stylesheet" href="../css/style.css"> 
</head>
<body>
    <header class="dashboard-header">
        <div class="container">
            <h1>Create Post</h1>
            <nav>
                <a href="dashboard.php">Dashboard</a> |
                <a href="profile.php?user_id=<?php echo htmlspecialchars($_SESSION['user_id']); ?>">My Profile</a> |
                <a href="logout.php">Logout</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <section class="create-post-section">
            <h2>Share your thoughts!</h2>
            <?php if (!empty($message)): ?>
                <p class="message <?php echo (strpos($message, 'successfully') !== false) ? 'success' : 'error'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </p>
            <?php endif; ?>
            <form action="create_post.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="content">What's on your mind?</label>
                    <textarea id="content" name="content" rows="6" required><?php echo htmlspecialchars($post_content); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="post_image">Upload Image (Optional):</label>
                    <input type="file" id="post_image" name="post_image" accept=".jpg,.jpeg,.png,.gif">
                    <div class="image-preview">
                        <img id="postImagePreview" src="#" alt="Image Preview" style="display: none; max-width: 100%; height: auto; border-radius: 8px; margin-top: 10px; border: 1px solid #555;">
                    </div>
                    <small>Max file size: 5MB. Allowed types: JPG, JPEG, PNG, GIF.</small>
                </div>
                <button type="submit" class="btn">Post</button>
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