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

// Handle AJAX request for checking event organiser
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['check_event_organiser'])) {
    $event_id = $_POST['event_id'];
    $user_id = $_POST['user_id'];

    $stmt = $conn->prepare("SELECT organiser_id FROM event WHERE event_id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $response = [];
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $response['exists'] = $row['organiser_id'] !== null;
        $response['organiser_id'] = $row['organiser_id'];
    } else {
        $response['exists'] = false;
    }

    $stmt->close();
    echo json_encode($response);
    exit();
}

// Fetch all users
$users = [];
$result = $conn->query("SELECT u.*, e.event_id AS organiser_event_id FROM user u LEFT JOIN event e ON u.user_id = e.organiser_id");
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

// Fetch all events for dropdown
$all_events = [];
$all_event_result = $conn->query("SELECT event_id, description FROM event");
while ($event_row = $all_event_result->fetch_assoc()) {
    $all_events[] = $event_row;
}

// Fetch available events
$available_events = [];
$event_result = $conn->query("SELECT event_id, description FROM event WHERE organiser_id IS NULL");
while ($event_row = $event_result->fetch_assoc()) {
    $available_events[] = $event_row;
}

// Handle user update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_user'])) {
    $user_id = $_POST['user_id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $points = $_POST['points'];
    $dob = $_POST['dob'];
    $is_organiser = isset($_POST['is_organiser']) ? 1 : 0;
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;
    $event_id = $_POST['event_id'] ?? null;

    // Check if event selection is valid for organiser
    if ($is_organiser && !$event_id) {
        die("An event must be selected when setting someone as an organiser.");
    }

    // Check if the selected event already has an organiser
    if ($is_organiser && $event_id) {
        $stmt = $conn->prepare("SELECT organiser_id FROM event WHERE event_id = ?");
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        if ($row['organiser_id'] !== null && $row['organiser_id'] != $user_id) {
            die("This event already has an organiser. Please choose another event.");
        }
        $stmt->close();
    }

    // Update user details
    $stmt = $conn->prepare("UPDATE user SET username = ?, email = ?, points = ?, dob = ?, is_organiser = ?, is_admin = ? WHERE user_id = ?");
    $stmt->bind_param("ssisiii", $username, $email, $points, $dob, $is_organiser, $is_admin, $user_id);
    $stmt->execute();
    $stmt->close();

    // Handle password update if provided
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE user SET password = ? WHERE user_id = ?");
        $stmt->bind_param("si", $password, $user_id);
        $stmt->execute();
        $stmt->close();
    }

    // Handle organiser status
    if ($is_organiser) {
        // Insert or update the organiser table
        $stmt = $conn->prepare("SELECT * FROM organiser WHERE organiser_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 0) {
            $stmt = $conn->prepare("INSERT INTO organiser (organiser_id, name) VALUES (?, ?)");
            $stmt->bind_param("is", $user_id, $username);
            $stmt->execute();
        } else {
            $stmt = $conn->prepare("UPDATE organiser SET name = ? WHERE organiser_id = ?");
            $stmt->bind_param("si", $username, $user_id);
            $stmt->execute();
        }
        $stmt->close();

        // Update the event table with the new organiser
        $stmt = $conn->prepare("UPDATE event SET organiser_id = ? WHERE event_id = ?");
        $stmt->bind_param("ii", $user_id, $event_id);
        $stmt->execute();
        $stmt->close();
    } else {
        // Remove organiser association if user is not an organiser
        $stmt = $conn->prepare("UPDATE event SET organiser_id = NULL WHERE organiser_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();

        // Delete from organiser table
        $stmt = $conn->prepare("DELETE FROM organiser WHERE organiser_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: manage_users.php");
    exit();
}

// Handle user deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];

    // Update events to remove the organiser association
    $stmt = $conn->prepare("UPDATE event SET organiser_id = NULL WHERE organiser_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    // Delete user from user table
    $stmt = $conn->prepare("DELETE FROM user WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    // Delete user from organiser table if they are an organiser
    $stmt = $conn->prepare("DELETE FROM organiser WHERE organiser_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    header("Location: manage_users.php");
    exit();
}

include 'header.php';
?>

<main>
    <section class="manage-users">
        <div class="container">
            <h2>Manage Users</h2>
            <table class="user-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Points</th>
                        <th>DOB</th>
                        <th>Organiser</th>
                        <th>Admin</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['user_id']; ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['points']); ?></td>
                            <td><?php echo htmlspecialchars($user['dob']); ?></td>
                            <td><?php echo $user['is_organiser'] ? 'Yes' : 'No'; ?></td>
                            <td><?php echo $user['is_admin'] ? 'Yes' : 'No'; ?></td>
                            <td>
                                <div class="form-inline">
                                    <button class="btn edit-btn" data-user='<?php echo json_encode($user); ?>' data-event='<?php echo $user['organiser_event_id']; ?>'>Edit</button>
                                    <form action="manage_users.php" method="POST">
                                        <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                        <button type="submit" name="delete_user" class="btn" onclick="return confirm('Are you sure you want to delete this user?');">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Modal for editing user -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Edit User</h2>
            <p class="error" style="display: none;"></p>
            <form id="editForm" action="manage_users.php" method="POST">
                <input type="hidden" name="user_id" id="edit-user_id">
                <div class="form-group">
                    <label for="edit-username">Username:</label>
                    <input type="text" id="edit-username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="edit-email">Email:</label>
                    <input type="email" id="edit-email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="edit-password">Password:</label>
                    <input type="password" id="edit-password" name="password" placeholder="Leave blank to keep current password">
                </div>
                <div class="form-group">
                    <label for="edit-points">Points:</label>
                    <input type="number" id="edit-points" name="points" required>
                </div>
                <div class="form-group">
                    <label for="edit-dob">DOB:</label>
                    <input type="date" id="edit-dob" name="dob" required>
                </div>
                <div class="form-group">
                    <label for="edit-is_organiser">Organiser:</label>
                    <input type="checkbox" id="edit-is_organiser" name="is_organiser">
                </div>
                <div class="form-group event-select">
                    <label for="edit-event_id">Event:</label>
                    <select id="edit-event_id" name="event_id">
                        <?php foreach ($all_events as $event): ?>
                            <option value="<?php echo $event['event_id']; ?>"><?php echo htmlspecialchars($event['description']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit-is_admin">Admin:</label>
                    <input type="checkbox" id="edit-is_admin" name="is_admin">
                </div>
                <div class="form-group">
                    <button type="submit" name="update_user" class="btn">Update User</button>
                </div>
            </form>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var modal = document.getElementById("editModal");
        var span = document.getElementsByClassName("close")[0];
        var errorMessage = document.querySelector('.error');

        document.querySelectorAll('.edit-btn').forEach(function (button) {
            button.onclick = function () {
                var user = JSON.parse(this.getAttribute('data-user'));
                var userEventId = this.getAttribute('data-event');

                document.getElementById('edit-user_id').value = user.user_id;
                document.getElementById('edit-username').value = user.username;
                document.getElementById('edit-email').value = user.email;
                document.getElementById('edit-points').value = user.points;
                document.getElementById('edit-dob').value = user.dob;
                document.getElementById('edit-is_organiser').checked = user.is_organiser == 1;
                document.getElementById('edit-is_admin').checked = user.is_admin == 1;

                // Show/hide event select based on organiser status
                var eventSelect = document.querySelector('.event-select');
                if (user.is_organiser == 1) {
                    eventSelect.style.display = 'block';
                    var eventDropdown = document.getElementById('edit-event_id');
                    for (var i = 0; i < eventDropdown.options.length; i++) {
                        if (eventDropdown.options[i].value == userEventId) {
                            eventDropdown.options[i].selected = true;
                            break;
                        }
                    }
                } else {
                    eventSelect.style.display = 'none';
                }

                modal.style.display = "block";
                errorMessage.style.display = 'none'; // Hide error message initially
            };
        });

        span.onclick = function () {
            modal.style.display = "none";
            errorMessage.style.display = 'none'; // Hide error message when modal is closed
        };

        window.onclick = function (event) {
            if (event.target == modal) {
                modal.style.display = "none";
                errorMessage.style.display = 'none'; // Hide error message when modal is closed
            }
        };

        // Toggle event select visibility based on organiser checkbox
        document.getElementById('edit-is_organiser').addEventListener('change', function () {
            var eventSelect = document.querySelector('.event-select');
            if (this.checked) {
                eventSelect.style.display = 'block';
            } else {
                eventSelect.style.display = 'none';
            }
        });
    });

    function handleFormSubmit(event) {
        event.preventDefault();

        var user_id = document.getElementById('edit-user_id').value;
        var is_organiser = document.getElementById('edit-is_organiser').checked;
        var event_id = document.getElementById('edit-event_id').value;

        var errorMessage = document.querySelector('.error');

        if (is_organiser && !event_id) {
            errorMessage.textContent = "An event must be selected when setting someone as an organiser.";
            errorMessage.style.display = 'block';
            return;
        }

        if (is_organiser) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "manage_users.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.exists && response.organiser_id != user_id) {
                        errorMessage.textContent = "This event already has an organiser. Please choose another event.";
                        errorMessage.style.display = 'block';
                    } else if (response.exists && response.organiser_id == user_id) {
                        errorMessage.textContent = "This user is already the organiser for this event.";
                        errorMessage.style.display = 'block';
                    } else {
                        document.getElementById('editForm').submit();
                    }
                }
            };
            xhr.send("check_event_organiser=1&event_id=" + event_id + "&user_id=" + user_id);
        } else {
            document.getElementById('editForm').submit();
        }
    }
</script>

<?php
$conn->close();
?>
