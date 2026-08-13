<?php
//sem2Assignment.php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Insert user into the database
    $connection = new mysqli('localhost', 'root', '', 'socialNet');
    
    // Check if email already exists
    $statement = $connection->prepare("SELECT id FROM userdetails WHERE email = ?");
    $statement->bind_param("s", $email);
    $statement->execute();
    $statement->store_result();

    if ($statement->num_rows > 0) {
        $error = "Email already exists!";
    } else {
        // Register the new user
        $statement->close();
        $statement = $connection->prepare("INSERT INTO userdetails (name, email, password) VALUES (?, ?, ?)");
        $statement->bind_param("sss", $name, $email, $password);
        if ($statement->execute()) {
            $success = "Registration successful! You can now log in.";
        } else {
            $error = "Error occurred during registration.";
        }
    }
    $statement->close();
    $connection->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - SMP</title>
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

input{
    width: 100%;
    padding: 10px;
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
    <div class="login-container"> <!-- Use the same container class for consistent styling -->
        <h1>Register for SMP</h1>
        <form method="POST">
            <input type="text" name="name" placeholder="Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Sign Up</button>
        </form>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <!-- Back to Login Button -->
        <h2>Already have an account?</h2>
        <form action="sem2Assignmentlog.php">
            <button type="submit">Back to Login</button>
        </form>
    </div>
</body>
</html>
