<?php


require_once '../conn/conn.php';
session_start();


if (isset($_GET['view'])) {
    $viewBooks = $_GET['view'];

    $books = $conn->query("SELECT * FROM books WHERE id = '$viewBooks'");
    $books = $books->fetch_assoc();
    $bookId = $books['id'];

    if (empty($books['date_received'])) {
        // echo 'walai sulod ang date';
        $edate_received = '';
    } else {
        // echo 'naay sulod ang date';
        $edate_received = $books['date_received'];
        $edate_received = date('Y-m-d', strtotime($edate_received));
    }
    $eauthor = $books['author'];
    $ebook_title = $books['book_title'];
    $edition = $books['edition'];
    $epages = $books['pages'];
    $esource_fund = $books['source_of_fund'];
    $epublisher = $books['publisher'];
    $eyear = $books['year'];
    $edepartment = $books['department'];
    $edescription = $books['course_description_major'];
    $ecallnumber = $books['call_number'];
    $ecollection  = $books['collection'];
    $equantity = $books['quantity'];
}

// echo json_encode($books);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Details Books</title>

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

        .error {
            color: red;
            font-size: 12px;
        }

        .form-label {
            font-size: 14px;
        }
    </style>
</head>

<body>
    <?php include('./theme/aside.php'); ?>

    <main class="main-container">
        <div class="shadow border-card p-5 rounded">
            <h5 class="text-center">View Book Details</h5>
            <h3 class="text-center"><?= $ebook_title ?></h3>
            <hr style="border-top: 1px dashed;">
            <div class="p-4">
                <form class="mt-4">
                    <div class="d-flex flex-row gap-4 mb-5">
                        <div class="form-group w-100">
                            <label for="mebdate_received" class="form-label">Date Received</label>
                            <input type="date" name="date_received" id="mebdate_received" class="form-control" value="<?= isset($edate_received) ? $edate_received : '' ?>" readonly>
                            <?php if (empty($edate_received)) : ?>
                                <span class="error"><em>No Data Available</em></span>
                            <?php endif; ?>
                        </div>
                        <div class="form-group w-100">
                            <label for="mebauthor" class="form-label">Author</label>
                            <input type="text" name="author" id="mebauthor" class="form-control" value="<?= $eauthor ?>" readonly>
                            <?php if (empty($eauthor)) : ?>
                                <span class="error"><em>No Data Available</em></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="d-flex flex-row gap-4 mb-5">
                        <div class="form-group w-100">
                            <label for="mebedition" class="form-label">Edition</label>
                            <input type="text" name="edition" id="mebedition" class="form-control" value="<?= $edition ?>" readonly>
                            <?php if (empty($edition)) : ?>
                                <span class="error"><em>No Data Available</em></span>
                            <?php endif; ?>
                        </div>
                        <div class="form-group w-100">
                            <label for="mebsource_fund" class="form-label">Source of Fund</label>
                            <input type="text" name="source_fund" id="mebsource_fund" class="form-control" value="<?= $esource_fund ?>" readonly>
                            <?php if (empty($esource_fund)) : ?>
                                <span class="error"><em>No Data Available</em></span>
                            <?php endif; ?>

                        </div>
                    </div>
                    <div class="d-flex flex-row gap-4 mb-5">
                        <div class="form-group w-100">
                            <label for="mebpages" class="form-label">Pages</label>
                            <input type="number" name="pages" id="mebpages" class="form-control" value="<?= $epages ?>" readonly>
                            <?php if (empty($epages)) : ?>
                                <span class="error"><em>No Data Available</em></span>
                            <?php endif; ?>
                        </div>

                        <div class="form-group w-100">
                            <label for="mebpublish" class="form-label">Publisher</label>
                            <input type="text" name="publish" id="mebpublish" class="form-control" value="<?= $epublisher ?>" readonly>
                            <?php if (empty($epublisher)) : ?>
                                <span class="error"><em>No Data Available</em></span>
                            <?php endif; ?>
                            <!-- <span class="error"><em>No Data Available</em></span> -->
                        </div>
                        <div class="form-group w-100">
                            <label for="mebpublish_year" class="form-label">Publish Year</label>
                            <select name="publish_year" id="mebpublish_year" class="form-control" disabled>
                                <option hidden selected disabled value="<?= $eyear ?>"><?= $eyear ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="d-flex flex-row gap-4 mb-5">
                        <div class="form-group w-100">
                            <label for="mebdepartment" class="form-label">Department</label>
                            <input type="text" name="department" id="mebdepartment" class="form-control" value="<?= $edepartment ?>" readonly>
                            <?php if (empty($edepartment)) : ?>
                                <span class="error"><em>No Data Available</em></span>
                            <?php endif; ?>
                        </div>
                        <div class="form-group w-100">
                            <label class="form-label" for="mebcourse_descript">Course Description Major</label>
                            <input type="text" name="course_descript" id="mebcourse_descript" class="form-control" value="<?= $edescription ?>" readonly>
                            <?php if (empty($edescription)) : ?>
                                <span class="error"><em>No Data Available</em></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="d-flex flex-row gap-4 mb-5">
                        <div class="form-group w-100">
                            <label for="mebcall_number" class="form-label">Call Number</label>
                            <input type="tel" name="call_number" id="mebcall_number" class="form-control" value="<?= $ecallnumber ?>" readonly>
                            <?php if (empty($ecallnumber)) : ?>
                                <span class="error"><em>No Data Available</em></span>
                            <?php endif; ?>
                        </div>
                        <div class="form-group w-100">
                            <label for="mebcollection" class="form-label">Collection</label>
                            <input type="tel" name="collection" id="mebcollection" class="form-control" value="<?= $ecollection ?>" readonly>
                            <?php if (empty($ecollection)) : ?>
                                <span class="error"><em>No Data Available</em></span>
                            <?php endif; ?>
                        </div>
                        <div class="form-group w-100">
                            <label for="mebquantity" class="form-label">Quantity</label>
                            <input type="number" name="quantity" id="mebquantity" class="form-control" value="<?= $equantity ?>" readonly>
                            <?php if (empty($equantity)) : ?>
                                <span class="error"><em>No Data Available</em></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
                <div class="col-md-4 mt-2">
                    <a href="viewbooks.php">
                        <button type="buton" class="btn btn-primary form-control">Back</button>
                    </a>
                </div>
            </div>
        </div>
    </main>

    <!-- Bootstrap JS -->
    <!-- <script src="../js/libraries/jquery.slim.min.js"></script> -->
    <script src="../js/libraries/bootstrap.bundle.min.js"></script>
    <script src="../js/libraries/popper.min.js"></script>
    <script src="../js/libraries/bootstrap.min.js"></script>


</body>

</html>