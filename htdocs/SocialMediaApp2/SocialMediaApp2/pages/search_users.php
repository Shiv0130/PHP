<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../includes/db_connection.php';

$search_term = '';
$users = [];
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search'])) {
    $search_term = trim($_POST['search_term']);

    if (empty($search_term)) {
        $message = "Please enter a name or email to search.";
    } else {
        try {
            $sql = "SELECT user_id, full_name, email, profile_picture
                    FROM users
                    WHERE full_name LIKE ? OR email LIKE ?
                    ORDER BY full_name ASC
                    LIMIT 10";
            $stmt = $pdo->prepare($sql);
            $like_term = "%" . $search_term . "%";
            $stmt->execute([$like_term, $like_term]);
            $users = $stmt->fetchAll();

            if (empty($users)) {
                $message = "No users found matching your search criteria.";
            }
        } catch (PDOException $e) {
            $message = "Error searching users. Please try again later.";
            error_log("Error searching users: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Users - Dark Social</title>
    <link rel="stylesheet" href="../css/style.css"> 
    <link rel="stylesheet" href="../css/search_suggestions.css">
</head>
<body>
    <header class="dashboard-header">
        <div class="container">
            <h1>Search Users</h1>
            <nav>
                <a href="dashboard.php">Dashboard</a> |
                <a href="profile.php?user_id=<?php echo htmlspecialchars($_SESSION['user_id']); ?>">My Profile</a> |
                <a href="create_post.php">Create Post</a> |
                <a href="messages.php">Messages</a> |
                <a href="logout.php">Logout</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <section class="search-section">
            <h2>Find Users</h2>
            <div class="search-container-wrapper"> <!-- Wrapper for positioning -->
                <form action="search_users.php" method="post" id="userSearchForm">
                    <div class="form-group search-bar">
                        <input type="text" id="search_term" name="search_term" placeholder="Search by name or email..." value="<?php echo htmlspecialchars($search_term); ?>" required autocomplete="off">
                        <button type="submit" name="search" class="btn">Search</button>
                    </div>
                </form>
                <div id="searchSuggestions" class="search-suggestions-dropdown">
                    <ul>
                      
                    </ul>
                </div>
            </div>

            <?php if (!empty($message)): ?>
                <p class="message <?php echo (strpos($message, 'found') !== false) ? 'error' : 'success'; ?>"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>

            <?php if (!empty($users)): ?>
                <div class="search-results">
                    <h3>Results:</h3>
                    <ul>
                        <?php foreach ($users as $user): ?>
                            <li>
                                <img src="<?php echo htmlspecialchars($user['profile_picture'] ? '../' . $user['profile_picture'] : '../images/default_profile.png'); ?>" alt="Profile Pic" class="profile-pic-small">
                                <a href="profile.php?user_id=<?php echo $user['user_id']; ?>">
                                    <strong><?php echo htmlspecialchars($user['full_name']); ?></strong>
                                </a>
                                <small>(<?php echo htmlspecialchars($user['email']); ?>)</small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </section>
    </div>
    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Dark Social. All rights reserved.</p>
        </div>
    </footer>
    <script src="../js/live_search.js"></script>
</body>
</html>