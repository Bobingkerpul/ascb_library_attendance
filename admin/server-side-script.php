<?php

include('../conn/conn.php');
// SELECT * FROM `tbl_student`

$query = "SELECT * FROM tbl_student";
$result = $conn->query($query);

// Pag-check kung may result at pag-format ng data para sa DataTable
$data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

// I-echo ang JSON data
echo json_encode(['data' => $data]);

// I-close ang database connection
$conn->close();
