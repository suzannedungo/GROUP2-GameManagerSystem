<?php
require_once __DIR__ . "/../config/Database.php";
require_once __DIR__ . "/Authentication.php";

class Game {
  private $db;

  public function __construct() {
    $this->db = Database::connect();
  }

  public function addGame($name, $dev, $info, $dl_link, $icon, $bg, $samples) {
    Authentication::checkInputEmpty($name, "name", "./admin/dashboard.php");
    Authentication::checkInputEmpty($dev, "developer", "./admin/dashboard.php");
    Authentication::checkInputEmpty($info, "info", "./admin/dashboard.php");
    Authentication::checkInputEmpty($dl_link, "download link", "./admin/dashboard.php");

    $stmt = $this->db->prepare("INSERT INTO game(name, developer, info, download_link) VALUES(:name, :dev, :info, :dl_link)");
    $stmt->execute([
      ":name" => $name,
      ":dev" => $dev,
      ":info" => $info,
      ":dl_link" => $dl_link
    ]);

    $stmt = $this->db->prepare("SELECT id FROM game ORDER BY date_added DESC LIMIT 1");
    $stmt->execute();
    $gid = $stmt->fetch(PDO::FETCH_ASSOC);

    $game_path = __DIR__ . "/../src/uploads/games_images/{$gid}";
    mkdir($game_path, 0777, true);

    /*
    print_r($icon);
    echo "<br>";
    print_r($bg);
    echo "<br>";
    // print_r($samples);
    echo count($samples["name"]);
    */
    $total_samples = count($samples["name"]);
    if($total_samples == 5) {
      echo
      "<script>
        alert(\"Sample Images Must Be 5.\");
        window.location.href = \"./admin/dashboard.php\";
      </script>";
      exit();
    }

    // Allowed Extensions.
    $allowed_type = ["jpg", "jpeg", "png"];

    // Image Extensions.
    $icon_ext = explode(".", $icon["name"]);
    $icon_ext = end($icon_ext);
    $icon_ext = strtolower($icon_ext);
    if(!in_array($icon_ext, $allowed_type)) {
      echo
      "<script>
        alert(\"JPEG and PNG Only.\");
        window.location.href = \"./admin/dashboard.php\";
      </script>";
      exit();
    }

    $bg_ext = explode(".", $bg["name"]);
    $bg_ext = end($bg_ext);
    $bg_ext = strtolower($bg_ext);
    if(!in_array($bg_ext, $allowed_type)) {
      echo
      "<script>
        alert(\"JPEG and PNG Only.\");
        window.location.href = \"./admin/dashboard.php\";
      </script>";
      exit();
    }

    $samples_ext = null;
    for($i = 0; $i < $total_samples; $i++) {
      $samples_ext[$i] = explode(".", $samples["name"][$i]);
      $samples_ext[$i] = end($samples_ext[$i]);
      $samples_ext[$i] = strtolower($samples_ext[$i]);
      if(!in_array($samples_ext[$i], $allowed_type)) {
        echo
        "<script>
          alert(\"JPEG and PNG Only.\");
          window.location.href = \"./admin/dashboard.php\";
        </script>";
        exit();
      }
    }

    // Image Tmp Name.
    $icon_tmp_name = $icon["tmp_name"];
    $bg_tmp_name = $bg["tmp_name"];

    $samples_tmp_name = null;
    for($i = 0; $i < $total_samples; $i++) {
      $samples_tmp_name[$i] = $samples["tmp_name"][$i];
    }

    // Image Error.
    if($icon["error"] != 0) {
      echo
      "<script>
        alert(\"An error occured upon uploading game icon.\");
        window.location.href = \"./admin/dashboard.php\";
      </script>";
      exit();
    }

    if($bg["error"] != 0) {
      echo
      "<script>
        alert(\"An error occured upon uploading game background image.\");
        window.location.href = \"./admin/dashboard.php\";
      </script>";
      exit();
    }

    for($i = 0; $i < $total_samples; $i++) {
      $error = $samples["error"][$i];
      if($error != 0) {
        echo
        "<script>
          alert(\"An error occured upon uploading game samples image.\");
          window.location.href = \"./admin/dashboard.php\";
        </script>";
        exit();
      }
    }

    // Image Size.
    if($icon["size"] > 5000000) {
      echo
      "<script>
        alert(\"Do not upload game icon bigger than 5mb.\");
        window.location.href = \"./admin/dashboard.php\";
      </script>";
      exit();
    }

    if($bg["size"] > 5000000) {
      echo
      "<script>
        alert(\"Do not upload game background image bigger than 5mb.\");
        window.location.href = \"./admin/dashboard.php\";
      </script>";
      exit();
    }

    for($i = 0; $i < $total_samples; $i++) {
      $size = $samples["size"][$i];
      if($size > 5000000) {
        echo
        "<script>
          alert(\"Do not upload game samples image bigger than 5mb.\");
          window.location.href = \"./admin/dashboard.php\";
        </script>";
        exit();
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

      move_uploaded_file($samples["tmp_name"][$i], $game_path . "/sample{$i}." . $bg_ext);
    }
  }

  public function isFavorite($gid, $uid) {
    $stmt = $this->db->prepare("SELECT * FROM favorite_games WHERE user_id = :uid AND game_id = :gid");
    $stmt->execute([
      ":uid" => $uid,
      ":gid" => $gid
    ]);

    if($stmt->rowCount() > 0) {
      return "checked";
    } else {
      return false;
    }
  }

  public function addFavorite($gid, $uid) {
    if(($this->isFavorite($gid, $uid)) === false) {
      $stmt = $this->db->prepare("INSERT INTO favorite_games(user_id, game_id) VALUES(:uid, :gid)");
      $stmt->execute([
        ":uid" => $uid,
        ":gid" => $gid
      ]);
    }
  }

  public function delFavorite($gid, $uid) {
    if(($this->isFavorite($gid, $uid)) !== false) {
      $stmt = $this->db->prepare("DELETE FROM favorite_games WHERE user_id = :uid AND game_id = :gid");
      $stmt->execute([
        ":uid" => $uid,
        ":gid" => $gid
      ]);
    }
  }

  public function getFiveFavorites($uid) {
    $stmt = $this->db->prepare("SELECT game_id FROM favorite_games WHERE user_id = :uid LIMIT 5");
    $stmt->execute([":uid" => $uid]);

    if($stmt->rowCount() > 0) {
      $gids = $stmt->fetchAll(PDO::FETCH_ASSOC);
      for($i = 0; $i < $stmt->rowCount(); $i++) {
        print_r($gids);
        // $stmt = $this->db->prepare("SELECT * FROM game WHERE id = :gid");
      }
    }
  }

  public function getAllReviews($gid, $uid) {
    $stmt = $this->db->prepare("SELECT * FROM game_review WHERE game_id = :gid");
    $stmt->execute([":gid" => $gid]);
    if($stmt->rowCount() <= 0) {
      echo "<i>No reviews yet.</i>";
      // echo false;
    } else {
      $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

      foreach($reviews as $review) {
        $stmt = $this->db->prepare("SELECT id, profile_image, username FROM account WHERE id = :id");
        $stmt->execute([":id" => $review["user_id"]]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        echo
        "<div class='review_container'>
          <img src=\"{$user['profile_image']}\" alt=\"{$user['profile_image']}\">
          <h4>{$user['username']}</h4>
          <p>{$review['rating']}</p>
          <p>{$review['comment']}</p>
          <p>{$review['date_added']}</p>";

        if($user["id"] == $uid) {
          echo "<button id='delete_review'>Delete</button>";
        }

        echo "</div>";
      }
    }
  }

  public function submitReview($gid, $uid, $rating, $comment) {
    Authentication::checkInputEmpty($rating, "rating", "./game.php?id={$gid}");

    $stmt = $this->db->prepare("INSERT INTO game_review(game_id, user_id, rating, comment) VALUES(:gid, :uid, :rating, :comment)");
    return $stmt->execute([
      ":gid" => $gid,
      ":uid" => $uid,
      ":rating" => $rating,
      ":comment" => $comment,
    ]);
  }

  public function deleteReview($gid, $uid) {
    $stmt = $this->db->prepare("DELETE FROM game_review WHERE game_id = :gid AND user_id = :uid");
    return $stmt->execute([
      ":gid" => $gid,
      ":uid" => $uid
    ]);
  }
}





if(isset($_POST["add_game"])) {
  (new Game())->addGame(
    trim($_POST["name"]),
    trim($_POST["developer"]),
    trim($_POST["info"]),
    trim($_POST["dl_link"]),
    $_FILES["icon"],
    $_FILES["bg"],
    $_FILES["samples"]
  );
}

if($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["isFav"])) {
  echo (new Game())->isFavorite(trim($_GET["gid"]), trim($_GET["uid"]));
}

if(
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    (isset($_POST["addFav"]) && $_POST["addFav"] == true)
) {
  (new Game())->addFavorite(
    trim($_POST["gid"]),
    trim($_POST["uid"])
  );
}

if(
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    (isset($_POST["_method"]) && $_POST["_method"] === "DELETE") && 
    (isset($_POST["delFav"]) && $_POST["delFav"] == true)
) {
  (new Game())->delFavorite(
    trim($_POST["gid"]),
    trim($_POST["uid"])
  );
}

if(
    $_SERVER["REQUEST_METHOD"] === "POST" && 
    isset($_POST["submit_review"])
) {
  echo (new Game())->submitReview(
    trim($_POST["gid"]),
    trim($_POST["uid"]),
    trim($_POST["rating"]),
    trim($_POST["comment"])
  );
}

if(
    $_SERVER["REQUEST_METHOD"] === "GET" && 
    (isset($_GET["gid"]) && isset($_GET["uid"]))
) {
  echo (new Game())->getAllReviews(trim($_GET["gid"]), trim($_GET["uid"]));
}

if(
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    (isset($_POST["_method"]) && $_POST["_method"] === "DELETE") && 
    (isset($_POST["delReview"]) && $_POST["delReview"] == true)
) {
  echo (new Game())->deleteReview(trim($_POST["gid"]), trim($_POST["uid"]));
}