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

    $studentBbooks = $conn->query("SELECT * FROM borrowed_books INNER JOIN tbl_attendance ON borrowed_books.student_id = tbl_attendance.tbl_attendance_id
         INNER JOIN tbl_student ON tbl_attendance.tbl_student_id = tbl_student.generated_code WHERE borrowed_date >= '$from' AND borrowed_date <= '$to'");
    $studentBbooks = $studentBbooks->fetch_all(MYSQLI_ASSOC);
} else {
    $studentBbooks = $conn->query("SELECT * FROM borrowed_books
         INNER JOIN tbl_attendance ON borrowed_books.student_id = tbl_attendance.tbl_attendance_id
         INNER JOIN tbl_student ON tbl_attendance.tbl_student_id = tbl_student.generated_code");
    $studentBbooks = $studentBbooks->fetch_all(MYSQLI_ASSOC);
}

if (isset($_POST['borrowed_books'])) {
    $studentBbooks = $conn->query("SELECT * FROM borrowed_books
        INNER JOIN tbl_attendance ON borrowed_books.student_id = tbl_attendance.tbl_attendance_id
        INNER JOIN tbl_student ON tbl_attendance.tbl_student_id = tbl_student.generated_code WHERE book_status = 0");
    $studentBbooks = $studentBbooks->fetch_all(MYSQLI_ASSOC);
} else if (isset($_POST['returned_books'])) {
    $studentBbooks = $conn->query("SELECT * FROM borrowed_books
        INNER JOIN tbl_attendance ON borrowed_books.student_id = tbl_attendance.tbl_attendance_id
        INNER JOIN tbl_student ON tbl_attendance.tbl_student_id = tbl_student.generated_code WHERE book_status = 1");
    $studentBbooks = $studentBbooks->fetch_all(MYSQLI_ASSOC);
}

date_default_timezone_set('Asia/Manila');
// $timeIn =  date("Y-m-d H:i:s");
$date = date("Y-m-d");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrowed Books</title>

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

    <!-- Font Family -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/poppins.css">

</head>

<body>

    <?php include('./theme/aside.php') ?>


    <main class="main-container">
        <div class="d-flex flex-row justify-content-between gap-4 mt-5">
            <div class="border-card  p-4 shadow rounded" style="width: 40%;">
                <div class="scanner-con">
                    <h5 class="text-center">Scan your QR Code here to return books</h5>
                    <video id="interactive" class="viewport" width="100%">
                </div>

                <div class="qr-detected-container" style="display: none;">
                    <form action="returnbooksbe.php" method="POST">
                        <h4 class="text-center">QR Detected!</h4>

                        <input type="hidden" id="detected-qr-code" name="qr_code">
                        <div id="borrowed-books-list"></div>

                        <button type="submit" class="btn btn-success form-control">Return Book</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="d-flex flex-row justify-content-between gap-4 mt-5">
            <div class="border-card shadow rounded p-4" style="width: 100%;">
                <?php if (isset($_SESSION["qrcode_error"])) : ?>
                    <p class="text-center quote bg-danger py-2 rounded text-white" style="padding-inline: 24%;"><?= $_SESSION["qrcode_error"] ?></p>
                    <?php unset($_SESSION['qrcode_error']); ?>
                <?php endif; ?>
                <?php if (isset($_SESSION['book_updated'])) : ?>
                    <p class="text-center quote bg-success py-2 rounded text-white" style="padding-inline: 24%;"><?= $_SESSION['book_updated'] ?></p>
                    <?php unset($_SESSION['book_updated']); ?>
                <?php endif; ?>
                <br>
                <h4 class="mb-4">Borrowed Books Report</h4>

                <div class="form-filter mb-5">
                    <form action="borrowedbooks.php" method="POST">
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
                <?php if ($studentBbooks) : ?>
                    <div class="table-container table-responsive">
                        <div class="actions mb-4">
                            <form action="borrowedbooks.php" method="post">
                                <button type="button" name="back" class="btn btn-outline-primary"><a href="borrowedbooks.php"> Back</a></button>
                                <button type="submit" name="borrowed_books" class="btn btn-outline-primary borrowedbooks">Borrowed Books</button>
                                <button type="submit" name="returned_books" class="btn btn-outline-primary">Returned Books</button>
                            </form>
                        </div>

                        <table class="table text-center table-sm mt-4 table-hover" id="borrowedbook">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Student Name</th>
                                    <th>Author</th>
                                    <th>Book Title</th>
                                    <th>Publisher</th>
                                    <th>Due Date</th>
                                    <th>Fines</th>
                                    <th>Book Status</th>
                                    <th>Returned Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($studentBbooks as $studbook) : ?>
                                    <tr>
                                        <td><?= $studbook['student_name'] ?></td>
                                        <td><?= $studbook['author'] ?></td>
                                        <td><?= $studbook['book_title'] ?></td>
                                        <td><?= $studbook['publisher'] ?></td>
                                        <td><?= $studbook['return_date'] ?></td>
                                        <td><?= $studbook['fine'] ?></td>
                                        <td><?= $studbook['book_status'] == 1 ? 'Returned' : 'Not Returned' ?></td>
                                        <td><?= $studbook['date_returned'] ?></td>
                                        <!-- <td>
                                            <?php if ($studbook['book_status'] == 0) : ?>
                                                <button type="button" class="btn btn-primary return" data-href="returnbooksbe.php?returnedbook=<?= $studbook['id'] ?>">
                                                    <img src="../img/icons/return.svg" alt="Return" style="width: 20px; transform: scaleX(-1);">
                                                </button>
                                            <?php else : ?>
                                                <button type="submit" class="btn btn-success" disabled><img src="../img/icons/return.svg" alt="Return" style="width: 20px;"></button>
                                            <?php endif; ?>
                                        </td> -->
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else : ?>
                    <div class="table-container table-responsive">
                        <div class="actions mb-4">
                            <form action="borrowedbooks.php" method="post">

                                <button type="button" name="back" class="btn btn-outline-primary"><a href="borrowedbooks.php"> Back</a></button>
                                <button type="submit" name="borrowed_books" class="btn btn-outline-primary borrowedbooks">Borrowed Books</button>
                                <button type="submit" name="returned_books" class="btn btn-outline-primary">Returned Books</button>
                            </form>
                        </div>

                        <table class="table text-center table-sm mt-4 table-hover" id="borrowedbook">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Student Name</th>
                                    <th>Author</th>
                                    <th>Book Title</th>
                                    <th>Publisher</th>
                                    <th>Due Date</th>
                                    <th>Fines</th>
                                    <th>Book Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($studentBbooks as $studbook) : ?>
                                    <tr>
                                        <td><?= $studbook['student_name'] ?></td>
                                        <td><?= $studbook['author'] ?></td>
                                        <td><?= $studbook['book_title'] ?></td>
                                        <td><?= $studbook['publisher'] ?></td>
                                        <td><?= $studbook['return_date'] ?></td>
                                        <td><?= $studbook['fine'] ?></td>
                                        <td><?= $studbook['book_status'] == 1 ? 'Returned' : 'Not Returned' ?></td>
                                        <td>
                                            <?php if ($studbook['book_status'] == 0) : ?>
                                                <button type="button" class="btn btn-primary return" data-href="returnbooksbe.php?returnedbook=<?= $studbook['id'] ?>">
                                                    <img src="../img/icons/return.svg" alt="Return" style="width: 20px; transform: scaleX(-1);">
                                                </button>
                                            <?php else : ?>
                                                <button type="submit" class="btn btn-success" disabled><img src="../img/icons/return.svg" alt="Return" style="width: 20px;"></button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>


            </div>

        </div>
    </main>

    <!-- instascan Js -->
    <script src="../js/libraries/instascan.min.js"></script>
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
    <!-- Sweet Alert -->
    <script src="../js/libraries/sweetalert2@11.js"></script>

    <script>
        // Table
        $(document).ready(function() {
            // Search Table Data
            $('#borrowedbook').DataTable();


            $('.return').on('click', function(e) {
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
                    confirmButtonText: "Yes, Returned it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.location.href = href;
                    }
                });
            });
        });
    </script>

    <script>
        let scanner;

        function startScanner() {
            scanner = new Instascan.Scanner({
                video: document.getElementById('interactive')
            });

            scanner.addListener('scan', function(content) {
                $("#detected-qr-code").val(content);
                console.log(content);
                fetchBorrowedBooks(content);
                scanner.stop();
                document.querySelector(".qr-detected-container").style.display = '';
                document.querySelector(".scanner-con").style.display = 'none';
            });

            Instascan.Camera.getCameras()
                .then(function(cameras) {
                    if (cameras.length > 0) {
                        scanner.start(cameras[0]);
                    } else {
                        console.error('No cameras found.');
                        alert('No cameras found.');
                    }
                })
                .catch(function(err) {
                    console.error('Camera access error:', err);
                    alert('Camera access error: ' + err);
                });
        }

        function fetchBorrowedBooks(qrCode) {
            $.ajax({
                url: 'fetchbooks.php',
                type: 'POST',
                data: {
                    qr_code: qrCode
                },
                success: function(response) {
                    $('#borrowed-books-list').html(response);
                },
                error: function(error) {
                    console.error('Error fetching borrowed books:', error);
                }
            });
        }

        document.addEventListener('DOMContentLoaded', startScanner);
    </script>

</body>

</html>