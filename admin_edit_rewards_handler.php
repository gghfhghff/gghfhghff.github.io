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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $points_required = $_POST['points_required'];
    $description = $_POST['description'];
    $reward_id = $_POST['reward_id'];

    // Handle image upload
    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        // Check if image file is an actual image or fake image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            // Allow certain file formats
            if ($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg" || $imageFileType == "gif") {
                // Move the uploaded file to the target directory
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    $image_path = $target_file;
                } else {
                    echo "Sorry, there was an error uploading your file.";
                    exit();
                }
            } else {
                echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                exit();
            }
        } else {
            echo "File is not an image.";
            exit();
        }
    }

    // Update reward details
    if ($image_path) {
        $stmt = $conn->prepare("UPDATE coupon SET name=?, points_required=?, description=?, image_url=? WHERE coupon_id=?");
        $stmt->bind_param("sissi", $name, $points_required, $description, $image_path, $reward_id);
    } else {
        $stmt = $conn->prepare("UPDATE coupon SET name=?, points_required=?, description=? WHERE coupon_id=?");
        $stmt->bind_param("sisi", $name, $points_required, $description, $reward_id);
    }
    $stmt->execute();
    $stmt->close();

    $conn->close();
    header("Location: admin_create_rewards.php");
    exit();
}
?>
