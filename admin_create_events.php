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

$events = [];
$result = $conn->query("SELECT * FROM event");
while ($row = $result->fetch_assoc()) {
    $events[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $description = $_POST['description'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $points = $_POST['points'];

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
        $stmt = $conn->prepare("INSERT INTO event (description, start_date, end_date, image_url, points) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $description, $start_date, $end_date, $image_path, $points);
    } else {
        $stmt = $conn->prepare("INSERT INTO event (description, start_date, end_date, points) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $description, $start_date, $end_date, $image_path, $points);
    }
    $stmt->execute();
    $stmt->close();

    header("Location: admin_create_events.php");
    exit();
}

include 'header.php';
?>

<main>
    <section class="create-event">
        <div class="container">
            <h2>Create New Event</h2>
            <form action="admin_create_events.php" method="POST" enctype="multipart/form-data" onsubmit="return validateDates()">
                <div class="form-group">
                    <label for="description">Description:</label>
                    <input type="text" id="description" name="description" required>
                </div>
                <div class="form-group">
                    <label for="start_date">Start Date:</label>
                    <input type="date" id="start_date" name="start_date" required>
                </div>
                <div class="form-group">
                    <label for="end_date">End Date:</label>
                    <input type="date" id="end_date" name="end_date" required>
                </div>
                <div class="form-group">
                    <label for="image">Image:</label>
                    <input type="file" id="image" name="image" accept="image/*" required>
                </div>
              <div class="form-group">
                    <label for="points">Points:</label>
                    <input type="number" id="points" name="points" required>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn">Create Event</button>
                </div>
            </form>
        </div>
    </section>
  <section class="display-events">
    <div class="container">
      <h3>Events</h3>
                <div class="activity-list">
                  <?php foreach ($events as $event): ?>
                    <div class="activity">
                      <img src="<?php echo htmlspecialchars($event['image_url']); ?>" alt="<?php echo htmlspecialchars($event['description']); ?>" class="activity-image">
                            <h3><?php echo htmlspecialchars($event['description']); ?></h3>
                            <p><strong>Start Date:</strong> <?php echo htmlspecialchars($event['start_date']); ?></p>
                            <p><strong>End Date:</strong> <?php echo htmlspecialchars($event['end_date']); ?></p>
                            <p><strong>Points:</strong> <?php echo htmlspecialchars($event['points']); ?></p>
                            <form action="admin_edit_event.php" method="POST">
                                <input type="hidden" name="event_id" value="<?php echo htmlspecialchars($event['event_id']); ?>">
                                <button type="submit" class="btn">Edit Event</button>
                            </form>
                            <form action="admin_delete_event.php" method="POST" onsubmit="return confirmDelete()">
                                <input type="hidden" name="event_id" value="<?php echo htmlspecialchars($event['event_id']); ?>">
                                <button type="submit" class="btn">Delete Event</button>
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
    function validateDates() {
        var startDate = document.getElementById("start_date").value;
        var endDate = document.getElementById("end_date").value;
        if (new Date(startDate) >= new Date(endDate)) {
            alert("Start Date must be before End Date.");
            return false;
        }
        return true;
    }

    function confirmDelete() {
        return confirm("Are you sure you want to delete this event?");
    }
</script>
