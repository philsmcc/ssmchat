<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

$conn = new mysqli("localhost", "chat", "chat", "chat");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database and table if not exists
$conn->query("CREATE DATABASE IF NOT EXISTS chat");
$conn->select_db("chat");
$conn->query("CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    venue VARCHAR(50),
    name VARCHAR(50),
    timestamp DATETIME,
    message TEXT
)");

// Log session and GET data for debugging
file_put_contents('/tmp/chat_debug.log', "Session: " . print_r($_SESSION, true) . "\nGET: " . print_r($_GET, true) . "\n", FILE_APPEND);

// Clear session if accessing a different venue
if (isset($_GET['venue']) && isset($_SESSION['venue']) && $_SESSION['venue'] !== $_GET['venue']) {
    $_SESSION = array();
    session_destroy();
    session_start();
}

// Set venue if provided
if (isset($_GET['venue'])) {
    $_SESSION['venue'] = htmlspecialchars($_GET['venue']);
}

// Handle name submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name']) && isset($_SESSION['venue'])) {
    $_SESSION['name'] = htmlspecialchars($_POST['name']);
    session_write_close();
    header("Location: chat.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelly's Pub Chat</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body {
            background: #1a1a1a;
            color: #e0e0e0;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            overflow-x: hidden;
        }
        .container {
            max-width: 90%;
            width: 100%;
            padding: 40px;
            background: #2a2a2a;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            animation: fadeIn 1s ease-in;
            text-align: center;
        }
        h1 {
            color: #00ff88;
            text-shadow: 0 0 15px #00ff88;
            animation: pulse 2s infinite;
            font-size: 4rem;
            font-weight: 800;
            margin: 20px 0;
        }
        .welcome-text {
            font-size: 2rem;
            font-weight: 600;
            margin: 20px 0;
            color: #e0e0e0;
        }
        .qr-code {
            display: block;
            margin: 40px auto;
            border: 10px solid #00ff88;
            border-radius: 20px;
            animation: bounce 1.5s infinite;
            width: 500px;
            height: 500px;
        }
        input, button {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        input {
            background: #3a3a3a;
            color: #e0e0e0;
        }
        input:focus {
            outline: none;
            box-shadow: 0 0 10px #00ff88;
            transform: scale(1.05);
        }
        button {
            background: #00ff88;
            color: #1a1a1a;
            cursor: pointer;
            font-weight: 600;
        }
        button:hover {
            transform: scale(1.05);
            box-shadow: 0 0 15px #00ff88;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        @media (min-width: 1920px) {
            h1 {
                font-size: 5rem;
            }
            .welcome-text {
                font-size: 2.5rem;
            }
            .qr-code {
                width: 600px;
                height: 600px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (isset($_SESSION['venue']) && !isset($_SESSION['name'])): ?>
            <h1>Join the Chat!</h1>
            <form method="POST" action="index.php?venue=<?php echo urlencode($_SESSION['venue']); ?>">
                <input type="text" name="name" placeholder="Choose a name" required>
                <button type="submit">Join Chat</button>
            </form>
        <?php else: ?>
            <h1>Join the Fun at Kelly's Pub!</h1>
            <p class="welcome-text">Scan to join the Kelly's chat!</p>
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=500x500&data=http://52.34.246.115/ssmchat/index.php?venue=Kellys" class="qr-code" alt="Kellys QR">
        <?php endif; ?>
    </div>
</body>
</html>
<?php
$conn->close();
?>