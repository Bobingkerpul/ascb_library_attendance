<?php

session_start();
require_once '../conn/conn.php';

date_default_timezone_set('Asia/Manila');

$date = date('Y-m-d');

$_SESSION["date"] = $date;


if (isset($_SESSION["user"])) {
    if (($_SESSION["user"]) == "" or $_SESSION["usertype"] != 'a') {
        header("location: ../index.php");
    }
} else {
    header("location: ../index.php");
}
// Select Courses
$courses =  $conn->query("SELECT * FROM courses");
$courses = $courses->fetch_all(MYSQLI_ASSOC);

// Select Educational Level
$educational =  $conn->query("SELECT * FROM educational");
$educational = $educational->fetch_all(MYSQLI_ASSOC);


$educational_levels = $conn->query("SELECT DISTINCT educational_level FROM educational");
$educational_levels = $educational_levels->fetch_all(MYSQLI_ASSOC);


// Insert Data
if (isset($_POST['saveclass'])) {

    $course = $_POST['course'];
    $level = $_POST['level'];
    $section = $_POST['section'];
    $educational = $_POST['educational'];
    $year_level = $_POST['year_level'];
    $strandname = $_POST['strandname'];
    $student_adviser = $_POST['student_adviser'];

    $conn->query("INSERT INTO class(course,strand, section,educational_level,grade_level,adviser) VALUES ('$course', '$strandname','$section','$educational','$year_level','$student_adviser')");

    header('location:class.php');
}

if (isset($_GET['editclass'])) {
    $id = $_GET['editclass'];


    $classes =  $conn->query("SELECT 
    class.id AS CLASS_ID,
    educational.id AS EDUC_ID,
    class.strand AS STRAND,
    class.course AS COURSE,
    class.adviser AS ADVISER,
    gradelevel.year_level AS YEAR_LEVEL,
    educational.educational_level AS EDUCATIONAL,
    class.section AS SECTION
    FROM `class`
    JOIN educational ON class.educational_level = educational.id 
    JOIN gradelevel ON educational.id = gradelevel.education_id
    WHERE class.grade_level = gradelevel.id AND class.id = '$id'");

    $classedit = $classes->fetch_assoc();
    $hiddenid = $classedit['CLASS_ID'];
    $course = $classedit['COURSE'];
    $strandname = $classedit['STRAND'];
    $educationalV = $classedit['EDUCATIONAL'];
    $educationalI = $classedit['EDUC_ID'];
    $yearLevel = $classedit['YEAR_LEVEL'];
    $level = $classedit['YEAR_LEVEL'];
    $section = $classedit['SECTION'];
    $student_adviser = $classedit['ADVISER'];
} else if (isset($_POST['updateclass'])) {
    $id = $_POST['id'];

    $course = $_POST['course'];
    $level = $_POST['level'];
    $section = $_POST['section'];
    $educational = $_POST['educational'];
    $year_level = $_POST['year_level'];
    $strandname = $_POST['strandname'];
    $student_adviser = $_POST['student_adviser'];

    $conn->query("UPDATE class SET course='$course',strand='$strandname',section='$section', educational_level='$educational',grade_level='$year_level',adviser='$student_adviser' WHERE id = '$id'");
    header('location:class.php');
}

if (isset($_GET['deleteclass'])) {
    $deleteclass = $_GET['deleteclass'];


    $conn->query("DELETE FROM class WHERE id = '$deleteclass'");
    header('location:class.php');
}

// Initialize row counter
$row_count = 1;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../css/bootstrap.min.css" />

    <script src="../js/libraries/jquery-3.6.1.min.js"></script>

    <!-- Data Table -->
    <link rel="stylesheet" href="../css/jquery.dataTables.css" />

    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/admin.css">
    <!-- Font Family -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/poppins.css">
</head>

<body>
    <?php include('./theme/aside.php') ?>
    <main class="main-container">
        <div class="d-flex flex-column gap-4">
            <div class="quote py-2" style="background-color: #061651">
                <marquee onmouseover="this.stop();" onmouseout="this.start();">
                    <h5 class="m-0 text-white"><q><em>Librarians are the true keepers of knowledge and the guardians of stories.</em></q>
                    </h5>
                </marquee>
            </div>
            <hr>
            <div class="d-flex flex-row gap-4">
                <div class="border-card col-12 p-0 shadow rounded">
                    <div class="heading p-2">
                        <h4 class="m-0">CLASS FORM</h4>
                    </div>
                    <div class="p-4">
                        <?php
                        $educationalI = isset($educationalI) ? $educationalI : "";
                        $educationalV = isset($educationalV) ? $educationalV : "";
                        $yearLevel = isset($yearLevel) ? $yearLevel : "";
                        ?>
                        <form method="post" action="class.php">
                            <input type="hidden" name="id" value="<?= $hiddenid ?>">
                            <div class="d-flex flex-row gap-4">
                                <div class="form-group mb-4 w-100">
                                    <label for="educational" class="form-label poppins-bold">Educational Level</label>
                                    <select name="educational" id="educational" class="form-control">
                                        <option selected hidden disabled>Select Educational Level</option>
                                        <?php if (isset($_GET['editclass'])) : ?>
                                            <option selected hidden value="<?= isset($educationalI) ? $educationalI : "" ?>"><?= isset($educationalV) ? $educationalV : "" ?></option>
                                        <?php endif; ?>
                                        <?php foreach ($educational as $educ) : ?>
                                            <option value="<?= $educ['id'] ?>" <?= ($educ['id'] == $educationalI) ? 'selected' : '' ?>> <?= $educ['educational_level'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group mb-4 w-100">
                                    <label for="student_adviser" class="form-label poppins-bold">Adviser</label>
                                    <input type="text" name="student_adviser" id="student_adviser" class="form-control" placeholder="Input Adviser" value="<?= isset($student_adviser) ? $student_adviser : "" ?>">
                                </div>
                            </div>
                            <div class="d-flex flex-row gap-4">
                                <div class="form-group mb-4 w-100">
                                    <label for="year_level" class="form-label poppins-bold">Grade Level</label>
                                    <select name="year_level" id="year_level" class="form-control">
                                        <option selected hidden disabled>Please select grade level</option>
                                        <?php if (isset($_GET['editclass'])) : ?>
                                            <option selected hidden value="<?= $yearLevel ?>"><?= $yearLevel ?></option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="form-group mb-4 w-100">
                                    <label for="strandname" class="form-label poppins-bold">Senior High Strand</label>
                                    <select name="strandname" id="strandname" class="form-control" disabled>
                                        <option selected hidden disabled>Please select strand name</option>
                                        <option value="TVLIA">TVL-IA</option>
                                        <option value="TVLHE">TVL-HE</option>
                                        <option value="TVLICT">TVL-ICT</option>
                                        <option value="STEM">STEM</option>
                                        <option value="HUMSS">HUMSS</option>
                                        <option value="GAS">GAS</option>
                                        <option value="ABM">ABM</option>
                                    </select>
                                </div>
                            </div>
                            <div class="d-flex flex-row gap-4">
                                <div class="form-group mb-4 w-100">
                                    <label for="course" class="form-label poppins-bold">Course</label>
                                    <select name="course" class="form-control" id="course" disabled>
                                        <?php if (isset($_GET['editclass'])) : ?>
                                            <option selected hidden value="<?= isset($course) ? $course : "" ?>"><?= isset($course) ? $course : "" ?></option>
                                        <?php else : ?>
                                            <option selected disabled hidden>Select Course</option>
                                        <?php endif; ?>
                                        <?php foreach ($courses as $course) : ?>
                                            <option value="<?= $course['course'] ?>"> <?= $course['course'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group mb-4 w-100">
                                    <label for="section" class="form-label poppins-bold">Department / Section</label>
                                    <input type="section" class="form-control" id="course" name="section" placeholder="Enter Section" required value="<?= isset($section) ? $section : "" ?>">
                                </div>
                            </div>
                            <div class="form-group mt-4">
                                <?php if (isset($_GET['editclass'])) : ?>
                                    <div class="d-flex flex-row gap-4">
                                        <button type="submit" name="updateclass" class="btn btn-primary w-100">Update</button>
                                        <a href="course.php" class="w-100">
                                            <button type="submit" class="btn btn-primary w-100">Cancel</button>
                                        </a>
                                    </div>
                                <?php else : ?>
                                    <div class="d-flex flex-row gap-4">
                                        <button type="submit" name="saveclass" class="btn btn-primary w-100">Save</button>
                                        <button type="reset" class="btn btn-primary w-100">Cancel</button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="text-quote">
                </div>
            </div>
            <hr>
            <div class="border-card shadow rounded w-100">
                <div class="heading p-2">
                    <h4 class="m-0">CLASS LIST</h4>
                </div>
                <div class="p-4">
                    <?php foreach ($educational_levels as $level) : ?>
                        <?php
                        $educational = $level['educational_level'];

                        $classes = $conn->query("SELECT 
                            class.id AS CLASS_ID,
                            class.course AS COURSE,
                            class.adviser AS ADVISER,
                            gradelevel.year_level AS YEAR_LEVEL,
                            class.strand AS STRAND,
                            educational.educational_level AS EDUCATIONAL,
                            class.section AS SECTION
                            FROM `class`
                            JOIN educational ON class.educational_level = educational.id 
                            JOIN gradelevel ON educational.id = gradelevel.education_id
                            WHERE class.grade_level = gradelevel.id AND educational.educational_level = '$educational'");
                        $classes = $classes->fetch_all(MYSQLI_ASSOC);
                        ?>
                        <br>
                        <hr style="border-top: 1px dashed red; opacity:1">
                        <h4><strong><?php echo $educational; ?></strong></h4>
                        <hr>
                        <table class="classTable table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Course / Strand</th>
                                    <th>Grade Level</th>
                                    <!-- <th>Educational Level</th> -->
                                    <th>Department / Section</th>
                                    <th>Adviser</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($classes as $class) : ?>
                                    <tr>
                                        <td><?= $row_count ?></td>
                                        <td><?php echo $class['COURSE']; ?> <?php echo $class['STRAND']; ?></td>
                                        <td><?php echo $class['YEAR_LEVEL']; ?></td>
                                        <!-- <td><?php echo $class['EDUCATIONAL']; ?></td> -->
                                        <td><?php echo $class['SECTION']; ?></td>
                                        <td><?php echo $class['ADVISER']; ?></td>
                                        <td>
                                            <div class="action-button" style="display: flex;align-items: center;justify-content: center;gap: 8px;">
                                                <a href="?editclass=<?= $class['CLASS_ID'] ?>">
                                                    <button type="submit" class="btn btn-primary" style="width: 34px;height: 31px;display: flex;justify-content: center;align-items: center;"> <img src="../img/icons/edit.svg" alt="Edit" style="width: 20px;"></button>
                                                </a>
                                                <button type="button" class="btn btn-danger deletecla" data-href="?deleteclass=<?= $class['CLASS_ID']; ?>" style="width: 34px;height: 31px;display: flex;justify-content: center;align-items: center;">
                                                    <img src="../img/icons/delete.svg" alt="Delete" style="width: 20px;">
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php $row_count++;
                                endforeach; ?>
                            </tbody>
                        </table>
                    <?php endforeach; ?>

                </div>
            </div>
        </div>
    </main>




    <!-- Bootstrap Modal JS -->
    <script src="../js/libraries/bootstrap.bundle.min.js"></script>

    <!-- Bootstrap JS -->
    <!-- <script src="../js/libraries/jquery.slim.min.js"></script> -->
    <script src="../js/libraries/popper.min.js"></script>
    <script src="../js/libraries/bootstrap.min.js"></script>
    <script src="../js/libraries/jquery.betterdropdown.js"></script>

    <!-- Data Table -->
    <script src="../js/libraries/jquery.dataTables.js"></script>


    <!-- Sweet Alert -->
    <script src="../js/libraries/sweetalert2@11.js"></script>

    <script>
        $(document).ready(function() {

            // alert();


            $('.deletecla').on('click', function(e) {
                // alert('hello');
                e.preventDefault();
                const href = $(this).data('href');

                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.location.href = href;
                    }
                });
            });

            function loadYearLevels(educationalId) {
                $.ajax({
                    url: 'year_level.php',
                    method: 'GET',
                    data: {
                        id: educationalId
                    },
                    dataType: 'json',
                    success: function(response) {
                        $("#year_level").html(response.options);

                        if (response.disabled_course) {
                            $("#course").prop('disabled', true);
                        } else {
                            $("#course").prop('disabled', false);
                        }

                        var selectedEducationalLevel = $("#educational option:selected").text();
                        // console.log("Selected Educational Level: ", selectedEducationalLevel);
                        if (selectedEducationalLevel.trim() === "Senior High School") {
                            $("#strandname").prop('disabled', false);
                        } else {
                            $("#strandname").prop('disabled', true);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            }

            var selectedEducational = $("#educational").val();
            if (selectedEducational) {
                loadYearLevels(selectedEducational);
            }

            $("#educational").change(function() {
                var educationalId = $(this).val();
                loadYearLevels(educationalId);
            });

            $('.classTable').DataTable();

        });
    </script>
</body>

</html>