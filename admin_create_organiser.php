<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is an admin
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: index.php");
    exit();
}

// Include database connection
$conn = new mysqli('localhost', 'root', '', 'fyp_app');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$events = [];
$result = $conn->query("SELECT event_id, description FROM event");
while ($row = $result->fetch_assoc()) {
    $events[] = $row;
}

$error_message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $event_id = $_POST['event_id'];

    // Check if the event already has an organiser
    $check_sql = "SELECT organiser_id FROM event WHERE event_id = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $stmt->bind_result($existing_organiser_id);
    $stmt->fetch();
    $stmt->close();

    if ($existing_organiser_id) {
        $error_message = "An organiser account already exists for this event.";
    } else {
        // Insert the organiser account into the user table
        $stmt = $conn->prepare("INSERT INTO user (username, email, password, is_organiser) VALUES (?, ?, ?, 1)");
        $stmt->bind_param("sss", $username, $email, $password);
        $stmt->execute();
        $organiser_id = $stmt->insert_id;
        $stmt->close();

        // Insert into organiser table
        $stmt = $conn->prepare("INSERT INTO organiser (organiser_id, name) VALUES (?, ?)");
        $stmt->bind_param("is", $organiser_id, $username);
        $stmt->execute();
        $stmt->close();

        // Update the event to link with the organiser
        $stmt = $conn->prepare("UPDATE event SET organiser_id = ? WHERE event_id = ?");
        $stmt->bind_param("ii", $organiser_id, $event_id);
        $stmt->execute();
        $stmt->close();

        header("Location: admin_dashboard.php");
        exit();
    }
}

include 'header.php';
?>

<main>
    <section class="create-organiser">
        <div class="container">
            <h2>Create Organiser Account</h2>
            <?php if (!empty($error_message)): ?>
                <p class="error-message"><?php echo $error_message; ?></p>
            <?php endif; ?>
            <form action="admin_create_organiser.php" method="POST">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="event_id">Event:</label>
                    <select id="event_id" name="event_id" required>
                        <?php foreach ($events as $event): ?>
                            <option value="<?php echo $event['event_id']; ?>"><?php echo htmlspecialchars($event['description']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn">Create Organiser</button>
                </div>
            </form>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>

<?php
$conn->close();
?>
