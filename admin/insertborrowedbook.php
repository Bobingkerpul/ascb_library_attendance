<?php
require_once '../conn/conn.php';
require_once 'phpMQTT/phpMQTT.php';

use cjabarca\phpMQTT;

$data = json_decode(file_get_contents('php://input'), TRUE);

$server = "localhost";
$port = 1883;
$username = "";
$password = "";
$client_id = "browser-client-insertbooks";

$mqtt = new phpMQTT($server, $port, $client_id);

set_time_limit(300);

if ($mqtt->connect(true, NULL, $username, $password)) {
    if (!empty($data)) {

        $stmt = $conn->prepare("INSERT INTO borrowed_books (book_id, author, book_title, publisher, student_id, return_date, borrowed_date, qrcode, course) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

        if ($stmt === false) {
            die("Failed to prepare SQL statement: " . $conn->error);
        }

        $messages = [];

        foreach ($data as $book) {
            $student_name = $book['student_name'];
            $studentid = $book['tbl_attendance_id'];
            $id = $book['id'];
            $author = $book['author'];
            $title = $book['book_title'];
            $publisher = $book['publisher'];
            $returndate = $book['return_date'];
            $log_date = $book['tbl_logdate'];
            $qrcode = $book['qrcode'];
            $course = $book['course'];

            $stmt->bind_param("sssssssss", $id, $author, $title, $publisher, $studentid, $returndate, $log_date, $qrcode, $course);
            $execute_result = $stmt->execute();

            if ($execute_result === false) {
                echo "Failed to insert record: " . $stmt->error;
            } else {
                $messages[] = [
                    'action' => 'borrow',
                    'student_id' => $studentid,
                    'student_name' => $student_name,
                    'book_title' => $title,
                    'book_id' => $id,
                    'return_date' => $returndate
                ];
            }
        }

        if (!empty($messages)) {
            $publish_result = $mqtt->publish("library/borrowed_books", json_encode($messages), 0);
            if ($publish_result === false) {
                echo "Failed to publish MQTT message.";
            }
        }

        $stmt->close();
    }
    $mqtt->close();
} else {
    echo "MQTT connection timeout!\n";
}
