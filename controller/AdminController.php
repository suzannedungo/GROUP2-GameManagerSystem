<?php

namespace App\Controller;

use App\Model\Admin;
use App\Core\Utilities;
use App\Model\SMTPUser;
use App\Model\User;

class AdminController {
  public static function updateProfile() {
    if(!isset($_POST["update_profile"]) && $_POST["_method"] == "PUT") {
      header("Location: /admin/profile");
      exit();
    }

    // Check csrf mamaya.
    Utilities::validateCSRFToken($_POST["csrf_token"]);

    $name = false;
    if(isset($_POST["name"])) {
      $name = trim($_POST["name"]);
      Authentication::checkInputEmpty($name, "name", "/admin/profile");
    }

    $email = false;
    if(isset($_POST["email"])) {
      $email = trim($_POST["email"]);
      Authentication::checkInputEmpty($email, "email", "/admin/profile");

      $user = new User();
      $smtp = new SMTPUser();
      $user_email = $user->getEmailByEmail($email);
      $smtp_email = $smtp->getEmailByEmail($email);
      if(
        $user_email != 0 || $user_email != false ||
        $smtp_email != 0 || $smtp_email != false
      ) {
        Utilities::showAlertAndExit("Email is already in the database.", "/admin/profile", 400);
      }

      if($_SESSION["signed_in_acc"]["email"] == $email) {
        Utilities::showAlertAndExit("Email is the same to your current email.", "/admin/profile", 400);
      }
    }

    $dp = false;
    if(isset($_FILES["dp"])) {
      if($_FILES["dp"]["name"] == "" && $_FILES["dp"]["error"] == 4) {
        Utilities::showAlertAndExit("There is an error to the image.", "/admin/profile", 403);
      }
      $dp = $_FILES["dp"];
    }

    $id = $_POST["id"];
    $admin = new Admin();

    if($name !== false) {
      if(!$admin->updateNameById($id, $name)) {
        Utilities::showAlertAndExit("An error occured on updating name.", "/admin/profile", 500);
      }

      $_SESSION["signed_in_acc"]["name"] = $name;
    }
    
    if($email !== false) {
      $_SESSION["verify_user"]["id"] = $_SESSION["signed_in_acc"]["id"];
      $_SESSION["verify_user"]["type"] = "admin";
      Authentication::generateOTP($email, $_SESSION["signed_in_acc"]["name"]);
    }

    if($dp !== false) {
      $_SESSION["signed_in_acc"]["profile_image"] = $admin->updateDPById($_SESSION["signed_in_acc"]["id"], $dp);
    }

    Utilities::showAlertAndExit("Update profile successfully!", "/admin/profile", 201);
  }

  public static function resetDP() {
    $admin = new Admin();

    if(($admin->getAdminByEmail($_SESSION["signed_in_acc"]["email"]))["default_image"] == 0) {
      Utilities::showAlertAndExit("Your image is already default.", "/admin/profile", 400);
    }

    $dp = $admin->resetDPById($_SESSION["signed_in_acc"]["id"]);
    if($dp === false) {
      Utilities::showAlertAndExit("An error occured on resetting your image.", "/admin/profile", 500);
    }

    $_SESSION["signed_in_acc"]["profile_image"] = $dp;
    Utilities::showAlertAndExit("Reset Profile Image Successfully!", "/admin/profile", 201);
  }

  public static function changePassword() {
    if(!isset($_POST["change_pass"])) {
      header("Location: /admin/profile");
      exit();
    }


    $password = trim($_POST["password"]);
    if($password == "" || $password == null || $password == false) {
      Utilities::showAlertAndExit("Do not leave password blank. Try again.", "/admin/profile", 400);
    }

    if(strlen($password) < 8) {
      Utilities::showAlertAndExit("Password must be at least 8 characters. Try again.", "/admin/profile", 400);
    }

    $password = Authentication::hashPass($password);

    $admin = new Admin();
    if(!($admin->updatePasswordById($_SESSION["signed_in_acc"]["id"], $password))) {
      Utilities::showAlertAndExit("An error occured on updating your passwod.", "/admin/profile", 500);
    }

    session_unset();
    session_destroy();

    Utilities::showAlertAndExit("Reset Password Successfully. Login your account with your new password.", "/", 201);
  }
}