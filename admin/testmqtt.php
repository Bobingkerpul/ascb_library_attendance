<?php
require("phpMQTT/phpMQTT.php");

use cjabarca\phpMQTT;

$server = "localhost";   // hostname ng iyong MQTT broker (localhost kung naka-run sa local machine)
$port = 1883;            // port number (default: 1883)
$username = "";          // MQTT broker username (optional)
$password = "";          // MQTT broker password (optional)
$client_id = "phpMQTT-client"; // unique client identifier

$mqtt = new phpMQTT($server, $port, $client_id);

if ($mqtt->connect(true, NULL, $username, $password)) {
    $mqtt->publish("your/topic", "Hello World!", 0);
    $mqtt->close();
} else {
    echo "Time out!\n";
}
