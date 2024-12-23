<?php

namespace App\Model;

use PDO;
use App\Core\Database;

class GameGenre {
  private $db;

  public function __construct() {
    $this->db = Database::connect();
  }

  /* === CREATE FUNCTION === */
  public function add($game_id, $genre_id) {
    $stmt = $this->db->prepare("INSERT INTO `game_genre`(`game_id`, `genre_id`) VALUES(:game_id, :genre_id)");
    return $stmt->execute([
      ":game_id" => $game_id,
      ":genre_id" => $genre_id
    ]);
  }

  /* === RETRIEVE FUNCTIONS === */
  public function getGenreNamesByGameId($game_id) {
    $stmt = $this->db->prepare(
      "SELECT GROUP_CONCAT(`genre`.`name` SEPARATOR ', ') AS genre_names
       FROM `game`
       JOIN `game_genre` ON `game`.`id` = `game_genre`.`game_id`
       JOIN `genre` ON `game_genre`.`genre_id` = `genre`.`id`
       WHERE `game`.`id` = :gid
       GROUP BY `game`.`id`, `game`.`name`");

    if(!$stmt->execute([ ":gid" => $game_id ])) {
      return false;
    }

    if($stmt->rowCount() <= 0) {
      return 0;
    }

    return ($stmt->fetch(PDO::FETCH_ASSOC))["genre_names"];
  }

  public function getAllGamesByGenreId($genre_id) {
    $stmt = $this->db->prepare(
      "SELECT `game`.`id`, `game`.`name`, `game`.`info`
       FROM `game`
       JOIN `game_genre` ON `game`.`id` = `game_genre`.`game_id`
       JOIN `genre` ON `game_genre`.`genre_id` = `genre`.`id`
       WHERE `genre`.`id` = :genre_id"
    );

    if(!$stmt->execute([":genre_id" => $genre_id])) {
      return false;
    }

    if($stmt->rowCount() <= 0) {
      return 0;
    }

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getTotalGamesByGenreId($genre_id) {
    $stmt = $this->db->prepare(
      "SELECT `game`.`id`, `game`.`name`
       FROM `game`
       JOIN `game_genre` ON `game`.`id` = `game_genre`.`game_id`
       JOIN `genre` ON `game_genre`.`genre_id` = `genre`.`id`
       WHERE `genre`.`id` = :genre_id"
    );

    if(!$stmt->execute([":genre_id" => $genre_id])) {
      return false;
    }

    return $stmt->rowCount();
  }

  /* === DELETE FUNCTIONS === */
  public function deleteByGameId($gid) {
    $stmt = $this->db->prepare("DELETE FROM `game_genre` WHERE game_id = :gid");
    return $stmt->execute([ ":gid" => $gid ]);
  }
}