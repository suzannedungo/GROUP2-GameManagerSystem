<?php
include_once __DIR__ . "/../../config/settings.php";
include_once __DIR__ . "/../Authentication.php";

Authentication::checkAccNotLoggedIn("../../page/index.php");
Authentication::redirectAdminPage();

if(!isset($_GET["id"])) {
  header("Location: ./dashboard.php");
  exit();
}

require_once __DIR__ . "/../../config/Database.php";
$db = Database::connect();

$account = $_SESSION["signed_in_acc"];

$stmt = $db->prepare("SELECT * FROM game_visited WHERE game_id = :gid AND user_id = :uid");
$stmt->execute([
  ":gid" => $_GET["id"],
  ":uid" => $account["id"]
]);

if($stmt->rowCount() <= 0) {
  $stmt = $db->prepare("INSERT INTO game_visited(game_id, user_id) VALUES(:gid, :uid)");
  $stmt->execute([
    ":gid" => $_GET["id"],
    ":uid" => $account["id"],
  ]);
} else {
  $stmt = $db->prepare("UPDATE game_visited SET visited_at = CURRENT_TIMESTAMP WHERE game_id = :gid AND user_id =:uid");
  $stmt->execute([
    ":gid" => $_GET["id"],
    ":uid" => $account["id"],
  ]);
}

$stmt = $db->prepare("SELECT * FROM game WHERE id = :id");
$stmt->execute([":id" => $_GET["id"]]);
if($stmt->rowCount() <= 0) {
  http_response_code(404);
  echo
  "<script>
    alert(\"No game found.\");
    window.location.href = \"./dashboard.php\";
  </script>";
  exit();
}

$game = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $db->prepare("SELECT * FROM favorite_games WHERE user_id = :uid AND game_id = :gid");
$stmt->execute([
  ":uid" => $account["id"],
  ":gid" => $game["id"]
]);

$fav_res = null;
if($stmt->rowCount() > 0) {
  $fav_res = "checked";
}

$stmt = $db->prepare("SELECT * FROM game_review WHERE user_id = :uid AND game_id = :gid");
$stmt->execute([
  ":uid" => $account["id"],
  ":gid" => $game["id"]
]);

$has_review = false;
if($stmt->rowCount() > 0) {
  $has_review = true;
}

$stmt = $db->prepare("SELECT AVG(`rating`) as avg_rating FROM game_review WHERE game_id = :gid");
$stmt->execute([":gid" => $game["id"]]);
$rating = $stmt->fetch(PDO::FETCH_ASSOC);
$rating = intval($rating["avg_rating"]);
if(!$rating) {
  $rating = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="../../src/img/voidlogo.png.png">
  <link rel="stylesheet" href="../../popup_card/style.css">
  <title><?= $game["name"] ?> | Void</title>
</head>
<body>
  <input type="hidden" name="game_id" id="game_id" value="<?= $game["id"] ?>">
  <input type="hidden" name="user_id" id="user_id" value="<?= $account["id"] ?>">
  <input type="hidden" name="has_review" id="has_review" value="<?= $has_review ?>">

  <img src="../../src/uploads/games_images/<?= $game["game_image"] ?>" alt="<?= $game["game_image"] ?>">
  <h1><?= $game["name"] ?></h1>
  <p><?= $game["info"] ?></p>
  <p><?= $rating ?></p>
  <input type="checkbox" name="favorite" id="favorite" <?= $fav_res ?> />
  <label for="favorite">Add to Favorites</label>
  <br>
  <a href="<?= $game["download_link"] ?>">Download</a>
  <hr>
  <h1>Responses</h1>
  <div id="review_form">
    <form>
      <input type="number" name="rating" id="rating" required>
      <textarea name="comment" id="comment"></textarea>
      <input type="submit" name="submit_review" id="submit_review" value="Send">
    </form>
    <p id="error_message"></p>
  </div>

  <div id="reviews_container">
  </div>

  <div id="popup_wrapper"></div>

  <script src="../../src/js/jquery-3.7.1.min.js"></script>
  <script src="../../popup_card/script.js"></script>
  <script src="../../src/js/game-page-data-process.js"></script>
</body>
</html>