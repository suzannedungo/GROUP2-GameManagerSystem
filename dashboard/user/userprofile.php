<?php
require_once __DIR__ . "/../../config/settings.php";
require_once __DIR__ . "/../Authentication.php";

Authentication::checkAccNotLoggedIn("../../page/index.php");
Authentication::redirectAdminPage();

$account = $_SESSION["signed_in_acc"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Profile | Void</title>
</head>
<body>
  <h1>Hi <?= $account['username'] ?>!</h1>
</body>
</html>