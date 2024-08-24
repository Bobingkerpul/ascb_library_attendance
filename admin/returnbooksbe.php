<?php

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('../conn/conn.php');

if (isset($_POST['qr_code'])) {
    $qr_code = $_POST['qr_code'];
    $book_ids = isset($_POST['book_ids']) ? $_POST['book_ids'] : null;
    $current_date = date("Y-m-d");
    // $current_date = date("2024-07-16");

    // Check if any book is selected
    if ($book_ids) {
        foreach ($book_ids as $book_id) {
            // Get the book record using the book id
            $sql = "SELECT `return_date`, `fine` FROM `borrowed_books` WHERE `id` = $book_id";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $return_date = $row['return_date'];
                $fine = $row['fine'];

                if ($current_date > $return_date) {
                    $datetime1 = new DateTime($current_date);
                    $datetime2 = new DateTime($return_date);
                    $interval = $datetime1->diff($datetime2);
                    $days_overdue = $interval->days;

                    $new_fine = $fine + (5 * $days_overdue);

                    $update_sql = "UPDATE `borrowed_books` SET `book_status` = 1, `fine` = $new_fine, `date_returned` = '$current_date' WHERE `id` = $book_id";
                    // echo $update_sql . "<br>";
                    $conn->query($update_sql);
                    $_SESSION['book_updated'] = "Books Have Been Successfully Returned";
                    echo $_SESSION['book_updated'];
                    header('location:borrowedbooks.php');
                } else {
                    $update_sql = "UPDATE `borrowed_books` SET `book_status` = 1, `fine` = 0, `date_returned` = '$current_date' WHERE `id` = $book_id";
                    // echo $update_sql . "<br>";
                    $conn->query($update_sql);
                    $_SESSION['book_updated'] = "Books Have Been Successfully Returned";
                    echo $_SESSION['book_updated'];
                    header('location:borrowedbooks.php');
                }
            } else {
                $_SESSION['qrcode_error'] = "Error: No book selected for return.";
                echo $_SESSION['qrcode_error'];
                header('location:borrowedbooks.php');
            }
        }
    } else {
        $_SESSION['qrcode_error'] = "Error: No book selected for return.";
        echo $_SESSION['qrcode_error'];
        header('location:borrowedbooks.php');
    }

    // header("Location: borrowedbooks.php");
    // exit;
}

$conn->close();
