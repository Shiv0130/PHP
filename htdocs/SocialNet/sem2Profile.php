<?php
//sem2Profile.php
session_start();
require_once 'sem2AssignmentDatabase.php';

if (!isset($_SESSION['userdetails_id'])) {
    header("Location: Sem2Assignmentlog.php");
    exit();
}

$connection = dbConnect();

// Fetch user details
$statement = $connection->prepare("SELECT name, profilePic FROM userdetails WHERE id = ?");
$statement->bind_param("i", $_SESSION['userdetails_id']);
$statement->execute();
$statement->bind_result($name, $profilePic);
$statement->fetch();
$statement->close();

$profilePic = empty($profilePic) ? 'uploads/default_profile.png' : 'uploads/' . basename($profilePic);

// Handle profile picture upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['profilePic'])) {
    $uploadDir = "uploads/";
    $uploadFile = $uploadDir . basename($_FILES["profilePic"]["name"]);
    $imageFileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));
    $allowedExtensions = ["jpg", "png", "jpeg", "gif"];

    if (in_array($imageFileType, $allowedExtensions) && $_FILES["profilePic"]["size"] <= 500000) {
        if (move_uploaded_file($_FILES["profilePic"]["tmp_name"], $uploadFile)) {
            $statement = $connection->prepare("UPDATE userdetails SET profilePic = ? WHERE id = ?");
            $statement->bind_param("si", basename($uploadFile), $_SESSION['userdetails_id']);
            $statement->execute();
            $statement->close();
            header("Location: Sem2Profile.php");
            exit();
        } else {
            $uploadError = "Sorry, there was an error uploading your file.";
        }
    } else {
        $uploadError = "Invalid file. Please upload a JPG, JPEG, PNG or GIF file under 500KB.";
    }
}

// Handle new post creation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['new_post'])) {
    $newPost = $_POST['new_post'];
    $statement = $connection->prepare("INSERT INTO attachments (userdetails_id, content) VALUES (?, ?)");
    $statement->bind_param("is", $_SESSION['userdetails_id'], $newPost);
    $statement->execute();
    $statement->close();
    header("Location: Sem2Profile.php");
    exit();
}

// Handle user search
$searchResults = [];
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['query'])) {
    $query = "%{$_GET['query']}%";
    $statement = $connection->prepare("SELECT id, name, email FROM userdetails WHERE name LIKE ?");
    $statement->bind_param("s", $query);
    $statement->execute();
    $result = $statement->get_result();
    while ($row = $result->fetch_assoc()) {
        $searchResults[] = $row;
    }
    $statement->close();
}

// Fetch user posts
$statement = $connection->prepare("SELECT content, created_at FROM attachments WHERE userdetails_id = ? ORDER BY created_at DESC");
$statement->bind_param("i", $_SESSION['userdetails_id']);
$statement->execute();
$posts = $statement->get_result()->fetch_all(MYSQLI_ASSOC);
$statement->close();

$connection->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMP - <?php echo htmlspecialchars($name); ?>'s Profile</title>
    <style>
        body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

.profile-container {
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    width: 500px;
    text-align: center;
}

h1, h2 {
    margin-bottom: 20px;
    color: #333;
}

.profile-pic {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    margin-bottom: 20px;
    object-fit: cover;
}

.upload-form {
    margin-bottom: 20px;
}

.new-post-form textarea {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    border: 1px solid #ccc;
    border-radius: 5px;
}

button {
    width: 100%;
    padding: 10px;
    background-color: #007bff;
    border: none;
    color: #fff;
    border-radius: 5px;
    cursor: pointer;
}

button:hover {
    background-color: #0056b3;
}

.search-form input {
    width: 80%;
    padding: 10px;
    margin: 10px 0;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.search-result {
    margin-bottom: 20px;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.post {
    background-color: #f9f9f9;
    padding: 10px;
    margin-bottom: 20px;
    border-radius: 5px;
}

.success {
    color: green;
    margin-top: 10px;
}

    </style>
</head>
<body>
    <div class="profile-container">
        <h1>Welcome to SMP, <?php echo htmlspecialchars($name); ?>!</h1>
        <img src="<?php echo htmlspecialchars($profilePic); ?>" alt="Profile Picture" style="width: 150px; height: auto;">

        <h2>Upload Profile Picture</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="profilePic" required>
            <button type="submit">Upload</button>
        </form>
        <?php if (isset($uploadError)): ?>
            <p class="error"><?php echo htmlspecialchars($uploadError); ?></p>
        <?php endif; ?>

        <h2>Create a New Post</h2>
        <form method="POST">
            <textarea name="new_post" placeholder="What's on your mind?" required></textarea><br>
            <button type="submit">Post</button>
        </form>

        <h2>Search for Other Users</h2>
        <form method="GET">
            <input type="text" name="query" placeholder="Enter username" required>
            <button type="submit">Search</button>
        </form>

        <?php if (!empty($searchResults)): ?>
            <h2>Search Results:</h2>
            <?php foreach ($searchResults as $user): ?>
                <div>
                    <h3><?php echo htmlspecialchars($user['name']); ?></h3>
                    <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
                    <a href="Sem2Assignmentview_profile.php?id=<?php echo $user['id']; ?>">View Profile</a> |
                    <a href="Sem2Assignmentnotifications.php?id=<?php echo $user['id']; ?>">Message</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <h2>Your Posts</h2>
        <?php foreach ($posts as $post): ?>
            <div>
                <p><?php echo htmlspecialchars($post['content']); ?></p>
                <small><?php echo $post['created_at']; ?></small>
            </div>
        <?php endforeach; ?>

        <form method="POST" action="sem2Assignmentlogout.php" style="margin-top: 20px;">
            <button type="submit">Logout</button>
        </form>
    </div>
</body>
</html>