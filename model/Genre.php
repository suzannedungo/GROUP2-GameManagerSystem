<?php

namespace App\Model;

use PDO;
use App\Core\Database;

class Genre {
  private $db;

  public function __construct() {
    $this->db = Database::connect();
  }

  /* === CREATE FUNCTION === */
  public function add($name) {
    $stmt = $this->db->prepare("INSERT INTO `genre`(`name`) VALUES(:name)");
    return $stmt->execute([ ":name" => $name ]);
  }

  /* === RETRIEVE FUNCTIONS === */
  public function getAllGenre() {
    $stmt = $this->db->prepare("SELECT * FROM `genre`");
    if(!$stmt->execute()) {
      return false;
    }

    if($stmt->rowCount() <= 0) {
      return 0;
    }

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getAllGenreNames() {
    $stmt = $this->db->prepare("SELECT `name` FROM `genre`");
    if(!$stmt->execute()) {
      return false;
    }

    if($stmt->rowCount() <= 0) {
      return 0;
    }

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getTotalGenre() {
    $stmt = $this->db->prepare("SELECT COUNT(*) AS total_genre FROM `genre`");
    if(!($stmt->execute())) {
      return false;
    }

    return ($stmt->fetch(PDO::FETCH_ASSOC))["total_genre"];
  }

  public function getGenreById($id) {
    $stmt = $this->db->prepare("SELECT * FROM `genre` WHERE `id` = :id");
    if(!$stmt->execute([ ":id" => $id ])) {
      return false;
    }

    if($stmt->rowCount() <= 0) {
      return 0;
    }

    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function getNameById($id) {
    $stmt = $this->db->prepare("SELECT `genre`.`name` FROM `genre` WHERE id = :id");
    if(!$stmt->execute([":id" => $id])) {
      return false;
    }

    if($stmt->rowCount() <= 0) {
      return 0;
    }

    return ($stmt->fetch(PDO::FETCH_ASSOC))["name"];
  }
  public function getNameByName($name) {
    $stmt = $this->db->prepare("SELECT `genre`.`name` FROM `genre` WHERE name = :name");
    if(!$stmt->execute([":name" => $name])) {
      return false;
    }

    if($stmt->rowCount() <= 0) {
      return 0;
    }

    return ($stmt->fetch(PDO::FETCH_ASSOC))["name"];
  }

  /* === UPDATE FUNCTIONS === */
  public function updateById($id, $name) {
    $stmt = $this->db->prepare("UPDATE `genre` SET name = :name WHERE id = :id");
    return $stmt->execute([
      ":name" => $name,
      ":id" => $id
    ]);
  }

  /* === DELETE FUNCTIONS === */
  public function deleteById($id) {
    $stmt = $this->db->prepare("DELETE FROM `genre` WHERE id = :id");
    return $stmt->execute([ ":id" => $id ]);
  }
}