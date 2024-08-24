<?php
include('../conn/conn.php');

if (isset($_POST['print-data'])) {
    $department = $_POST['department'];
    $query = "SELECT * FROM tbl_student ";
    if (!empty($department)) {
        $query .= "WHERE course_section LIKE '%$department%' ";
    }
    $query .= "ORDER BY tbl_student_id DESC";

    $stmt = $conn->query($query);
    $result = $stmt->fetch_all(MYSQLI_ASSOC);

    $students_by_level = [];
    foreach ($result as $row) {
        $course_section = $row['course_section'];
        list($course, $level) = explode('-', $course_section);
        $students_by_level[$level][] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Data</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/jquery.dataTables.css" />
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/admin.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/poppins.css">
    <style>
        table td {
            padding: 6px !important;
        }

        .date-prepared-show {
            display: none;
        }

        .header-background {
            display: none;
        }

        @media print {

            #print,
            #print-label {
                display: none;
            }

            .date-prepared-hide {
                display: none;
            }

            .date-prepared-show {
                display: block;
            }

            .header-background {
                display: block;
                width: 100%;
                margin-bottom: 32px;
                overflow: hidden;
            }

            .header-background img {
                height: 250px;
                width: 100%;
                /* object-fit: scale-down; */
            }
        }
    </style>
    <script>
        function printPage() {
            window.print();
        }
    </script>
</head>

<body>
    <main class="p-5">
        <div class="header-background">
            <div class="img-holder">
                <img src="../img/371279462_326527990136771_929301046899726381_n.png" alt="">
            </div>
        </div>
        <div class="shadow border-card p-5 rounded">
            <div class="student-list">
                <div class="table-container table-responsive">
                    <div class="filter-department mb-5">
                        <div id="prepared-print" class="d-flex flex-row gap-4 justify-content-between align-items-end">
                            <div class="form-group" style="width: 70%;">
                                <label for="department" id="print-label">Click to Print</label>
                                <button type="submit" id="print" onclick="printPage()" class="btn btn-primary">
                                    <img src="../img/icons/print.svg" alt="Print Icon"> Print Data
                                </button>
                            </div>
                            <div class="date-prepared-hide">
                                <b style="color:blue;">Date Prepared:</b>
                                <?php include('current-date.php'); ?>
                            </div>
                        </div>
                        <div class="date-prepared-show">
                            <b style="color:blue;">Date Prepared:</b>
                            <?php include('current-date.php'); ?>
                        </div>
                    </div>

                    <?php foreach ($students_by_level as $level => $students) : ?>
                        <hr>
                        <div class="d-flex flex-row gap-5 mb-2">
                            <div>
                                <span style="font-size: 14px;">Educational Level:</span>
                                <h6><?= $level ?></h6>
                            </div>
                            <div>
                                <span style="font-size: 14px;">Total Count:</span>
                                <h6><?= count($students) ?></h6>
                            </div>
                        </div>
                        <table class="table text-center table-sm" id="studentTable">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col">Name</th>
                                    <th scope="col">Course & Section</th>
                                    <th scope="col">Adviser</th>
                                    <th scope="col">School Year</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $row) : ?>
                                    <tr>
                                        <td><?= $row['student_name'] ?></td>
                                        <td><?= $row['course_section'] ?></td>
                                        <td><?= $row['adviser'] ?></td>
                                        <td><?= $row['school_year'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <br>
                        <br>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </main>
    <script src="../js/libraries/jquery.slim.min.js"></script>
    <script src="../js/libraries/popper.min.js"></script>
    <script src="../js/libraries/bootstrap.min.js"></script>
</body>

</html>