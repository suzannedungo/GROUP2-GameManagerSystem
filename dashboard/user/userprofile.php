<?php
require_once __DIR__ . "/../../config/settings.php";
require_once __DIR__ . "/../Authentication.php";

Authentication::checkAccNotLoggedIn("../../page/index.php");
Authentication::redirectAdminPage();

$account = $_SESSION["signed_in_acc"];

require_once __DIR__ . "/../../config/Database.php";
$db = Database::connect();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../../popup_card/style.css">
  <link rel="stylesheet" href="../../input_popup_card/style.css">
  <title>User Profile | Void</title>
</head>
<body>
  <img id="acc_image" alt="profile_image" />
  <button id="image">Edit</button>
  <h1 id="acc_name"></h1>
  <button id="name">Edit</button>
  <p id="acc_username"></p>
  <button id="username">Edit</button>
  <form method="POST">
    <input type="hidden" id="email" name="email" value="<?= $account["email"] ?>" />
    <input type="submit" id="reset_pass" name="find_email" value="Reset Password" />
  </form>

  <h2>Favorite Games</h2>
  <div id="fav_games">
    <?php
      $stmt = $db->prepare("SELECT game_id FROM favorite_games WHERE user_id = :uid LIMIT 5");
      $stmt->execute([":uid" => $account['id']]);

      if($stmt->rowCount() > 0) {
        $gids = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total_gids = $stmt->rowCount();
        for($i = 0; $i < $total_gids; $i++) {
          $stmt = $db->prepare("SELECT * FROM game WHERE id = :gid");
          $stmt->execute([":gid" => $gids[$i]["game_id"]]);

          if($stmt->rowCount() > 0) {
            $fav_game = $stmt->fetch(PDO::FETCH_ASSOC);

            echo "<div>";
            echo "<h3>{$fav_game['name']}</h3>";
            echo "<p>{$fav_game['info']}</p>";
            echo "<a href=\"./game.php?id={$fav_game['id']}\">View</a>";
            echo "</div>";
          }
        }
      } else {
        echo "<i>No favorite games yet.</i>";
      }
    ?>
  </div>

  <h2>Recent Games</h2>
  <div id="recent_games">
    <?php
      $stmt = $db->prepare("SELECT game_id FROM game_visited WHERE user_id = :uid ORDER BY game_visited.visited_at DESC LIMIT 5");
      $stmt->execute([":uid" => $account['id']]);

      if($stmt->rowCount() > 0) {
        $gids = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total_gids = $stmt->rowCount();
        for($i = 0; $i < $total_gids; $i++) {
          $stmt = $db->prepare("SELECT * FROM game WHERE id = :gid");
          $stmt->execute([":gid" => $gids[$i]["game_id"]]);

          if($stmt->rowCount() > 0) {
            $recent_game = $stmt->fetch(PDO::FETCH_ASSOC);

            echo "<div>";
            echo "<h3>{$recent_game['name']}</h3>";
            echo "<p>{$recent_game['info']}</p>";
            echo "<a href=\"./game.php?id={$recent_game['id']}\">View</a>";
            echo "</div>";
          }
        }
      } else {
        echo "<i>No game visited yet.</i>";
      }
    ?>
  </div>

  <div id="popup_wrapper"></div>
  <div id="input_popup_wrapper"></div>

  <script src="../../src/js/jquery-3.7.1.min.js"></script>
  <script src="../../popup_card/script.js"></script>
  <script src="../../input_popup_card/script.js"></script>
  <script src="../../src/js/user-profile-page-data-process.js"></script>
</body>
</html>