<?php
// done by zixu
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_POST['coupon_id'])) {
    header("Location: index.php");
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'fyp_app');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$reward_id = $_POST['coupon_id'];

$reward;
$result = $conn->query("SELECT * FROM coupon WHERE coupon_id = '$reward_id'");
while ($row = $result->fetch_assoc()) {
    $reward = $row;
}

include 'header.php';
?>
<main>
  <section class="display-rewards">
    <div class="container">
      <h3>Edit Reward</h3>
      <form action="admin_edit_rewards_handler.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
          <label for="name">Name:</label>
          <input type="text" id="name" name="name" value="<?php echo $reward['name'] ?>" required>
        </div>
        <div class="form-group">
          <label for="points_required">Points Required:</label>
          <input type="number" id="points_required" name="points_required" value="<?php echo $reward['points_required'] ?>" required>
        </div>
        <div class="form-group">
          <label for="description">Description:</label>
          <input type="text" id="description" name="description" value="<?php echo $reward['description'] ?>" required>
        </div>
        <div class="form-group">
          <label for="image">Image:</label>
          <input type="file" id="image" name="image" accept="image/*">
        </div>
        <input type="hidden" id="reward_id" name="reward_id" value="<?php echo $reward['coupon_id'] ?>">
        <div class="form-group">
          <button type="submit" class="btn">Save Reward</button>
        </div>
      </form>
    </div>
  </section>
</main>
<?php include 'footer.php'; 

$conn->close();
?>
