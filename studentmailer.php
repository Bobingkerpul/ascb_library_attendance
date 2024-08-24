<?php

require_once './conn/conn.php'; // Adjust path as necessary
require 'vendor-mailer/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

// Load environment variables from .env file
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Open log file
$log_file = 'script_log.txt';
$log_handle = fopen($log_file, 'a');
fwrite($log_handle, "Script started at: " . date('Y-m-d H:i:s') . "\n");

// Query to find all borrowed books
$sql = "SELECT student_id, return_date, book_title 
        FROM borrowed_books 
        WHERE book_status = 0";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $student_id = $row['student_id'];
        $return_date = $row['return_date'];
        $book_title = $row['book_title'];

        // Calculate the difference in days between today and the return date
        $return_date_obj = new DateTime($return_date);
        $current_date_obj = new DateTime();
        $interval = $current_date_obj->diff($return_date_obj);
        $date_diff = $interval->days;

        // Check if the return date is 2 days from now
        if ($interval->invert == 0 && $date_diff == 2) {
            // Retrieve email address of the student from another table (example)
            $user_sql = "SELECT tbl_student.gmail 
                         FROM tbl_attendance 
                         LEFT JOIN tbl_student ON tbl_student.generated_code = tbl_attendance.tbl_student_id 
                         WHERE tbl_attendance_id = $student_id";

            $user_result = $conn->query($user_sql);

            if ($user_result->num_rows > 0) {
                $user_row = $user_result->fetch_assoc();
                $email = $user_row['gmail'];

                $subject = "Reminder: Book Return Due Date";
                $body = "Hi,\n\nThis is a reminder that your borrowed book '$book_title' is due on $return_date. Please return it by the due date to avoid any fines.\n\nThank you.";

                try {
                    $mail = new PHPMailer(true);
                    // Server settings
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = $_ENV['GMAIL_USERNAME'];
                    $mail->Password = $_ENV['GMAIL_PASSWORD'];
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    // Recipients
                    $mail->setFrom($_ENV['GMAIL_USERNAME'], 'Library Notification');
                    $mail->addAddress($email);

                    // Content
                    $mail->isHTML(false);
                    $mail->Subject = $subject;
                    $mail->Body = $body;

                    $mail->send();
                    fwrite($log_handle, "Message has been sent to $email\n");
                } catch (Exception $e) {
                    fwrite($log_handle, "Message could not be sent to $email. Mailer Error: {$mail->ErrorInfo}\n");
                }
            } else {
                fwrite($log_handle, "No email found for student ID: $student_id\n");
            }
        } else {
            fwrite($log_handle, "Return date is not 2 days from now for student ID: $student_id\n");
        }
    }
} else {
    fwrite($log_handle, "No borrowed books found.\n");
}

fwrite($log_handle, "Script ended at: " . date('Y-m-d H:i:s') . "\n");
fclose($log_handle);

$conn->close();
