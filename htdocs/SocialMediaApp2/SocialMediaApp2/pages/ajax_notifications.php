<?php
session_start();
require_once '../includes/db_connection.php';
header('Content-Type: application/json');

$user_id = $_SESSION['user_id'] ?? null;
$notifications_data = [];

if ($user_id) {
    try {
        // Fetch unread notifications for the current user
        $sql = "SELECT n.*, u.full_name AS sender_name
                FROM notifications n
                LEFT JOIN users u ON n.sender_id = u.user_id
                WHERE n.recipient_id = ? AND n.read_status = FALSE
                ORDER BY n.created_at DESC
                LIMIT 10"; // Limit to last 10 unread notifications
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id]);
        $notifications_data = $stmt->fetchAll();

        // Mark notifications as read after fetching (optional, depending on UX)
        /*
        if ($notifications_data) {
            $notification_ids = array_column($notifications_data, 'notification_id');
            if (!empty($notification_ids)) {
                $placeholders = implode(',', array_fill(0, count($notification_ids), '?'));
                $sql_mark_read = "UPDATE notifications SET read_status = TRUE WHERE notification_id IN ($placeholders)";
                $stmt_mark_read = $pdo->prepare($sql_mark_read);
                $stmt_mark_read->execute($notification_ids);
            }
        }
        */

    } catch (PDOException $e) {
        error_log("Error fetching notifications: " . $e->getMessage());
        // Return an error response or empty array
        $notifications_data = ['error' => 'Could not fetch notifications.'];
    }
} else {
    $notifications_data = ['error' => 'User not logged in.'];
}

echo json_encode($notifications_data);
?>