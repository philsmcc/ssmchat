<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION['name']) || !isset($_SESSION['venue'])) {
    header("Location: index.php");
    exit;
}

$conn = new mysqli("localhost", "chat", "chat", "chat");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Log session for debugging
file_put_contents('/tmp/chat_debug.log', "Chat Session: " . print_r($_SESSION, true) . "\n", FILE_APPEND);
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
            display: block;
            min-height: 100vh;
            overflow-x: auto;
        }
        .tos-container {
            position: absolute;
            top: 10px;
            left: 10px;
            color: #b0b0b0;
            font-size: 0.875rem;
            z-index: 1000;
        }
        .tos-container a {
            color: #00ff88;
            text-decoration: none;
            font-weight: 600;
        }
        .tos-container a:hover {
            text-decoration: underline;
        }
        .container {
            max-width: 600px;
            width: 90%;
            padding: 20px;
            margin: 50px auto;
            background: #2a2a2a;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.5s ease-in;
            position: relative;
        }
        h1 {
            text-align: center;
            color: #00ff88;
            text-shadow: 0 0 10px #00ff88;
            animation: pulse 2s infinite;
            font-size: 2.5rem;
            margin-top: 40px;
            margin-bottom: 20px;
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
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
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
        @keyframes slideIn {
            from { transform: translateY(10px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="tos-container">
        By using this chat, you agree to these <a href="terms.php">Terms of Service</a>.
    </div>
    <div class="container">
        <form method="POST" action="exit.php">
            <button type="submit" class="exit-button">X</button>
        </form>
        <h1>Kelly's Pub Chat</h1>
        <div id="chatBox"></div>
        <form id="messageForm">
            <input type="text" id="message" placeholder="Type a message" required>
            <button type="submit">Send</button>
        </form>
    </div>

    <script>
        let lastMessages = [];

        function fetchMessages() {
            fetch('fetch_messages.php?venue=<?php echo urlencode($_SESSION['venue']); ?>')
                .then(response => response.json())
                .then(data => {
                    console.log('Fetched messages:', data);
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
                })
                .catch(error => console.error('Fetch error:', error));
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

        setTimeout(fetchMessages, 100);
        setInterval(fetchMessages, 2000);
    </script>
</body>
</html>
<?php
$conn->close();
?>