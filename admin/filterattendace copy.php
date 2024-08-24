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


if (isset($_POST['filter'])) {

    $from = $_POST['from'];
    $to = $_POST['to'];

    $stmt = $conn->query("SELECT * FROM tbl_attendance LEFT JOIN tbl_student ON tbl_student.generated_code = tbl_attendance.tbl_student_id WHERE log_date >= '$from' AND log_date <= '$to'");
    $result = $stmt->fetch_all(MYSQLI_ASSOC);
}


date_default_timezone_set('Asia/Manila');
// $timeIn =  date("Y-m-d H:i:s");
$date = date("Y-m-d");

// // Query to select log dates from tbl_attendance
// $sqllog = "SELECT log_date FROM tbl_attendance";
// $resultlog = $conn->query($sqllog);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Report</title>

    <!-- Bootstrap CSS -->
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"> -->
    <link rel="stylesheet" href="../css/bootstrap.min.css" />

    <script src="../js/libraries/jquery-3.6.1.min.js"></script>

    <!-- Data Table -->
    <link rel="stylesheet" href="../css/jquery.dataTables.css" />

    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/admin.css">

    <script src="../js/libraries/chartjs.js"></script>
</head>

<body>
    <?php include('./theme/aside.php') ?>
    <main class="main-container">
        <div class="d-flex flex-row justify-content-between gap-4 mt-5">
            <div class="border-card shadow rounded p-4" style="width: 100%;">
                <h4 class="mb-4">Attendance Report</h4>

                <div class="form-filter mb-5">
                    <form action="filterattendace.php" method="POST">
                        <div class="d-flex flex-row gap-4 align-items-end">
                            <div class="form-group w-100">
                                <label for="from">From</label>
                                <input type="date" name="from" id="from" class="form-control" required>
                            </div>
                            <div class="form-group w-100">
                                <label for="to">To</label>
                                <input type="date" name="to" id="to" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary form-control" name="filter">Filter</button>
                            </div>
                        </div>
                    </form>
                    <hr>
                </div>
                <?php if (!isset($result)) : ?>
                    <h4 class="text-center">Set a Date to Customize Student Attendance Filtering</h4>
                <?php else : ?>
                    <div class="table-container table-responsive">
                        <table class="table text-center table-sm mt-4 table-hover" id="attendanceTable">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Course & Section</th>
                                    <th scope="col">Time In</th>
                                    <th scope="col">Time Out</th>
                                    <th scope="col">Log Date</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                foreach ($result as $row) {
                                    $attendanceID = $row["tbl_attendance_id"];
                                    $studentName = $row["student_name"];
                                    $studentCourse = $row["course_section"];
                                    $timeIn = $row["time_in"];
                                    $timeOut = $row["time_out"];
                                    $logdate = $row["log_date"];
                                ?>

                                    <tr>
                                        <td scope="row"><?= $attendanceID ?></td>
                                        <td><?= $studentName ?></td>
                                        <td><?= $studentCourse ?></td>
                                        <td style="font-size: 12px;"><?= $timeIn ?> </td>
                                        <td style="font-size: 12px;"><?= $timeOut ?> </td>
                                        <td>
                                            <?= $logdate ?>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-row gap-4">
                                                <button class="btn btn-warning">View</button>
                                                <button type="button" class="btn btn-primary">Edit</button>
                                            </div>
                                        </td>
                                    </tr>

                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

            </div>

        </div>


        <!-- Modal trigger button -->
        <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#issued">
            Launch
        </button>


        <!-- Modal trigger button issued book-->
        <!-- <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#yawa">
            Launch
        </button> -->
    </main>



    <!-- Add Modal -->
    <!-- <div class="modal fade" id="yawa" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="addStudent" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addStudent">Add Student</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">

                </div>
            </div>
        </div>
    </div> -->









    <!-- Bootstrap JS -->
    <script src="../js/libraries/jquery.slim.min.js"></script>
    <script src="../js/libraries/popper.min.js"></script>
    <script src="../js/libraries/bootstrap.min.js"></script>

    <!-- Data Table -->
    <script src="../js/libraries/jquery.dataTables.js"></script>


    <script>
        // Table
        $(document).ready(function() {
            $('#attendanceTable').DataTable();
        });
    </script>
</body>

</html>