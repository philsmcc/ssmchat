<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Get venue from URL, default to 'Kellys' if not provided
$venue = isset($_GET['venue']) ? htmlspecialchars($_GET['venue']) : 'Kellys';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelly's Pub Chat Preview</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body {
            background: #1a1a1a;
            color: #e0e0e0;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            min-height: 100vh;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .header {
            text-align: center;
            color: #00ff88;
            text-shadow: 0 0 15px #00ff88;
            animation: pulse 2s infinite;
            font-size: 3rem;
            font-weight: 800;
            margin: 20px 0;
            padding: 0 20px;
        }
        .content {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: flex-start;
            width: 90%;
            max-width: 1200px;
            gap: 20px;
            padding: 20px;
        }
        .messages {
            flex: 1;
            min-width: 300px;
            max-height: 500px;
            overflow-y: auto;
            padding: 15px;
            background: #3a3a3a;
            border-radius: 10px;
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
        .qr-container {
            flex: 0 0 auto;
            min-width: 300px;
            text-align: center;
        }
        .qr-code {
            display: block;
            margin: 20px auto;
            border: 10px solid #00ff88;
            border-radius: 20px;
            animation: bounce 1.5s infinite;
            width: 350px;
            height: 350px;
        }
        .qr-text {
            font-size: 1.5rem;
            font-weight: 600;
            color: #e0e0e0;
            margin: 10px 0;
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

        @media (min-width: 1920px) {
            .header {
                font-size: 4rem;
            }
            .qr-code {
                width: 400px;
                height: 400px;
            }
            .qr-text {
                font-size: 2rem;
            }
            .messages {
                max-height: 600px;
            }
        }
    </style>
</head>
<body>
    <h1 class="header">Join this conversation instantly!</h1>
    <div class="content">
        <div class="messages" id="messageBox"></div>
        <div class="qr-container">
            <p class="qr-text">Scan to join the chat!</p>
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=350x350&data=http://35.81.219.212/ssmchat/index.php?venue=<?php echo urlencode($venue); ?>" class="qr-code" alt="<?php echo htmlspecialchars($venue); ?> QR">
        </div>
    </div>

    <script>
        let lastMessages = [];

        function fetchMessages() {
            fetch('fetch_messages.php?venue=<?php echo urlencode($venue); ?>')
                .then(response => response.json())
                .then(data => {
                    console.log('Fetched messages:', data);
                    const messagesChanged = JSON.stringify(data) !== JSON.stringify(lastMessages);
                    if (messagesChanged) {
                        const messageBox = document.getElementById('messageBox');
                        messageBox.innerHTML = '';
                        data.forEach(msg => {
                            const div = document.createElement('div');
                            // Treat all messages as 'other' since no user is logged in
                            div.className = 'message other';
                            div.innerHTML = `<span>${msg.name}</span>: ${msg.message}`;
                            messageBox.appendChild(div);
                        });
                        messageBox.scrollTop = messageBox.scrollHeight;
                        lastMessages = data;
                    }
                })
                .catch(error => console.error('Fetch error:', error));
        }

        setTimeout(fetchMessages, 100);
        setInterval(fetchMessages, 2000);
    </script>
</body>
</html>