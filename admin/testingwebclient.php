<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MQTT WebSocket Client</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/paho-mqtt/1.0.2/mqttws31.min.js"></script>
    <style>
        #messages {
            border: 1px solid #ccc;
            padding: 10px;
            height: 200px;
            overflow-y: auto;
        }
    </style>
</head>

<body>
    <h1>MQTT WebSocket Client</h1>
    <div>
        <button onclick="connect()">Connect</button>
        <button onclick="subscribe()">Subscribe</button>
    </div>
    <form id="publishForm" onsubmit="publish(event)">
        <div>
            <label for="topic">Topic:</label>
            <input type="text" id="topic" name="topic" required>
        </div>
        <div>
            <label for="message">Message:</label>
            <textarea id="message" name="message" required></textarea>
        </div>
        <button type="submit">Publish</button>
    </form>
    <div id="messages"></div>

    <script>
        let client;

        function connect() {
            client = new Paho.MQTT.Client('localhost', Number(9001), 'web-client');

            client.onConnectionLost = function(responseObject) {
                if (responseObject.errorCode !== 0) {
                    console.log("onConnectionLost: " + responseObject.errorMessage);
                }
            };

            client.onMessageArrived = function(message) {
                console.log("Message arrived: " + message.payloadString);
                displayMessage(message.payloadString);
            };

            client.connect({
                onSuccess: function() {
                    console.log("Connected");
                },
                onFailure: function(error) {
                    console.error("Connection failed: ", error);
                },
                keepAliveInterval: 30,
                timeout: 3,
                cleanSession: true
            });
        }

        function subscribe() {
            if (client) {
                client.subscribe('library/borrowed_books', {
                    onSuccess: function() {
                        console.log("Subscribed to topic");
                    },
                    onFailure: function(error) {
                        console.error("Subscription failed: ", error);
                    }
                });
            } else {
                console.log("Client not connected");
            }
        }

        function publish(event) {
            event.preventDefault();
            if (client) {
                const topic = document.getElementById('topic').value;
                const messageContent = document.getElementById('message').value;
                const message = new Paho.MQTT.Message(messageContent);
                message.destinationName = topic;
                client.send(message);
                console.log(`Published message: ${messageContent} to topic: ${topic}`);
            } else {
                console.log("Client not connected");
            }
        }

        function displayMessage(message) {
            console.log("Displaying message: " + message);
            const messagesDiv = document.getElementById('messages');
            const msgDiv = document.createElement('div');
            msgDiv.textContent = `Message: ${message}`;
            messagesDiv.appendChild(msgDiv);
            messagesDiv.scrollTop = messagesDiv.scrollHeight; // Scroll to the bottom
        }
    </script>
</body>

</html>