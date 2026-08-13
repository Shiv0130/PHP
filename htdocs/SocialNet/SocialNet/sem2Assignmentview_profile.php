<?php 
// sem2Assignmentview_profile.php
session_start();
if (!isset($_SESSION['userdetails_id'])) {
    header("Location: Sem2Assignmentlog.php");
    exit();
}

$connection = new mysqli('localhost', 'root', '', 'socialNet');

// Ensure the 'id' parameter exists in the URL before accessing it
if (isset($_GET['id'])) {
    $view_userdetails_id = $_GET['id'];
} else {
    echo "Error: User ID not provided.";
    exit();
}

// Fetch user details
$sql = "SELECT name, profilePic FROM userdetails WHERE id = '$view_userdetails_id'";
$result = $connection->query($sql);
$profile_userdetails = $result->fetch_assoc();

if (!$profile_userdetails) {
    echo "User details not found.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sender_id = $_SESSION['userdetails_id'];
    $recipient_id = $view_userdetails_id; 
    $message = $_POST['message']; // Use the 'message' field from the form

    // Escape inputs to prevent SQL injection
    $sender_id = $connection->real_escape_string($sender_id);
    $recipient_id = $connection->real_escape_string($recipient_id);
    $message = $connection->real_escape_string($message);

    // Insert notification into the database
    $sql = "INSERT INTO notifications(sender_id, receiver_id, notification) VALUES ('$sender_id', '$recipient_id', '$message')";
    if ($connection->query($sql) === TRUE) {
        echo "Notification sent successfully!";
    } else {
        echo "Error: " . $connection->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($profile_userdetails['name']); ?>'s Profile</title>
</head>
<body>
    <h1><?php echo htmlspecialchars($profile_userdetails['name']); ?></h1>
    <img src="uploads/<?php echo htmlspecialchars($profile_userdetails['profilePic']); ?>" alt="Profile Picture" style="width: 150px; height: auto;">

    <h2>Send a Message</h2>
    <form method="POST" action="">
        <textarea name="message" placeholder="Type your message here..." required></textarea>
        <button type="submit">Send Message</button>
    </form>

    <h2>Chat History</h2>
    <?php
    $sender_id = $_SESSION['userdetails_id'];
    $sql = "SELECT sender_id, notification, created_at FROM notifications
            WHERE (sender_id = '$sender_id' AND receiver_id = '$view_userdetails_id') 
            OR (sender_id = '$view_userdetails_id' AND receiver_id = '$sender_id') 
            ORDER BY created_at ASC";
    
    $result = $connection->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $notification_sender = ($row['sender_id'] == $sender_id) ? 'You' : 'User ' . $row['sender_id'];
            echo "<div><strong>{$notification_sender}:</strong> " . htmlspecialchars($row['notification']) . " <small>" . $row['created_at'] . "</small></div>";
        }
    } else {
        echo "No notifications found.";
    }
    ?>

    <?php $connection->close(); ?>
</body>
</html>
