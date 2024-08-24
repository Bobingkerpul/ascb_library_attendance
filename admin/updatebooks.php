<?php
require_once '../conn/conn.php';
session_start();


if (isset($_POST['updatebooks'])) {
    $mebid = $_POST['id'];
    $date_received = trim($_POST['date_received']);
    $book_title = trim($_POST['book_title']);
    $author = trim($_POST['author']);
    $edition = trim($_POST['edition']);
    $pages = trim($_POST['pages']);
    $source_fund = trim($_POST['source_fund']);
    $publish = trim($_POST['publish']);
    $publish_year = trim($_POST['publish_year']);
    $department = trim($_POST['department']);
    $course_descript = trim($_POST['course_descript']);
    $call_number = trim($_POST['call_number']);
    $collection = trim($_POST['collection']);
    $quantity = trim($_POST['quantity']);

    //---------------------------------
    $books = $conn->query("SELECT * FROM books WHERE id = '$mebid'");
    $books = $books->fetch_assoc();

    if (empty($books['date_received'])) {
        $edate_received = '';
    } else {
        $edate_received = date('Y-m-d', strtotime($books['date_received']));
    }
    $eauthor = trim($books['author']);
    $ebook_title = trim($books['book_title']);
    $eedition = trim($books['edition']);
    $epages = trim($books['pages']);
    $esource_fund = trim($books['source_of_fund']);
    $epublisher = trim($books['publisher']);
    $eyear = trim($books['year']);
    $edepartment = trim($books['department']);
    $edescription = trim($books['course_description_major']);
    $ecallnumber = trim($books['call_number']);
    $ecollection  = trim($books['collection']);
    $equantity = trim($books['quantity']);

    $no_changes = true;
    if (
        $date_received !== $edate_received ||
        $book_title !== $ebook_title ||
        $author !== $eauthor ||
        $edition !== $eedition ||
        $pages !== $epages ||
        $source_fund !== $esource_fund ||
        $publish !== $epublisher ||
        $publish_year !== $eyear ||
        $department !== $edepartment ||
        $course_descript !== $edescription ||
        $call_number !== $ecallnumber ||
        $collection !== $ecollection ||
        $quantity !== $equantity
    ) {
        $no_changes = false;
    }


    // if (!$no_changes) {
    //     echo 'Naay ge bago';
    // } else {
    //     echo 'Walai ge bago';
    // }

    if (!$no_changes) {
        $conn->query("UPDATE books SET 
            date_received='$date_received',
            author='$author',
            book_title='$book_title',
            edition='$edition',
            pages='$pages',
            source_of_fund='$source_fund',
            publisher='$publish',
            year='$publish_year',
            department='$department',
            course_description_major='$course_descript',
            call_number='$call_number',
            collection='$collection',
            quantity='$quantity' 
            WHERE id = '$mebid'");

        $_SESSION["message"] = "The Book " . "<span class='quote'>$book_title</span>" . " Update completed";
        echo $_SESSION["message"];
        header('location:viewbooks.php');
    } else {
        $_SESSION["message2"] = "The Book " . "<span class='quote'>$book_title</span>" . " Absolutely everything remains exactly the same.";
        echo $_SESSION["message2"];
        header('location:viewbooks.php');
    }
}
