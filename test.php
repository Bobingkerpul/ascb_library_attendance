<?php
session_start();
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception;


function generateRandomCode($length = 10)
{
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomIndex = rand(0, strlen($characters) - 1);
        $randomString .= $characters[$randomIndex];
    }
    return $randomString;
}

if (isset($_POST['import'])) {
    $fileName = $_FILES['file']['name'];
    $fileTmp = $_FILES['file']['tmp_name'];

    // Load the spreadsheet
    try {
        $spreadsheet = IOFactory::load($fileTmp);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        require_once '../conn/conn.php';

        // Array to store successfully imported student names
        $successMessages = [];

        foreach ($rows as $row) {
            $name = $row[0];
            $gender = $row[1];
            $course = $row[2];
            $adviser = $row[3];
            $gmail = $row[4];
            $contact = $row[5];
            $school_year = $row[6];

            // Generate random QR code
            $qrCode = generateRandomCode();

            // Check if the student already exists in the database with the same data

            $stmt = $conn->prepare("SELECT COUNT(*) FROM tbl_student WHERE student_name = ? AND course_section = ? AND school_year = ?");
            $stmt->bind_param("sss", $name, $course, $school_year);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();

            if ($count == 0) {
                // Insert into database
                $stmt = $conn->prepare("INSERT INTO tbl_student (student_name, course_section, contact, gender, generated_code, gmail, adviser, school_year) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssssss", $name, $course, $contact, $gender, $qrCode, $gmail, $adviser, $school_year);
                $stmt->execute();
                $stmt->close();

                $successMessages[] = $name;
            } else {

                // Check if the exact data exists in any row
                $exactDataExists = false;

                $existingRecordsStmt = $conn->prepare("SELECT COUNT(*) FROM tbl_student WHERE student_name = ? AND course_section = ? AND school_year = ?");
                $existingRecordsStmt->bind_param("sss", $name, $course, $school_year);
                $existingRecordsStmt->execute();
                $existingRecordsStmt->bind_result($existingCount);
                $existingRecordsStmt->fetch();
                $existingRecordsStmt->close();

                if ($existingCount > 0) {
                    // Fetch all rows with the exact same data
                    $fetchDataStmt = $conn->prepare("SELECT student_name, course_section, school_year FROM tbl_student WHERE student_name = ? AND course_section = ? AND school_year = ?");
                    $fetchDataStmt->bind_param("sss", $name, $course, $school_year);
                    $fetchDataStmt->execute();
                    $fetchDataStmt->bind_result($existingName, $existingCourse, $existingSchoolYear);

                    while ($fetchDataStmt->fetch()) {
                        // Compare each row's data with the current row being imported
                        if ($existingName == $name && $existingCourse == $course && $existingSchoolYear == $school_year) {
                            $exactDataExists = true;
                            break;
                        }
                    }

                    $fetchDataStmt->close();
                }

                if (!$exactDataExists) {
                    // Insert into database if exact data does not exist
                    $stmt = $conn->prepare("INSERT INTO tbl_student (student_name, course_section, contact, gender, generated_code, gmail, adviser, school_year) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssssss", $name, $course, $contact, $gender, $qrCode, $gmail, $adviser, $school_year);
                    $stmt->execute();
                    $stmt->close();

                    // Add student name to success messages array
                    $successMessages[] = $name;
                } else {
                    // Set session variable for existing record
                    $_SESSION['exists'][] = "Record already exists for $name in $course for the school year $school_year";
                }
            }
        }

        // Store success messages in session
        if (!empty($successMessages)) {
            $_SESSION['success'] = "Successfully imported students: " . implode(", ", $successMessages);
        }

        $conn->close();

        // Redirect to masterlist.php
        header('location:masterlist.php');
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = "Error loading file: " . $e->getMessage();
        header('location:masterlist.php');
        exit;
    }
}
