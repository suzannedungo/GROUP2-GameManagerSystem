<?php

namespace App\Controller;

use App\Core\Utilities;
use App\Model\Game;
use App\Model\GameGenre;
use FilesystemIterator;

class GameController {
  public static function addGame() {
    if(!isset($_POST["add_game"])) {
      header("Location: /admin/manage_games");
      exit();
    }

    // Check CSRF Token bug.
    Utilities::validateCSRFToken($_POST["csrf_token"]);

    $name = trim($_POST["name"]);
    Authentication::checkInputEmpty($name, "game name", "/admin/manage_games");
    $developer = trim($_POST["developer"]);
    Authentication::checkInputEmpty($developer, "game developer", "/admin/manage_games");
    $info = trim($_POST["info"]);
    Authentication::checkInputEmpty($info, "game info", "/admin/manage_games");
    $dl_link = trim($_POST["dl_link"]);
    Authentication::checkInputEmpty($dl_link, "game download link", "/admin/manage_games");

    $game = new Game();
    if(!$game->add($name, $developer, $info, $dl_link)) {
      Utilities::showAlertAndExit("An error occured on adding game.", "/admin/manage_games", 500);
    }

    $gid = $game->getLastId();
    if($gid == false) {
      Utilities::showAlertAndExit("An error on getting last game id.", "/admin/manage_games", 500);
    }

    if($gid == 0) {
      Utilities::showAlertAndExit("No game found.", "/admin/manage_games", 404);
    }

    if(isset($_POST["genres"]) != false) {
      $total_genres = count($_POST["genres"]);

      $game_genre = new GameGenre();
      for($i = 0; $i < $total_genres; $i++) {
        if(!$game_genre->add($gid, $_POST["genres"][$i])) {
          Utilities::showAlertAndExit("An error occured on adding game genre.", "/admin/manage_games", 500);
        }
      }
    }
    
    $icon = $_FILES["icon"];
    $bg = $_FILES["bg"];
    $samples = $_FILES["samples"];

    $game_path = Utilities::getPath() . "/public/uploads/games_images/{$gid}";
    mkdir($game_path, 0777, true);

    $total_samples = count($samples["name"]);
    if($total_samples != 5) {
      if(!$game->delete($gid)) {
        Utilities::showAlertAndExit("An error occured on deleting game.", "/admin/manage_games", 500);
      }
      Utilities::showAlertAndExit("Sample Images Must Be 5.", "/admin/manage_games", 400);
    }

    // Allowed Extensions.
    $allowed_type = ["jpg", "jpeg", "png", "webp", "avif"];

    // Image Extensions.
    $icon_ext = explode(".", $icon["name"]);
    $icon_ext = end($icon_ext);
    $icon_ext = strtolower($icon_ext);
    if(!in_array($icon_ext, $allowed_type)) {
      if(!$game->delete($gid)) {
        Utilities::showAlertAndExit("An error occured on deleting game.", "/admin/manage_games", 500);
      }
      Utilities::showAlertAndExit("JPEG, PNG, WEBP, and AVIF Only.", "/admin/manage_games", 400);
    }

    $bg_ext = explode(".", $bg["name"]);
    $bg_ext = end($bg_ext);
    $bg_ext = strtolower($bg_ext);
    if(!in_array($bg_ext, $allowed_type)) {
      if(!$game->delete($gid)) {
        Utilities::showAlertAndExit("An error occured on deleting game.", "/admin/manage_games", 500);
      }
      Utilities::showAlertAndExit("JPEG, PNG, WEBP, and AVIF Only.", "/admin/manage_games", 400);
    }

    $samples_ext = null;
    for($i = 0; $i < $total_samples; $i++) {
      $samples_ext[$i] = explode(".", $samples["name"][$i]);
      $samples_ext[$i] = end($samples_ext[$i]);
      $samples_ext[$i] = strtolower($samples_ext[$i]);
      if(!in_array($samples_ext[$i], $allowed_type)) {
        if(!$game->delete($gid)) {
          Utilities::showAlertAndExit("An error occured on deleting game.", "/admin/manage_games", 500);
        }
        Utilities::showAlertAndExit("JPEG, PNG, WEBP, and AVIF Only.", "/admin/manage_games", 400);
      }
    }

    // Image Error.
    if($icon["error"] != 0) {
      if(!$game->delete($gid)) {
        Utilities::showAlertAndExit("An error occured on deleting game.", "/admin/manage_games", 500);
      }
      Utilities::showAlertAndExit("An error occured on uploading game icon.", "/admin/manage_games", 400);
    }

    if($bg["error"] != 0) {
      if(!$game->delete($gid)) {
        Utilities::showAlertAndExit("An error occured on deleting game.", "/admin/manage_games", 500);
      }
      Utilities::showAlertAndExit("An error occured on uploading game icon.", "/admin/manage_games", 400);
    }

    for($i = 0; $i < $total_samples; $i++) {
      $error = $samples["error"][$i];
      if($error != 0) {
        if(!$game->delete($gid)) {
          Utilities::showAlertAndExit("An error occured on deleting game.", "/admin/manage_games", 500);
        }
        Utilities::showAlertAndExit("An error occured on uploading game icon.", "/admin/manage_games", 400);
      }
    }

    // Image Size.
    if($icon["size"] > 5000000) {
      if(!$game->delete($gid)) {
        Utilities::showAlertAndExit("An error occured on deleting game.", "/admin/manage_games", 500);
      }
      Utilities::showAlertAndExit("Do not upload game icon bigger than 5mb.", "/admin/manage_games", 400);
    }

    if($bg["size"] > 5000000) {
      if(!$game->delete($gid)) {
        Utilities::showAlertAndExit("An error occured on deleting game.", "/admin/manage_games", 500);
      }
      Utilities::showAlertAndExit("Do not upload game bg image bigger than 5mb.", "/admin/manage_games", 400);
    }

    for($i = 0; $i < $total_samples; $i++) {
      $size = $samples["size"][$i];
      if($size > 5000000) {
        if(!$game->delete($gid)) {
          Utilities::showAlertAndExit("An error occured on deleting game.", "/admin/manage_games", 500);
        }
        Utilities::showAlertAndExit("Do not upload sample image bigger than 5mb.", "/admin/manage_games", 400);
      }
    }

    // Cleaning saved game icon.
    $old_icon = "{$game_path}/icon.jpeg";
    if(file_exists($old_icon)) {
      unlink($old_icon);
    }

    $old_icon = "{$game_path}/icon.jpg";
    if(file_exists($old_icon)) {
      unlink($old_icon);
    }

    $old_icon = "{$game_path}/icon.png";
    if(file_exists($old_icon)) {
      unlink($old_icon);
    }

    $old_icon = "{$game_path}/icon.webp";
    if(file_exists($old_icon)) {
      unlink($old_icon);
    }

    $old_icon = "{$game_path}/icon.avif";
    if(file_exists($old_icon)) {
      unlink($old_icon);
    }

    move_uploaded_file($icon["tmp_name"], $game_path . "/icon." . $icon_ext);

    // Cleaning saved game background image.
    $old_bg = "{$game_path}/bg.jpeg";
    if(file_exists($old_bg)) {
      unlink($old_bg);
    }

    $old_bg = "{$game_path}/bg.jpg";
    if(file_exists($old_bg)) {
      unlink($old_bg);
    }

    $old_bg = "{$game_path}/bg.png";
    if(file_exists($old_bg)) {
      unlink($old_bg);
    }

    $old_bg = "{$game_path}/bg.webp";
    if(file_exists($old_bg)) {
      unlink($old_bg);
    }

    $old_bg = "{$game_path}/bg.avif";
    if(file_exists($old_bg)) {
      unlink($old_bg);
    }

    move_uploaded_file($bg["tmp_name"], $game_path . "/bg." . $bg_ext);

    // Cleaning saved game samples image.
    for($i = 0; $i < $total_samples; $i++) {
      $old = "{$game_path}/sample{$i}.jpeg";
      if(file_exists($old)) {
        unlink($old);
      }

      $old = "{$game_path}/sample{$i}.jpg";
      if(file_exists($old)) {
        unlink($old);
      }

      $old = "{$game_path}/sample{$i}.png";
      if(file_exists($old)) {
        unlink($old);
      }

      $old = "{$game_path}/sample{$i}.webp";
      if(file_exists($old)) {
        unlink($old);
      }

      $old = "{$game_path}/sample{$i}.avif";
      if(file_exists($old)) {
        unlink($old);
      }

      move_uploaded_file($samples["tmp_name"][$i], $game_path . "/sample{$i}." . $bg_ext);
    }

    Utilities::showAlertAndExit("{$name} added successfully!", "/admin/manage_games", 201);
  }

  public static function editGame() {
    if(!isset($_POST["edit_game"]) && $_POST["_method"] != "PUT") {
      header("Location: /admin/manage_games");
      exit();
    }

    Utilities::validateCSRFToken($_POST["csrf_token"]);

    $id = $_POST["game"];

    $name = false;
    if(isset($_POST["name"]) && (trim($_POST["name"]) !== "" || trim($_POST["name"]) !== "")) {
      $name = trim($_POST["name"]);
    }

    $dev = false;
    if(isset($_POST["developer"]) && (trim($_POST["developer"]) !== "" || trim($_POST["developer"]) !== "")) {
      $dev = trim($_POST["developer"]);
    }

    $info = false;
    if(isset($_POST["info"]) && (trim($_POST["info"]) !== "" || trim($_POST["info"]) !== "")) {
      $info = trim($_POST["info"]);
    }

    $dl_link = false;
    if(isset($_POST["dl_link"]) && (trim($_POST["dl_link"]) !== "" || trim($_POST["dl_link"]) !== "")) {
      $dl_link = trim($_POST["dl_link"]);
    }

    $genres = false;
    if(isset($_POST["genres"])) {
      $genres = $_POST["genres"];
    }

    $icon = false;
    if(isset($_FILES["icon"]) && $_FILES["icon"]["name"] != "" && $_FILES["icon"]["error"] != 4) {
      $icon = $_FILES["icon"];
    }

    $bg = false;
    if(isset($_FILES["bg"]) && $_FILES["bg"]["name"] != "" && $_FILES["bg"]["error"] != 4) {
      $bg = $_FILES["bg"];
    }
    
    $samples = false;
    if(isset($_FILES["samples"]) && $_FILES["samples"]["name"][0] != "" && $_FILES["samples"]["error"][0] != 4) {
      $samples = $_FILES["samples"];
    }

    $res = false;

    $game = new Game();
    if($name !== false) {
      Authentication::checkInputEmpty($name, "name", "/admin/manage_games");
      if(!$game->updateNameById($id, $name)) {
        Utilities::showAlertAndExit("An error occured on editing game name.", "/admin/manage_games", 500);
      }

      $res = true;
    }

    if($dev !== false) {
      Authentication::checkInputEmpty($dev, "developer", "/admin/manage_games");
      if(!$game->updateDeveloperById($id, $dev)) {
        Utilities::showAlertAndExit("An error occured on editing game developer.", "/admin/manage_games", 500);
      }

      $res = true;
    }

    if($info !== false) {
      Authentication::checkInputEmpty($info, "info", "/admin/manage_games");
      if(!$game->updateInfoById($id, $info)) {
        Utilities::showAlertAndExit("An error occured on editing game information.", "/admin/manage_games", 500);
      }
      $res = true;
    }

    if($dl_link !== false) {
      Authentication::checkInputEmpty($dl_link, "download link", "/admin/manage_games");
      if(!$game->updateDLLById($id, $dl_link)) {
        Utilities::showAlertAndExit("An error occured on editing game download link.", "/admin/manage_games", 500);
      }

      $res = true;
    }

    if($genres !== false) {
      $game_genre = new GameGenre();
      if(!$game_genre->deleteByGameId($id)) {
        Utilities::showAlertAndExit("An error occured on deleting game genre.", "/admin/manage_games", 500);
      }

      $total_genre = count($genres);

      for($i = 0; $i < $total_genre; $i++) {
        if(!$game_genre->add($id, $genres[$i])) {
          Utilities::showAlertAndExit("An error occured on adding game genre.", "/admin/manage_games", 500);
        }
      }

      $res = true;
    }

    $game_path = Utilities::getPath() . "/public/uploads/games_images/{$id}";

    $allowed_type = ["jpg", "jpeg", "png", "webp", "avif"];

    if($icon !== false) {
      $icon_ext = explode(".", $icon["name"]);
      $icon_ext = end($icon_ext);
      $icon_ext = strtolower($icon_ext);
      if(!in_array($icon_ext, $allowed_type)) {
        Utilities::showAlertAndExit("JPEG, PNG, WEBP, and AVIF Only For Icon.", "/admin/manage_games", 400);
        exit();
      }

      if($icon["error"] != 0) {
        Utilities::showAlertAndExit("An error occured on uploading game icon.", "/admin/manage_games", 500);
      }

      if($icon["size"] > 5000000) {
        Utilities::showAlertAndExit("Do not upload game icon bigger than 5mb.", "/admin/manage_games", 400);
      }

      $old_icon = "{$game_path}/icon.jpeg";
      if(file_exists($old_icon)) {
        unlink($old_icon);
      }

      $old_icon = "{$game_path}/icon.jpg";
      if(file_exists($old_icon)) {
        unlink($old_icon);
      }

      $old_icon = "{$game_path}/icon.png";
      if(file_exists($old_icon)) {
        unlink($old_icon);
      }

      $old_icon = "{$game_path}/icon.webp";
      if(file_exists($old_icon)) {
        unlink($old_icon);
      }

      $old_icon = "{$game_path}/icon.avif";
      if(file_exists($old_icon)) {
        unlink($old_icon);
      }

      move_uploaded_file($icon["tmp_name"], $game_path . "/icon." . $icon_ext);

      $res = true;
    }

    if($bg !== false) {
      $bg_ext = explode(".", $bg["name"]);
      $bg_ext = end($bg_ext);
      $bg_ext = strtolower($bg_ext);
      if(!in_array($bg_ext, $allowed_type)) {
        Utilities::showAlertAndExit("JPEG, PNG, WEBP, and AVIF Only.", "/admin/manage_games", 400);
        exit();
      }

      if($bg["error"] != 0) {
        Utilities::showAlertAndExit("An error occured on uploading game bg image.", "/admin/manage_games", 500);
      }

      if($bg["size"] > 5000000) {
        Utilities::showAlertAndExit("Do not upload game bg image bigger than 5mb.", "/admin/manage_games", 400);
      }

      $old_bg = "{$game_path}/bg.jpeg";
      if(file_exists($old_bg)) {
        unlink($old_bg);
      }

      $old_bg = "{$game_path}/bg.jpg";
      if(file_exists($old_bg)) {
        unlink($old_bg);
      }

      $old_bg = "{$game_path}/bg.png";
      if(file_exists($old_bg)) {
        unlink($old_bg);
      }

      $old_bg = "{$game_path}/bg.webp";
      if(file_exists($old_bg)) {
        unlink($old_bg);
      }

      $old_bg = "{$game_path}/bg.avif";
      if(file_exists($old_bg)) {
        unlink($old_bg);
      }

      move_uploaded_file($bg["tmp_name"], $game_path . "/bg." . $bg_ext);

      $res = true;
    }

    if($samples !== false) {
      $total_samples = count($samples["name"]);
      if($total_samples != 5) {
        Utilities::showAlertAndExit("Sample Images Must Be 5.", "/admin/manage_games", 400);
      }

      $samples_ext = null;
      for($i = 0; $i < $total_samples; $i++) {
        $samples_ext[$i] = explode(".", $samples["name"][$i]);
        $samples_ext[$i] = end($samples_ext[$i]);
        $samples_ext[$i] = strtolower($samples_ext[$i]);
        if(!in_array($samples_ext[$i], $allowed_type)) {
          Utilities::showAlertAndExit("JPEG, PNG, WEBP, and AVIF Only For Sample Image {$i}.", "/admin/manage_games", 400);
        }
      }

      for($i = 0; $i < $total_samples; $i++) {
        $error = $samples["error"][$i];
        if($error != 0) {
          Utilities::showAlertAndExit("An error occured on uploading game samples image {$i}.", "/admin/manage_games", 400);
        }
      }

      for($i = 0; $i < $total_samples; $i++) {
        $size = $samples["size"][$i];
        if($size > 5000000) {
          Utilities::showAlertAndExit("Do not upload game samples image {$i} bigger than 5mb.", "/admin/manage_games", 400);
        }
      }

      for($i = 0; $i < $total_samples; $i++) {
        $old = "{$game_path}/sample{$i}.jpeg";
        if(file_exists($old)) {
          unlink($old);
        }

        $old = "{$game_path}/sample{$i}.jpg";
        if(file_exists($old)) {
          unlink($old);
        }

        $old = "{$game_path}/sample{$i}.png";
        if(file_exists($old)) {
          unlink($old);
        }

        $old = "{$game_path}/sample{$i}.webp";
        if(file_exists($old)) {
          unlink($old);
        }

        $old = "{$game_path}/sample{$i}.avif";
        if(file_exists($old)) {
          unlink($old);
        }

        move_uploaded_file($samples["tmp_name"][$i], $game_path . "/sample{$i}." . $samples_ext[$i]);

        $res = true;
      }
    }

    if($res) {
      Utilities::showAlert("Game edited successfully.");
      if(!$game->updateDateUpdatedById($id)) {
        Utilities::showAlertAndExit("An error occured on updating game date updated.", "/admin/manage_games", 500);
      }
    }

    echo
    "<script>
      window.location.href = \"/admin/manage_games\";
    </script>";
    exit();
  }

  public static function deleteGame() {
    if(!isset($_POST["delete_game"]) && $_POST["_method"] != "DEL") {
      header("Location: /admin/manage_games");
      exit();
    }

    Utilities::validateCSRFToken($_POST["csrf_token"]);

    $id = $_POST["game"];
    $game = new Game();
    if(!$game->delete($id)) {
      Utilities::showAlertAndExit("An error occured on deleting game.", "/admin/manage_games", 500);
    }

    $directory = Utilities::getPath() . "/public/uploads/games_images/{$id}";
    $iterator = new FilesystemIterator($directory, FilesystemIterator::SKIP_DOTS);

    foreach ($iterator as $item) {
      unlink($item->getPathname());
    }

    rmdir($directory);

    Utilities::showAlertAndExit("Game deleted successfully!", "/admin/manage_games", 201);
  }
}