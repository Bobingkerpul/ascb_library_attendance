<?php
require_once '../conn/conn.php';

$id = $_GET['id'];

$stmt = $conn->query("SELECT * FROM tbl_attendance LEFT JOIN tbl_student ON tbl_student.generated_code = tbl_attendance.tbl_student_id WHERE tbl_attendance_id = '$id'");
$stmt = $stmt->fetch_array(MYSQLI_ASSOC);

echo json_encode($stmt);
