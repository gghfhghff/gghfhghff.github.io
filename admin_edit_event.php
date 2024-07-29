<?php
// done by zixu
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_POST['event_id'])) {
    header("Location: index.php");
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'fyp_app');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$event_id = $_POST['event_id'];

$event;
$result = $conn->query("SELECT * FROM event WHERE event_id = '$event_id'");
while ($row = $result->fetch_assoc()) {
    $event = $row;
}

include 'header.php';
?>
<main>
  <section class="display-events">
    <div class="container">
      <h3>Edit Event</h3>
        <form action="admin_edit_events_handler.php" method="POST" enctype="multipart/form-data">
          <div class="form-group">
            <label for="description">Description:</label>
            <input type="text" id="description" name="description" value="<?php echo $event['description'] ?>" required>
          </div>
          <div class="form-group">
            <label for="start_date">Start Date:</label>
            <input type="date" id="start_date" name="start_date" value="<?php echo $event['start_date'] ?>" required>
          </div>
          <div class="form-group">
            <label for="end_date">End Date:</label>
            <input type="date" id="end_date" name="end_date" value="<?php echo $event['end_date'] ?>" required>
          </div>
          <div class="form-group">
            <label for="image">Image:</label>
            <input type="file" id="image" name="image" accept="image/*">
          </div>
          <div class="form-group">
            <label for="points">Points:</label>
            <input type="number" id="points" name="points" value="<?php echo $event['points'] ?>" required>
          </div>
          <input type="hidden" id="event_id" name="event_id" value="<?php echo $event['event_id'] ?>">
          <div class="form-group">
            <button type="submit" class="btn">Save Event</button>
          </div>
        </form>
    </div>
  </section>
</main>
<?php include 'footer.php'; ?>

<?php
$conn->close();
?>
