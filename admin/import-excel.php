<?php
session_start(); // Simulan ang session
require '../vendor-import-excel/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

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

// Initialize session variables
if (!isset($_SESSION['success'])) {
    $_SESSION['success'] = [];
}
if (!isset($_SESSION['exists'])) {
    $_SESSION['exists'] = [];
}

if (isset($_POST['import'])) {
    $fileName = $_FILES['file']['name'];
    $fileTmp = $_FILES['file']['tmp_name'];

    try {
        // Load the spreadsheet
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
            $grade_level = $row[3];
            $educational_level = $row[4];
            $department = $row[5];
            $adviser = $row[6];
            $gmail = $row[7];
            $contact = $row[8];
            $school_year = $row[9];

            // Concatenate course, grade level, educational level, and department
            if ($educational_level == 'Elementary') {
                $concatenatedInfo = "$grade_level - $educational_level - $department";
            } else {
                $concatenatedInfo = "$course - $grade_level - $educational_level - $department";
            }

            // Generate random QR code
            $qrCode = generateRandomCode();

            // Check if the student already exists in the database with the same data
            $stmt = $conn->prepare("SELECT COUNT(*) FROM tbl_student WHERE student_name = ? AND course_section = ? AND school_year = ?");
            $stmt->bind_param("sss", $name, $concatenatedInfo, $school_year);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();

            if ($count == 0) {
                // Insert into database
                $stmt = $conn->prepare("INSERT INTO tbl_student (student_name, course_section, contact, gender, generated_code, gmail, adviser, school_year) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssssss", $name, $concatenatedInfo, $contact, $gender, $qrCode, $gmail, $adviser, $school_year);
                $stmt->execute();
                $stmt->close();

                // Format the success message similarly to the existing record message
                $successMessages[] = " $name in $concatenatedInfo for the school year SY $school_year";
            } else {
                // Set session variable for existing record
                $_SESSION['exists'][] = " $name in $concatenatedInfo for the school year SY $school_year";
            }
        }

        // Store success messages in session
        if (!empty($successMessages)) {
            $_SESSION['success'] = $successMessages;
        }

        $conn->close();

        // Redirect to masterlist.php
        header('Location: masterlist.php');
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = "Error loading file: " . $e->getMessage();
        header('Location: masterlist.php');
        exit;
    }
}
