<?php

namespace App\Model;

use PDO;
use App\Core\Database;

class VisitedGame {
  private $db;

  public function __construct() {
    $this->db = Database::connect();
  }

  public function add($uid, $gid) {
    $stmt = $this->db->prepare("INSERT INTO `visited_game`(game_id, user_id) VALUES(:gid, :uid)");
    return $stmt->execute([
      ":gid" => $gid,
      ":uid" => $uid
    ]);
  }

  public function isGameVisitedByUserId($uid, $gid) {
    $stmt = $this->db->prepare("SELECT * FROM `visited_game` WHERE `user_id` = :uid AND `game_id` = :gid");
    $stmt->execute([ ":uid" => $uid, ":gid" => $gid ]);

    $is_visited = false;
    if($stmt->rowCount() > 0) {
      $is_visited = true;
    }

    return $is_visited;
  }

  public function getAllRecentGamesByUserId($uid) {
    $stmt = $this->db->prepare(
      "SELECT `game`.`id`, `game`.`name`, `game`.`info`
       FROM `game`
       JOIN `visited_game` ON `game`.`id` = `visited_game`.`game_id`
       JOIN `user` ON `visited_game`.`user_id` = `user`.`id`
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

  public function getTotalRecentGamesByUserId($uid) {
    $stmt = $this->db->prepare(
      "SELECT `game`.`id`, `game`.`name`, `game`.`info`
       FROM `game`
       JOIN `visited_game` ON `game`.`id` = `visited_game`.`game_id`
       JOIN `user` ON `visited_game`.`user_id` = `user`.`id`
       WHERE `user`.`id` = :uid"
    );

    if(!$stmt->execute([":uid" => $uid])) {
      return false;
    }

    return $stmt->rowCount();
  }
  public function getMostVisitedGames() {
    $stmt = $this->db->prepare(
      "SELECT `game`.`id`, `game`.`name`, COUNT(`visited_game`.`user_id`) AS visits_count
       FROM `game`
       JOIN `visited_game` ON `game`.`id` = `visited_game`.`game_id`
       JOIN `user` ON `visited_game`.`user_id` = `user`.`id`
       GROUP BY `game_id`
       ORDER BY visits_count DESC
       LIMIT 15");

    if(!$stmt->execute()) {
      return false;
    }

    if($stmt->rowCount() <= 0) {
      return 0;
    }

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getTotalMostVisitedGames() {
    $stmt = $this->db->prepare(
      "SELECT `game`.`id`, `game`.`name`, COUNT(`visited_game`.`user_id`) AS visits_count
       FROM `game`
       JOIN `visited_game` ON `game`.`id` = `visited_game`.`game_id`
       JOIN `user` ON `visited_game`.`user_id` = `user`.`id`
       GROUP BY `game_id`
       ORDER BY visits_count DESC
       LIMIT 15");

    if(!$stmt->execute()) {
      return false;
    }

    return $stmt->rowCount();
  }

  public function update($uid, $gid) {
    $stmt = $this->db->prepare("UPDATE `visited_game` SET `date_visited` = CURRENT_TIMESTAMP WHERE game_id = :gid AND user_id =:uid");
    return $stmt->execute([
      ":gid" => $gid,
      ":uid" => $uid
    ]);
  }
}