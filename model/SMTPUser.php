<?php

namespace App\Model;

use PDO;
use App\Core\Database;

class SMTPUser {
  private $db;

  public function __construct() {
    $this->db = Database::connect();
  }

  /* === RETRIEVE FUNCTIONS === */
  // Get By Id.
  public function getUserById($id) {
    $stmt = $this->db->prepare("SELECT * FROM `smtp_user` WHERE id = :id");
    if(!($stmt->execute([ ":id" => $id ]))) {
      return false;
    }

    if($stmt->rowCount() <= 0) {
      return 0;
    }

    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  // Get By Email.
  public function getEmailByEmail($email) {
    $stmt = $this->db->prepare("SELECT `smtp_user`.`email` FROM `smtp_user` WHERE email = :email");
    if(!($stmt->execute([ ":email" => $email ]))) {
      return false;
    }

    if($stmt->rowCount() <= 0) {
      return 0;
    }

    return ($stmt->fetch(PDO::FETCH_ASSOC))["email"];
  }

  
  /* === UPDATE FUNCTIONS === */
  public function update($name, $email, $password) {
    $stmt = $this->db->prepare("UPDATE `smtp_user` SET `name` = :name, `email` = :email, `password` = :password, `date_updated` = NOW() WHERE id = 1");
    return $stmt->execute([
      ":name" => $name,
      ":email" => $email,
      ":password" => $password
    ]);
  }
}