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

// --------------------------------------------------------------
// Filter By Gender in Table
if (isset($_POST['filter-gender'])) {

    $gender = $_POST['gender'];
    $stmt = $conn->query("SELECT * FROM tbl_attendance LEFT JOIN tbl_student ON tbl_student.generated_code = tbl_attendance.tbl_student_id WHERE gender = '$gender' ");

    $result = $stmt->fetch_all(MYSQLI_ASSOC);
} else {

    $stmt = $conn->query("SELECT * FROM tbl_attendance LEFT JOIN tbl_student ON tbl_student.generated_code = tbl_attendance.tbl_student_id ORDER BY timein_log_date DESC");

    $result = $stmt->fetch_all(MYSQLI_ASSOC);
}
date_default_timezone_set('Asia/Manila');
// $timeIn =  date("Y-m-d H:i:s");
$date = date("Y-m-d");


// --------------------------------------------------------------
// Query to select log dates from tbl_attendance Attendance Chart
$sqllog = "SELECT DATE_FORMAT(timein_log_date, '%Y-%m') as log_month, WEEK(timein_log_date) as log_week, COUNT(*) as attendance_count 
           FROM tbl_attendance 
           GROUP BY log_month, log_week 
           ORDER BY log_month, log_week";
$resultlog = $conn->query($sqllog);


// Array to store log dates
$logData = [];
if ($resultlog->num_rows > 0) {
    while ($row = $resultlog->fetch_assoc()) {
        // Store the week number and attendance count
        $logData[] = [
            "log_month" => $row["log_month"],
            "log_week" => $row["log_week"],
            "attendance_count" => $row["attendance_count"]
        ];
    }
}

// --------------------------------------------------------------
// Gender Chart
$sqlgen = "SELECT gender, COUNT(*) as count FROM tbl_student WHERE gender IS NOT NULL GROUP BY gender";
$resultgen = $conn->query($sqlgen);

// Store the result in an associative array
$genderData = [];
$genderColor = [];
while ($row = $resultgen->fetch_assoc()) {
    // $genderData[$row['gender']] = $row['count'];
    $genderData[$row['gender']] = $row['count'];
    $gender = $row['gender'];

    switch (strtolower(trim($gender))) {
        case 'lgbtq':
            $genderColor[] = 'rgba(72, 202, 228, 1)';
            break;
        case 'male':
            $genderColor[] = 'rgba(3, 4, 94, 1)';
            break;
        case 'female':
            $genderColor[] = 'rgba(0, 119, 182, 1)';
            break;
    }
}

// --------------------------------------------------------------
// Department Chart
$department_query = "SELECT course_section, COUNT(*) as count FROM tbl_student GROUP BY course_section";
$department_result = $conn->query($department_query);

$course_year_level = [];
$department_labels = [];
$department_counts = [];
$department_colors = [];

if ($department_result->num_rows > 0) {
    while ($row = $department_result->fetch_assoc()) {
        $course_parts = explode("-", $row['course_section']);

        $year_level = isset($course_parts[1]) ? $course_parts[1] : 'Unknown'; // Handle missing year level

        $course_year_level[] = [
            'year_level' => $year_level,
            'course_section' => $row['course_section']
        ];

        $department_labels[] = $course_parts[0];
        $department_counts[] = $row['count'];

        // Set colors based on course (adjust as per your requirement)
        switch (strtolower(trim($course_parts[0]))) {
            case 'grade 1':
                $department_colors[] = 'rgba(3, 4, 94,1)';
                break;
            case 'grade 2':
                $department_colors[] = 'rgba(2, 62, 138,1)';
                break;
            case 'grade 3':
                $department_colors[] = 'rgba(0, 119, 182,1)';
                break;
            case 'grade 4':
                $department_colors[] = 'rgba(0, 150, 199,1)';
                break;
            case 'grade 5':
                $department_colors[] = 'rgba(0, 180, 216,1)';
                break;
            case 'grade 6':
                $department_colors[] = 'rgba(72, 202, 228,1)';
                break;
            case 'grade 7':
                $department_colors[] = 'rgba(144, 224, 239,1)';
                break;
            case 'grade 8':
                $department_colors[] = 'rgba(2, 62, 138,1)';
                break;
            case 'grade 9':
                $department_colors[] = 'rgba(0, 180, 216,1)';
                break;
            case 'grade 10':
                $department_colors[] = 'rgba(144, 224, 239,1)';
                break;
            case 'tvlia':
                $department_colors[] = 'rgba(0, 180, 216,1)';
                break;
            case 'tvlhe':
                $department_colors[] = 'rgba(0, 119, 182,1)';
                break;
            case 'tvlict':
                $department_colors[] = 'rgba(2, 62, 138,1)';
                break;
            case 'stem':
                $department_colors[] = 'rgba(3, 4, 94,1)';
                break;
            case 'humss':
                $department_colors[] = 'rgba(2, 62, 138,1)';
                break;
            case 'gas':
                $department_colors[] = 'rgba(0, 119, 182,1)';
                break;
            case 'abm':
                $department_colors[] = 'rgba(0, 150, 199,1)';
                break;
            case 'bsit':
                $department_colors[] = 'rgba(72, 202, 228,1)';
                break;
            case 'bsis':
                $department_colors[] = 'rgba(144, 224, 239,1)';
                break;
            case 'bscs':
                $department_colors[] = 'rgba(0, 180, 216,1)';
                break;
            case 'bscrim':
                $department_colors[] = 'rgba(0, 150, 199,1)';
                break;
            case 'bsba':
                $department_colors[] = 'rgba(2, 62, 138,1)';
                break;
            case 'beed':
                $department_colors[] = 'rgba(3, 4, 94,1)';
                break;
            case 'dit':
                $department_colors[] = 'rgba(0, 180, 216,1)';
                break;
            case 'dist':
                $department_colors[] = 'rgba(0, 150, 199,1)';
                break;
            case 'dbot':
                $department_colors[] = 'rgba(2, 62, 138,1)';
                break;
            case 'dsot':
                $department_colors[] = 'rgba(3, 4, 94,1)';
                break;
            default:
                $department_colors[] = 'rgba(3, 4, 94,1)'; // Default color
                break;
        }
    }
}

// --------------------------------------------------------------
// Chart Fines
$course_fines = "SELECT course, SUM(fine) AS fines  FROM `borrowed_books` GROUP BY course";
$course_result = $conn->query($course_fines);

$fines = [];
$course_color = [];
if ($course_result->num_rows > 0) {
    while ($row = $course_result->fetch_assoc()) {
        $fines[] = $row;
        $color = $row['course'];
        // echo strtolower($color);
        switch (strtolower(trim($color))) {
            case 'grade 1':
                $course_color[] = 'rgba(3, 4, 94,1)';
                break;
            case 'grade 2':
                $course_color[] = 'rgba(2, 62, 138,1)';
                break;
            case 'grade 3':
                $course_color[] = 'rgba(0, 119, 182,1)';
                break;
            case 'grade 4':
                $course_color[] = 'rgba(0, 150, 199,1)';
                break;
            case 'grade 5':
                $course_color[] = 'rgba(0, 180, 216,1)';
                break;
            case 'grade 6':
                $course_color[] = 'rgba(72, 202, 228,1)';
                break;
            case 'grade 7':
                $course_color[] = 'rgba(144, 224, 239,1)';
                break;
            case 'grade 8':
                $course_color[] = 'rgba(2, 62, 138,1)';
                break;
            case 'grade 9':
                $course_color[] = 'rgba(0, 180, 216,1)';
                break;
            case 'grade 10':
                $course_color[] = 'rgba(144, 224, 239,1)';
                break;
            case 'tvlia':
                $course_color[] = 'rgba(0, 180, 216,1)';
                break;
            case 'tvlhe':
                $course_color[] = 'rgba(0, 119, 182,1)';
                break;
            case 'tvlict':
                $course_color[] = 'rgba(2, 62, 138,1)';
                break;
            case 'stem':
                $course_color[] = 'rgba(3, 4, 94,1)';
                break;
            case 'humss':
                $course_color[] = 'rgba(2, 62, 138,1)';
                break;
            case 'gas':
                $course_color[] = 'rgba(0, 119, 182,1)';
                break;
            case 'abm':
                $course_color[] = 'rgba(0, 150, 199,1)';
                break;
            case 'bsit':
                $course_color[] = 'rgba(72, 202, 228,1)';
                break;
            case 'bsis':
                $course_color[] = 'rgba(144, 224, 239,1)';
                break;
            case 'bscs':
                $course_color[] = 'rgba(0, 180, 216,1)';
                break;
            case 'bscrim':
                $course_color[] = 'rgba(0, 150, 199,1)';
                break;
            case 'bsba':
                $course_color[] = 'rgba(2, 62, 138,1)';
                break;
            case 'beed':
                $course_color[] = 'rgba(3, 4, 94,1)';
                break;
            default:
                $course_color[] = 'rgba(3, 4, 94,1)'; // Default color
                break;
        }
    }
}

// var_dump($course_color);
$color_json = json_encode($course_color);
$fines_json = json_encode($fines);

// --------------------------------------------------------------

// initialize row counter Table
$row_count = 1;

// --------------------------------------------------------------
// ------------------ Single Query Fines----------
$count_fines = $conn->query("SELECT  SUM(fine) AS FINES  FROM `borrowed_books`");
$count_fines = $count_fines->fetch_assoc();
$total_fines = $count_fines['FINES'];

// ------------------ Single Query Students----------
$count_students = $conn->query("SELECT COUNT(*) AS STUDENTS FROM `tbl_student`");
$count_students = $count_students->fetch_assoc();
$total_students = $count_students['STUDENTS'];

// ------------------ Single Query Borrow Books----------
$count_borrow_books = $conn->query("SELECT COUNT(book_status) AS BORROW_BOOKS FROM `borrowed_books` WHERE book_status = 0");
$count_borrow_books = $count_borrow_books->fetch_assoc();
$total_borrow = $count_borrow_books['BORROW_BOOKS'];

// ------------------ Single Query Borrow Books----------
$count_return_books = $conn->query("SELECT COUNT(book_status) AS RETURN_BOOKS FROM `borrowed_books` WHERE book_status = 1");
$count_return_books = $count_return_books->fetch_assoc();
$total_return = $count_return_books['RETURN_BOOKS'];

// Close database connection
$conn->close();



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Attendance System</title>

    <!-- Bootstrap CSS -->
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"> -->
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
    <script src="../js/libraries/chartjs.js"></script>
    <style>
        .chart-container {
            width: 100%;
            height: auto;
        }
    </style>
</head>

<body>
    <?php include('./theme/aside.php') ?>
    <main class="main-container dashboard">
        <div class="intro d-flex flex-row justify-content-between mb-4">
            <h2 style="color: var(--primary);">ASCB Library Information <br>
                Management System using QR Code</h2>
            <div class="time-date gap-4">
                <div class="d-flex justify-content-between gap-2">
                    <img src="../img/icons/clock.svg">
                    <span id="MyClockDisplay" class="clock" onload="showTime()" style="font-size: 24px;"></span>
                </div>
                <div class="d-flex justify-content-between gap-2">
                    <img src="../img/icons/date.svg">
                    <span style="font-size: 24px;"> <?= $date ?></span>
                </div>
            </div>
        </div>

        <hr>
        <div class="d-flex flex-row gap-4 mb-4 justify-content-between">
            <div class="p-4 card-flip">
                <div class="card-flip-front shadow border-card rounded">
                    <h6>Fines</span></h6>
                </div>
                <div class="card-flip-back shadow border-card rounded">
                    <h6 class="text-center"><span>Total Fines <br></span> <span style="font-size:24px;" class="poppins-bold"><?= $total_fines ?></span></h6>
                </div>
            </div>
            <div class="p-4 card-flip">
                <div class="card-flip-front shadow border-card rounded">
                    <h6>Students</span></h6>
                </div>
                <div class="card-flip-back shadow border-card rounded">
                    <h6 class="text-center"><span>Total Students <br></span> <span style="font-size:24px;" class="poppins-bold"><?= $total_students ?></span></h6>
                </div>
            </div>
            <div class="p-4 card-flip">
                <div class="card-flip-front shadow border-card rounded">
                    <h6>Borrowed Books</span></h6>
                </div>
                <div class="card-flip-back shadow border-card rounded">
                    <h6 class="text-center"><span>Total Borrowed Books <br></span> <span style="font-size:24px;" class="poppins-bold"><?= $total_borrow ?></span></h6>
                </div>
            </div>
            <div class="p-4 card-flip">
                <div class="card-flip-front shadow border-card rounded">
                    <h6>Returned Books</span></h6>
                </div>
                <div class="card-flip-back shadow border-card rounded">
                    <h6 class="text-center"><span>Total Returned Books <br> </span> <span style="font-size:24px;" class="poppins-bold"><?= $total_return ?></span></h6>
                </div>
            </div>

            <!-- <div class="shadow border-card rounded p-4">
                <span>Total Students <?= $total_students ?></span>
            </div>
            <div class="shadow border-card rounded p-4">
                <span>Total Borrow Books <?= $total_borrow ?></span>
            </div>
            <div class="shadow border-card rounded p-4">
                <span>Total Return Books <?= $total_return ?></span>
            </div> -->
        </div>
        <!-- <canvas id="departmentChart" style="height:500px !important"></canvas> -->
        <div class="d-flex flex-row gap-4">
            <div class="shadow border-card p-4 rounded chart-container" style="width:70%">
                <h5 class="text-center poppins-semibold">Data Analytics by Course</h5>
                <canvas id="departmentChart"></canvas>
            </div>
            <div class="shadow border-card p-4 rounded" style="width:38%">
                <h5 class="text-center poppins-semibold">Gender</h5>
                <canvas id="genderChart" width="200" height="200"></canvas>
            </div>
        </div>

        <div class="d-flex flex-row gap-4">
            <div class="shadow border-card p-4 rounded mt-5" style="width:38%">
                <h6 class="text-center poppins-semibold">Total Fines By Course / Strand</h6>
                <canvas id="finesChart" width="400" height="200"></canvas>
            </div>
            <div class="shadow border-card p-4 rounded mt-5" style="width: 60%;">
                <h5 class="text-center poppins-semibold">Attendance Analytics</h5>
                <canvas id="attendanceChart" width="800" height="300"></canvas>
            </div>
        </div>
        <br>
        <div class="message-inserting-data">
            <!-- Error Message or Successfully Inserted Data -->
            <?php if (isset($_SESSION["qrcode_error"])) : ?>
                <p class="text-center quote bg-danger py-2 rounded text-white" style="padding-inline: 24%;"><?= $_SESSION["qrcode_error"] ?></p>
                <?php
                unset($_SESSION["qrcode_error"]);
                ?>
            <?php endif; ?>

            <?php if (isset($_SESSION["attendance"])) : ?>
                <p class="text-center quote bg-success py-2 rounded text-white" style="padding-inline: 24%;"><?= $_SESSION["attendance"] ?></p>
                <?php
                unset($_SESSION["attendance"]);
                ?>
            <?php endif; ?>

            <?php if (isset($_SESSION["attendance_updated"])) : ?>
                <p class="text-center quote bg-success py-2 rounded text-white" style="padding-inline: 24%;"><?= $_SESSION["attendance_updated"] ?></p>
                <?php
                unset($_SESSION["attendance_updated"]);
                ?>
            <?php endif; ?>
        </div>
        <div class="d-flex flex-row justify-content-between gap-4 mt-5">
            <div class="border-card  p-4 shadow rounded" style="width: 40%;">
                <div class="scanner-con">
                    <h5 class="text-center">Scan you QR Code here for your attedance</h5>
                    <video id="interactive" class="viewport" width="100%">
                </div>

                <div class="qr-detected-container" style="display: none;">
                    <form action="attendance.php" method="POST">
                        <h4 class="text-center">QR Detected!</h4>

                        <input type="hidden" id="detected-qr-code" name="qr_code">
                        <button type="submit" class="btn btn-success form-control">Submit Attendance</button>
                    </form>
                </div>
            </div>

            <div class="border-card shadow rounded p-4" style="width: 60%;">
                <h4 class="mb-4">List of Present Students</h4>
                <div class="table-container table-responsive">
                    <div class="filter-gender mb-4">
                        <form action="index.php" method="post">
                            <div class="d-flex flex-row gap-4 justify-content-between align-items-end">
                                <div class="select" style="width: 70%;">
                                    <label for="gender">Filter Gender</label>
                                    <select name="gender" id="gender" class="form-control">
                                        <option hidden>Select Gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                </div>
                                <div class="action" style="width: 30%;">
                                    <button type="submit" name="filter-gender" class="btn btn-primary form-control">Filter</button>
                                </div>
                            </div>
                        </form>

                        <div class="display-selected-gender">
                            <?php if (isset($gender)) : ?>
                                <span>Gender Filtered By : <strong> <?= $gender ?></strong> </span>
                            <?php endif; ?>
                        </div>

                    </div>
                    <table class="table text-center table-sm mt-4 table-hover" id="attendanceTable">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Name</th>
                                <th scope="col">Course & Section</th>
                                <th scope="col">Time In</th>
                                <th scope="col">Time Out</th>
                                <!-- <th scope="col">Action</th> -->
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
                                $timeinlogdate = $row["timein_log_date"];
                                $timeoutlogdate = $row["timeout_log_date"];
                            ?>
                                <tr>
                                    <td scope="row" id="studentID-<?= $attendanceID ?>"><?= $row_count ?></td>
                                    <td><?= $studentName ?></td>
                                    <td><?= $studentCourse ?></td>
                                    <td style="font-size: 12px;"><?= $timeIn ?> <br> <?= $timeinlogdate ?></td>
                                    <td style="font-size: 12px;"><?= $timeOut ?> <br> <?= $timeoutlogdate ?></td>
                                    <!-- <td>
                                        <div class="action-button">
                                            <button class="btn btn-danger delete-button" onclick="deleteAttendance(<?= $attendanceID ?>)">X</button>
                                        </div>
                                    </td> -->
                                </tr>

                            <?php
                                // initialize row counter
                                $row_count++;
                            }
                            ?>

                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>


    <!-- Bootstrap JS -->
    <script src="../js/libraries/jquery.slim.min.js"></script>
    <script src="../js/libraries/bootstrap.bundle.min.js"></script>
    <script src="../js/libraries/popper.min.js"></script>
    <script src="../js/libraries/bootstrap.min.js"></script>

    <!-- Data Table -->
    <script src="../js/libraries/jquery.dataTables.js"></script>

    <!-- instascan Js -->
    <script src="../js/libraries/instascan.min.js"></script>

    <!-- Scanner JS -->
    <script src="../js/scanner.js"></script>


    <script>
        // Table
        $(document).ready(function() {
            $('#attendanceTable').DataTable();
        });

        // Time
        function showTime() {
            var date = new Date();
            var h = date.getHours(); // 0 - 23
            var m = date.getMinutes(); // 0 - 59
            var s = date.getSeconds(); // 0 - 59
            var session = "AM";

            if (h == 0) {
                h = 12;
            }

            if (h > 12) {
                h = h - 12;
                session = "PM";
            }

            h = (h < 10) ? "0" + h : h;
            m = (m < 10) ? "0" + m : m;
            s = (s < 10) ? "0" + s : s;

            var time = h + ":" + m + ":" + s + " " + session;
            document.getElementById("MyClockDisplay").innerText = time;
            document.getElementById("MyClockDisplay").textContent = time;

            setTimeout(showTime, 1000);

        }

        showTime();

        // -----------------------------------------------------------------------------------------

        // Attendance Chart
        var logData = <?php echo json_encode($logData); ?>;

        var weeks = logData.map(data => `${data.log_month} Week ${data.log_week}`);
        var attendanceCounts = logData.map(data => data.attendance_count);

        var ctx = document.getElementById('attendanceChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: weeks,
                datasets: [{
                    label: 'Attendance Count',
                    data: attendanceCounts,
                    backgroundColor: 'rgba(3, 4, 94, 0.2)',
                    borderColor: 'rgba(3, 4, 94, 1)',
                    fill: true
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // -----------------------------------------------------------------------------------------

        // Gender Chart
        var genderData = <?php echo json_encode($genderData); ?>;
        var genderColor = <?php echo json_encode($genderColor); ?>

        var ctx = document.getElementById('genderChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(genderData),
                datasets: [{
                    label: 'Gender Distribution',
                    data: Object.values(genderData),
                    backgroundColor: genderColor,
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },

            }
        });

        // -----------------------------------------------------------------------------------------

        // Department Chart
        var courseYearLevel = <?php echo json_encode($course_year_level); ?>;
        var departmentLabels = <?php echo json_encode($department_labels); ?>;
        var departmentCounts = <?php echo json_encode($department_counts); ?>;
        var departmentColors = <?php echo json_encode($department_colors); ?>;

        var ctx = document.getElementById('departmentChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: departmentLabels,
                datasets: [{
                    label: 'Total Students by Course',
                    data: departmentCounts,
                    backgroundColor: departmentColors,
                    borderColor: departmentColors,
                    borderWidth: 1
                }]
            },
            options: {
                // responsive: true,
                // maintainAspectRatio: true,
                indexAxis: 'x',
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                var index = tooltipItem.dataIndex;
                                var yearLevel = courseYearLevel[index].year_level;
                                var count = tooltipItem.raw; // Get the count value
                                return `Year Level: ${yearLevel}, Count: ${count}`;
                            }
                        }
                    }
                }
            }
        });


        // -----------------------------------------------------------------------------------------
        // Fines Chart
        var finesData = <?php echo $fines_json; ?>;
        var finesColor = <?php echo $color_json; ?>;

        var bubbleData = finesData.map((data, index) => {
            return {
                x: index + 1,
                y: parseFloat(data.fines),
                r: 10
            };
        });

        var courses = finesData.map(data => data.course);

        var ctx = document.getElementById('finesChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bubble',
            data: {
                labels: courses,
                datasets: [{
                    label: 'Total Fines',
                    data: bubbleData,
                    backgroundColor: finesColor,
                    borderColor: finesColor,
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    x: {
                        beginAtZero: true
                    },
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>

</html>