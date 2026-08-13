<?php
// sem2AssignmentDatabase.php
// Create a connection to the MySQL server
$servName = "localhost";
$username = "root";
$password = ""; // Usually empty for XAMPP
$connection = new mysqli($servName, $username, $password);

// Check connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Create database if it doesn't exist
$sqlquery = "CREATE DATABASE IF NOT EXISTS socialNet";
if ($connection->query($sqlquery) === TRUE) {
    echo "Database created successfully or already exists<br>";
} else {
    echo "Error creating database: " . $connection->error . "<br>";
}

// Select the database
$connection->select_db("socialNet");

// Create tables
$sqlquery = "
CREATE TABLE IF NOT EXISTS userdetails (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    profilePic VARCHAR(255) DEFAULT 'default.png',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS attachments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    userdetails_id INT NOT NULL,
    content TEXT NOT NULL,
    picture VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (userdetails_id) REFERENCES userdetails(id)
);

CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    notification TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES userdetails(id),
    FOREIGN KEY (receiver_id) REFERENCES userdetails(id)
);";

if ($connection->multi_query($sqlquery) === TRUE) {
    do {
        // Store first result set
        if ($result = $connection->store_result()) {
            $result->free();
        }
    } while ($connection->more_results() && $connection->next_result());
    
    echo "Tables created successfully or already exist";
} else {
    echo "Error creating tables: " . $connection->error;
}

$connection->close();

// Connection function to be used in other files
function dbConnect() {
    $servName = "localhost";
    $username = "root";
    $password = "";
    $databaseName = "socialNet";
   
    $connection = new mysqli($servName, $username, $password, $databaseName);
   
    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }
   
    return $connection;
}
?>