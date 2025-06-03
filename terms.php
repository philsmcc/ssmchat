```php
<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelly's Pub Chat - Terms of Service</title>
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
            overflow-x: auto;
        }
        .container {
            max-width: 600px;
            width: 90%;
            padding: 20px;
            background: #2a2a2a;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.5s ease-in;
        }
        h1 {
            text-align: center;
            color: #00ff88;
            text-shadow: 0 0 10px #00ff88;
            font-size: 2.5rem;
            margin-bottom: 20px;
        }
        p {
            font-size: 1rem;
            line-height: 1.6;
            margin: 10px 0;
        }
        a {
            color: #00ff88;
            text-decoration: none;
            font-weight: 600;
        }
        a:hover {
            text-decoration: underline;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Kelly's Pub Chat - Terms of Service</h1>
        <p>Welcome to Kelly's Pub Chat! This is a for-fun service designed to bring patrons together in a lively, social environment. By using this chat, you agree to the following terms:</p>
        <p><strong>Keep it Fun and Safe:</strong> This chat is for lighthearted, friendly conversation. Do not share any private or sensitive information, as messages are visible to other users, including strangers.</p>
        <p><strong>Respect Others:</strong> Do not use provoking language, harass, or bully anyone. We encourage a positive and inclusive atmosphere for all users.</p>
        <p><strong>Responsibility:</strong> You are responsible for your actions and content shared in the chat. Kelly's Pub and SunScreen Media are not liable for any user-generated content.</p>
        <p><strong>Have a Great Time:</strong> Enjoy connecting with others at Kelly's Pub! Keep the vibe fun and welcoming.</p>
        <p>If you have any questions, please visit SunScreenmedia.com. Return to the <a href="chat.php">chat</a>.</p>
    </div>
</body>
</html>
```