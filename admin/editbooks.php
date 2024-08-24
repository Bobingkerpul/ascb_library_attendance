<?php


require_once '../conn/conn.php';
session_start();







if (isset($_GET['editbooks'])) {
    $modalEditBooks = $_GET['editbooks'];

    $books = $conn->query("SELECT * FROM books WHERE id = '$modalEditBooks'");
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
            <h2 class="text-center">Edit Book Details</h2>
            <hr style="border-top: 1px dashed;">
            <div class="p-4">
                <form method="post" action="updatebooks.php" class="mt-4">
                    <input type="hidden" name="id" id="mebid" value="<?= $bookId ?>">
                    <div class="d-flex flex-row gap-4 mb-5">
                        <div class="form-group w-100">
                            <label for="mebdate_received" class="form-label">Date Received</label>
                            <input type="date" name="date_received" id="mebdate_received" class="form-control" value="<?= isset($edate_received) ? $edate_received : '' ?>">
                            <?php if (empty($edate_received)) : ?>
                                <span class="error"><em>No Data Available</em></span>
                            <?php endif; ?>
                        </div>
                        <div class="form-group w-100">
                            <label for="mebbook_title" class="form-label">Book Title</label>
                            <input type="text" name="book_title" id="mebbook_title" class="form-control" value="<?= $ebook_title ?>">
                            <?php if (empty($ebook_title)) : ?>
                                <span class="error"><em>No Data Available</em></span>
                            <?php endif; ?>
                        </div>
                        <div class="form-group w-100">
                            <label for="mebauthor" class="form-label">Author</label>
                            <input type="text" name="author" id="mebauthor" class="form-control" value="<?= $eauthor ?>">
                            <?php if (empty($eauthor)) : ?>
                                <span class="error"><em>No Data Available</em></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="d-flex flex-row gap-4 mb-5">
                        <div class="form-group w-100">
                            <label for="mebedition" class="form-label">Edition</label>
                            <input type="text" name="edition" id="mebedition" class="form-control" value="<?= $edition ?>">
                            <?php if (empty($edition)) : ?>
                                <span class="error"><em>No Data Available</em></span>
                            <?php endif; ?>
                        </div>
                        <div class="form-group w-100">
                            <label for="mebsource_fund" class="form-label">Source of Fund</label>
                            <input type="text" name="source_fund" id="mebsource_fund" class="form-control" value="<?= $esource_fund ?>">
                            <?php if (empty($esource_fund)) : ?>
                                <span class="error"><em>No Data Available</em></span>
                            <?php endif; ?>

                        </div>
                    </div>
                    <div class="d-flex flex-row gap-4 mb-5">
                        <div class="form-group w-100">
                            <label for="mebpages" class="form-label">Pages</label>
                            <input type="number" name="pages" id="mebpages" class="form-control" value="<?= $epages ?>">
                            <?php if (empty($epages)) : ?>
                                <span class="error"><em>No Data Available</em></span>
                            <?php endif; ?>
                        </div>

                        <div class="form-group w-100">
                            <label for="mebpublish" class="form-label">Publisher</label>
                            <input type="text" name="publish" id="mebpublish" class="form-control" value="<?= $epublisher ?>">
                            <?php if (empty($epublisher)) : ?>
                                <span class="error"><em>No Data Available</em></span>
                            <?php endif; ?>
                            <!-- <span class="error"><em>No Data Available</em></span> -->
                        </div>
                        <div class="form-group w-100">
                            <label for="mebpublish_year" class="form-label">Publish Year</label>
                            <select name="publish_year" id="mebpublish_year" class="form-control">
                                <option hidden selected value="<?= $eyear ?>"><?= $eyear ?></option>
                                <?php for ($year = date('Y'); $year >= 1900; $year--) { ?>
                                    <option value="<?= $year ?>"><?= $year ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="d-flex flex-row gap-4 mb-5">
                        <div class="form-group w-100">
                            <label for="mebdepartment" class="form-label">Department</label>
                            <input type="text" name="department" id="mebdepartment" class="form-control" value="<?= $edepartment ?>">
                            <?php if (empty($edepartment)) : ?>
                                <span class="error"><em>No Data Available</em></span>
                            <?php endif; ?>
                        </div>
                        <div class="form-group w-100">
                            <label class="form-label" for="mebcourse_descript">Course Description Major</label>
                            <input type="text" name="course_descript" id="mebcourse_descript" class="form-control" value="<?= $edescription ?>">
                            <?php if (empty($edescription)) : ?>
                                <span class="error"><em>No Data Available</em></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="d-flex flex-row gap-4 mb-5">
                        <div class="form-group w-100">
                            <label for="mebcall_number" class="form-label">Call Number</label>
                            <input type="tel" name="call_number" id="mebcall_number" class="form-control" value="<?= $ecallnumber ?>">
                            <?php if (empty($ecallnumber)) : ?>
                                <span class="error"><em>No Data Available</em></span>
                            <?php endif; ?>
                        </div>
                        <div class="form-group w-100">
                            <label for="mebcollection" class="form-label">Collection</label>
                            <input type="tel" name="collection" id="mebcollection" class="form-control" value="<?= $ecollection ?>">
                            <?php if (empty($ecollection)) : ?>
                                <span class="error"><em>No Data Available</em></span>
                            <?php endif; ?>
                        </div>
                        <div class="form-group w-100">
                            <label for="mebquantity" class="form-label">Quantity</label>
                            <input type="number" name="quantity" id="mebquantity" class="form-control" value="<?= $equantity ?>">
                            <?php if (empty($equantity)) : ?>
                                <span class="error"><em>No Data Available</em></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" name="updatebooks" class="btn btn-primary form-control">Update</button>
                    </div>
                </form>
                <div class="col-md-4 mt-2">
                    <a href="viewbooks.php">
                        <button type="buton" class="btn btn-warning form-control">Cancel</button>
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