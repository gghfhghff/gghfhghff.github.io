<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login page if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if event_id is set
if (!isset($_POST['event_id'])) {
    header("Location: activities.php");
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'fyp_app');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user ID and event ID
$user_id = $_SESSION['user_id'];
$event_id = $_POST['event_id'];

// Insert into user_event table
$sql = "INSERT INTO user_event (user_id, event_id) VALUES ('$user_id', '$event_id')";

if ($conn->query($sql) === TRUE) {
    header("Location: dashboard.php");
    exit();
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
