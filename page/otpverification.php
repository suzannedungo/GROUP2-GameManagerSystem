<?php
include_once __DIR__ . "/../config/settings.php";
include_once __DIR__ . "/../dashboard/Authentication.php";

Authentication::checkOTPVerifyNotOnGoing();
Authentication::checkAccLoggedIn();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP | VOID</title>
    <link rel="icon" href="../src/img/logo.png"> <!--logo-->
    <link rel="stylesheet" href="../src/css/styleforgotpass.css">
</head>

<body>
    <div class="form-container">
        <div class="title-logo">
            <img src="../src/img/logoName.png" alt="logo name" height="20px">
        </div>
        <div class="title">
            OTP VERIFICATION
        </div>
        <form class="form" action="../dashboard/Authentication.php" method="POST">
            <div class="form-group">
                <label for="email">Enter OTP</label>
                <input type="number" name="otp" placeholder="OTP" required>
            </div>
            <button class="form-submit-btn" name="otp_sent" type="submit">Verify Account</button>
        </form>
        

    </div>
</body>

</html>