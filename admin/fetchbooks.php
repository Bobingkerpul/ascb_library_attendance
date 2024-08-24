<?php
require_once '../conn/conn.php';

if (isset($_POST['qr_code'])) {
    $qr_code = $_POST['qr_code'];

    $sql = "SELECT * FROM `borrowed_books` WHERE qrcode = '$qr_code' AND book_status = '0';";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<ul>";
        while ($row = $result->fetch_assoc()) {
            echo '<li><input type="checkbox" name="book_ids[]" value="' . $row['id'] . '"> ' . $row['book_title'] . '</li>';
        }
        echo "</ul>";
    }
}
$conn->close();
