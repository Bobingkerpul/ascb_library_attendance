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



$faculties =  $conn->query("SELECT * FROM faculty");
$faculties = $faculties->fetch_all(MYSQLI_ASSOC);

if (isset($_POST['savefaculty'])) {
    $idnumb = $_POST['idnumb'];
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $address = $_POST['address'];

    $conn->query("INSERT INTO faculty(id_no, fullname, email, contact, address) VALUES ('$idnumb','$fullname','$email','$contact','$address')");
    header('location: faculty.php');
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty</title>

    <!-- Bootstrap CSS -->
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"> -->
    <link rel="stylesheet" href="../css/bootstrap.min.css" />

    <!-- Data Table -->
    <link rel="stylesheet" href="../css/jquery.dataTables.css" />

    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>

<body>
    <?php include('./theme/aside.php') ?>
    <main class="main-container">
        <div class="main">
            <div class="border-card shadow rounded" style="width:100%;">
                <div class="heading p-2">
                    <h4 class="m-0">List of Faculty</h4>
                </div>
                <div class="p-4">
                    <div class="addfaculty">
                        <!-- Modal trigger button -->
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addfaculty">
                            Add New Faculty
                        </button>
                    </div>
                    <table class="table  table-sm" id="attendanceTable">
                        <thead class="thead-dark">
                            <tr>
                                <th>Unique Id</th>
                                <th>Id #</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Contact</th>
                                <th>Address</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- id_no, fullname, email, contact, address -->
                            <?php foreach ($faculties as $faculty) : ?>
                                <tr>
                                    <td><?= $faculty['id'] ?></td>
                                    <td><?= $faculty['id_no'] ?></td>
                                    <td><?= $faculty['fullname'] ?></td>
                                    <td>
                                        <a href="mailto:<?= $faculty['email'] ?>"><?= $faculty['email'] ?></a>
                                    </td>
                                    <td>
                                        <a href="tel:<?= $faculty['contact'] ?>"><?= $faculty['contact'] ?></a>
                                    </td>
                                    <td><?= $faculty['address'] ?></td>
                                    <td>
                                        <div class="actionButton">
                                            <button>Edit</button>
                                            <button>Delete</button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>



    <!-- Modal Add New Faculty-->
    <!-- if you want to close by clicking outside the modal, delete the last endpoint:data-bs-backdrop and data-bs-keyboard -->
    <div class="modal fade" id="addfaculty" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">
                        Add New Faculty
                    </h5>
                </div>
                <div class="modal-body">
                    <div>
                        <form action="faculty.php" method="post">
                            <div class="">
                                <div class="form-group">
                                    <label for="idnumb">Id #</label>
                                    <input type="number" name="idnumb" id="idnumb" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="fullname">Full Name</label>
                                    <input type="text" name="fullname" id="fullname" class="form-control" required>
                                </div>
                            </div>
                            <div class="">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" name="email" id="email" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="contact">Contact</label>
                                    <input type="tel" name="contact" id="contact" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="address">Address</label>
                                <textarea name="address" id="address" cols="30" rows="10" class="form-control" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary" name="savefaculty">Save</button>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>



    <!-- Bootstrap Modal JS -->
    <script src="../js/libraries/bootstrap.bundle.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="../js/libraries/jquery.slim.min.js"></script>
    <script src="../js/libraries/popper.min.js"></script>
    <script src="../js/libraries/bootstrap.min.js"></script>

    <!-- Data Table -->
    <script src="../js/libraries/jquery.dataTables.js"></script>


    <script>
        $(document).ready(function() {
            $('#attendanceTable').DataTable();
        });
    </script>

</body>

</html>