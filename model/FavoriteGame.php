<?php

namespace App\Model;

use PDO;
use App\Core\Database;

class FavoriteGame {
  private $db;

  public function __construct() {
    $this->db = Database::connect();
  }

  public function add($uid, $gid) {
    $stmt = $this->db->prepare("INSERT INTO favorite_game(`user_id`, `game_id`) VALUES(:uid, :gid)");
    return $stmt->execute([ ":uid" => $uid, ":gid" => $gid ]);
  }

  public function isGameFavoriteByUserId($uid, $gid) {
    $stmt = $this->db->prepare("SELECT * FROM favorite_game WHERE user_id = :uid AND game_id = :gid");
    $stmt->execute([ ":uid" => $uid, ":gid" => $gid ]);

    $is_fav = false;
    if($stmt->rowCount() > 0) {
      $is_fav = true;
    }

    return $is_fav;
  }

  public function getAllFavoriteGamesByUserId($uid) {
    $stmt = $this->db->prepare(
      "SELECT `game`.`id`, `game`.`name`, `game`.`info`
       FROM `game`
       JOIN `favorite_game` ON `game`.`id` = `favorite_game`.`game_id`
       JOIN `user` ON `favorite_game`.`user_id` = `user`.`id`
       WHERE `user`.`id` = :uid"
    );

    if(!$stmt->execute([":uid" => $uid])) {
      return false;
    }

    if($stmt->rowCount() <= 0) {
      return 0;
    }

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getTotalFavoriteGamesByUserId($uid) {
    $stmt = $this->db->prepare(
      "SELECT `game`.`id`, `game`.`name`, `game`.`info`
       FROM `game`
       JOIN `favorite_game` ON `game`.`id` = `favorite_game`.`game_id`
       JOIN `user` ON `favorite_game`.`user_id` = `user`.`id`
       WHERE `user`.`id` = :uid"
    );

    if(!$stmt->execute([":uid" => $uid])) {
      return false;
    }

    return $stmt->rowCount();
  }

  public function getMostFavoriteGames() {
    $stmt = $this->db->prepare(
    "SELECT `game`.`id`, `game`.`name`, COUNT(`favorite_game`.`user_id`) AS favorites_count
     FROM `game`
     JOIN `favorite_game` ON `game`.`id` = `favorite_game`.`game_id`
     JOIN `user` ON `favorite_game`.`user_id` = `user`.`id`
     GROUP BY `game`.`id`
     ORDER BY favorites_count DESC
     LIMIT 15");

    if(!$stmt->execute()) {
      return false;
    }

    if($stmt->rowCount() <= 0) {
      return 0;
    }

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  
  public function getTotalMostFavoriteGames() {
    $stmt = $this->db->prepare(
    "SELECT `game`.`id`, `game`.`name`, COUNT(`favorite_game`.`user_id`) AS favorites_count
     FROM `game`
     JOIN `favorite_game` ON `game`.`id` = `favorite_game`.`game_id`
     JOIN `user` ON `favorite_game`.`user_id` = `user`.`id`
     GROUP BY `game`.`id`
     ORDER BY favorites_count DESC
     LIMIT 15");

    if(!$stmt->execute()) {
      return false;
    }

    return $stmt->rowCount();
  }

  public function delete($uid, $gid) {
    $stmt = $this->db->prepare("DELETE FROM favorite_game WHERE `user_id` = :uid AND `game_id` = :gid");
    return $stmt->execute([ ":uid" => $uid, ":gid" => $gid ]);
  }
}