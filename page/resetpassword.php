<?php
include_once __DIR__ . "/../config/settings.php";
include_once __DIR__ . "/../dashboard/Authentication.php";
require_once __DIR__ . "/../config/Database.php";

Authentication::checkOTPVerifyOnGoing();
Authentication::checkAccLoggedIn();

if(!isset($_GET["id"]) && !isset($_GET["tokencode"])) {
    header("Location: ./index.php");
    exit();
}

$stmt = (Database::connect())->prepare("SELECT * FROM account WHERE id = :id");
$stmt->execute([":id" => $_GET["id"]]);

if($stmt->rowCount() <= 0) {
  echo "
    <script>
      alert(\"No Account ID Found in our records.\");
      window.location.href = \"../page/index.php\";
    </script>
  ";
  exit();
}

$account_info = $stmt->fetch(PDO::FETCH_ASSOC);

if($_GET["tokencode"] !== $account_info["tokencode"]) {
  echo "
    <script>
      alert(\"Invalid Token Code!\");
      window.close();
    </script>
  ";
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | VOID</title>
    <link rel="icon" href="../src/img/logo.png"> <!--logo-->
    <link rel="stylesheet" href="../src/css/styleresetpass.css">
</head>

<body>
    <div class="form-container">
        <div class="title-logo">
            <img src="../src/img/logoName.png" alt="logo name" height="20px">
        </div>
        <div class="title">
            Reset Password
        </div>
        <form class="form" action="../dashboard/Authentication.php" method="POST">
            <input type="hidden" name="id" value="<?= $_GET['id'] ?>">
            <input type="hidden" name="tokencode" value="<?= $_GET['tokencode'] ?>">
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" placeholder="Enter your new password" required><br>
            </div>
            <button class="form-submit-btn" name="reset_pass" type="submit">Submit</button>
        </form>
    </div>
</body>
</html>
