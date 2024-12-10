<?php

require_once __DIR__ . "/../../config/Database.php";

class User {
  private $db;

  public function __construct() {
    $this->db = Database::connect();
  }

  public function getName($email) {
    $stmt = $this->db->prepare("SELECT name FROM account WHERE email = :email");
    $stmt->execute([":email" => $email]);

    if($stmt->rowCount() > 0) {
      echo ($stmt->fetch(PDO::FETCH_ASSOC))["name"];
    } else {
      // echo "An error occured upon fetching name.";
      echo false;
    }
  }

  public function getUsername($email) {
    $stmt = $this->db->prepare("SELECT username FROM account WHERE email = :email");
    $stmt->execute([":email" => $email]);

    if($stmt->rowCount() > 0) {
      echo ($stmt->fetch(PDO::FETCH_ASSOC))["username"];
    } else {
      // echo "An error occured upon fetching username.";
      echo false;
    }
  }

  public function getProfileImage($email) {
    $stmt = $this->db->prepare("SELECT username, profile_image FROM account WHERE email = :email");
    $stmt->execute([":email" => $email]);

    if($stmt->rowCount() > 0) {
      echo ($stmt->fetch(PDO::FETCH_ASSOC))["username"] . "/" . ($stmt->fetch(PDO::FETCH_ASSOC))["profile_image"];
    } else {
      echo false;
    }
  }

  public function updateProfileImage($email, $image) {
    if($image === null || $image === "") {
      echo "Do not leave input file blank.";
      exit();
    }

    $stmt = $this->db->prepare("SELECT username FROM account WHERE email = :email");
    $stmt->execute([":email" => $email]);
    $username = ($stmt->fetch(PDO::FETCH_ASSOC))["username"];

    $acc_dir = "../../src/uploads/users_images/" . $username;
    if(!is_dir($acc_dir)) {
      mkdir($acc_dir, 0777, true);
    }

    $allowed_type = ["jpg", "jpeg", "png"];

    $file_ext = explode(".", $image["name"]);
    $file_ext = end($file_ext);
    $file_ext = strtolower($file_ext);

    $file_tmp_name = $image["tmp_name"];

    $file_error = $image["error"];

    $file_size = $image["size"];

    if(!in_array($file_ext, $allowed_type)) {
      echo "JPEG and PNG Only.";
      exit();
    }

    if($file_error != 0) {
      echo "An error occured upon uploading your profile image.";
      exit();
    }

    if($file_size > 5000000) {
      echo "Do not upload bigger than 5mb.";
      exit();
    }

    // Cleaning saved profile picture/s.
    $image = "{$acc_dir}/dp.jpeg";
    if(file_exists($image)) {
      unlink($image);
    }

    $image = "{$acc_dir}/dp.jpg";
    if(file_exists($image)) {
      unlink($image);
    }

    $image = "{$acc_dir}/dp.png";
    if(file_exists($image)) {
      unlink($image);
    }

    move_uploaded_file($file_tmp_name, $acc_dir . "/dp." . $file_ext);

    $stmt = $this->db->prepare("UPDATE account SET profile_image = :image WHERE email = :email");
    return $stmt->execute([
      ":image" => "dp.{$file_ext}",
      ":email" => $email
    ]);
  }

  public function updateName($email, $name) {
    if($name === null || $name === "") {
      echo "Do not leave input name blank.";
      exit();
    }

    $stmt = $this->db->prepare("UPDATE account SET name = :name WHERE email = :email");
    return $stmt->execute([
      ":name" => $name,
      ":email" => $email
    ]);
  }

  public function updateUsername($email, $new_username) {
    if($new_username === null || $new_username === "") {
      echo "Do not leave input username blank.";
      exit();
    }

    $stmt = $this->db->prepare("SELECT username FROM account WHERE username = :username");
    $stmt->execute([":username" => $new_username]);
    if($stmt->rowCount() > 0) {
      echo "Username is already existing.";
      exit();
    }

    $stmt = $this->db->prepare("SELECT username FROM account WHERE email = :email");
    $stmt->execute([":email" => $email]);
    $old_username = ($stmt->fetch(PDO::FETCH_ASSOC))["username"];

    // Update user folder name.
    $old_acc_dir = "../../src/uploads/users_images/{$old_username}";
    $new_acc_dir = "../../src/uploads/users_images/{$new_username}";
    rename($old_acc_dir, $new_acc_dir);

    $stmt = $this->db->prepare("UPDATE account SET username = :username WHERE email = :email");
    return $stmt->execute([
      ":username" => $new_username,
      ":email" => $email
    ]);
  }

  public function updateCredential($email, $input_name, $input_value) {
    $res = false;
    switch($input_name) {
      case "profile_image":
        $res = $this->updateProfileImage($email, $input_value);
        break;
      
      case "name":
        $res = $this->updateName($email, $input_value);
        break;
      
      case "username":
        $res = $this->updateUsername($email, $input_value);
        break;
      
    }

    if($res) {
      echo "User {$input_name} updated successfully.";
    } else {
      echo "An error occured upon updating {$input_name}.";
    }
  }
}

if(isset($_POST["edit_profile"])) {
  $input_name = trim($_POST["input_name"]);
  $value = $input_name === "profile_image" ? $_FILES["input_value"] : trim($_POST["input_value"]);

  (new User())->updateCredential(
    trim($_POST["email"]),
    $input_name,
    $value
  );
}

if(isset($_GET["get_name"])) {
  (new User())->getName(trim($_GET["email"]));
}

if(isset($_GET["get_username"])) {
  (new User())->getUsername(trim($_GET["email"]));
}

if(isset($_GET["get_profile_image"])) {
  (new User())->getProfileImage(trim($_GET["email"]));
}