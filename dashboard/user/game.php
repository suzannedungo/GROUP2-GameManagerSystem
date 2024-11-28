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

if(isset($_POST["submit_review"])) {
  $stmt = $db->prepare("INSERT INTO review(game_id, user_id, rating, comment) VALUES(:gid, :uid, :rating, :comment)");
  $stmt->execute([
    ":gid" => $_POST["game_id"],
    ":uid" => $_POST["user_id"],
    ":rating" => $_POST["rating"],
    ":comment" => $_POST["comment"],
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="../../src/img/voidlogo.png.png">
  <title><?= $game["name"] ?> | Void</title>
</head>
<body>
  <img src="../../src/uploads/games_images/<?= $game["game_image"] ?>" alt="<?= $game["game_image"] ?>">
  <h1><?= $game["name"] ?></h1>
  <p><?= $game["info"] ?></p>
  <a href="<?= $game["download_link"] ?>">Download</a>
  <hr>
  <h1>Responses</h1>
  <form action="./game.php?id=<?= $_GET['id'] ?>" method="POST">
    <input type="hidden" name="game_id" value="<?= $game["id"] ?>">
    <input type="hidden" name="user_id" value="<?= $account["id"] ?>">
    <input type="number" name="rating" id="rating" required>
    <textarea name="comment" id="comment"></textarea>
    <input type="submit" value="Send" name="submit_review">
  </form>
  <div id="reviews_container">
    <?php
      $stmt = $db->prepare("SELECT * FROM game_review WHERE game_id = :gid");
      $stmt->execute([":gid" => $game["id"]]);
      if($stmt->rowCount() <= 0) {
        echo "<i>No reviews yet.</i>";
      } else {
        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach($reviews as $review) {
          $stmt = $db->prepare("SELECT profile_image, username FROM account WHERE id = :id");
          $stmt->execute([":id" => $review["user_id"]]);
          $user = $stmt->fetch(PDO::FETCH_ASSOC);

          echo
          "<div>
            <img src=\"{$user['profile_image']}\" alt=\"{$user['profile_image']}\">
            <h4>{$user['username']}</h4>
            <p>{$review['rating']}</p>
            <p>{$review['comment']}</p>
            <p>{$review['created_at']}</p>
          </div>";
        }
      }
    ?>
  </div>
</body>
</html>