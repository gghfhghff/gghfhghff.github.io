<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login page if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'fyp_app');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle reward usage
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['unique_id'])) {
    $unique_id = $_POST['unique_id'];

    // Mark the reward as used
    $sql = "UPDATE user_reward SET used_at = NOW() WHERE unique_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $unique_id, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->close();
}

// Fetch user rewards
$user_id = $_SESSION['user_id'];
$sql = "SELECT coupon.name, user_reward.unique_id, user_reward.used_at FROM user_reward JOIN coupon ON user_reward.coupon_id = coupon.coupon_id WHERE user_reward.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$rewards = [];
$used_rewards = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if ($row['used_at']) {
            $used_rewards[] = $row;
        } else {
            $rewards[] = $row;
        }
    }
}
$stmt->close();

include 'header.php';
?>

<main>
    <section class="user-rewards">
        <div class="container">
            <h2>Your Rewards</h2>
            <div class="rewards-list">
                <?php if (empty($rewards)): ?>
                    <p>You have not redeemed any rewards yet.</p>
                <?php else: ?>
                    <table class="user-table">
                        <thead>
                            <tr>
                                <th>Reward</th>
                                <th>Unique ID</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rewards as $reward): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($reward['name']); ?></td>
                                    <td><?php echo htmlspecialchars($reward['unique_id']); ?></td>
                                    <td>
                                        <form method="POST" action="user_rewards.php" onsubmit="return confirm('Are you sure you want to use this reward?');">
                                            <input type="hidden" name="unique_id" value="<?php echo htmlspecialchars($reward['unique_id']); ?>">
                                            <button type="submit" class="btn">Use</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
            <h2>Used Rewards</h2>
            <div class="used-rewards-list">
                <?php if (empty($used_rewards)): ?>
                    <p>You have not used any rewards yet.</p>
                <?php else: ?>
                    <table class="user-table">
                        <thead>
                            <tr>
                                <th>Reward</th>
                                <th>Unique ID</th>
                                <th>Used At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($used_rewards as $reward): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($reward['name']); ?></td>
                                    <td><?php echo htmlspecialchars($reward['unique_id']); ?></td>
                                    <td><?php echo htmlspecialchars($reward['used_at']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>

<?php
$conn->close();
?>
