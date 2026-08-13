<?php
require_once '../includes/db_connection.php'; 

header('Content-Type: application/json'); 

$users_data = [];
$search_term = '';

// Check if search_term is provided via GET and is not empty
if (isset($_GET['search_term']) && !empty($_GET['search_term'])) {
    $search_term = trim($_GET['search_term']);

    try {
        // Prepare SQL query to search users by full name or email
        $sql = "SELECT user_id, full_name, email, profile_picture
                FROM users
                WHERE full_name LIKE ? OR email LIKE ?
                ORDER BY full_name ASC
                LIMIT 10"; // Limit results for performance

        $stmt = $pdo->prepare($sql);

        // Use wildcards for LIKE search
        $like_term = "%" . $search_term . "%";
        $stmt->execute([$like_term, $like_term]);
        $users_data = $stmt->fetchAll();

    } catch (PDOException $e) {
        // Log the error and return an empty array or error message
        error_log("AJAX search error: " . $e->getMessage());
        $users_data = [];
    }
}

// Output the results as JSON
echo json_encode($users_data);
?>