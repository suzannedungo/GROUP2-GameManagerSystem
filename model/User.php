<?php

namespace App\Model;

use PDO;
use App\Core\Database;
use App\Core\Utilities;
use FilesystemIterator;

class User {
  private $db;

  public function __construct() {
    $this->db = Database::connect();
  }

  /* === CREATE FUNCTION === */
  public function add($name, $username, $email, $password, $tokencode) {
    $stmt = $this->db->prepare("INSERT INTO `user`(`name`, `username`, `email`, `password`, `tokencode`) VALUES(:name, :username, :email, :password, :tokencode)");
    return $stmt->execute([
      ":name" => $name,
      ":username" => $username,
      ":email" => $email,
      ":password" => $password,
      ":tokencode" => $tokencode
    ]);
  }



  /* === RETRIEVE FUNCTIONS === */
  public function getAllUsers() {
    $stmt = $this->db->prepare("SELECT * FROM `user`");
    if(!($stmt->execute())) {
      return false;
    }

    if($stmt->rowCount() <= 0) {
      return 0;
    }

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getTotalUser() {
    $stmt = $this->db->prepare("SELECT COUNT(*) AS total_user FROM `user`");
    if(!($stmt->execute())) {
      return false;
    }

    return ($stmt->fetch(PDO::FETCH_ASSOC))["total_user"];
  }

  public function getRecentJoined() {
    // $stmt = $this->db->prepare("SELECT * FROM `user` ORDER BY `date_added` DESC LIMIT :limit");
    $stmt = $this->db->prepare("SELECT * FROM `user` WHERE `date_added` >= DATE_SUB(NOW(), INTERVAL 3 DAY)");
    if(!($stmt->execute())) {
      return false;
    }

    if($stmt->rowCount() <= 0) {
      return 0;
    }

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  // Get By Id.
  public function getUserById($id) {
    $stmt = $this->db->prepare("SELECT * FROM `user` WHERE id = :id");
    if(!($stmt->execute([ ":id" => $id ]))) {
      return false;
    }

    if($stmt->rowCount() <= 0) {
      return 0;
    }

    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function getUsernameById($id) {
    $stmt = $this->db->prepare("SELECT `user`.`username` FROM `user` WHERE id = :id");
    if(!($stmt->execute([ ":id" => $id ]))) {
      return false;
    }

    if($stmt->rowCount() <= 0) {
      return 0;
    }

    return ($stmt->fetch(PDO::FETCH_ASSOC))["username"];
  }

  public function getEmailById($id) {
    $stmt = $this->db->prepare("SELECT `user`.`email` FROM `user` WHERE id = :id");
    if(!($stmt->execute([ ":id" => $id ]))) {
      return false;
    }

    if($stmt->rowCount() <= 0) {
      return 0;
    }

    return ($stmt->fetch(PDO::FETCH_ASSOC))["email"];
  }

  public function getProfileImageById($id) {
    $stmt = $this->db->prepare("SELECT `user`.`default_image`, `user`.`username` FROM `user` WHERE id = :id");
    if(!($stmt->execute([ ":id" => $id ]))) {
      return false;
    }

    $user_info = $stmt->fetch(PDO::FETCH_ASSOC);
    $img_path = "uploads/users_images/default_dp.jpg";
    if(!$user_info["default_image"]) {
      return $img_path;
    }

    $img_path = "uploads/users_images/{$user_info['username']}";
    $files = scandir(Utilities::getPath() . "/public/" . $img_path);
    $file = null;

    foreach ($files as $file) {
      if ($file === "." || $file === "..") {
          continue;
      }

      $fileNameWithoutExt = pathinfo($file, PATHINFO_FILENAME);

      if ($fileNameWithoutExt === "dp") {
          $img_path = $img_path . "/" . $file;
      }
    }

    return $img_path;
  }

  // Get By Email.
  public function getUserByEmail($email) {
    $stmt = $this->db->prepare("SELECT * FROM `user` WHERE email = :email");
    if(!($stmt->execute([ ":email" => $email ]))) {
      return false;
    }

    if($stmt->rowCount() <= 0) {
      return 0;
    }

    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function getProfileImageByEmail($email) {
    $stmt = $this->db->prepare("SELECT `user`.`default_image`, `user`.`username` FROM `user` WHERE email = :email");
    if(!($stmt->execute([ ":email" => $email ]))) {
      return false;
    }

    $user_info = $stmt->fetch(PDO::FETCH_ASSOC);
    $img_path = "uploads/users_images/default_dp.jpg";
    if(!$user_info["default_image"]) {
      return $img_path;
    }

    $img_path = "uploads/users_images/{$user_info['username']}";
    $files = scandir(Utilities::getPath() . "/public/" . $img_path);
    $file = null;

    foreach ($files as $file) {
      if ($file === "." || $file === "..") {
          continue;
      }

      $fileNameWithoutExt = pathinfo($file, PATHINFO_FILENAME);

      if ($fileNameWithoutExt === "dp") {
          $img_path = $img_path . "/" . $file;
      }
    }

    return $img_path;
  }

  public function getEmailByEmail($email) {
    $stmt = $this->db->prepare("SELECT `user`.`email` FROM `user` WHERE email = :email");
    if(!($stmt->execute([ ":email" => $email ]))) {
      return false;
    }

    if($stmt->rowCount() <= 0) {
      return 0;
    }

    return ($stmt->fetch(PDO::FETCH_ASSOC))["email"];
  }

  public function getStatusByEmail($email) {
    $stmt = $this->db->prepare("SELECT `user`.`status` FROM `user` WHERE email = :email");
    if(!($stmt->execute([ ":email" => $email ]))) {
      return false;
    }

    if($stmt->rowCount() <= 0) {
      return 0;
    }

    return ($stmt->fetch(PDO::FETCH_ASSOC))["status"];
  }

  // Get By Username.
  public function getUsernameByUsername($username) {
    $stmt = $this->db->prepare("SELECT `user`.`username` FROM `user` WHERE username = :username");
    if(!($stmt->execute([ ":username" => $username ]))) {
      return false;
    }

    if($stmt->rowCount() <= 0) {
      return 0;
    }

    return ($stmt->fetch(PDO::FETCH_ASSOC))["username"];
  }



  /* === UPDATE FUNCTIONS === */
  // Update By Id.
  public function updateNameById($id, $name) {
    $stmt = $this->db->prepare("UPDATE `user` SET `name` = :name, `date_updated` = NOW() WHERE id = :id");
    return $stmt->execute([
      ":name" => $name,
      ":id" => $id
    ]);
  }

  public function updateUsernameById($id, $uname) {
    $stmt = $this->db->prepare("UPDATE `user` SET `username` = :uname, `date_updated` = NOW() WHERE id = :id");
    return $stmt->execute([
      ":uname" => $uname,
      ":id" => $id
    ]);
  }

  public function updateEmailById($id, $email) {
    $stmt = $this->db->prepare("UPDATE `user` SET `email` = :email, `date_updated` = NOW() WHERE id = :id");
    return $stmt->execute([
      ":email" => $email,
      ":id" => $id
    ]);
  }

  public function updateDPById($id, $dp) {
    $username = $this->getUsernameById($id);
    $path = Utilities::getPath() . "/public/uploads/users_images/{$username}";
    $allowed_type = ["jpg", "jpeg", "png", "webp", "avif"];

    $img_ext = explode(".", $dp["name"]);
    $img_ext = end($img_ext);
    $img_ext = strtolower($img_ext);
    if(!in_array($img_ext, $allowed_type)) {
      Utilities::showAlertAndExit("JPEG, PNG, WEBP, and AVIF Only.", "/user/profile", 400);
      exit();
    }

    if($dp["error"] != 0) {
      Utilities::showAlertAndExit("An error occured on uploading your image.", "/user/profile", 500);
    }

    if($dp["size"] > 5000000) {
      Utilities::showAlertAndExit("Do not upload image bigger than 5mb.", "/user/profile", 400);
    }

    $old = "{$path}/dp.jpeg";
    if(file_exists($old)) {
      unlink($old);
    }

    $old = "{$path}/dp.jpg";
    if(file_exists($old)) {
      unlink($old);
    }

    $old = "{$path}/dp.png";
    if(file_exists($old)) {
      unlink($old);
    }

    $old = "{$path}/dp.webp";
    if(file_exists($old)) {
      unlink($old);
    }

    $old = "{$path}/dp.avif";
    if(file_exists($old)) {
      unlink($old);
    }

    move_uploaded_file($dp["tmp_name"], $path . "/dp." . $img_ext);

    $stmt = $this->db->prepare("UPDATE `user` SET `default_image` = 1, `date_updated` = NOW() WHERE `id` = :id");
    if(!$stmt->execute([ ":id" => $id ])) {
      return false;
    }

    return "uploads/users_images/{$username}/dp.{$img_ext}";
  }

  public function updatePasswordById($id, $password) {
    $stmt = $this->db->prepare("UPDATE `user` SET `password` = :password, `date_updated` = NOW() WHERE id = :id");
    return $stmt->execute([
      ":password" => $password,
      ":id" => $id
    ]);
  }

  public function updateTCById($id, $tc) {
    $stmt = $this->db->prepare("UPDATE `user` SET `tokencode` = :tc, `date_updated` = NOW() WHERE id = :id");
    return $stmt->execute([
      ":tc" => $tc,
      ":id" => $id
    ]);
  }

  public function updateDateUpdatedById($id) {
    $stmt = $this->db->prepare("UPDATE `user` SET `date_updated` = NOW() WHERE id = :id");
    return $stmt->execute([ ":id" => $id ]);
  }

  public function resetDPById($id) {
    $username = $this->getUsernameById($id);
    $path = Utilities::getPath() . "/public/uploads/users_images/{$username}";
    $old = "{$path}/dp.jpeg";
    if(file_exists($old)) {
      unlink($old);
    }

    $old = "{$path}/dp.jpg";
    if(file_exists($old)) {
      unlink($old);
    }

    $old = "{$path}/dp.png";
    if(file_exists($old)) {
      unlink($old);
    }

    $old = "{$path}/dp.webp";
    if(file_exists($old)) {
      unlink($old);
    }

    $old = "{$path}/dp.avif";
    if(file_exists($old)) {
      unlink($old);
    }

    $stmt = $this->db->prepare("UPDATE `user` SET `default_image` = 0, `date_updated` = NOW() WHERE `id` = :id");
    if(!$stmt->execute([ ":id" => $id ])) {
      return false;
    }

    return "uploads/users_images/default_dp.jpg";
  }

  // Update By Email.
  public function updateStatusByEmail($email, $status) {
    $stmt = $this->db->prepare("UPDATE `user` SET `status` = :status, `date_updated` = NOW() WHERE email = :email");
    return $stmt->execute([
      ":status" => $status,
      ":email" => $email
    ]);
  }

  public function updateDateUpdatedByEmail($email) {
    $stmt = $this->db->prepare("UPDATE `user` SET `date_updated` = NOW() WHERE email = :email");
    return $stmt->execute([ ":email" => $email ]);
  }

  public function deleteByEmail($email) {
    $stmt = $this->db->prepare("DELETE FROM `user` WHERE `email` = :email");
    $stmt->execute([ ":email" => $email ]);

    $username = ($this->getUserByEmail($email))["email"];

    $directory = Utilities::getPath() . "/public/uploads/users_images/{$username}";
    $iterator = new FilesystemIterator($directory, FilesystemIterator::SKIP_DOTS);

    foreach ($iterator as $item) {
      unlink($item->getPathname());
    }

    return rmdir($directory);
  }
}