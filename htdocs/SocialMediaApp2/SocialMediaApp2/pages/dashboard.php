<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../includes/db_connection.php';

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['full_name'];
$user_email = $_SESSION['user_email'];

$all_posts = [];
try {
    $sql_posts = "SELECT p.*, u.full_name AS author_name, u.profile_picture AS author_pic
                  FROM posts p
                  JOIN users u ON p.user_id = u.user_id
                  ORDER BY p.created_at DESC
                  LIMIT 50";
    $stmt_posts = $pdo->prepare($sql_posts);
    $stmt_posts->execute();
    $all_posts = $stmt_posts->fetchAll();
} catch (PDOException $e) {
    die("Error fetching posts: " . $e->getMessage()); 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Dark Social</title>
    <link rel="stylesheet" href="../css/style.css"> 
</head>
<body>
    <header class="dashboard-header">
        <div class="container">
            <h1>Welcome, <?php echo htmlspecialchars($user_name); ?>!</h1>
            <nav>
                <a href="create_post.php">Create Post</a> |
                <a href="profile.php?user_id=<?php echo $user_id; ?>">My Profile</a> |
                <a href="search_users.php">Search Users</a> |
                <a href="messages.php">Messages</a> |
                <a href="logout.php">Logout</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <section class="dashboard-content">
            <h2>Latest Posts</h2>
            <?php if (empty($all_posts)): ?>
                <p>No posts found yet. Be the first to post!</p>
            <?php else: ?>
                <?php foreach ($all_posts as $post): ?>
                    <div class="post-item">
                        <div class="post-author-info">
                        <img src="<?php echo htmlspecialchars($post['author_pic'] ? '../' . $post['author_pic'] : '../images/default_profile.png'); ?>" alt="Author Pic" class="profile-pic-small">
                            <a href="profile.php?user_id=<?php echo $post['user_id']; ?>">
                                <strong><?php echo htmlspecialchars($post['author_name']); ?></strong>
                            </a>
                            <span class="post-timestamp"><?php echo date('M j, Y H:i', strtotime($post['created_at'])); ?></span>
                        </div>
                        <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                        <?php if (!empty($post['image_path'])): ?><img src="<?php echo '../' . htmlspecialchars($post['image_path']); ?>" alt="Post Image" class="post-image"><?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
        <section class="user-info-box">
            <h3>Your Information</h3>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($user_name); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user_email); ?></p>
            <p><strong>User ID:</strong> <?php echo $user_id; ?></p>
            <p><a href="edit_profile.php">Edit Profile</a></p>
        </section>
    </div>
    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Dark Social. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>



