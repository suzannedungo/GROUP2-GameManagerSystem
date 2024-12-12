<?php
require_once __DIR__ . "/../config/Database.php";
require_once __DIR__ . "/Authentication.php";

class Game {
  private $db;

  public function __construct() {
    $this->db = Database::connect();
  }

  public function addGame($name, $dev, $info, $dl_link, $genres, $icon, $bg, $samples) {
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

    $stmt = $this->db->prepare("SELECT id FROM game ORDER BY id DESC");
    $stmt->execute();
    $gid = ($stmt->fetch(PDO::FETCH_ASSOC))["id"];

    if($genres !== false) {
      $total_genre = count($genres);

      for($i = 0; $i < $total_genre; $i++) {
        $stmt = $this->db->prepare("INSERT INTO game_genre(game_id, genre_id) VALUES(:game_id, :genre_id)");
        $stmt->execute([
          ":game_id" => $gid,
          ":genre_id" => (int) $genres[$i]
        ]);
      }
    }

    $game_path = __DIR__ . "/../src/uploads/games_images/{$gid}";
    mkdir($game_path, 0777, true);

    $total_samples = count($samples["name"]);
    if($total_samples != 5) {
      echo
      "<script>
        alert(\"Sample Images Must Be 5.\");
        window.location.href = \"./admin/dashboard.php\";
      </script>";

      $stmt = $this->db->prepare("DELETE FROM game WHERE id = :id");
      $stmt->execute([":id" => $gid]);

      exit();
    }

    // Allowed Extensions.
    $allowed_type = ["jpg", "jpeg", "png", "webp", "avif"];

    // Image Extensions.
    $icon_ext = explode(".", $icon["name"]);
    $icon_ext = end($icon_ext);
    $icon_ext = strtolower($icon_ext);
    if(!in_array($icon_ext, $allowed_type)) {
      echo
      "<script>
        alert(\"JPEG, PNG, WEBP, and AVIF Only.\");
        window.location.href = \"./admin/dashboard.php\";
      </script>";

      $stmt = $this->db->prepare("DELETE FROM game WHERE id = :id");
      $stmt->execute([":id" => $gid]);

      exit();
    }

    $bg_ext = explode(".", $bg["name"]);
    $bg_ext = end($bg_ext);
    $bg_ext = strtolower($bg_ext);
    if(!in_array($bg_ext, $allowed_type)) {
      echo
      "<script>
        alert(\"JPEG, PNG, WEBP, and AVIF Only.\");
        window.location.href = \"./admin/dashboard.php\";
      </script>";

      $stmt = $this->db->prepare("DELETE FROM game WHERE id = :id");
      $stmt->execute([":id" => $gid]);

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
          alert(\"JPEG, PNG, WEBP, and AVIF Only.\");
          window.location.href = \"./admin/dashboard.php\";
        </script>";

        $stmt = $this->db->prepare("DELETE FROM game WHERE id = :id");
        $stmt->execute([":id" => $gid]);

        exit();
      }
    }

    // Image Error.
    if($icon["error"] != 0) {
      echo
      "<script>
        alert(\"An error occured upon uploading game icon.\");
        window.location.href = \"./admin/dashboard.php\";
      </script>";

      $stmt = $this->db->prepare("DELETE FROM game WHERE id = :id");
      $stmt->execute([":id" => $gid]);

      exit();
    }

    if($bg["error"] != 0) {
      echo
      "<script>
        alert(\"An error occured upon uploading game background image.\");
        window.location.href = \"./admin/dashboard.php\";
      </script>";

      $stmt = $this->db->prepare("DELETE FROM game WHERE id = :id");
      $stmt->execute([":id" => $gid]);

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

        $stmt = $this->db->prepare("DELETE FROM game WHERE id = :id");
        $stmt->execute([":id" => $gid]);

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

      $stmt = $this->db->prepare("DELETE FROM game WHERE id = :id");
      $stmt->execute([":id" => $gid]);

      exit();
    }

    if($bg["size"] > 5000000) {
      echo
      "<script>
        alert(\"Do not upload game background image bigger than 5mb.\");
        window.location.href = \"./admin/dashboard.php\";
      </script>";

      $stmt = $this->db->prepare("DELETE FROM game WHERE id = :id");
      $stmt->execute([":id" => $gid]);

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

        $stmt = $this->db->prepare("DELETE FROM game WHERE id = :id");
        $stmt->execute([":id" => $gid]);

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

    echo
    "<script>
      alert(\"{$name} added successfully!\");
      window.location.href = \"./admin/dashboard.php\";
    </script>";
    exit();
  }

  public function editGame($id, $name, $dev, $info, $dl_link, $genres, $icon, $bg, $samples) {
    $res = false;

    if($name !== false) {
      Authentication::checkInputEmpty($name, "name", "./admin/dashboard.php");
      $stmt = $this->db->prepare("UPDATE game SET name = :name WHERE id = :id");
      $stmt->execute([
        ":id" => $id,
        ":name" => $name
      ]);

      $res = true;
    }

    if($dev !== false) {
      Authentication::checkInputEmpty($dev, "developer", "./admin/dashboard.php");
      $stmt = $this->db->prepare("UPDATE game SET developer = :developer WHERE id = :id");
      $stmt->execute([
        ":id" => $id,
        ":developer" => $dev
      ]);

      $res = true;
    }

    if($info !== false) {
      Authentication::checkInputEmpty($info, "info", "./admin/dashboard.php");
      $stmt = $this->db->prepare("UPDATE game SET info = :info WHERE id = :id");
      $stmt->execute([
        ":id" => $id,
        ":info" => $info
      ]);

      $res = true;
    }

    if($dl_link !== false) {
      Authentication::checkInputEmpty($dl_link, "download link", "./admin/dashboard.php");
      $stmt = $this->db->prepare("UPDATE game SET download_link = :dl_link WHERE id = :id");
      $stmt->execute([
        ":id" => $id,
        ":dl_link" => $dl_link
      ]);

      $res = true;
    }

    if($dl_link !== false) {
      Authentication::checkInputEmpty($dl_link, "download link", "./admin/dashboard.php");
      $stmt = $this->db->prepare("UPDATE game SET download_link = :dl_link WHERE id = :id");
      $stmt->execute([
        ":id" => $id,
        ":dl_link" => $dl_link
      ]);

      $res = true;
    }

    if($genres !== false) {
      $stmt = $this->db->prepare("DELETE FROM game_genre WHERE game_id = :game_id");
      $stmt->execute([":game_id" => $id]);

      $total_genre = count($genres);

      for($i = 0; $i < $total_genre; $i++) {
        $stmt = $this->db->prepare("INSERT INTO game_genre VALUES(:game_id, :genre_id)");
        $stmt->execute([
          ":game_id" => $id,
          ":genre_id" => $genres[$i]
        ]);
      }

      $res = true;
    }

    $game_path = __DIR__ . "/../src/uploads/games_images/{$id}";

    $allowed_type = ["jpg", "jpeg", "png", "webp", "avif"];

    if($icon !== false) {
      $icon_ext = explode(".", $icon["name"]);
      $icon_ext = end($icon_ext);
      $icon_ext = strtolower($icon_ext);
      if(!in_array($icon_ext, $allowed_type)) {
        echo
        "<script>
          alert(\"JPEG, PNG, WEBP, and AVIF Only.\");
          window.location.href = \"./admin/dashboard.php\";
        </script>";
        exit();
      }

      if($icon["error"] != 0) {
        echo
        "<script>
          alert(\"An error occured upon uploading game icon.\");
          window.location.href = \"./admin/dashboard.php\";
        </script>";
        exit();
      }

      if($icon["size"] > 5000000) {
        echo
        "<script>
          alert(\"Do not upload game icon bigger than 5mb.\");
          window.location.href = \"./admin/dashboard.php\";
        </script>";
        exit();
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

      move_uploaded_file($icon["tmp_name"], $game_path . "/icon." . $icon_ext);

      $res = true;
    }

    if($bg !== false) {
      $bg_ext = explode(".", $bg["name"]);
      $bg_ext = end($bg_ext);
      $bg_ext = strtolower($bg_ext);
      if(!in_array($bg_ext, $allowed_type)) {
        echo
        "<script>
          alert(\"JPEG, PNG, WEBP, and AVIF Only.\");
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

      if($bg["size"] > 5000000) {
        echo
        "<script>
          alert(\"Do not upload game background image bigger than 5mb.\");
          window.location.href = \"./admin/dashboard.php\";
        </script>";
        exit();
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

      move_uploaded_file($bg["tmp_name"], $game_path . "/bg." . $bg_ext);

      $res = true;
    }

    if($samples !== false) {
      $total_samples = count($samples["name"]);
      if($total_samples != 5) {
        echo
        "<script>
          alert(\"Sample Images Must Be 5.\");
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
            alert(\"JPEG, PNG, WEBP, and AVIF Only.\");
            window.location.href = \"./admin/dashboard.php\";
          </script>";
          exit();
        }
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

        $res = true;
      }
    }

    if($res) {
      echo
      "<script>
        alert(\"Game edited successfully.\");
      </script>";
    }

    echo
    "<script>
      window.location.href = \"./admin/dashboard.php\";
    </script>";
    exit();
  }

  public function deleteGame($id) {
    $stmt = $this->db->prepare("DELETE FROM game WHERE id = :id");
    $stmt->execute([
      ":id" => $id
    ]);

    $directory = "../src/uploads/games_images/{$id}";
    $iterator = new FilesystemIterator($directory, FilesystemIterator::SKIP_DOTS);

    // Loop through the files and directories
    foreach ($iterator as $item) {
      unlink($item->getPathname());
    }

    // Once all contents are deleted, remove the directory itself
    rmdir($directory);

    echo
    "<script>
      alert(\"Game deleted successfully!\");
      window.location.href = \"./admin/dashboard.php\";
    </script>";
    exit();
  }

  public function addGenre($name) {
    Authentication::checkInputEmpty($name, "name", "./admin/dashboard.php");

    $stmt = $this->db->prepare("SELECT name FROM genre WHERE name = :name");
    $stmt->execute([":name" => $name]);
    if($stmt->rowCount() > 0) {
      echo
      "<script>
        alert(\"Genre {$name} is already in the system.\");
        window.location.href = \"./admin/dashboard.php\";
      </script>";
      exit();
    }

    $stmt = $this->db->prepare("INSERT INTO genre(name) VALUES(:name)");
    $stmt->execute([":name" => $name]);

    echo
    "<script>
      alert(\"Genre {$name} added successfully!\");
      window.location.href = \"./admin/dashboard.php\";
    </script>";
    exit();
  }

  public function editGenre($id, $name) {
    Authentication::checkInputEmpty($name, "name", "./admin/dashboard.php");

    $stmt = $this->db->prepare("SELECT name FROM genre WHERE name = :name");
    $stmt->execute([":name" => $name]);
    if($stmt->rowCount() > 0) {
      echo
      "<script>
        alert(\"Genre {$name} is already in the system.\");
        window.location.href = \"./admin/dashboard.php\";
      </script>";
      exit();
    }

    $stmt = $this->db->prepare("UPDATE genre SET name = :name WHERE id = :id");
    $stmt->execute([
      ":id" => $id,
      ":name" => $name
    ]);

    echo
    "<script>
      alert(\"Genre {$name} updated successfully!\");
      window.location.href = \"./admin/dashboard.php\";
    </script>";
    exit();
  }

  public function deleteGenre($id) {
    $stmt = $this->db->prepare("DELETE FROM genre WHERE id = :id");
    $stmt->execute([
      ":id" => $id
    ]);

    echo
    "<script>
      alert(\"Genre deleted successfully!\");
      window.location.href = \"./admin/dashboard.php\";
    </script>";
    exit();
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
  $genres = false;
  if(isset($_POST["genres"])) {
    $genres = $_POST["genres"];
  }

  (new Game())->addGame(
    trim($_POST["name"]),
    trim($_POST["developer"]),
    trim($_POST["info"]),
    trim($_POST["dl_link"]),
    $genres,
    $_FILES["icon"],
    $_FILES["bg"],
    $_FILES["samples"]
  );
}

if(isset($_POST["edit_game"])) {
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

  $icon = isset($_FILES["icon"]) ? $_FILES["icon"] : false;
  $bg = isset($_FILES["bg"]) ? $_FILES["bg"] : false;
  $samples = isset($_FILES["samples"]) ? $_FILES["samples"] : false;

  (new Game())->editGame(
    $_POST["game"],
    $name,
    $dev,
    $info,
    $dl_link,
    $genres,
    $icon,
    $bg,
    $samples
  );
}

if(isset($_POST["delete_game"])) {
  (new Game())->deleteGame($_POST["game"]);
}

if(isset($_POST["add_genre"])) {
  (new Game())->addGenre(strtolower(trim($_POST["name"])));
}

if(isset($_POST["edit_genre"])) {
  (new Game())->editGenre($_POST["genre"], strtolower(trim($_POST["name"])));
}

if(isset($_POST["delete_genre"])) {
  (new Game())->deleteGenre($_POST["genre"]);
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