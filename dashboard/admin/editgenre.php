<?php
include_once __DIR__ . "/../../config/settings.php";
include_once __DIR__ . "/../Authentication.php";

Authentication::checkAccNotLoggedIn("../../page/index.php");
Authentication::redirectUserPage();

$account = $_SESSION["signed_in_user"];

require_once __DIR__ . "/../../config/Database.php";
$db = Database::connect();
$stmt = $db->prepare("SELECT * FROM genre");
$stmt->execute();

if($stmt->rowCount() <= 0) {
  echo 
  "<script>
    alert('No Genre Available.');
    window.location.href = './dashboard.php';
  </script>";
  exit();
}

$genre_total = $stmt->rowCount();
$genres = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Genre</title>
</head>
<body>
  <form action="../game-class.php" method="post">
    <label for="genre">Choose genre:</label>
    <select name="genre" id="genre" required>
      <?php
        for($i = 0; $i < $genre_total; $i++) {
          echo "<option value='{$genres[$i]['id']}'>{$genres[$i]['name']}</option>\n";
        }
      ?>
    </select>
    <br>
    <label for="name">New name:</label>
    <input type="text" name="name" required/>
    <br>
    <input type="submit" name="edit_genre" value="Edit Genre"/>
  </form>
</body>
</html>