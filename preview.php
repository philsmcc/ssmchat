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
        * {
            box-sizing: border-box;
        }
        body {
            background: #1a1a1a;
            color: #e0e0e0;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .header {
            text-align: center;
            color: #00c4b4;
            text-shadow: 0 0 5px #00c4b4, 0 0 10px #00c4b4;
            font-size: 3.5rem;
            font-weight: 800;
            margin: 10px 0;
            padding: 0 20px;
            animation: glow 2s ease-in-out infinite alternate;
        }
        .content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            width: 100%;
            height: calc(100vh - 100px);
            gap: 40px;
            padding: 20px;
        }
        .messages {
            background: #2a2a2a;
            border-radius: 15px;
            padding: 20px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 15px;
            box-shadow: 0 0 20px rgba(0, 196, 180, 0.3);
            max-height: 80vh;
        }
        .message {
            max-width: 80%;
            padding: 15px 20px;
            border-radius: 20px;
            font-size: 1.2rem;
            animation: fadeIn 0.5s ease;
            word-wrap: break-word;
        }
        .message.other {
            background: #3a3a3a;
            align-self: flex-start;
            border-bottom-left-radius: 5px;
        }
        .message span {
            color: #00c4b4;
            font-weight: 600;
            font-size: 1.1rem;
        }
        .qr-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: #2a2a2a;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 0 20px rgba(0, 196, 180, 0.3);
            max-height: 80vh;
        }
        .qr-code {
            border: 15px solid #00c4b4;
            border-radius: 20px;
            width: 500px;
            height: 500px;
            animation: pulseQR 2s ease-in-out infinite;
            margin-bottom: 10px;
        }
        .qr-text {
            font-size: 2rem;
            font-weight: 600;
            color: #e0e0e0;
            margin: 10px 0;
            text-shadow: 0 0 5px #00c4b4;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes glow {
            from { text-shadow: 0 0 5px #00c4b4, 0 0 10px #00c4b4; }
            to { text-shadow: 0 0 10px #00c4b4, 0 0 15px #00c4b4; }
        }
        @keyframes pulseQR {
            0% { box-shadow: 0 0 10px #00c4b4; }
            50% { box-shadow: 0 0 20px #00c4b4; }
            100% { box-shadow: 0 0 10px #00c4b4; }
        }

        @media (max-width: 1200px) {
            .content {
                grid-template-columns: 1fr;
                grid-template-rows: auto auto;
                padding: 15px;
                gap: 20px;
            }
            .qr-code {
                width: 350px;
                height: 350px;
            }
            .header {
                font-size: 2.5rem;
            }
            .qr-text {
                font-size: 1.5rem;
            }
            .messages, .qr-container {
                max-height: 45vh;
            }
        }
        @media (min-width: 1920px) {
            .header {
                font-size: 4rem;
            }
            .qr-code {
                width: 550px;
                height: 550px;
            }
            .qr-text {
                font-size: 2.5rem;
            }
            .message {
                font-size: 1.4rem;
            }
            .message span {
                font-size: 1.3rem;
            }
            .content {
                padding: 30px;
                gap: 50px;
            }
            .messages, .qr-container {
                padding: 25px;
            }
        }
    </style>
</head>
<body>
    <h1 class="header">Join the group text with others at Kelly's right here right now!</h1>
    <div class="content">
        <div class="messages" id="messageBox"></div>
        <div class="qr-container">
            <p class="qr-text">Scan to join the chat!</p>
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=600x600&data=http://35.81.99.212/ssmchat/index.php?venue=Kellys" class="qr-code" alt="Kellys QR">
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