<?php

session_start(); // Start session to know if the current user is viewing their own profile

require_once '../includes/db_connection.php'; 

$user_id_to_display = null;
$profile_user = null;
$user_posts = [];
$is_owner = false; // Flag to check if the current logged-in user is viewing their own profile

//  Get user ID from URL parameter 
if (isset($_GET['user_id']) && is_numeric($_GET['user_id'])) {
    $user_id_to_display = intval($_GET['user_id']);
}

//  Check if logged-in user is viewing their own profile 
if (isset($_SESSION['user_id']) && $_SESSION['user_id'] === $user_id_to_display) {
    $is_owner = true;
}

// Fetch profile user data 
try {
    $sql_user = "SELECT user_id, full_name, email, profile_picture FROM users WHERE user_id = ?";
    $stmt_user = $pdo->prepare($sql_user);
    $stmt_user->execute([$user_id_to_display]);
    $profile_user = $stmt_user->fetch();

    // If user not found, display an error or redirect
    if (!$profile_user) {
        die("User not found."); 
    }

    //  Fetch user's posts 
    // Query posts for the user_id_to_display
    $sql_posts = "SELECT p.*, u.full_name AS author_name, u.profile_picture AS author_pic
                  FROM posts p
                  JOIN users u ON p.user_id = u.user_id
                  WHERE p.user_id = ?
                  ORDER BY p.created_at DESC";
    $stmt_posts = $pdo->prepare($sql_posts);
    $stmt_posts->execute([$user_id_to_display]);
    $user_posts = $stmt_posts->fetchAll();

} catch (PDOException $e) {
    die("Error fetching profile data: " . $e->getMessage()); // 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - <?php echo htmlspecialchars($profile_user['full_name']); ?> - Dark Social</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header class="dashboard-header">
        <div class="container">
            <h1>Profile: <?php echo htmlspecialchars($profile_user['full_name']); ?></h1>
            <nav>
                <a href="dashboard.php">Dashboard</a> |
                <?php if ($is_owner): ?>
                    <a href="edit_profile.php">Edit Profile</a> |
                <?php endif; ?>
                <a href="logout.php">Logout</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <section class="profile-section">
            <div class="profile-header">
            <img src="<?php echo htmlspecialchars($profile_user['profile_picture'] ? '../' . $profile_user['profile_picture'] : '../images/default_profile.png'); ?>" alt="Profile Picture" class="profile-pic-large">
                <h2><?php echo htmlspecialchars($profile_user['full_name']); ?></h2>
                <p>Email: <?php echo htmlspecialchars($profile_user['email']); ?></p>
                <?php if (!$is_owner && isset($_SESSION['user_id'])): // Only show message button if not owner and logged in ?>
                    <a href="messages.php?user_id=<?php echo $user_id_to_display; ?>" class="btn">Message</a>
                <?php endif; ?>
            </div>

            <div class="user-posts-section">
                <h3>Posts by <?php echo htmlspecialchars($profile_user['full_name']); ?></h3>
                <?php if (empty($user_posts)): ?>
                    <p>This user has not made any posts yet.</p>
                <?php else: ?>
                    <?php foreach ($user_posts as $post): ?>
                        <div class="post-item">
                            <div class="post-author-info">
                            <img src="<?php echo htmlspecialchars($profile_user['profile_picture'] ? '../' . $profile_user['profile_picture'] : '../images/default_profile.png'); ?>" alt="Profile Picture" class="profile-pic-small">
                                <a href="profile.php?user_id=<?php echo $post['user_id']; ?>">
                                    <strong><?php echo htmlspecialchars($post['author_name']); ?></strong>
                                </a>
                                <span class="post-timestamp"><?php echo date('M j, Y H:i', strtotime($post['created_at'])); ?></span>
                            </div>
                            <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                            <?php if (!empty($post['image_path'])): ?>
                                <img src="<?php echo '../' . htmlspecialchars($post['image_path']); ?>" alt="Post Image" class="post-image"
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Dark Social. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>