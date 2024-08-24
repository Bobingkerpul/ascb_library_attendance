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


if (isset($_POST['savecourse'])) {

    $course = $_POST['course'];
    $description = $_POST['description'];

    $conn->query("INSERT INTO courses(course, description) VALUES ('$course','$description')");

    header("location: course.php");
} else {

    $courses =  $conn->query("SELECT * FROM courses");
    $courses1 = $courses->fetch_all(MYSQLI_ASSOC);
}

if (isset($_GET['editcourse'])) {
    $id = $_GET['editcourse'];

    $courses =  $conn->query("SELECT * FROM courses WHERE id = '$id'");
    $courseedit = $courses->fetch_assoc();

    $hiddenid = $courseedit['id'];
    $course = $courseedit['course'];
    $description = $courseedit['description'];
} else if (isset($_POST['update'])) {

    $id = $_POST['id'];
    $course = $_POST['course'];
    $description = $_POST['description'];

    // echo "UPDATE courses SET course='$course',description='$description' WHERE id = '$id'";
    // exit;
    $conn->query("UPDATE courses SET course='$course',description='$description' WHERE id = '$id'");
    header("location: course.php");
}

// Delete Course
if (isset($_GET['deletecourse'])) {

    $id = $_GET['deletecourse'];

    $conn->query("DELETE FROM courses WHERE id = '$id'");
    header("location: course.php");
}

// Initialize row counter
$row_count = 1;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course</title>
    <script src="../js/libraries/jquery-3.6.1.min.js"></script>

    <!-- Bootstrap CSS -->
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"> -->
    <link rel="stylesheet" href="../css/bootstrap.min.css" />


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
        <div class="d-flex flex-row">
            <div class="border-card col-4 p-0 shadow rounded mx-4">
                <div class="heading p-2">
                    <h4 class="m-0">Course Form</h4>
                </div>
                <div class="p-4">
                    <form method="post" action="course.php">
                        <input type="hidden" name="id" value="<?= $hiddenid ?>">
                        <div class="form-group mb-2">
                            <label for="course" class="form-label">Course</label>
                            <input type="text" class="form-control" id="course" name="course" placeholder="Enter Course" required value="<?php echo isset($course) ? $course : '' ?>">
                            <small id="emailHelp" class="form-text text-muted" style="font-size:12px"> <em>Input the valid Course only</em></small>
                        </div>
                        <div class="form-group mb-4">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required><?php echo isset($description) ? $description : '' ?></textarea>
                        </div>
                        <div class="form-group">
                            <?php if (isset($_GET['editcourse'])) : ?>
                                <button type="submit" name="update" class="btn btn-primary">Update</button>
                                <a href="course.php">
                                    <button type="submit" class="btn btn-primary">Cancel</button>
                                </a>
                            <?php else : ?>
                                <button type="submit" name="savecourse" class="btn btn-primary">Save</button>
                                <button type="reset" class="btn btn-primary">Cancel</button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
            <div class="border-card shadow rounded" style="width: 60%;">
                <div class="heading p-2">
                    <h4 class="m-0">Course List</h4>
                </div>
                <div class="p-4">
                    <table class="table  table-sm" id="attendanceTable">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>Course & Description</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            foreach ($courses1 as $course) {
                            ?>
                                <tr>
                                    <td scope="row"><?= $row_count ?></td>
                                    <td>
                                        <div class="course_desc">
                                            <h6 class="m-0"><?= $course['course'] ?></h6>
                                            <small style="font-size: 12px;"><?= $course['description'] ?></small>
                                        </div>
                                    <td>
                                        <div class="action-button" style="display: flex;align-items: center;justify-content: center;gap: 8px;">
                                            <a href="?editcourse=<?= $course['id'] ?>">
                                                <button type="submit" class="btn btn-primary" style="width: 34px;height: 31px;display: flex;justify-content: center;align-items: center;"> <img src="../img/icons/edit.svg" alt="Edit" style="width: 20px;"> </button>
                                            </a>
                                            <!-- Modal trigger button -->
                                            <!-- <a href="?deletecourse=<?= $course['id'] ?>"> -->
                                            <button type="button" class="btn btn-danger deleteco" data-href="?deletecourse=<?= $course['id'] ?>" style="width: 34px;height: 31px;display: flex;justify-content: center;align-items: center;">
                                                <img src="../img/icons/delete.svg" alt="Delete" style="width: 20px;">
                                            </button>
                                            <!-- </a> -->
                                            <!-- onclick="deleteco(<?= $course['id'] ?>)" -->
                                        </div>
                                    </td>
                                </tr>
                            <?php
                                $row_count++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>



    <!-- Bootstrap Modal JS -->
    <script src="../js/libraries/bootstrap.bundle.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="../js/libraries/jquery.slim.min.js"></script>
    <script src="../js/libraries/popper.min.js"></script>
    <script src="../js/libraries/bootstrap.min.js"></script>

    <!-- Data Table -->
    <script src="../js/libraries/jquery.dataTables.js"></script>

    <!-- Sweet Alert -->
    <script src="../js/libraries/sweetalert2@11.js"></script>


    <script>
        $(document).ready(function() {
            $('#attendanceTable').DataTable();
            // alert();

            $('.deleteco').on('click', function(e) {
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
        });
    </script>

</body>

</html>