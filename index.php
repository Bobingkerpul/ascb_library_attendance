<?php
session_start();

require_once './conn/conn.php';

$_SESSION["user"] = "";
$_SESSION["usertype"] = "";

date_default_timezone_set('Asia/Manila');

$date = date('Y-m-d');

$_SESSION["date"] = $date;

if ($_POST) {

    $usermail = $_POST['email'];
    $userpassword = $_POST['password'];

    // echo $userpassword . '' . $usermail;
    // exit;

    $getemail = $conn->query("SELECT * FROM webuser WHERE email = '$usermail'");

    if ($getemail->num_rows == 1) {

        $usertype = $getemail->fetch_assoc()['usertype'];
        // echo $usertype;
        // exit;

        if ($usertype == 'a') {
            // Patient Login
            $validate = $conn->query("SELECT * FROM admin WHERE username = '$usermail' AND password = '$userpassword'");

            if ($validate->num_rows == 1) {

                $_SESSION['user'] = $usermail;
                $_SESSION['usertype'] = 'a';
                header('location: admin/index.php');
                // exit;
            } else {
                $error = "Wrong credentials: Invalid Email or Password!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Attendance</title>
    <link rel="stylesheet" href="./css/login.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        .clock {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translateX(-50%) translateY(-100%);
            color: #fff;
            font-size: 60px;
            letter-spacing: 7px;
            font-weight: 200;
        }

        .heading-intro {
            position: absolute;
            top: 65%;
            left: 50%;
            transform: translateX(-50%) translateY(-220%);
            font-weight: 900;
        }

        .image-logo {
            width: 150px;
            height: 150px;
            position: absolute;
            margin-top: 20px;
        }

        .image-logo img {
            width: 100%;
        }
    </style>
</head>

<body>
    <header></header>
    <main>
        <section>
            <div class="wrapper">
                <div class="image-logo">
                    <img src="./img/ascblogo.png" alt="ASCB Logo">
                </div>
                <h1 class="text-white heading-intro text-center">ASCB Library Information<br />Management System using QR Code</h1>
                <div id="MyClockDisplay" class="clock" onload="showTime()"></div>
            </div>
        </section>
        <section class="form-section">
            <div class="col-md-4 form-login">
                <div class="input-box m-auto">
                    <form action="" method="post" autocomplete="off">
                        <div class="input-field">
                            <input type="text" class="input" id="email" name="email" required="">
                            <label for="email">Email</label>
                        </div>
                        <div class="input-field">
                            <input type="password" class="input" id="pass" name="password" required="">
                            <label for="pass">Password</label>
                        </div>
                        <div class="input-field">
                            <input type="submit" class="submit" value="Login">
                        </div>
                        <?php
                        if (isset($error)) {
                        ?>
                            <p style="color: #8E0D0D; font-size: 14px;" class="text-center"><?= $error ?></p>
                        <?php
                        }
                        ?>
                    </form>
                </div>
            </div>
        </section>
    </main>
    <script>
        function showTime() {
            var date = new Date();
            var h = date.getHours(); // 0 - 23
            var m = date.getMinutes(); // 0 - 59
            var s = date.getSeconds(); // 0 - 59
            var session = "AM";

            if (h == 0) {
                h = 12;
            }

            if (h > 12) {
                h = h - 12;
                session = "PM";
            }

            h = (h < 10) ? "0" + h : h;
            m = (m < 10) ? "0" + m : m;
            s = (s < 10) ? "0" + s : s;

            var time = h + ":" + m + ":" + s + " " + session;
            document.getElementById("MyClockDisplay").innerText = time;
            document.getElementById("MyClockDisplay").textContent = time;

            setTimeout(showTime, 1000);

        }

        showTime();
    </script>
</body>

</html>