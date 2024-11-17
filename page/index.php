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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../src/css/loginstyle.css">
    <link rel="icon" href="../src/img/logo.png">
    <title>Login & Registration | VOID</title>
</head>

<body>
    <div class="wrapper">
        <nav class="nav">
            <div class="nav-logo">
                <img src="../src/img/logoName.png" alt="logo" height="80px">
            </div>

            <div class="nav-button">
                <button class="btn white-btn" id="loginBtn" onclick="login()">Sign In</button>
                <button class="btn" id="registerBtn" onclick="register()">Sign Up</button>
            </div>
        </nav>
        <form action="../dashboard/Authentication.php" method="POST">
            <!----------------------------- Form box ----------------------------------->
            <div class="form-box">

                <!------------------- login form -------------------------->

                <div class="login-container" id="login">
                    <div class="top">
                        <header>Login</header>
                    </div>
                    <div class="input-box">
                        <input type="email" class="input-field" name="email" required placeholder="Email">
                        <i class="bx bx-user"></i>
                    </div>
                    <div class="input-box">
                        <input type="password" class="input-field" name="password" required placeholder="Password">
                        <i class="bx bx-lock-alt"></i>
                    </div>
                    <div class="input-box">
                        <a href="#"><input type="submit" class="submit" name="signin" value="Sign In"></a>
                    </div>
                    <div class="two-col">
                        <div class="one">
                            <span>Don't have an account? <a href="#" onclick="register()">Sign Up</a></span>
                        </div>
                        <div class="two">
                            <label><a href="./forgotpassword.php">Forgot password?</a></label>
                        </div>
                    </div>
                </div>
        </form>


        <!------------------- registration form -------------------------->
        <div class="register-container" id="register">
            <form action="../dashboard/Authentication.php" method="POST">
                <div class="top">
                    <header>Sign Up</header>
                </div>
                <div class="two-forms">
                    <div class="input-box">
                        <input type="text" class="input-field" name="name" placeholder="Name">
                        <i class="bx bx-user"></i>
                    </div>
                    <div class="input-box">
                        <input type="text" class="input-field" name="username" required placeholder="Username">
                        <i class="bx bx-user"></i>
                    </div>
                </div>
                <div class="input-box">
                    <input type="email" class="input-field" name="email" required placeholder="Email">
                    <i class="bx bx-envelope"></i>
                </div>
                <div class="input-box">
                    <input type="password" class="input-field" name="password" required placeholder="Password">
                    <i class="bx bx-lock-alt"></i>
                </div>
                <div class="input-box">
                    <input type="submit" class="submit" name="signup" value="Register">
                </div>
                <div class="three-col">
                    <div class="one">
                        <span>Have an account? <a href="#" onclick="login()">Login</a></span>
                    </div>
                </div>
            </form>
        </div>

    </div>

    </div>
    <script src="../src/js/nav-menu.js"></script>
    <script src="../src/js/signupin-transition.js"></script>
</body>
</html>