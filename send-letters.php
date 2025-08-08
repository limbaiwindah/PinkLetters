<?php
// Show errors during testing
error_reporting(E_ALL);
ini_set('display_errors', 1);

// PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; 

// Connect to DB
$conn = new mysqli("127.0.0.1", "root", "", "pinkletters");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$today = date('Y-m-d');

// Fetch letters scheduled for today
$sql = "SELECT id, email, message FROM letters WHERE delivery_date <= ? AND sent = 0";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $today);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "No letters scheduled for today.<br>";
}

// Loop through each letter and send
while ($row = $result->fetch_assoc()) {
    echo "📨 Preparing to send to: {$row['email']}<br>";
    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'herwithpen@gmail.com';         // gmail acc that the letters will be receiving from
        $mail->Password   = 'tmoo jdwv opcs dmnn';           // the sender (herwithpen@gmail.com)'s gmail app password (not login pass)
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('herwithpen@gmail.com', 'Pink Letters');
        $mail->addAddress($row['email']);                    // Recipient from DB
        $mail->isHTML(true);
        $mail->Subject = 'Hi, a letter from the past for you :)';
        $mail->Body    = nl2br(htmlspecialchars($row['message'])); // Clean + preserve line breaks

        $mail->send();

        // Update DB to mark as sent (1)
        $update = $conn->prepare("UPDATE letters SET sent = 1 WHERE id = ?");
        $update->bind_param("i", $row['id']);
        $update->execute();
        if ($update->affected_rows > 0) {
            echo "✅ Marked letter as sent for ID: {$row['id']}<br>";
        } else {
            echo "⚠️ Letter not marked as sent (ID: {$row['id']})<br>";
        }

        $update->close();

        echo "✔️ Sent to {$row['email']}<br>";

    } catch (Exception $e) {
        echo "❌ Failed to send to {$row['email']}. Error: {$mail->ErrorInfo}<br>";
    }
}

// Close DB
$stmt->close();
$conn->close();
?>
