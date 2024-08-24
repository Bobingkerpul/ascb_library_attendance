<?php

require_once '../conn/conn.php';

$id = $_GET['id'];


$view = $conn->query("SELECT * FROM books WHERE id = '$id'");
$view = $view->fetch_array(MYSQLI_ASSOC);

echo json_encode($view);
