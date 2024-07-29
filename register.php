<?php
session_start();

// If user is already logged in, redirect to home page
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include 'header.php';
?>

<main>
    <section class="register">
        <div class="container">
            <h2>User Registration</h2>
            <form action="register.php" method="POST">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="dob">Date of Birth:</label>
                    <input type="date" id="dob" name="dob" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn">Register</button>
                </div>
            </form>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>

<?php
// Registration logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $username = $_POST['username'];
    $email = $_POST['email'];
    $dob = $_POST['dob'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'fyp_app');

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Insert user into database
    $sql = "INSERT INTO user (username, dob, email, password, points) VALUES ('$username', '$dob', '$email', '$password', 0)";

    if ($conn->query($sql) === TRUE) {
        echo "<p>Registration successful! <a href='login.php'>Login here</a></p>";
    } else {
        echo "<p>Error: " . $sql . "<br>" . $conn->error . "</p>";
    }

    $conn->close();
}
?>
