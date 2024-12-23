<?php

namespace App\Model;

use PDO;
use App\Core\Database;
use App\Core\Utilities;

class GameReview {
  private $db;

  public function __construct() {
    $this->db = Database::connect();
  }

  public function add($gid, $uid, $rating, $comment) {
    $stmt = $this->db->prepare("INSERT INTO game_review(game_id, user_id, rating, comment) VALUES(:gid, :uid, :rating, :comment)");
    return $stmt->execute([
      ":gid" => $gid,
      ":uid" => $uid,
      ":rating" => $rating,
      ":comment" => $comment
    ]);
  }

  /* === RETRIEVE FUNCTIONS === */
  public function getAllReviewsByGameId($gid) {
    $stmt = $this->db->prepare("SELECT * FROM `game_review` WHERE `game_id` = :gid");
    if(!$stmt->execute([ ":gid" => $gid ])) {
      return false;
    }

    if($stmt->rowCount() <= 0) {
      return 0;
    }

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getTotalAllReviewsByGameId($gid) {
    $stmt = $this->db->prepare("SELECT * FROM `game_review` WHERE `game_id` = :gid");
    if(!$stmt->execute([ ":gid" => $gid ])) {
      return false;
    }

    return $stmt->rowCount();
  }

  public function getAverageRatingForAllGames() {
    $stmt = $this->db->prepare(
    "SELECT `game`.`id` AS game_id, `game`.`name` AS game_name, AVG(`game_review`.`rating`) AS average_rating
     FROM `game`
     LEFT JOIN `game_review` ON `game`.`id` = `game_review`.`game_id`
     GROUP BY `game`.`id`, `game`.`name`
     ORDER BY average_rating DESC");

    if(!$stmt->execute()) {
      return false;
    }

    if($stmt->rowCount() <= 0) {
      return 0;
    }

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getAverageRatingByGameId($game_id) {
    $stmt = $this->db->prepare("SELECT AVG(`rating`) AS avg FROM `game_review` WHERE game_id = :gid");
    if(!$stmt->execute([ ":gid" => $game_id ])) {
      Utilities::showAlert("dito");
      return false;
    }

    if($stmt->rowCount() <= 0) {
      Utilities::showAlert("dito1");
      return 0;
    }

    return ($stmt->fetch(PDO::FETCH_ASSOC))["avg"];
  }

  public function delete($uid, $gid) {
    $stmt = $this->db->prepare("DELETE FROM game_review WHERE game_id = :gid AND user_id = :uid");
    return $stmt->execute([
      ":gid" => $gid,
      ":uid" => $uid
    ]);
  }
}