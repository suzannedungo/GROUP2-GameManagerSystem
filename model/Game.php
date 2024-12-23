<?php

namespace App\Model;

use PDO;
use App\Core\Database;
use App\Core\Utilities;

class Game {
  private $db;

  public function __construct() {
    $this->db = Database::connect();
  }

  /* === CREATE FUNCTIONS === */
  public function add($name, $developer, $info, $dl_link) {
    $stmt = $this->db->prepare("INSERT INTO `game`(`name`, `developer`, `info`, `dl_link`) VALUES(:name, :dev, :info, :dl_link)");
    return $stmt->execute([
      ":name" => $name,
      ":dev" => $developer,
      ":info" => $info,
      ":dl_link" => $dl_link
    ]);
  }

  /* === RETRIEVE FUNCTIONS === */
  public function getAllGames() {
    $stmt = $this->db->prepare("SELECT * FROM `game`");
    if(!$stmt->execute()) {
      return false;
    }

    if($stmt->rowCount() <= 0) {
      return 0;
    }

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getRecentAdded() {
    $stmt = $this->db->prepare("SELECT * FROM `game` WHERE `date_added` >= DATE_SUB(NOW(), INTERVAL 3 DAY)");
    if(!($stmt->execute())) {
      return false;
    }

    if($stmt->rowCount() <= 0) {
      return 0;
    }

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getAllId() {
    $stmt = $this->db->prepare("SELECT `id` FROM `game`");
    if(!$stmt->execute()) {
      return false;
    }

    if($stmt->rowCount() <= 0) {
      return 0;
    }

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  
  public function getAllNames() {
    $stmt = $this->db->prepare("SELECT `name` FROM `game`");
    if(!$stmt->execute()) {
      return false;
    }

    if($stmt->rowCount() <= 0) {
      return 0;
    }

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  
  public function getTotalGame() {
    $stmt = $this->db->prepare("SELECT COUNT(*) AS total_game FROM `game`");
    if(!($stmt->execute())) {
      return false;
    }

    return ($stmt->fetch(PDO::FETCH_ASSOC))["total_game"];
  }

  // Get By Id.
  public function getGameById($id) {
    $stmt = $this->db->prepare("SELECT * FROM `game` WHERE id = :id");
    if(!$stmt->execute([ ":id" => $id ])) {
      return false;
    }

    if($stmt->rowCount() <= 0) {
      return 0;
    }

    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function getIdById($id) {
    $stmt = $this->db->prepare("SELECT `id` FROM `game` WHERE id = :id");
    if(!$stmt->execute([ ":id" => $id ])) {
      return false;
    }

    if($stmt->rowCount() <= 0) {
      return 0;
    }

    return ($stmt->fetch(PDO::FETCH_ASSOC))["id"];
  }

  public function getLastId() {
    $stmt = $this->db->prepare("SELECT `id` FROM `game` ORDER BY `id` DESC LIMIT 1");
    if(!$stmt->execute()) {
      return false;
    }

    if($stmt->rowCount() <= 0) {
      return 0;
    }

    return ($stmt->fetch(PDO::FETCH_ASSOC))["id"];
  }

  public function getIconById($id) {
    $id = $this->getIdById($id);
    if($id == false || $id == 0) {
      return $id;
    }

    $img = "uploads/games_images/{$id}";
    $files = scandir(Utilities::getPath() . "/public/" . $img);
    $file = null;

    foreach($files as $file) {
      if($file === "." || $file === "..") {
        continue;
      }

      $fileNameWithoutExt = pathinfo($file, PATHINFO_FILENAME);

      if($fileNameWithoutExt === "icon") {
        $img = $img . "/" . $file;
        break;
      }
    }

    return $img;
  }

  public function getBGById($id) {
    $id = $this->getIdById($id);
    if($id == false || $id == 0) {
      return $id;
    }

    $img = "uploads/games_images/{$id}";
    $files = scandir(Utilities::getPath() . "/public/" . $img);
    $file = null;

    foreach($files as $file) {
      if($file === "." || $file === "..") {
        continue;
      }

      $fileNameWithoutExt = pathinfo($file, PATHINFO_FILENAME);

      if($fileNameWithoutExt === "bg") {
        $img = $img . "/" . $file;
        break;
      }
    }

    return $img;
  }

  public function getSamplesById($id) {
    $id = $this->getIdById($id);
    if($id == false || $id == 0) {
      return $id;
    }

    $path = "uploads/games_images/{$id}";
    $files = scandir(Utilities::getPath() . "/public/" . $path);
    $total_files  = count($files);
    $img = null;

    for($i = 0; $i < $total_files; $i++) {
      if ($files[$i] === "." || $files[$i] === "..") {
        continue;
      }

      $fileNameWithoutExt = pathinfo($files[$i], PATHINFO_FILENAME);

      if ($fileNameWithoutExt === "bg" && $fileNameWithoutExt === "icon") {
        continue;
      }

      $img[$i] = $path . "/" . $files[$i];
    }

    return $img;
  }

  public function getImagesById($id) {
    $id = $this->getIdById($id);
    if($id == false || $id == 0) {
      return $id;
    }

    $path = "uploads/games_images/{$id}";
    $files = scandir(Utilities::getPath() . "/public/" . $path);
    $total_files  = count($files);
    $img = null;

    for($i = 0; $i < $total_files; $i++) {
      if($files[$i] === "." || $files[$i] === "..") {
        continue;
      }
    
      if($i === 2) {
        $img["bg"] = $path . "/" . $files[$i];
        continue;
      }
    
      if($i === 3) {
        $img["icon"] = $path . "/" . $files[$i];
        continue;
      }
    
      $img["samples"][$i] = $path . "/" . $files[$i];
    }

    return $img;
  }

  /* === UPDATE FUNCTIONS === */
  // Update By Id.
  public function updateNameById($id, $name) {
    $stmt = $this->db->prepare("UPDATE `game` SET name = :name WHERE id = :id");
    return $stmt->execute([
      ":name" => $name,
      ":id" => $id
    ]);
  }

  public function updateDeveloperById($id, $developer) {
    $stmt = $this->db->prepare("UPDATE `game` SET developer = :dev WHERE id = :id");
    return $stmt->execute([
      ":dev" => $developer,
      ":id" => $id
    ]);
  }

  public function updateInfoById($id, $info) {
    $stmt = $this->db->prepare("UPDATE `game` SET info = :info WHERE id = :id");
    return $stmt->execute([
      ":info" => $info,
      ":id" => $id
    ]);
  }

  public function updateDLLById($id, $dl_link) {
    $stmt = $this->db->prepare("UPDATE `game` SET dl_link = :dl_link WHERE id = :id");
    return $stmt->execute([
      ":dl_link" => $dl_link,
      ":id" => $id
    ]);
  }

  public function updateDateUpdatedById($id) {
    $stmt = $this->db->prepare("UPDATE `game` SET date_updated = NOW() WHERE id = :id");
    return $stmt->execute([ ":id" => $id ]);
  }

  /* === DELETE FUNCTIONS === */
  public function delete($id) {
    $stmt = $this->db->prepare("DELETE FROM `game` WHERE id = :id");
    return $stmt->execute([ ":id" => $id ]);
  }
}