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

include 'header.php';
?>

<main>
    <section class="profile">
        <div class="container">
            <h2>User Profile</h2>
            <form action="profile.php" method="POST">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="dob">Date of Birth:</label>
                    <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($user['dob']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password">
                    <small>Leave blank to keep current password</small>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn">Update Profile</button>
                </div>
            </form>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>

<?php
// Update profile logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $dob = $_POST['dob'];
    $password = $_POST['password'];

    if (!empty($password)) {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE user SET username='$username', email='$email', dob='$dob', password='$password' WHERE user_id='$user_id'";
    } else {
        $sql = "UPDATE user SET username='$username', email='$email', dob='$dob' WHERE user_id='$user_id'";
    }

    if ($conn->query($sql) === TRUE) {
        echo "<p>Profile updated successfully.</p>";
        // Update session username if changed
        $_SESSION['username'] = $username;
    } else {
        echo "<p>Error updating profile: " . $conn->error . "</p>";
    }
}

$conn->close();
?>
