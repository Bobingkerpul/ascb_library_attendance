<?php
require_once '../conn/conn.php';

if (isset($_POST['downloadQR'])) {
    $studentname = $_POST['studentname'];
    $studentcontact = $_POST['studentcontact'];
    $qrcode = $_POST['qrcode'];

    $qrcodeContent = "Student Name: " . $studentname . "\nStudent Contact: " . $studentcontact;

    // Set the actual size of ID
    $cardwidth = 400;
    $cardheight = 300;

    // Load the background image
    $backgroundPath = '../img/ascblogo.jpg';
    if (!file_exists($backgroundPath)) {
        echo "Background image not found!";
        exit;
    }

    $background = imagecreatefromjpeg($backgroundPath);

    $cardimage = imagecreatetruecolor($cardwidth, $cardheight);

    imagecopyresampled($cardimage, $background, 0, 0, 0, 0, $cardwidth, $cardheight, imagesx($background), imagesy($background));

    $opacity = 80;
    $opacityColor = imagecolorallocatealpha($cardimage, 255, 255, 255, 100 * (100 - $opacity) / 100);
    imagefilledrectangle($cardimage, 0, 0, $cardwidth, $cardheight, $opacityColor);

    $qrcodeimage = imagecreatefromstring(file_get_contents("https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($qrcode)));
    imagecopyresampled($cardimage, $qrcodeimage, 50, 50, 0, 0, 270, 180, imagesx($qrcodeimage), imagesy($qrcodeimage));

    $headingColor = imagecolorallocate($cardimage, 0, 0, 0);
    $fontHeading = '../css/fontswoff/Poppins-Semibold-600.ttf';

    $heading = "Library QR Code Attendance";
    $headingX = 50;
    $headingY = 30;

    imagettftext($cardimage, 14, 0, $headingX, $headingY, $headingColor, $fontHeading, $heading);


    $textColor = imagecolorallocate($cardimage, 0, 0, 0);
    $font = '../css/fontswoff/Poppins-Regular-400.ttf';

    $text = "Student Name:" . $studentname . "\nStudent Contact: " . $studentcontact;
    $textX = 50;
    $textY = 260;


    $lines = explode("\n", $text);
    foreach ($lines as $line) {
        imagettftext($cardimage, 12, 0, $textX, $textY, $textColor, $font, $line);
        $textY += 20;
    }
    header("Content-Type: image/jpeg");
    header("Content-Disposition: attachment; filename=$studentname Student QR Code Card.jpg");


    imagejpeg($cardimage);

    // Free up memory
    imagedestroy($cardimage);
    imagedestroy($qrcodeimage);
    imagedestroy($background);


    // // Save image to a file
    // $imagePath = 'generated_student_card.jpg';
    // imagejpeg($cardimage, $imagePath);

    // // Free up memory
    // imagedestroy($cardimage);
    // imagedestroy($qrcodeimage);
    // imagedestroy($background);

    // // Display the image
    // echo '<img src="' . $imagePath . '" alt="Generated Student Card">';
    // echo '<br>';
    // echo '<a href="' . $imagePath . '" download="student_card.jpg">Download Image</a>';

    exit;
} else {
    echo "Invalid request.";
}
