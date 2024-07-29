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
    $description = $_POST['description'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $points = $_POST['points'];
    $event_id = $_POST['event_id'];

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

    if ($image_path) {
        $sql = "UPDATE event SET description='$description', start_date='$start_date', end_date='$end_date', image_url='$image_path', points='$points' WHERE event_id='$event_id'";
    } else {
        $sql = "UPDATE event SET description='$description', start_date='$start_date', end_date='$end_date', points='$points' WHERE event_id='$event_id'";
    }
    $conn->query($sql);
    $conn->close();

    header("Location: admin_create_events.php");
    exit();
}
?>
