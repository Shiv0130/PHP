<?php
//sem2Assignmentnotifications.php
session_start();
if (!isset($_SESSION['userdetails_id'])) {
    header("Location: sem2Assignmentlog.php");
    exit();
}


$connection = new mysqli('localhost', 'root', '', "socialNet");


$userdetails_id = $_SESSION['userdetails_id'];


$sql = "SELECT sender_id, receiver_id, notification, created_at 
        FROM notifications 
        WHERE sender_id = '$userdetails_id' OR receiver_id = '$userdetails_id' 
        ORDER BY created_at ASC";
$result = $connection->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your notifications</title>
</head>
<body>
    <h1>Your notifications</h1>
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $sendMessage = ($row['sender_id'] == $userdetails_id) ? 'You' : 'userdetails ' . $row['sender_id'];
            echo "<div><strong>{$sendMessage}:</strong> " . htmlspecialchars($row['message']) . " <small>" . $row['created_at'] . "</small></div>";
        }
    } else {
        echo "No notification found.";
    }
    ?>

    <?php
    
    $connection->close();
    ?>
</body>
</html>