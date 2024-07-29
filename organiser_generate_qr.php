<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login page if not logged in or not an organiser
if (!isset($_SESSION['user_id']) || !$_SESSION['is_organiser']) {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'fyp_app');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the organiser's event and points
$organiser_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT event.points FROM event WHERE organiser_id = ?");
$stmt->bind_param("i", $organiser_id);
$stmt->execute();
$stmt->bind_result($points);
$stmt->fetch();
$stmt->close();

$qr_image_html = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Generate QR code data
    $qr_data = uniqid();

    // Insert QR code data and points into the database
    $stmt = $conn->prepare("INSERT INTO qr_code (data, points) VALUES (?, ?)");
    $stmt->bind_param("si", $qr_data, $points);
    $stmt->execute();
    $stmt->close();

    // Generate QR code image
    require 'phpqrcode/qrlib.php';
    ob_start();
    QRcode::png($qr_data, null);
    $image_string = base64_encode(ob_get_contents());
    ob_end_clean();

    $qr_image_html = "<img src='data:image/png;base64,$image_string' />";
}

include 'header.php';
?>

<main>
    <section class="generate-qr">
        <div class="container">
            <h2>Generate QR Code</h2>
            <form action="organiser_generate_qr.php" method="POST">
                <div class="form-group">
                    <button type="submit" class="btn">Generate QR Code</button>
                </div>
            </form>
            <?php if ($qr_image_html): ?>
                <div class="qr-code">
                    <?php echo $qr_image_html; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>

<?php
$conn->close();
?>
