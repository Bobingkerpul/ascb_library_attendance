<?php

require_once '../conn/conn.php';


if (isset($_POST['submitstud'])) {

    $student_name = $_POST['student_name'];
    $course_section = $_POST['course_section'];
    $contact = $_POST['contact'];
    $gender  = $_POST['gender'];
    $generated_code  = $_POST['generated_code'];
    $gmail  = $_POST['gmail'];
    $adviser  = $_POST['adviser'];
    $school_year  = $_POST['school_year'];

    // echo "INSERT INTO tbl_student(student_name, course_section, contact, gender, generated_code, gmail) VALUES ('$student_name','$course_section','$contact','$gender','$generated_code', '$gmail')";
    // exit;

    $conn->query("INSERT INTO tbl_student(student_name, course_section, contact, gender, generated_code, gmail,adviser,school_year) VALUES ('$student_name','$course_section','$contact','$gender','$generated_code', '$gmail','$adviser','$school_year')");

    header('location:masterlist.php');
}
