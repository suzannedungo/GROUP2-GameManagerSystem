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

$genres = false;
$genre_total = 0;
if($stmt->rowCount() > 0) {
  $genre_total = $stmt->rowCount();
  $genres = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$stmt = $db->prepare("SELECT * FROM game");
$stmt->execute();

$games = false;
$games_total = 0;
if($stmt->rowCount() > 0) {
  $games_total = $stmt->rowCount();
  $games = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../../popup_card/style.css">
  <link rel="stylesheet" href="../../input_popup_card/style.css">
  <title>Dashboard</title>
</head>
<body>
  <h1>Hi Admin <?= $account['name'] ?>!</h1>
  <a href="../Authentication.php?logout">Logout</a>

  <div>
    <h1>Add Genre</h1>
    <form action="../game-class.php" method="post">
      <label for="name">Genre Name: </label>
      <input type="text" name="name" id="genre_name" placeholder="Genre Name" required />
      <br />
      <input type="submit" name="add_genre" value="Add Genre" />
    </form>
  </div>

  <hr />

  <div>
    <h1>Add Game</h1>
    <form action="../game-class.php" method="POST" enctype="multipart/form-data">
      <label for="name">Name: </label>
      <input type="text" name="name" id="name" placeholder="Name" required />

      <br/>

      <label for="developer">Developer: </label>
      <input type="text" name="developer" id="developer" placeholder="Developer" required />

      <br/>

      <label for="info">Info: </label>
      <textarea name="info" id="info" placeholder="Information" required></textarea>

      <br/>

      <label for="dl_link">Download Link: </label>
      <input type="text" name="dl_link" id="dl_link" placeholder="Download Link" required />

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
      <input type="file" name="icon" id="icon" accept=".jpg,.jpeg,.png,.webp,.avif" required />

      <br />

      <label for="bg">Choose Background Image: </label>
      <input type="file" name="bg" id="bg" accept=".jpg,.jpeg,.png,.webp,.avif" required />

      <br />

      <label for="samples[]">Choose 5 Samples: </label>
      <input type="file" name="samples[]" id="samples" multiple accept=".jpg,.jpeg,.png,.webp,.avif" required />

      <br />

      <input type="submit" name="add_game" id="add_game" value="Add Game" />
    </form>
  </div>

  <hr />

  <div>
    <h1>Genres</h1>
    <a href="./editgenre.php">Edit</a>
    <br />
    <a href="./deletegenre.php">Delete</a>
    <form>
        <?php
          if($genre_total <= 0) {
            echo "<i>No Genre Yet.</i>";
          } else {
            for($i = 0; $i < $genre_total; $i++) {
              echo "<p>{$genres[$i]['name']}</p>\n";
            }
          }
        ?>
    </form>
  </div>

  <div>
    <h1>Games</h1>
    <a href="./editgame.php">Edit</a>
    <br />
    <a href="./deletegame.php">Delete</a>
    <form>
        <?php
          if($games_total <= 0) {
            echo "<i>No Games Yet.</i>";
          } else {
            for($i = 0; $i < $games_total; $i++) {
              $img = "../../src/uploads/games_images/{$games[$i]['id']}";

              // Scan the directory for all files and folders
              $files = scandir($img);
              $file = null;

              // Loop through the files
              foreach ($files as $file) {
                // Skip . and .. (current and parent directory)
                if ($file === "." || $file === "..") {
                    continue;
                }

                // Extract the file name without extension
                $fileNameWithoutExt = pathinfo($file, PATHINFO_FILENAME);

                // Compare it with the search name
                if ($fileNameWithoutExt === "icon") {
                    $img = $img . "/" . $file; // Return the full file name with extension
                }
              }

              echo "<a href='./gamepreview.php?id={$games[$i]['id']}'>";
              echo "<img src='{$img}' alt='image'/>";
              echo "<p>{$games[$i]['name']}</p>\n";
              echo "</a>";
            }
          }
        ?>
    </form>
  </div>
</body>
</html>