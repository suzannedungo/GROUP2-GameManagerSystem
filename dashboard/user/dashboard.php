<?php
include_once __DIR__ . "/../../config/settings.php";
include_once __DIR__ . "/../Authentication.php";
require_once __DIR__ . "/../../config/Database.php";

Authentication::checkAccNotLoggedIn("../../../../page/index.php");
Authentication::redirectAdminPage();

$db = Database::connect();
$account = $_SESSION["signed_in_acc"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
</head>
<body>
  <h1>Hi User <?= $account['name'] ?>!</h1>
  <a href="../Authentication.php?logout">Logout</a>
  <h1>Games</h1>
  <div id="games_container">
    <?php
      $stmt = $db->prepare("SELECT * FROM game");
      if(!($stmt->execute())) {
        http_response_code(500);
        echo
        "<script>
          alert(\"An error occured on fetching all games.\");
          window.location.href = \"./\";
        </script>";
        exit();
      }

      $games = $stmt->fetchAll(PDO::FETCH_ASSOC);

      for($i = 0; $i < $stmt->rowCount(); $i++) {
        echo
        "<div>
          <a href=\"./game.php?id={$games[$i]['id']}\">
            <img src=\"{$games[$i]['image']}\" alt=\"{$games[$i]['image']}\">
            <h3>{$games[$i]['name']}</h3>
          </a>
        </div>";
      }
    ?>
  </div>
</body>
</html>