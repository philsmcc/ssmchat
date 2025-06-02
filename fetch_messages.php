<?php
$conn = new mysqli("localhost", "chat", "chat", "chat");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$venue = $_GET['venue'];
$stmt = $conn->prepare("SELECT name, message FROM messages WHERE venue = ? ORDER BY timestamp DESC LIMIT 20");
$stmt->bind_param("s", $venue);
$stmt->execute();
$result = $stmt->get_result();
$messages = array_reverse($result->fetch_all(MYSQLI_ASSOC));
echo json_encode($messages);
$stmt->close();
$conn->close();
?>