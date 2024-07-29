<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'fyp_app');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch activities
$sql = "SELECT * FROM event";
$result = $conn->query($sql);

// Fetch user's joined events
$user_joined_events = [];
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $joined_sql = "SELECT event_id FROM user_event WHERE user_id = '$user_id'";
    $joined_result = $conn->query($joined_sql);
    while ($row = $joined_result->fetch_assoc()) {
        $user_joined_events[] = $row['event_id'];
    }
}

include 'header.php';
?>

<main>
    <section class="activities">
        <div class="container">
            <h2>Available Activities</h2>
            <div class="activity-list">
                <?php while ($activity = $result->fetch_assoc()): ?>
                    <div class="activity">
                        <img src="<?php echo htmlspecialchars($activity['image_url']); ?>" alt="<?php echo htmlspecialchars($activity['description']); ?>" class="activity-image">
                        <h3><?php echo htmlspecialchars($activity['description']); ?></h3>
                        <p><strong>Start Date:</strong> <?php echo htmlspecialchars($activity['start_date']); ?></p>
                        <p><strong>End Date:</strong> <?php echo htmlspecialchars($activity['end_date']); ?></p>
                        <p><strong>Points:</strong> <?php echo htmlspecialchars($activity['points']); ?></p>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <?php if (in_array($activity['event_id'], $user_joined_events)): ?>
                                <button class="btn joined" disabled>Joined</button>
                            <?php else: ?>
                                <form action="join_activity.php" method="POST">
                                    <input type="hidden" name="event_id" value="<?php echo htmlspecialchars($activity['event_id']); ?>">
                                    <button type="submit" class="btn">Join Activity</button>
                                </form>
                            <?php endif; ?>
                        <?php else: ?>
                            <p><a href="login.php">Login</a> to join this activity.</p>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>

<?php
$conn->close();
?>
