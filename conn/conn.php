<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "qrcode_attendace";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connecton failed!: " . $conn->connect_error);
}
