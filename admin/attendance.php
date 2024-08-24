<?php
include("../conn/conn.php");

session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['qr_code'])) {
        $qrCode = $_POST['qr_code'];

        date_default_timezone_set('Asia/Manila');
        // $timeIn =  date("Y-m-d H:i:s");
        $time = date("H:i:s");
        $date = date("Y-m-d");

        // $month = date("m");
        // $year = date("Y");
        // $day = date("d");

        // die($year);

        $selectStmt = $conn->query("SELECT * FROM tbl_student WHERE generated_code = '$qrCode'");

        if ($selectStmt->num_rows > 0) {

            $result = $selectStmt->fetch_assoc();
            $studentID = $result["generated_code"];
            // echo $studentID;
            // exit;
            $selectstud = $conn->query("SELECT * FROM tbl_attendance WHERE tbl_student_id = '$studentID' AND timein_log_date = '$date' AND status ='0'");

            // $select = $selectstud->fetch_assoc();
            // $status = $select['status'];
            // $tbl_attendance_id = $select['tbl_attendance_id'];

            // echo $tbl_attendance_id;
            // exit;

            if ($selectstud->num_rows > 0) {

                $attendanceRow = $selectstud->fetch_assoc();
                $tbl_attendance_id = $attendanceRow['tbl_attendance_id'];

                // die("UPDATE tbl_attendance SET time_out='$time',timeout_log_date='$date', status='1' WHERE tbl_attendance_id = '$tbl_attendance_id'");
                $conn->query("UPDATE tbl_attendance SET time_out='$time',timeout_log_date='$date', status='1' WHERE tbl_attendance_id = '$tbl_attendance_id'");
                $_SESSION['attendance_updated'] = "Attendance has been successfully updated as the student has logged out.";
                echo $_SESSION['attendance_updated'];
                header("Location: index.php");
            } else {
                // die("INSERT INTO tbl_attendance (tbl_student_id, time_in,status,timein_log_date) VALUES ('$studentID', '$time','0','$date')");
                $conn->query("INSERT INTO tbl_attendance (tbl_student_id, time_in,status,timein_log_date) VALUES ('$studentID', '$time','0','$date')");
                $_SESSION['attendance'] = "Attendance has been successfully recorded";
                echo $_SESSION['attendace'];
                header("Location: index.php");
            }
        } else {
            $_SESSION['qrcode_error'] = "Invalid QR Code!..";
            echo $_SESSION['qrcode_error'];

            header('location:index.php');
        }
    } else {
        echo "
            <script>
                alert('Please fill in all fields!');
                 window.location.href = 'http://localhost/ascb_library_attendance/admin/index.php';
            </script>
        ";
    }
}
