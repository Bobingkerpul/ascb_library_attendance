<?php

include('../conn/conn.php');
// SELECT * FROM `tbl_student`

$id = $_GET['id'];

$students = $conn->query("SELECT * FROM tbl_student WHERE tbl_student_id = '$id'");
$students = $students->fetch_array(MYSQLI_ASSOC);
echo json_encode($students);
