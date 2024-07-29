<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Charity QR App</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Charity QR App</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="activities.php">Activities</a></li>
                    <li><a href="rewards.php">Rewards</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                            <li><a href="admin_dashboard.php">Admin Dashboard</a></li>
                        <?php endif; ?>
                        <li><a href="scan_qr.php">Scan QR Code</a></li>
                        <li class="dropdown">
                            <span class="username"><?php echo strtoupper($_SESSION['username']); ?></span>
                            <div class="dropdown-content">
                                <a href="dashboard.php">Dashboard</a>
                                <a href="profile.php">Profile</a>
                                <a href="user_rewards.php">My Rewards</a>
                                <?php if (isset($_SESSION['is_organiser']) && $_SESSION['is_organiser']): ?>
                                    <a href="organiser_generate_qr.php">Generate QR Code</a>
                                <?php endif; ?>
                                <a href="logout.php">Logout</a>
                            </div>
                        </li>
                    <?php else: ?>
                        <li><a href="login.php">Login</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
</body>
</html>
