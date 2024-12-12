<?php
include_once __DIR__ . "/../../config/settings.php";
include_once __DIR__ . "/../Authentication.php";

Authentication::checkAccNotLoggedIn("../../page/index.php");
Authentication::redirectUserPage();

$account = $_SESSION["signed_in_user"];

require_once __DIR__ . "/../../config/Database.php";
$db = Database::connect();
$stmt = $db->prepare("SELECT * FROM game");
$stmt->execute();

if($stmt->rowCount() <= 0) {
  echo 
  "<script>
    alert('No Game Available.');
    window.location.href = './dashboard.php';
  </script>";
  exit();
}

$games_total = $stmt->rowCount();
$games = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Delete Game</title>
</head>
<body>
  <form action="../game-class.php" method="post">
    <label for="game">Choose game:</label>
    <select name="game" id="game" required>
      <?php
        for($i = 0; $i < $games_total; $i++) {
          echo "<option value='{$games[$i]['id']}'>{$games[$i]['name']}</option>\n";
        }
      ?>
    </select>
    <input type="submit" name="delete_game" value="Delete Game"/>
  </form>
</body>
</html>