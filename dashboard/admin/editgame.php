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

$stmt = $db->prepare("SELECT * FROM genre");
$stmt->execute();

/*
if($stmt->rowCount() <= 0) {
  echo 
  "<script>
    alert('No Genre Available.');
    window.location.href = './dashboard.php';
  </script>";
  exit();
}
  */

$genre_total = 0;
$genres = false;
if($stmt->rowCount() > 0) {
  $genre_total = $stmt->rowCount();
  $genres = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Game</title>
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

    <br>

    <label for="name">Name: </label>
    <input type="text" name="name" id="name" placeholder="Name"/>

    <br/>

    <label for="developer">Developer: </label>
    <input type="text" name="developer" id="developer" placeholder="Developer"/>

    <br/>

    <label for="info">Info: </label>
    <textarea name="info" id="info" placeholder="Information"></textarea>

    <br/>

    <label for="dl_link">Download Link: </label>
    <input type="text" name="dl_link" id="dl_link" placeholder="Download Link"/>

    <br />

    <?php
      if($genre_total > 0) {
        echo "<label>Pick At Least 1 Genre:</label>";
        echo "<br />";
      }

      for($i = 0; $i < $genre_total; $i++) {
        echo "<input type='checkbox' name='genres[]' class='genres' value='{$genres[$i]['id']}'/> {$genres[$i]['name']}";
        echo "<br />";
      }
    ?>

    <br />

    <label for="icon">Choose Icon: </label>
    <input type="file" name="icon" id="icon" accept=".jpg,.jpeg,.png,.webp,.avif"/>

    <br />

    <label for="bg">Choose Background Image: </label>
    <input type="file" name="bg" id="bg" accept=".jpg,.jpeg,.png,.webp,.avif"/>

    <br />

    <label for="samples[]">Choose 5 Samples: </label>
    <input type="file" name="samples[]" id="samples" multiple accept=".jpg,.jpeg,.png,.webp,.avif"/>

    <br />

    <input type="submit" name="edit_game" value="Edit Game"/>
  </form>
</body>
</html>