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

$reward_id = $_POST['coupon_id'];

// Delete from rewards table
$sql = "DELETE FROM coupon WHERE coupon_id = '$reward_id'";

if ($conn->query($sql) === TRUE) {
    header("Location: admin_create_rewards.php");
    exit();
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
