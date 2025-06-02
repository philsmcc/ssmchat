<?php
$conn = new mysqli("localhost", "chat", "chat", "chat");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$venue = $_POST['venue'];
$name = $_POST['name'];
$message = htmlspecialchars($_POST['message']);
$timestamp = date("Y-m-d H:i:s");

$stmt = $conn->prepare("INSERT INTO messages (venue, name, timestamp, message) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $venue, $name, $timestamp, $message);
$stmt->execute();
$stmt->close();
$conn->close();
?>