<?php
// done by zixu
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'fyp_app');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$event_id = $_POST['event_id'];

// Delete from user_event table
$sql = "DELETE FROM event WHERE event_id = '$event_id'";

if ($conn->query($sql) === TRUE) {
    header("Location: admin_create_events.php");
    exit();
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
