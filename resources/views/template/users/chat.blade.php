<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChatGPT Conversation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f0f0f0;
        }
        #chat-container {
            width: 400px;
            background-color: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        #chat {
            height: 400px;
            overflow-y: auto;
            padding: 20px;
        }
        .message {
            margin-bottom: 15px;
        }
        .message.user {
            text-align: right;
        }
        .message .text {
            display: inline-block;
            padding: 10px 15px;
            border-radius: 20px;
            max-width: 70%;
        }
        .message.user .text {
            background-color: #007bff;
            color: white;
        }
        .message.bot .text {
            background-color: #e9e9e9;
            color: black;
        }
        #input-container {
            display: flex;
            border-top: 1px solid #e9e9e9;
        }
        #input-container input {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 0;
            outline: none;
        }
        #input-container button {
            padding: 10px 15px;
            border: none;
            background-color: #007bff;
            color: white;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div id="chat-container">
        <div id="chat"></div>
        <div id="input-container">
            <input type="text" id="messageInput" placeholder="Type a message...">
            <button id="sendButton">Send</button>
        </div>
    </div>

    <script>
        const chatContainer = document.getElementById('chat');
        const messageInput = document.getElementById('messageInput');
        const sendButton = document.getElementById('sendButton');

        let conversationId = null;
        let followUps = [];

        sendButton.addEventListener('click', () => {
            const message = messageInput.value;
            if (message.trim() === '') return;

            addMessageToChat('user', message);
            messageInput.value = '';

            if (!conversationId) {
                startConversation(message);
            } else {
                sendFollowUp(message);
            }
        });

        function addMessageToChat(sender, text) {
            const messageElement = document.createElement('div');
            messageElement.classList.add('message', sender);
            messageElement.innerHTML = `<div class="text">${text}</div>`;
            chatContainer.appendChild(messageElement);
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }

        function startConversation(message) {
            fetch('/api/conversation', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ message }),
            })
            .then(response => response.json())
            .then(data => {
                conversationId = data.conversation_id;
                addMessageToChat('bot', data.response);
            })
            .catch((error) => {
                console.error('Error:', error);
            });
        }

        function sendFollowUp(message) {
            const context = followUps.join('\n');
            const requestData = {
                message,
                context
            };

            fetch(`/api/conversation/${conversationId}/follow-up`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(requestData),
            })
            .then(response => response.json())
            .then(data => {
                addMessageToChat('bot', data.response);
                followUps.push(message); // Add user message to follow-ups
                followUps.push(data.response); // Add bot response to follow-ups
            })
            .catch((error) => {
                console.error('Error:', error);
            });
        }
    </script>
</body>
</html>
