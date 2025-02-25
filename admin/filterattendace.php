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


// Fetch all Courses From Student List 
$adviserByStudents = $conn->query("SELECT * FROM `tbl_student` GROUP BY adviser");
$adviserByStudents = $adviserByStudents->fetch_all(MYSQLI_ASSOC);

// Fetch all Courses From Student List 
$courseByStudents = $conn->query("SELECT * FROM `tbl_student` GROUP BY course_section");
$courseByStudents = $courseByStudents->fetch_all(MYSQLI_ASSOC);



if (isset($_POST['filter'])) {

    $from = $_POST['from'];
    $to = $_POST['to'];
    $adviser = $_POST['adviser'];
    $course = $_POST['course'];


    $stmt = $conn->query("SELECT * FROM tbl_attendance LEFT JOIN tbl_student ON tbl_student.generated_code = tbl_attendance.tbl_student_id WHERE timein_log_date >= '$from' AND timein_log_date <= '$to' AND tbl_student.adviser = '$adviser' AND tbl_student.course_section = '$course'");

    // $stmt = $conn->query("SELECT * FROM tbl_attendance LEFT JOIN tbl_student ON tbl_student.generated_code = tbl_attendance.tbl_student_id WHERE timein_log_date >= '$from' AND timein_log_date <= '$to'");

    $result = $stmt->fetch_all(MYSQLI_ASSOC);
}

$books = $conn->query("SELECT * FROM books");
$books = $books->fetch_all(MYSQLI_ASSOC);

date_default_timezone_set('Asia/Manila');
// $timeIn =  date("Y-m-d H:i:s");
$date = date("Y-m-d");

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

    <link rel="stylesheet" href="../css/jquery.betterdropdown.css" />

    <script src="../js/libraries/jquery-3.6.1.min.js"></script>


    <!-- Data Table -->
    <link rel="stylesheet" href="../css/jquery.dataTables.css" />

    <link rel="stylesheet" href="../css/main.css">
    <!-- Admin CSS -->
    <link rel="stylesheet" href="../css/admin.css" />

    <!-- SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">


    <!-- Font Family -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/poppins.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/paho-mqtt/1.0.2/mqttws31.min.js" type="text/javascript"></script>

    <style>
        #swal2-html-container {
            text-align: left !important;
        }
    </style>
</head>

<body>


    <?php include('./theme/aside.php') ?>
    <main class="main-container">
        <div id="messages" class="text-success mb-4"></div>
        <div class="d-flex flex-row justify-content-between gap-4 mt-5">
            <div class="border-card shadow rounded p-4" style="width: 100%;">
                <p><em>//Set a Date to Customize Student Attendance Filtering</em></p>
                <h4 class="mb-4 poppins-bold">ATTENDANCE REPORT</h4>
                <div class="form-filter mb-5">
                    <form action="filterattendace.php" method="POST">
                        <div class="d-flex flex-row gap-4 justify-content-between mb-4">
                            <div class="form-group w-100">
                                <label for="adviser" class="form-label"><b>ADVISER</b></label>
                                <select name="adviser" id="adviser" class="form-control">
                                    <option selected hidden disabled>Select Adviser</option>
                                    <?php foreach ($adviserByStudents as $adviser) : ?>
                                        <option value="<?= $adviser['adviser'] ?>"><?= $adviser['adviser'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group w-100">
                                <label for="course_section_s" class="form-label"><b>COURSE / STRAND</b></label>
                                <select name="course" id="course_section_s" class="form-control">
                                    <option selected hidden disabled>Select Course / Strand</option>
                                    <?php foreach ($courseByStudents as $course) : ?>
                                        <option value="<?= $course['course_section'] ?>"><?= $course['course_section'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="d-flex flex-row gap-4 justify-content-between mb-4">
                            <div class="form-group w-100">
                                <label for="from" class="form-label"><b>FROM</b></label>
                                <input type="date" name="from" id="from" class="form-control">
                            </div>
                            <div class="form-group w-100">
                                <label for="to" class="form-label"><b>TO</b></label>
                                <input type="date" name="to" id="to" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary col-3" name="filter">Filter</button>
                        </div>
                    </form>
                    <hr>
                </div>
                <?php if (!isset($result)) : ?>
                    <!-- <p>walai data taka taka rakag filter</p> -->
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
                                    $logdate = $row["timein_log_date"];
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
                                                <!-- <button class="btn btn-warning"><img src="../img/icons/view.svg" alt="View" style="width: 20px;"></button> -->

                                                <!-- Modal trigger button Issuedbook-->
                                                <button type="button" class="btn btn-primary edit" data-bs-toggle="modal" data-bs-target="#issuedbook" value="<?= $attendanceID ?>"><img src="../img/icons/borrowedbooks.svg" alt="Edit" style="width: 20px;"></button>
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
    </main>


    <!-- Modal Body -->
    <div class="modal fade" id="issuedbook" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">
                        Borrow Books
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form action="" method="post">
                        <input type="hidden" name="qrcodem" id="qrcodem">
                        <input type="hidden" name="stud_id" id="id">
                        <div class="d-flex flex-row gap-4 align-items-end mb-4">
                            <div class="col">
                                <label for="student_name" class="form-label">Full Name</label>
                                <input type="text" id="student_name" class="form-control" readonly>
                            </div>
                            <div class="col">
                                <label for="course_m">Course & Section</label>
                                <input type="text" id="course_m" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="d-flex flex-row gap-4 mb-4">
                            <div class="col">
                                <label for="time_in_m">Time In</label>
                                <input type="text" id="time_in_m" class="form-control" readonly>
                            </div>
                            <div class="col">
                                <label for="time_out_m">Time Out</label>
                                <input type="text" id="time_out_m" class="form-control" readonly>
                            </div>
                            <div class="col">
                                <label for="log_date_m">Log Date</label>
                                <input type="text" id="log_date_m" class="form-control" readonly>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col">
                                <span>Borrowed Book</span>
                                <table class="table" id="book-list">
                                    <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th>Author</th>
                                            <th>Book Title</th>
                                            <th>Publisher</th>
                                            <th>Return Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                                <button type="button" id="savebook" class="btn btn-success col-3">Save Books</button>
                                <hr class="mb-5">
                                <div class="d-flex gap-4">
                                    <div class="col-6">
                                        <label for="">Choose Book</label>
                                        <select name="book" id="book" class="form-control book">
                                            <?php foreach ($books as $book) : ?>
                                                <option value="<?= $book['id'] ?>" class="booktitle"><?= $book['book_title'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label for="returndate">Return Date</label>
                                        <input type="date" id="returndate" name="returndate" class="form-control" />
                                    </div>
                                    <br />
                                </div>
                                <button type="button" class="btn btn-primary col-3" id="add-book">Add</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Issuedbook JS -->
    <script src="../js/issuedbook.js"></script>
    <!-- AddBookIssued -->
    <script src="../js/addbookissued.js"></script>
    <!-- Bootstrap JS -->
    <!-- <script src="../js/libraries/jquery.slim.min.js"></script> -->
    <script src="../js/libraries/popper.min.js"></script>
    <script src="../js/libraries/bootstrap.bundle.min.js"></script>
    <script src="../js/libraries/bootstrap.min.js"></script>
    <script src="../js/libraries/jquery.betterdropdown.js"></script>
    <!-- Data Table -->
    <script src="../js/libraries/jquery.dataTables.js"></script>


    <script>
        if (typeof Paho === 'undefined') {
            console.error('Paho library is not loaded.');
        } else {
            const client = new Paho.MQTT.Client('localhost', 9001, 'browser-client-filterattendance');
            client.onConnectionLost = function(responseObject) {
                console.log('Connection lost:', responseObject.errorMessage);
            };
            client.onMessageArrived = function(message) {
                try {
                    const msgs = JSON.parse(message.payloadString);
                    console.log(`Received messages: ${JSON.stringify(msgs)}`);

                    if (Array.isArray(msgs)) {
                        const validMessages = msgs.filter(msg => msg.student_id && msg.book_id && msg.return_date);

                        if (validMessages.length > 0) {
                            const booksList = validMessages.map(book =>
                                `Action: ${book.action}, Student ID: ${book.student_id}, Student Name: ${book.student_name}, Book Title: ${book.book_title}, Book ID: ${book.book_id}, Return Date: ${book.return_date}`).join('\n');

                            Swal.fire({
                                title: 'New Books Borrowed!',
                                text: booksList,
                                icon: 'info',
                                confirmButtonText: 'Ok'
                            }).then(() => {
                                console.log('SweetAlert2 displayed successfully.');
                            });
                        } else {
                            console.error("No valid messages found in array:", msgs);
                        }
                    } else {
                        console.error("Received message is not an array:", msgs);
                    }
                } catch (e) {
                    console.error("Error parsing message payload:", e);
                }

                const msgContent = JSON.parse(message.payloadString);
                const msgText = msgContent.map(book =>
                    `Action: ${book.action}, Student ID: ${book.student_id}, Student Name: ${book.student_name}, Book Title: ${book.book_title}, Book ID: ${book.book_id}, Return Date: ${book.return_date}`
                ).join('\n');

                const msgDiv = document.createElement('div');
                msgDiv.textContent = `Message:\n${msgText}`;
                document.getElementById('messages').appendChild(msgDiv);
            };

            client.connect({
                onSuccess: function() {
                    console.log("Connected to MQTT broker");
                    client.subscribe('library/borrowed_books');
                },
                onFailure: function(error) {
                    console.error("MQTT connection error:", error);
                }
            });
        }
    </script>



    <script>
        // Table
        $(document).ready(function() {
            // Search Table Data
            $('#attendanceTable').DataTable();

            // Search Select Value
            $('#book').betterDropdown();
            $('#course_section_s').betterDropdown();


            // $('#bookid').click(function() {
            //     alert('hello world');
            // })
        });
    </script>

</body>

</html>