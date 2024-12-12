<?php
require_once __DIR__ . "/../../config/settings.php";

if(!isset($_GET["id"])) {
  header("Location: ./dashboard.php");
  exit();
}

require_once __DIR__ . "/../../config/Database.php";
$db = Database::connect();

$stmt = $db->prepare("SELECT * FROM game WHERE id = :id");
$stmt->execute([":id" => $_GET["id"]]);
if($stmt->rowCount() <= 0) {
  echo
  "<script>
    alert(\"Game is not found in the system.\");
    window.location.href = \"./dashboard.php\";
  </script>";
  exit();
}

$id = $_GET["id"];
$game = $stmt->fetch(PDO::FETCH_ASSOC);

$img_path = "../../src/uploads/games_images/{$game['id']}";
$files = scandir($img_path);
$img = null;
$total_files = count($files);
for($i = 0; $i < $total_files; $i++) {
  if($files[$i] === "." || $files[$i] === "..") {
    continue;
  }

  if($i === 2) {
    $img["bg"] = $img_path . "/" . $files[$i];
    continue;
  }

  if($i === 3) {
    $img["icon"] = $img_path . "/" . $files[$i];
    continue;
  }

  $img["samples"][$i] = $img_path . "/" . $files[$i];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $game['name'] ?> - Game Preview</title>
</head>
<body>
  <img src="<?= $img['bg'] ?>" alt="background image">
  <h1><?= $game['name'] ?></h1>
  <h3><?= $game['developer'] ?></h3>
  <img src="<?= $img['icon'] ?>" alt="icon image">
  <a href="<?= $game['download_link'] ?>">Download</a>
  <?php
    foreach($img["samples"] as $sample) {
      echo "<img src='{$sample}' alt='sample image'>";
    }
  ?>
  <h2>About this game</h2>
  <p><?= $game['info'] ?></p>
  <a href="./dashboard.php">Go Back</a>
</body>
</html>