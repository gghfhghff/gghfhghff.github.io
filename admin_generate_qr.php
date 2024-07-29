<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is an admin
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: index.php");
    exit();
}

// Include database connection
$conn = new mysqli('localhost', 'root', '', 'fyp_app');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$qr_image_data = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $points = $_POST['points'];

    // Generate QR code data
    $qr_data = uniqid(); // Unique identifier for the QR code

    // Insert QR code data into the database
    $stmt = $conn->prepare("INSERT INTO qr_code (data, points) VALUES (?, ?)");
    $stmt->bind_param("si", $qr_data, $points);
    $stmt->execute();
    $qr_id = $stmt->insert_id;

    // Generate QR code image as a Base64 string
    include('phpqrcode/qrlib.php');
    ob_start();
    QRcode::png($qr_data, null, QR_ECLEVEL_L, 3);
    $qr_image_data = base64_encode(ob_get_contents());
    ob_end_clean();

    echo "QR code generated successfully!";
}

include 'header.php';
?>

<main>
    <section class="generate-qr">
        <div class="container">
            <h2>Generate QR Code</h2>
            <form action="admin_generate_qr.php" method="POST">
                <div class="form-group">
                    <label for="points">Points:</label>
                    <input type="number" id="points" name="points" required>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn">Generate QR Code</button>
                </div>
            </form>
            <?php if ($qr_image_data): ?>
                <div class="qr-code">
                    <h3>Your QR Code:</h3>
                    <img src="data:image/png;base64,<?php echo $qr_image_data; ?>" alt="QR Code">
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>

<?php
$conn->close();
?>
