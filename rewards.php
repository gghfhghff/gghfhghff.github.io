<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'header.php';

// Database connection
$conn = new mysqli('localhost', 'root', '', 'fyp_app');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch rewards from database
$sql = "SELECT coupon_id, name, points_required, description, image_url FROM coupon";
$result = $conn->query($sql);

$rewards = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $rewards[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['coupon_id'])) {
    $user_id = $_SESSION['user_id'];
    $coupon_id = $_POST['coupon_id'];
    $points_required = $_POST['points_required'];

    // Fetch user points
    $stmt = $conn->prepare("SELECT points FROM user WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($user_points);
    $stmt->fetch();
    $stmt->close();

    if ($user_points >= $points_required) {
        // Deduct points
        $stmt = $conn->prepare("UPDATE user SET points = points - ? WHERE user_id = ?");
        $stmt->bind_param("ii", $points_required, $user_id);
        $stmt->execute();
        $stmt->close();

        // Generate unique ID for the reward
        $unique_id = uniqid();

        // Insert into user_reward table
        $stmt = $conn->prepare("INSERT INTO user_reward (user_id, coupon_id, unique_id) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $user_id, $coupon_id, $unique_id);
        $stmt->execute();
        $stmt->close();

        echo "<script>alert('Reward redeemed successfully!');</script>";
    } else {
        echo "<script>alert('Not enough points to redeem this reward.');</script>";
    }
}
?>

<main>
    <section class="rewards">
        <div class="container">
            <h2>Available Rewards</h2>
            <div class="reward-list">
                <?php foreach ($rewards as $reward): ?>
                    <div class="reward">
                        <img src="<?php echo htmlspecialchars($reward['image_url']); ?>" alt="<?php echo htmlspecialchars($reward['name']); ?>" class="reward-image">
                        <h3><?php echo htmlspecialchars($reward['name']); ?></h3>
                        <p><?php echo htmlspecialchars($reward['description']); ?></p>
                        <p><strong>Points Required:</strong> <?php echo htmlspecialchars($reward['points_required']); ?></p>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <form action="rewards.php" method="POST" onsubmit="return confirm('Are you sure you want to redeem <?php echo $reward['points_required']; ?> points for this reward?');">
                                <input type="hidden" name="coupon_id" value="<?php echo htmlspecialchars($reward['coupon_id']); ?>">
                                <input type="hidden" name="points_required" value="<?php echo htmlspecialchars($reward['points_required']); ?>">
                                <button type="submit" class="btn">Redeem</button>
                            </form>
                        <?php else: ?>
                            <p><a href="login.php">Login</a> to redeem this reward.</p>
                        <?php endif; ?>
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
