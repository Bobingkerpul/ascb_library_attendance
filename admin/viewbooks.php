<?php
include('../conn/conn.php');


$books = $conn->query("SELECT * FROM books");
$books = $books->fetch_all(MYSQLI_ASSOC);

session_start();


if (isset($_GET['deletebook'])) {

    $id = $_GET['deletebook'];

    $conn->query("DELETE FROM books WHERE id = '$id'");
    header("location: viewbooks.php");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Books</title>

    <!-- Bootstrap CSS -->
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"> -->
    <link rel="stylesheet" href="../css/bootstrap.min.css" />

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

    <style>
        .books {
            font-size: 12px;
            line-height: 1;
        }

        tbody .books {
            padding: 16px 12px !important;
        }

        .books:nth-child(odd) {
            width: 16%;
        }

        .error.derror {
            display: block;
        }

        .error {
            color: red;
            font-size: 12px;
            display: none;
        }

        .form-label {
            font-size: 14px;
        }

        .quote>span::after {
            content: '" ';

        }

        .quote>span::before {
            content: '"';

        }
    </style>
</head>

<body>
    <?php include('./theme/aside.php'); ?>


    <main class="main-container">

        <?php if (isset($_SESSION["message2"])) : ?>
            <p class="text-center quote bg-warning py-2 rounded" style="padding-inline: 24%;"><?= $_SESSION["message2"] ?></p>

        <?php
            session_unset();
        endif; ?>

        <?php if (isset($_SESSION["message"])) : ?>
            <p class="text-center quote bg-success py-2 text-white rounded" style="padding-inline: 24%;"><?= $_SESSION["message"] ?></p>

        <?php
            session_unset();
        endif; ?>

        <div class="shadow border-card p-5 rounded">
            <div class="student-list">
                <div class="title">
                    <h4>List of Books</h4>
                </div>
                <hr>
                <div class="table-container table-responsive">
                    <table class="table text-center table-sm w-100" id="studentTable">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col" style="display: none;">Id</th>
                                <th scope="col">Date Received</th>
                                <th scope="col">Author</th>
                                <th scope="col">Book Title</th>
                                <th scope="col">Publisher</th>
                                <th scope="col">Dept</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($books as $book) : ?>
                                <tr>
                                    <td style="display: none;"><?= $book['id'] ?></td>
                                    <td class="books"><?= $book['date_received'] ?></td>
                                    <td class="books"><?= $book['author'] ?></td>
                                    <td class="books"><?= substr($book['book_title'], 0, 22) ?></td>
                                    <td class="books"><?= $book['publisher'] ?></td>
                                    <td class="books"><?= $book['department'] ?></td>
                                    <td>
                                        <div class="d-flex flex-row gap-2">
                                            <a href="viewbookdetails.php?view=<?= $book['id'] ?>" target="_blank" rel="noopener noreferrer">
                                                <button type="button" class="btn btn-warning btn-sm">
                                                    <img src="../img/icons/view.svg" alt="View" style="width: 20px;">
                                                </button>
                                            </a>
                                            <a target="_blank" href="editbooks.php?editbooks=<?= $book['id'] ?>">
                                                <button type="button" class="btn btn-primary btn-sm meditbook">
                                                    <img src="../img/icons/edit.svg" alt="Edit" style="width: 20px;">
                                                </button>
                                            </a>
                                            <!-- <button type="button" class="btn btn-danger deletebook" data-href="?deletebook=<?= $book['id'] ?>">
                                                <img src="../img/icons/delete.svg" alt="Delete" style="width: 20px;">
                                            </button> -->
                                            <a href="?deletebook=<?= $book['id'] ?>">
                                                <button type="button" class="btn btn-danger deletebook" style="    height: 31.14px;width: 38px;display: flex;justify-content: center;">
                                                    <img src="../img/icons/delete.svg" alt="Delete" style="width: 16px;">
                                                </button>
                                            </a>
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

    <!-- Bootstrap JS -->
    <!-- <script src="../js/libraries/jquery.slim.min.js"></script> -->
    <script src="../js/libraries/bootstrap.bundle.min.js"></script>
    <script src="../js/libraries/popper.min.js"></script>
    <script src="../js/libraries/bootstrap.min.js"></script>

    <!-- Data Table -->
    <script src="../js/libraries/jquery.dataTables.js"></script>

    <!-- Sweet Alert -->
    <script src="../js/libraries/sweetalert2@11.js"></script>

    <script>
        $(document).ready(function() {
            // Data Table for Student Display
            $('#studentTable').DataTable();

            // Delete Books
            // $('.deletebook').on('click', function(e) {
            //     // alert('hello');
            //     e.preventDefault();
            //     const href = $(this).data('href');

            //     Swal.fire({
            //         title: "Are you sure?",
            //         text: "You won't be able to revert this!",
            //         icon: "warning",
            //         showCancelButton: true,
            //         confirmButtonColor: "#3085d6",
            //         cancelButtonColor: "#d33",
            //         confirmButtonText: "Yes, delete it!"
            //     }).then((result) => {
            //         if (result.isConfirmed) {
            //             document.location.href = href;
            //         }
            //     });
            // });
        });
    </script>

</body>

</html>