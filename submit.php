<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// only run if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $message = $_POST['message'];
    $delivery_date = $_POST['delivery_date'];
    $created_at = date('Y-m-d H:i:s');
    $sent = 0; // it's a new letter so it's not sent yet

    // connect to database
    $conn = new mysqli("localhost", "root", "", "pinkletters");

    // check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // insert into database
    $stmt = $conn->prepare("INSERT INTO letters (email, delivery_date, created_at, message, sent) VALUES (?,?,?,?,?)");
    $stmt->bind_param("ssssi", $email, $delivery_date, $created_at, $message, $sent);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "Letter saved :) \nSee you in the future.";
    } else {
        echo "Letter failed to be saved.";
    }

    $stmt->close();
    $conn->close();
}

?>