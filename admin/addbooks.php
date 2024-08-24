<?php
include('../conn/conn.php');


$books = $conn->query("SELECT * FROM books ORDER BY id ASC");
$books = $books->fetch_all(MYSQLI_ASSOC);


if (isset($_POST['addbook'])) {

    $date_received = $_POST['date_received'];
    $book_title = $_POST['book_title'];
    $author = $_POST['author'];
    $edition = $_POST['edition'];
    $pages = $_POST['pages'];
    $source_fund = $_POST['source_fund'];
    $publish = $_POST['publish'];
    $publish_year = $_POST['publish_year'];
    $department = $_POST['department'];
    $course_descript = $_POST['course_descript'];
    $call_number = $_POST['call_number'];
    $collection = $_POST['collection'];
    $quantity = $_POST['quantity'];


    // die("INSERT INTO books(date_received, author, book_title, edition, pages, source_of_fund, publisher, year, department, course_description_major, call_number, collection, quantity) VALUES ('$date_received','$author','$book_title','$edition','$pages','$source_fund','$publish','$publish_year','$department','$course_descript','$call_number','$collection','$quantity')");


    $conn->query("INSERT INTO books(date_received, author, book_title, edition, pages, source_of_fund, publisher, year, department, course_description_major, call_number, collection, quantity) VALUES ('$date_received','$author','$book_title','$edition','$pages','$source_fund','$publish','$publish_year','$department','$course_descript','$call_number','$collection','$quantity')");

    echo "<script>alert(Book '$book_title' Added Successfully)</script>";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Books</title>

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

</head>

<body>
    <?php include('./theme/aside.php'); ?>

    <main class="main-container">
        <div class="shadow border-card p-5 rounded">
            <!-- Import Excel File Form -->
            <div class="import-excel">
                <form action="import-books.php" method="post" enctype="multipart/form-data">
                    <div class="import-excel  d-flex flex-row align-items-end justify-content-between mb-4">
                        <div class="form-group">
                            <label for="file" class="form-label poppins-bold"><em>Select Excel File for Book Imports</em></label> <br>
                            <input type="file" name="file" id="file" accept=".xls,.xlsx">
                        </div>
                        <div class="action col-2">
                            <button type="submit" name="import" class="btn btn-primary w-100" disabled> <img src="../img/icons/import.svg" alt="Import Icon"> Import</button>
                        </div>
                    </div>
                </form>
            </div>
            <br>
            <hr>
            <br>
            <div class="student-list">
                <div class="title">
                    <h4>Add Books</h4>
                </div>
                <hr>
                <div class="table-container table-responsive">
                    <form action="addbooks.php" method="post">
                        <div class="d-flex flex-row gap-4 mb-5">
                            <div class="form-group w-100">
                                <label for="date_received" class="form-label poppins-bold">Date Received</label>
                                <input type="date" name="date_received" id="date_received" class="form-control" required>
                            </div>
                            <div class="form-group w-100">
                                <label for="book_title" class="form-label poppins-bold">Book Title</label>
                                <input type="text" name="book_title" id="book_title" class="form-control" required>
                            </div>
                            <div class="form-group w-100">
                                <label for="author" class="form-label poppins-bold">Author</label>
                                <input type="text" name="author" id="author" class="form-control" required>
                            </div>
                        </div>
                        <div class="d-flex flex-row gap-4 mb-5">
                            <div class="form-group w-100">
                                <label for="edition" class="form-label poppins-bold">Edition</label>
                                <input type="text" name="edition" id="edition" class="form-control">
                            </div>
                            <div class="form-group w-100">
                                <label for="pages" class="form-label poppins-bold">Pages</label>
                                <input type="number" name="pages" id="pages" class="form-control">
                            </div>
                            <div class="form-group w-100">
                                <label for="source_fund" class="form-label poppins-bold">Source of Fund</label>
                                <input type="text" name="source_fund" id="source_fund" class="form-control">
                            </div>
                            <div class="form-group w-100">
                                <label for="publish" class="form-label poppins-bold">Publisher</label>
                                <input type="text" name="publish" id="publish" class="form-control" required>
                            </div>
                            <div class="form-group w-100">
                                <label for="publish_year" class="form-label poppins-bold">Publish Year</label>
                                <select name="publish_year" id="publish_year" class="form-control">
                                    <option selected hidden>Year Publish</option>
                                    <?php for ($year = date('Y'); $year >= 1900; $year--) { ?>
                                        <option value="<?= $year ?>"><?= $year ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="d-flex flex-row gap-4 mb-5">
                            <div class="form-group w-100">
                                <label for="department" class="form-label poppins-bold">Department</label>
                                <input type="text" name="department" id="department" class="form-control">
                            </div>
                            <div class="form-group w-100">
                                <label for="course_descript" class="form-label poppins-bold">Course Description Major</label>
                                <input type="text" name="course_descript" id="course_descript" class="form-control">
                            </div>
                        </div>
                        <div class="d-flex flex-row gap-4 mb-5">
                            <div class="form-group w-100">
                                <label for="call_number" class="form-label poppins-bold">Call Number</label>
                                <input type="tel" name="call_number" id="call_number" class="form-control">
                            </div>
                            <div class="form-group w-100">
                                <label for="collection" class="form-label poppins-bold">Collection</label>
                                <input type="tel" name="collection" id="collection" class="form-control">
                            </div>
                            <div class="form-group w-100">
                                <label for="quantity" class="form-label poppins-bold">Quantity</label>
                                <input type="number" name="quantity" id="quantity" class="form-control">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary form-control" name="addbook">Save Book</button>
                    </form>
                </div>
            </div>
        </div>
    </main>


    <!-- Bootstrap JS -->
    <script src="../js/libraries/jquery.slim.min.js"></script>
    <script src="../js/libraries/popper.min.js"></script>
    <script src="../js/libraries/bootstrap.min.js"></script>


</body>

</html>