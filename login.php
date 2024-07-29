<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// If user is already logged in, redirect to home page
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include 'header.php';
?>

<main>
    <section class="login">
        <div class="container">
            <h2>User Login</h2>
            <form action="login.php" method="POST">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn">Login</button>
                </div>
                <p>Don't have an account? <a href="register.php">Register now</a></p>
            </form>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>

<?php
// Login logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'fyp_app');

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch user from database
    $sql = "SELECT * FROM user WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Start session and set user data
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = $user['is_admin'];
            $_SESSION['is_organiser'] = $user['is_organiser'];
            
            // Redirect based on user role
            if ($user['is_admin']) {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: index.php");
            }
        } else {
            echo "<p>Invalid password. Please try again.</p>";
        }
    } else {
        echo "<p>No account found with that email. Please register first.</p>";
    }

    $conn->close();
}
?>
