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

// Fetch user data
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM user WHERE user_id = '$user_id'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

// Fetch user's activities
$activities_sql = "SELECT * FROM user_event INNER JOIN event ON user_event.event_id = event.event_id WHERE user_event.user_id = '$user_id'";
$activities_result = $conn->query($activities_sql);

include 'header.php';
?>

<main>
    <section class="dashboard">
        <div class="container">
            <h2>User Dashboard</h2>
            <div class="user-info">
                <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($user['dob']); ?></p>
                <p><strong>Points:</strong> <?php echo htmlspecialchars($user['points']); ?></p>
            </div>

            <div class="user-activities">
                <h3>Your Activities</h3>
                <div class="activity-list">
                    <?php while ($activity = $activities_result->fetch_assoc()): ?>
                        <div class="activity">
                            <img src="<?php echo htmlspecialchars($activity['image_url']); ?>" alt="<?php echo htmlspecialchars($activity['description']); ?>" class="activity-image">
                            <h3><?php echo htmlspecialchars($activity['description']); ?></h3>
                            <p><strong>Start Date:</strong> <?php echo htmlspecialchars($activity['start_date']); ?></p>
                            <p><strong>End Date:</strong> <?php echo htmlspecialchars($activity['end_date']); ?></p>
                            <p><strong>Points:</strong> <?php echo htmlspecialchars($activity['points']); ?></p>
                            <form action="cancel_activity.php" method="POST">
                                <input type="hidden" name="event_id" value="<?php echo htmlspecialchars($activity['event_id']); ?>">
                                <button type="submit" class="btn">Cancel Event</button>
                            </form>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>

<?php
$conn->close();
?>
