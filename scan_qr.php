<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include database connection
$conn = new mysqli('localhost', 'root', '', 'fyp_app');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the QR code data from the form
    $qr_data = $_POST['qr_data'];

    // Check if the QR code exists and has not been scanned
    $stmt = $conn->prepare("SELECT qr_id, points, is_scanned FROM qr_code WHERE data = ?");
    $stmt->bind_param("s", $qr_data);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($qr_id, $points, $is_scanned);
        $stmt->fetch();

        if ($is_scanned) {
            $message = "This QR code has already been scanned.";
        } else {
            // Mark the QR code as scanned
            $update_stmt = $conn->prepare("UPDATE qr_code SET is_scanned = TRUE WHERE qr_id = ?");
            $update_stmt->bind_param("i", $qr_id);
            $update_stmt->execute();

            // Award points to the user
            $user_id = $_SESSION['user_id'];
            $insert_stmt = $conn->prepare("INSERT INTO user_qr_code (user_id, qr_id) VALUES (?, ?)");
            $insert_stmt->bind_param("ii", $user_id, $qr_id);
            $insert_stmt->execute();

            // Update user's points (assuming a `points` column in the `user` table)
            $update_points_stmt = $conn->prepare("UPDATE user SET points = points + ? WHERE user_id = ?");
            $update_points_stmt->bind_param("ii", $points, $user_id);
            $update_points_stmt->execute();

            $message = "Points awarded successfully!";
        }
    } else {
        $message = "Invalid QR code.";
    }

    $stmt->close();
}

include 'header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan QR Code</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
</head>
<body>
<main>
    <section class="scan-qr">
        <div class="container">
            <h2>Scan QR Code</h2>
            <?php if ($message): ?>
                <p><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>
            <div id="qr-reader" style="width: 100%; max-width: 500px; height: 500px; border: 1px solid #ccc; margin: auto;"></div>
            <button id="start-button" class="btn">Start</button>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>

<script>
    document.getElementById('start-button').addEventListener('click', () => {
        if (typeof Html5Qrcode === 'undefined') {
            console.error('Html5Qrcode is not defined. Ensure the script is correctly loaded.');
            return;
        }

        let html5QrCode = new Html5Qrcode("qr-reader");
        html5QrCode.start(
            { facingMode: "user" }, // Use front-facing camera
            {
                fps: 10, // Frames per second for scanning
                qrbox: { width: 250, height: 250 } // Display box for QR code scanning
            },
            onScanSuccess,
            onScanError
        ).catch(err => {
            console.error(`Error starting QR Code scanner: ${err}`);
        });
    });

    function onScanSuccess(decodedText, decodedResult) {
        console.log(`QR Code detected: ${decodedText}`);
        document.getElementById('qr_data').value = decodedText;
        // Automatically submit the form once the QR code is scanned
        document.querySelector('form').submit();
    }

    function onScanError(errorMessage) {
        // Handle scan error
        console.error(errorMessage);
    }
</script>

<?php
$conn->close();
?>
</body>
</html>
