<?php
//sem2Timeline.php
session_start();
if (!isset($_SESSION['userdetails_id'])) {
    header("Location: sem2Assignmentlog.php");
    exit();
}

$connection = new mysqli('localhost', 'root', '', 'socialNet');
$statement = $connection->prepare("SELECT content, created_at FROM attachments WHERE userdetails_id = ? ORDER BY created_at DESC");
$statement->bind_param("i", $_SESSION['userdetails_id']);
$statement->execute();
$statement->bind_result($content, $created_at);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Timeline</title>
</head>
<body>
    <h1>Your Timeline</h1>
    <a href="sem2Profile.php">Back to Profile</a>
    <?php while ($statement->fetch()): ?>
        <div>
            <p><?php echo htmlspecialchars($content); ?></p>
            <small><?php echo $created_at; ?></small>
        </div>
    <?php endwhile; ?>
    <?php $statement->close(); $connection->close(); ?>
</body>
</html>
