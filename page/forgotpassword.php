<?php
include_once __DIR__ . "/../config/settings.php";
include_once __DIR__ . "/../dashboard/Authentication.php";

Authentication::checkOTPVerifyOnGoing();
Authentication::checkAccLoggedIn();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | VOID</title>
    <link rel="icon" href="../src/img/logo.png"> <!--logo-->
    <link rel="stylesheet" href="../src/css/styleforgotpass.css">
</head>

<body>
    <div class="form-container">
        <div class="title-logo">
            <img src="../src/img/logoName.png" alt="logo name" height="20px">
        </div>
        <div class="title">
            Forgot Password
        </div>
        <form class="form" action="../dashboard/Authentication.php" method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" placeholder="Enter your Email" required>
            </div>
            <button class="form-submit-btn" name="find_email" type="submit">Send Email</button>
        </form>
        

    </div>
</body>

</html>