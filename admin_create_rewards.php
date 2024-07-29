<?php
// done by leezixu
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is an admin
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: index.php");
    exit();
}

// Include database connection
$conn = new mysqli('localhost', 'root', '', 'fyp_app');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$rewards = [];
$result = $conn->query("SELECT * FROM coupon");
while ($row = $result->fetch_assoc()) {
    $rewards[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $points_required = $_POST['points_required'];
    $description = $_POST['description'];

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

    // Insert the reward into the database
    $stmt = $conn->prepare("INSERT INTO coupon (name, points_required, description, image_url) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("siss", $name, $points_required, $description, $image_path);
    $stmt->execute();
    $stmt->close();

    header("Location: admin_dashboard.php");
    exit();
}

include 'header.php';
?>

<main>
    <section class="create-rewards">
        <div class="container">
            <h2>Create New Reward</h2>
            <form action="admin_create_rewards.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="points_required">Points Required:</label>
                    <input type="number" id="points_required" name="points_required" required>
                </div>
                <div class="form-group">
                    <label for="description">Description:</label>
                    <input type="text" id="description" name="description" required>
                </div>
                <div class="form-group">
                    <label for="image">Image:</label>
                    <input type="file" id="image" name="image" accept="image/*" required>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn">Create Reward</button>
                </div>
            </form>
        </div>
    </section>
    <section class="display-rewards">
        <div class="container">
            <h3>Rewards</h3>
            <div class="activity-list">
                <?php foreach ($rewards as $reward): ?>
                    <div class="activity">
                        <img src="<?php echo htmlspecialchars($reward['image_url']); ?>" alt="<?php echo htmlspecialchars($reward['name']); ?>" class="activity-image">
                        <h3><?php echo htmlspecialchars($reward['name']); ?></h3>
                        <p><strong>Points Required:</strong> <?php echo htmlspecialchars($reward['points_required']); ?></p>
                        <p><strong>Description:</strong> <?php echo htmlspecialchars($reward['description']); ?></p>
                        <form action="admin_edit_rewards.php" method="POST">
                            <input type="hidden" name="coupon_id" value="<?php echo htmlspecialchars($reward['coupon_id']); ?>">
                            <button type="submit" class="btn">Edit Reward</button>
                        </form>
                        <form action="admin_delete_rewards.php" method="POST" onsubmit="return confirmDelete()">
                            <input type="hidden" name="coupon_id" value="<?php echo htmlspecialchars($reward['coupon_id']); ?>">
                            <button type="submit" class="btn">Delete Reward</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>

<?php
$conn->close();
?>

<script>
    function confirmDelete() {
        return confirm("Are you sure you want to delete this reward?");
    }
</script>
