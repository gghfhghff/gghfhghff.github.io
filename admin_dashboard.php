<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login page if not logged in or not an admin
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'fyp_app');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch admin-specific data if needed

include 'header.php';
?>

<main>
    <section class="admin-dashboard">
        <div class="container">
            <h2>Admin Dashboard</h2>
            <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
            <p>This is the admin dashboard where you can manage the app.</p>
            <div class="admin-options">
                <a href="admin_create_events.php" class="btn-square">Manage Events</a>
                <a href="admin_generate_qr.php" class="btn-square">Create QR Codes</a>
                <a href="manage_users.php" class="btn-square">Manage Users</a>
                <a href="admin_create_rewards.php" class="btn-square">Manage Rewards</a>
                <a href="admin_create_organiser.php" class="btn-square">Create Organiser Account</a> <!-- New option for creating organizer accounts -->
            </div>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>

<?php
$conn->close();
?>
