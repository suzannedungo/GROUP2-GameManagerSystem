<?php
require_once __DIR__ . "/../../config/Database.php";
require_once __DIR__ . "/../Authentication.php";

class Game {
  private $db;

  public function __construct() {
    $this->db = Database::connect();
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