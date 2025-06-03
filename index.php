<?php
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

// Clear session if accessing a new venue
if (isset($_GET['venue']) && (!isset($_SESSION['venue']) || $_SESSION['venue'] !== $_GET['venue'])) {
    $_SESSION = array();
    $_SESSION['venue'] = htmlspecialchars($_GET['venue']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fun Chat</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
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
            max-width: 600px;
            width: 90%;
            padding: 20px;
            background: #2a2a2a;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            animation: fadeIn 1s ease-in;
            position: relative;
        }
        h1 {
            text-align: center;
            color: #00ff88;
            text-shadow: 0 0 10px #00ff88;
            animation: pulse 2s infinite;
            margin-top: 0;
        }
        .qr-code {
            display: block;
            margin: 20px auto;
            border: 5px solid #00ff88;
            border-radius: 10px;
            animation: bounce 1.5s infinite;
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
        #chatBox {
            max-height: 400px;
            overflow-y: auto;
            padding: 15px;
            background: #3a3a3a;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .message {
            max-width: 70%;
            padding: 10px 15px;
            border-radius: 15px;
            animation: slideIn 0.5s ease;
            word-wrap: break-word;
        }
        .message.other {
            background: #4a4a4a;
            align-self: flex-start;
            border-bottom-left-radius: 5px;
        }
        .message.user {
            background: #00ff88;
            color: #1a1a1a;
            align-self: flex-end;
            border-bottom-right-radius: 5px;
        }
        .message span {
            color: #00ff88;
            font-weight: 600;
        }
        .message.user span {
            color: #1a1a1a;
        }
        .exit-button {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #ff4444;
            color: #fff;
            padding: 8px 12px;
            border-radius: 50%;
            font-size: 14px;
            cursor: pointer;
            transition: transform 0.3s;
        }
        .exit-button:hover {
            transform: scale(1.1);
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
        @keyframes slideIn {
            from { transform: translateY(10px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="container">
        <?php
        if (!isset($_SESSION['name']) || !isset($_SESSION['venue'])) {
            if (isset($_GET['venue'])) {
                echo "<h1>Choose a name!</h1>";
                echo "<form method='POST' action='index.php'>";
                echo "<input type='text' name='name' placeholder='Choose a name' required>";
                echo "<button type='submit'>Join Chat</button>";
                echo "</form>";
            } else {
                echo "<h1>Welcome to Kelly's Pub!/h1>";
                echo "<p>Scan the QR code to join the chat room!</p>";
                // Single QR code for room1
                echo "<img src='https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=http://52.34.246.115/ssmchat/index.php?venue=kellys' class='qr-code' alt='Room 1 QR'>";
            }
        } else {
            echo "<h1>Chat Room: " . htmlspecialchars($_SESSION['venue']) . "</h1>";
            echo "<form method='POST' action='exit.php'>";
            echo "<button type='submit' class='exit-button'>Exit</button>";
            echo "</form>";
            echo "<div id='chatBox'></div>";
            echo "<form id='messageForm'>";
            echo "<input type='text' id='message' placeholder='Type a message' required>";
            echo "<button type='submit'>Send</button>";
            echo "</form>";
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
            $_SESSION['name'] = htmlspecialchars($_POST['name']);
            header("Location: index.php");
        }
        ?>
    </div>

    <script>
        <?php if (isset($_SESSION['name']) && isset($_SESSION['venue'])): ?>
        let lastMessages = [];

        function fetchMessages() {
            fetch('fetch_messages.php?venue=<?php echo urlencode($_SESSION['venue']); ?>')
                .then(response => response.json())
                .then(data => {
                    // Check if messages have changed
                    const messagesChanged = JSON.stringify(data) !== JSON.stringify(lastMessages);
                    if (messagesChanged) {
                        const chatBox = document.getElementById('chatBox');
                        chatBox.innerHTML = '';
                        data.forEach(msg => {
                            const div = document.createElement('div');
                            const isUser = msg.name === '<?php echo addslashes($_SESSION['name']); ?>';
                            div.className = `message ${isUser ? 'user' : 'other'}`;
                            div.innerHTML = `<span>${msg.name}</span>: ${msg.message}`;
                            chatBox.appendChild(div);
                        });
                        chatBox.scrollTop = chatBox.scrollHeight;
                        lastMessages = data;
                    }
                });
        }

        document.getElementById('messageForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const message = document.getElementById('message').value;
            fetch('send_message.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `venue=<?php echo urlencode($_SESSION['venue']); ?>&name=<?php echo urlencode($_SESSION['name']); ?>&message=${encodeURIComponent(message)}`
            }).then(() => {
                document.getElementById('message').value = '';
                fetchMessages();
            });
        });

        setInterval(fetchMessages, 2000);
        fetchMessages();
        <?php endif; ?>
    </script>
</body>
</html>