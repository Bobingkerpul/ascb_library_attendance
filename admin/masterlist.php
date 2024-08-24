<?php


include('../conn/conn.php');
session_start();

$students = $conn->query("SELECT * FROM tbl_student");
$students = $students->fetch_all(MYSQLI_ASSOC);


if (isset($_POST['filter-course'])) {
    $department = $_POST['department'];
    $school_year = $_POST['school_year'];

    // echo "SELECT * FROM tbl_student WHERE course_section LIKE '%$department%'";
    // exit;
    // SELECT * FROM tbl_student WHERE course_section LIKE '%$department%' AND school_year IS NOT NULL AND school_year != ''"

    $stmt = $conn->query("SELECT *  FROM tbl_student WHERE course_section LIKE '%$department%' AND school_year LIKE'%$school_year%'");
    $result = $stmt->fetch_all(MYSQLI_ASSOC);
    $studClass = $conn->query("SELECT 
    class.id AS CLASS_ID,
    class.adviser AS ADVISER,
    educational.id AS EDUC_ID,
    CONCAT(
        IF(educational.educational_level = 'Senior High School' AND class.strand IS NOT NULL AND class.strand != '', class.strand, ''),
        IF(educational.educational_level = 'Senior High School' AND class.strand IS NOT NULL AND class.strand != '', ' - ', ''),
        IF((educational.educational_level = 'Senior High School' OR educational.educational_level = 'College Degree' OR educational.educational_level = 'Technical and Vocational Education and Training (TVET)') AND class.course IS NOT NULL AND class.course != '', class.course, ''),
        IF((educational.educational_level = 'Senior High School' OR educational.educational_level = 'College Degree' OR educational.educational_level = 'Technical and Vocational Education and Training (TVET)') AND class.course IS NOT NULL AND class.course != '', ' - ', ''),
        gradelevel.year_level,
        ' - ', 
        educational.educational_level, 
        ' - ', 
        class.section
    ) AS COURSE_SECTION
    FROM `class`
    JOIN educational ON class.educational_level = educational.id 
    JOIN gradelevel ON educational.id = gradelevel.education_id
    WHERE class.grade_level = gradelevel.id");

    $studClass = $studClass->fetch_all(MYSQLI_ASSOC);


    $course_section = $conn->query("SELECT * FROM `tbl_student` GROUP BY course_section ORDER BY course_section");
    $course_section = $course_section->fetch_all(MYSQLI_ASSOC);

    $course_section1 = $conn->query("SELECT DISTINCT school_year FROM tbl_student WHERE school_year IS NOT NULL AND school_year != ''");
    $course_section1 = $course_section1->fetch_all(MYSQLI_ASSOC);
} else {
    $stmt = $conn->query("SELECT * FROM tbl_student");
    $result = $stmt->fetch_all(MYSQLI_ASSOC);

    // $studClass = $conn->query("SELECT id, CONCAT(course,' ', strand,'-',section) AS studclass FROM class");
    $studClass = $conn->query("SELECT 
    class.id AS CLASS_ID,
    class.adviser AS ADVISER,
    educational.id AS EDUC_ID,
    CONCAT(
        IF(educational.educational_level = 'Senior High School' AND class.strand IS NOT NULL AND class.strand != '', class.strand, ''),
        IF(educational.educational_level = 'Senior High School' AND class.strand IS NOT NULL AND class.strand != '', ' - ', ''),
        IF((educational.educational_level = 'Senior High School' OR educational.educational_level = 'College Degree' OR educational.educational_level = 'Technical and Vocational Education and Training (TVET)') AND class.course IS NOT NULL AND class.course != '', class.course, ''),
        IF((educational.educational_level = 'Senior High School' OR educational.educational_level = 'College Degree' OR educational.educational_level = 'Technical and Vocational Education and Training (TVET)') AND class.course IS NOT NULL AND class.course != '', ' - ', ''),
        gradelevel.year_level,
        ' - ', 
        educational.educational_level, 
        ' - ', 
        class.section
        ) AS COURSE_SECTION
    FROM `class`
    JOIN educational ON class.educational_level = educational.id 
    JOIN gradelevel ON educational.id = gradelevel.education_id
    WHERE class.grade_level = gradelevel.id");

    $studClass = $studClass->fetch_all(MYSQLI_ASSOC);


    $course_section = $conn->query("SELECT course_section FROM `tbl_student` GROUP BY course_section ORDER BY course_section");
    $course_section = $course_section->fetch_all(MYSQLI_ASSOC);


    $course_section1 = $conn->query("SELECT DISTINCT school_year FROM tbl_student WHERE school_year IS NOT NULL AND school_year != ''");
    $course_section1 = $course_section1->fetch_all(MYSQLI_ASSOC);

    // Handle AJAX request to get adviser
    if (isset($_POST['class_id'])) {
        $classId = $_POST['class_id'];

        // Query to get the adviser based on the class id
        $stmt = $conn->prepare("SELECT adviser FROM class WHERE id = ?");
        $stmt->bind_param("i", $classId);
        $stmt->execute();
        $stmt->bind_result($adviser);
        $stmt->fetch();
        $stmt->close();

        echo $adviser;
        exit;
    }
}


// Delete Student
if (isset($_GET['deletestud'])) {

    $deleteId = $_GET['deletestud'];
    $conn->query("DELETE FROM `tbl_student` WHERE tbl_student_id = $deleteId");
    header('location:masterlist.php');
}

// initialize row counter
$row_count = 1;


if (isset($_POST['updateStudent'])) {
    $id = $_POST['tbl_student_id'];
    $student_name = $_POST['student_name'];
    $student_contact = $_POST['student_contact'];
    $course_section = $_POST['course_section'];
    $gender = $_POST['gender'];
    $gmail = $_POST['updateGmail'];
    $adviserm = $_POST['adviser'];
    $school_year = $_POST['school_yearm'];

    // echo "UPDATE tbl_student SET student_name='$student_name',course_section='$course_section',contact='$student_contact',gender='$gender',adviser='$adviserm' WHERE tbl_student_id = '$id'";
    // exit;
    $conn->query("UPDATE tbl_student SET student_name='$student_name',course_section='$course_section',contact='$student_contact',gender='$gender',gmail='$gmail',adviser='$adviserm',school_year='$school_year' WHERE tbl_student_id = '$id'");

    header('location:masterlist.php');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List of Students</title>
    <script src="../js/libraries/jquery-3.6.1.min.js"></script>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../css/bootstrap.min.css" />


    <!-- Data Table -->
    <link rel="stylesheet" href="../css/jquery.dataTables.css" />
    <!-- <link rel="stylesheet" href="../css/datatables.min.css"> -->
    <!-- <script src="../js/datatables.min.js"></script> -->

    <link rel="stylesheet" href="../css/main.css">
    <!-- Admin CSS -->
    <link rel="stylesheet" href="../css/admin.css" />
    <link rel="stylesheet" href="../css/jquery.betterdropdown.css" />

    <!-- Font Family -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/poppins.css">

    <!-- Get Adviser -->
    <script src="../js/get_adviser.js"></script>
    <style>
        .handler-message-success {
            height: 160px;
            overflow: overlay;
            margin-bottom: 32px;
            padding: 32px;
        }

        .handler-message-success h2 {
            position: sticky;
            top: 0px;
            background: white;
            box-shadow: 0px -27px 0px 24px rgba(255, 255, 255, 1);
            -webkit-box-shadow: 0px -27px 0px 24px rgba(255, 255, 255, 1);
            -moz-box-shadow: 0px -27px 0px 24px rgba(255, 255, 255, 1);
            margin-bottom: 24px;
            padding-bottom: 12px;
        }

        .handler-message {
            height: 160px;
            overflow: overlay;
            margin-bottom: 32px;
            padding: 32px;
            position: relative;
        }

        .handler-message h2 {
            position: sticky;
            top: 0px;
            background: white;
            box-shadow: 0px -27px 0px 24px rgba(255, 255, 255, 1);
            -webkit-box-shadow: 0px -27px 0px 24px rgba(255, 255, 255, 1);
            -moz-box-shadow: 0px -27px 0px 24px rgba(255, 255, 255, 1);
            margin-bottom: 24px;
            padding-bottom: 12px;
        }
    </style>
</head>

<body>
    <?php include('./theme/aside.php'); ?>

    <main class="main-container">
        <div class="message-holder">
            <?php if (isset($_SESSION['exists']) && !empty($_SESSION['exists'])) : ?>
                <div class="handler-message">
                    <div style='color: red;'>
                        <h2>Record Already Exists</h2>
                        <?php foreach ($_SESSION['exists'] as $message) : ?>
                            <p><?= $message ?></p>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php unset($_SESSION['exists']); ?>
            <?php endif; ?>
            <?php if (isset($_SESSION['success']) && !empty($_SESSION['success'])) : ?>
                <div class="handler-message-success">
                    <div style='color: green;'>
                        <h2>Successfully Imported</h2>
                        <?php foreach ($_SESSION['success'] as $message) : ?>
                            <p><?= $message ?></p>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            <?php if (isset($_SESSION['error']) && !empty($_SESSION['error'])) : ?>
                <div class="">
                    <div style='color: red;'>
                        <p><?= $_SESSION['error'] ?></p>
                    </div>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>


        </div>
        <div class="shadow border-card p-5 rounded">
            <div class="student-list">
                <!-- Import Excel File Form -->
                <div class="import-excel mb-5">
                    <form action="import-excel.php" method="post" enctype="multipart/form-data">
                        <div class="import-excel  d-flex flex-row align-items-end justify-content-between mb-4">
                            <div class="form-group">
                                <label for="file" class="form-label poppins-bold"><em>Select Excel File for Student Imports</em></label> <br>
                                <input type="file" name="file" id="file" accept=".xls,.xlsx">
                            </div>
                            <div class="action col-2">
                                <button type="submit" name="import" class="btn btn-primary w-100"> <img src="../img/icons/import.svg" alt="Import Icon"> Import</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="title">
                    <h4>List of Students</h4>
                    <button class="btn btn-primary col-2" data-bs-toggle="modal" data-bs-target="#addStudentModal"><img src="../img/icons/add-v1.svg" alt="Add"> Add Student</button>
                </div>
                <hr>
                <div class="table-container table-responsive">

                    <div class="filter-department mb-5">
                        <form action="masterlist.php" method="post">
                            <div class="d-flex flex-row gap-4 justify-content-between align-items-end">
                                <div class="back" style="width: 10%;">
                                    <button class="btn btn-primary form-control">
                                        <img src=" ../img/icons/back.svg" alt="Back">
                                        <a href="masterlist.php" style="color: #fff !important;"></a>
                                    </button>
                                </div>
                                <div class="form-group" style="width: 70%;">
                                    <label for="department">Select Deparment</label>
                                    <select name="department" id="department" class="form-control">
                                        <option selected hidden>SELECT COURSE / DEPARTMENT</option>
                                        <?php foreach ($course_section as $course) : ?>
                                            <option value="<?= $course['course_section'] ?>"><?= $course['course_section'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group" style="width: 30%;">
                                    <label for="school_yeara">School Year</label>
                                    <select name="school_year" id="school_yeara" class="form-control">
                                        <option selected hidden>SELECT SCHOOL YEAR</option>
                                        <?php foreach ($course_section1 as $course) : ?>
                                            <option value="<?= $course['school_year'] ?>"><?= $course['school_year'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="action col-2">
                                    <button type="submit" id="filter-depart" class="btn btn-primary form-control" name="filter-course" disabled><img src="../img/icons/filter.svg" alt="Filter"> Filter</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <table class="table text-center table-sm" id="studentTable">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Name</th>
                                <th scope="col">Gender</th>
                                <th scope="col">Course & Section</th>
                                <th scope="col">Adviser</th>
                                <th scope="col">Gmail</th>
                                <th scope="col">Student Contact#</th>
                                <th scope="col">School Year</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($result as $row) {
                                $studentID = $row["tbl_student_id"];
                                $studentName = $row["student_name"];
                                $studentCourse = $row["course_section"];
                                $qrCode = $row["generated_code"];
                                $gender = $row["gender"];
                                $contact = $row['contact'];
                                $gmail = $row['gmail'];
                                $school_year = $row['school_year'];
                                $adviser = $row['adviser'];
                            ?>
                                <tr>
                                    <td scope="row" id="studentID-<?= $studentID ?>"><?= $row_count ?></td>
                                    <td id="studentName-<?= $studentID ?>" style=" font-size:14px"><?= $studentName ?></td>
                                    <td id="gender-<?= $studentID ?>" style=" font-size:12px"><?= $gender ?></td>
                                    <td id="studentCourse-<?= $studentID ?>" style="; font-size:12px"><?= $studentCourse ?></td>
                                    <td id="studentCourse-<?= $studentID ?>" style="; font-size:14px"><?= $adviser ?></td>
                                    <td style="font-size: 12px;"><?= $gmail ?></td>
                                    <td style="font-size: 12px;"><?= $contact ?></td>
                                    <td style="font-size: 12px;"><?= $school_year ?></td>
                                    <td>
                                        <div class="action-button" style="    display: flex;justify-content: center;align-items: center;gap: 10px;">
                                            <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#qrCodeModal<?= $studentID ?>">
                                                <img src="../img/qrcode.png" alt="" width="16">
                                            </button>

                                            <!-- QR Modal -->
                                            <div class="modal fade" id="qrCodeModal<?= $studentID ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title"><?= $studentName ?>'s QR Code</h5>
                                                        </div>
                                                        <div class="modal-body text-center">
                                                            <form action="download_student_card.php" method="post">
                                                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?= $qrCode ?>" alt="" width="300">
                                                                <input type="hidden" name="qrcode" value="<?= urlencode($qrCode) ?>">
                                                                <input type="hidden" name="studentname" value="<?= htmlspecialchars($studentName) ?>" readonly>
                                                                <input type="hidden" name="studentcontact" value="<?= htmlspecialchars($contact) ?>" readonly>
                                                                <br>
                                                                <br>
                                                                <button type="submit" name="downloadQR" class="btn btn-primary">Down Load</button>
                                                            </form>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- <button class="btn btn-secondary btn-sm" onclick="updateStudent(<?= $studentID ?>)"></button> -->
                                            <!-- Modal trigger button -->
                                            <button type="button" class="btn btn-primary editStudent" data-bs-toggle="modal" data-bs-target="#editStudent" value="<?= $studentID ?>" style="width: 34px;height: 32px;display: flex;align-items: center;justify-content: center;">
                                                &#128393;
                                            </button>

                                            <button type="button" class="btn btn-danger deletestud" data-href="?deletestud=<?= $studentID  ?>" style="width: 34px;height: 32px;display: flex;align-items: center;justify-content: center;">
                                                <img src="../img/icons/delete.svg" alt="Delete" style="width: 20px;">
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                            <?php
                                $row_count++;
                            }
                            ?>
                        </tbody>
                    </table>
                    <div class="filter-department mb-5">
                        <form action="dataprint.php" method="post" target="_new">
                            <div class="d-flex flex-row gap-4 justify-content-between align-items-end">
                                <div class="form-group" style="width: 70%;">
                                    <label for="department">Click to Print</label>
                                    <input type="hidden" name="department" value="<?= isset($_POST['department']) ? $_POST['department'] : '' ?>">
                                    <button type="submit" class="btn btn-primary col-3" name="print-data"><img src="../img/icons/print.svg" alt="Print Icon"> Print Data</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>




    <!-- Update Student Body -->
    <div class="modal fade" id="editStudent" tabindex="-1" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateStudent">Update Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form action="masterlist.php" method="POST">
                        <input type="hidden" class="form-control" id="updateStudentId" name="tbl_student_id">
                        <div class="d-flex flex-row gap-4 mb-4">
                            <div class="form-group w-100">
                                <label for="updateStudentName" class="form-label">Full Name:</label>
                                <input type="text" class="form-control" id="updateStudentName" name="student_name">
                            </div>
                            <div class="form-group w-100">
                                <label for="updateContactNumber" class="form-label">Contact Number:</label>
                                <input type="text" class="form-control" id="updateContactNumber" name="student_contact">
                            </div>
                        </div>
                        <div class="d-flex flex-row gap-4 mb-4">
                            <div class="form-group w-100">
                                <label for="updateStudentCourse" class="form-label">Course and Section:</label>
                                <select name="course_section" id="updateStudentCourse" class="form-control">
                                    <?php foreach ($studClass as $class) : ?>
                                        <option value="<?= $class['COURSE_SECTION'] ?>"><?= $class['COURSE_SECTION'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group w-100">
                                <label for="updateGmail" class="form-label">Gmail:</label>
                                <input type="email" name="updateGmail" id="updateGmail" class="form-control">
                            </div>
                            <div class="form-group w-100">
                                <label class="form-label">School Year</label>
                                <input list="school_yearm" name="school_yearm" id="school_yearm" class="form-control">
                                <datalist id="school_yearm">
                                    <option value="SY 2023-2024">
                                    <option value="SY 2024-2025">
                                    <option value="SY 2025-2026">
                                    <option value="SY 2026-2027">
                                </datalist>
                            </div>
                        </div>
                        <div class="d-flex flex-row gap-4 mb-4">
                            <div class="form-group w-100">
                                <label for="adviser">Adviser:</label>
                                <input type="text" name="adviser" id="adviserm" class="form-control mt-2">
                            </div>
                            <div class="form-group w-100">
                                <span for="updateGender">Gender:</span>
                                <br>
                                <div class="d-flex flex-column " style="width: 26%;">
                                    <div class="form-group d-flex justify-content-between">
                                        <label for="male" class="form-label">Male</label>
                                        <input type="radio" name="gender" id="male" value="male">
                                    </div>
                                    <div class="form-group d-flex justify-content-between">
                                        <label for="female" class="form-label">Female</label>
                                        <input type="radio" name="gender" id="female" value="female">
                                    </div>
                                    <div class="form-group d-flex justify-content-between">
                                        <label for="lgbtq" class="form-label">LGBTQ</label>
                                        <input type="radio" name="gender" id="lgbtq" value="lgbtq">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="modal-footer">
                            <button type="submit" name="updateStudent" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addStudentModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="addStudent" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addStudent">Add Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form action="add-student.php" method="POST">
                        <input type="hidden" name="course_id" id="course_id">
                        <div class="d-flex flex-row justify-content-between gap-4 w-100">
                            <div class="w-100 shadow p-4 rounded border-card">
                                <div class="d-flex flex-row gap-4">
                                    <div class="form-group w-100">
                                        <label for="studentName">Full Name:</label>
                                        <input type="text" class="form-control mt-2" id="studentName" name="student_name">
                                    </div>
                                    <div class="form-group w-100">
                                        <label for="adviser">Adviser:</label>
                                        <input type="text" name="adviser" id="adviser" class="form-control mt-2" readonly>
                                    </div>
                                </div>
                                <div class="d-flex flex-row gap-4 mt-4">
                                    <div class="form-group w-100">
                                        <label for="contact">Contact Number:</label>
                                        <input type="tel" name="contact" id="contact" class="form-control mt-2">
                                    </div>
                                    <div class="form-group w-100">
                                        <label for="gmail">Gmail:</label>
                                        <input type="email" name="gmail" id="gmail" class="form-control mt-2">
                                    </div>
                                    <div class="form-group w-100">
                                        <label class="form-label">School Year</label>
                                        <input list="school_year_add" name="school_year" id="school_year" class="form-control">
                                        <datalist id="school_year_add">
                                            <option value="SY 2023-2024">
                                            <option value="SY 2024-2025">
                                        </datalist>
                                    </div>
                                </div>
                                <div class="d-flex flex-row gap-4 mt-4">
                                    <div class="form-group w-100">
                                        <label for="studentCourse">Course and Section:</label><br />
                                        <select name="course_section_s" id="course_section_s" class="form-control mt-2">
                                            <option disabled selected hidden>Select Course and Section</option>
                                            <?php foreach ($studClass as $class) : ?>
                                                <option value="<?= $class['COURSE_SECTION'] ?>" data-id="<?= $class['CLASS_ID'] ?>"><?= $class['COURSE_SECTION'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group w-100">
                                        <span>Gender:</span>
                                        <div class="gender" style="width:26%;">
                                            <div class="d-flex justify-content-between mt-2">
                                                <label for="male">Male</label>
                                                <input type="radio" id="male" name="gender" value="male" />
                                            </div>
                                            <div class="d-flex justify-content-between ">
                                                <label for="female">Female</label>
                                                <input type="radio" id="female" name="gender" value="female" />
                                            </div>
                                            <div class="d-flex justify-content-between ">
                                                <label for="lgbtq">LGBTQ</label>
                                                <input type="radio" id="lgbtq" name="gender" value="lgbtq" />
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <br>
                                <button type="button" class="btn btn-primary form-control qr-generator mt-5" onclick="generateQrCode()" id="addstudent">Generate QR Code</button>
                            </div>

                            <div class="w-100 qr-con shadow p-4 rounded border-card" style="display: none;">
                                <div class="text-center">
                                    <input type="hidden" class="form-control" id="generatedCode" name="generated_code">
                                    <h4>Hello <span id="studfullname"></span></h4>
                                    <p>Take a pic with your qr code Please 😊.</p>
                                    <img class="mb-5 mt-4" src="" id="qrImg" alt="">
                                </div>
                                <div class="modal-close d-flex gap-2" style="display: none;">
                                    <button type="button" class="btn btn-warning form-control" data-bs-dismiss="modal">
                                        Close
                                    </button>
                                    <button type="submit" class="btn btn-primary form-control" name="submitstud">Add List</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <!-- Bootstrap JS -->
    <!-- <script src="../js/libraries/jquery.slim.min.js"></script> -->
    <script src="../js/libraries/bootstrap.bundle.min.js"></script>
    <script src="../js/libraries/popper.min.js"></script>
    <script src="../js/libraries/bootstrap.min.js"></script>

    <script src="../js/libraries/jquery.betterdropdown.js"></script>



    <!-- Data Table -->
    <script src="../js/libraries/jquery.dataTables.js"></script>


    <!-- Sweet Alert -->
    <script src="../js/libraries/sweetalert2@11.js"></script>

    <script>
        $(document).ready(function() {
            function filter() {
                var department = $('#department').val();
                var schoolYear = $("#school_yeara").val();
                return department && schoolYear;
            }

            $(' #school_yeara, #department').on('change', function() {
                $('#filter-depart').prop('disabled', !filter());
            });


            // Delete Student
            $('.deletestud').click(function(e) {
                // alert(this.value);
                const href = $(this).data('href');
                // alert(href);
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You woun't able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButton: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.location.href = href
                    }
                })
            })


            // Edit Student
            $('.editStudent').click(function() {
                // alert(this.value);
                var request = $.ajax({
                    url: "studentinfo.php",
                    method: "GET",
                    data: {
                        id: this.value
                    },
                    dataType: "json"
                });

                request.done(function(msg) {
                    console.log(msg);
                    // alert(msg.adviser);
                    $("#updateGmail").val(msg.gmail);
                    $("#updateStudentId").val(msg.tbl_student_id);
                    $('#updateStudentName').val(msg.student_name);
                    $('#updateContactNumber').val(msg.contact);
                    $('#adviserm').val(msg.adviser);
                    $('#school_yearm').val(msg.school_year);
                    // $('#updateStudentCourse').val(msg.course_section);


                    var courseSection = msg.course_section;
                    var updateCourse = $('#updateStudentCourse');

                    updateCourse.append(`<option value='${courseSection}'>${courseSection}</option>`);
                    updateCourse.val(courseSection);

                    if (msg.gender === 'male') {
                        $('#male').prop('checked', true);
                    } else if (msg.gender === 'female') {
                        $('#female').prop('checked', true);
                    } else {
                        $('#lgbtq').prop('checked', true);
                    }

                });
            })

            $('#studentTable').DataTable();
        });


        function generateRandomCode(length) {
            const characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
            let randomString = '';

            for (let i = 0; i < length; i++) {
                const randomIndex = Math.floor(Math.random() * characters.length);
                randomString += characters.charAt(randomIndex);
            }

            return randomString;
        }

        function generateQrCode() {
            const qrImg = document.getElementById('qrImg');
            const generatedCodeInput = document.getElementById('generatedCode');
            const studentNameElement = document.getElementById('studentName');
            const studentCourseElement = document.getElementById('studentCourse');
            const modalCloseElement = document.querySelector('.modal-close');
            const qrConElement = document.querySelector('.qr-con');
            const qrGeneratorElement = document.querySelector('.qr-generator');

            let text = generateRandomCode(10);
            generatedCodeInput.value = text;

            if (text === "") {
                alert("Please enter text to generate a QR code.");
                return;
            } else {
                const apiUrl = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${encodeURIComponent(text)}`;

                qrImg.src = apiUrl;

                if (studentNameElement) {
                    studentNameElement.style.pointerEvents = 'none';
                }
                if (studentCourseElement) {
                    studentCourseElement.style.pointerEvents = 'none';
                }
                if (modalCloseElement) {
                    modalCloseElement.style.display = '';
                }
                if (qrConElement) {
                    qrConElement.style.display = '';
                }
                if (qrGeneratorElement) {
                    qrGeneratorElement.style.display = 'none';
                }
            }
        }
    </script>
</body>

</html>