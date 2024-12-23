<?php

namespace App\Model;

use PDO;
use App\Core\Database;
use App\Core\Utilities;

class Admin {
  private $db;

  public function __construct() {
    $this->db = Database::connect();
  }

  /* === RETRIEVE FUNCTIONS === */
  // Get By Email.
  public function getAdminByEmail($email) {
    $stmt = $this->db->prepare("SELECT * FROM `admin` WHERE `email` = :email");
    if(!($stmt->execute([ ":email" => $email ]))) {
      return false;
    }

    if($stmt->rowCount() <= 0) {
      return 0;
    }

    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function getProfileImageByEmail($email) {
    $stmt = $this->db->prepare("SELECT `admin`.`default_image` FROM `admin` WHERE email = :email");
    if(!($stmt->execute([ ":email" => $email ]))) {
      return false;
    }

    $is_def_img = ($stmt->fetch(PDO::FETCH_ASSOC))["default_image"];
    $img_path = "uploads/admin_images/default_dp.jpg";
    if(!$is_def_img) {
      return $img_path;
    }

    $img_path = "uploads/admin_images";
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
    $stmt = $this->db->prepare("SELECT `admin`.`email` FROM `admin` WHERE `email` = :email");
    if(!($stmt->execute([ ":email" => $email ]))) {
      return false;
    }

    if($stmt->rowCount() <= 0) {
      return 0;
    }

    return ($stmt->fetch(PDO::FETCH_ASSOC))["email"];
  }

  /* === UPDATE FUNCTIONS === */
  public function updateNameById($id, $name) {
    $stmt = $this->db->prepare("UPDATE `admin` SET `name` = :name, `date_updated` = NOW() WHERE `id` = :id");
    return $stmt->execute([ 
      ":id" => $id,
      ":name" => $name
    ]);
  }

  public function updateEmailById($id, $email) {
    $stmt = $this->db->prepare("UPDATE `admin` SET `email` = :email, `date_updated` = NOW() WHERE `id` = :id");
    return $stmt->execute([ 
      ":id" => $id,
      ":email" => $email
    ]);
  }

  public function updatePasswordById($id, $password) {
    $stmt = $this->db->prepare("UPDATE `admin` SET `password` = :password, `date_updated` = NOW() WHERE `id` = :id");
    return $stmt->execute([ 
      ":id" => $id,
      ":password" => $password
    ]);
  }

  public function resetDPById($id) {
    $path = Utilities::getPath() . "/public/uploads/admin_images";
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

    $stmt = $this->db->prepare("UPDATE `admin` SET `default_image` = 0, `date_updated` = NOW() WHERE `id` = :id");
    if(!$stmt->execute([ ":id" => $id ])) {
      return false;
    }

    return "uploads/admin_images/default_dp.jpg";
  }

  public function updateDPById($id, $dp) {
    $path = Utilities::getPath() . "/public/uploads/admin_images";
    $allowed_type = ["jpg", "jpeg", "png", "webp", "avif"];

    $img_ext = explode(".", $dp["name"]);
    $img_ext = end($img_ext);
    $img_ext = strtolower($img_ext);
    if(!in_array($img_ext, $allowed_type)) {
      Utilities::showAlertAndExit("JPEG, PNG, WEBP, and AVIF Only.", "/admin/profile", 400);
      exit();
    }

    if($dp["error"] != 0) {
      Utilities::showAlertAndExit("An error occured on uploading your image.", "/admin/profile", 500);
    }

    if($dp["size"] > 5000000) {
      Utilities::showAlertAndExit("Do not upload image bigger than 5mb.", "/admin/profile", 400);
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

    $stmt = $this->db->prepare("UPDATE `admin` SET `default_image` = 1, `date_updated` = NOW() WHERE `id` = :id");
    if(!$stmt->execute([ ":id" => $id ])) {
      return false;
    }

    return "uploads/admin_images/dp.{$img_ext}";
  }

  public function updateDateUpdatedById($id) {
    $stmt = $this->db->prepare("UPDATE `admin` SET `date_updated` = NOW() WHERE `id` = :id");
    return $stmt->execute([ ":id" => $id ]);
  }
}