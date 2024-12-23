<?php

namespace App\Controller;

use App\Core\Utilities;
use App\Model\Admin;
use App\Model\FavoriteGame;
use App\Model\SMTPUser;
use App\Model\User;

class UserController {
  public static function updateProfile() {
    if(!isset($_POST["update_profile"]) && $_POST["_method"] == "PUT") {
      header("Location: /admin/profile");
      exit();
    }

    // Check CSRF Token bug.
    Utilities::validateCSRFToken($_POST["csrf_token"]);

    $name = false;
    if(isset($_POST["name"])) {
      $name = trim($_POST["name"]);
      Authentication::checkInputEmpty($name, "name", "/user/profile");
    }

    $username = false;
    if(isset($_POST["username"])) {
      $username = trim($_POST["username"]);
      Authentication::checkInputEmpty($username, "username", "/user/profile");

      $user = new User();
      if($user->getUsernameByUsername($username)) {
        Utilities::showAlertAndExit("Username is already in the database.", "/user/profile", 400);
      }

      if($_SESSION["signed_in_acc"]["username"] == $username) {
        Utilities::showAlertAndExit("Username is the same to your current username.", "/user/profile", 400);
      }
    }

    $email = false;
    if(isset($_POST["email"])) {
      $email = trim($_POST["email"]);
      Authentication::checkInputEmpty($email, "email", "/user/profile");

      $admin = new Admin();
      $smtp = new SMTPUser();
      $admin_email = $admin->getEmailByEmail($email);
      $smtp_email = $smtp->getEmailByEmail($email);
      if(
        $admin_email != 0 || $admin_email != false ||
        $smtp_email != 0 || $smtp_email != false
      ) {
        Utilities::showAlertAndExit("Email is already in the database.", "/user/profile", 400);
      }

      if($_SESSION["signed_in_acc"]["email"] == $email) {
        Utilities::showAlertAndExit("Email is the same to your current email.", "/user/profile", 400);
      }
    }

    $dp = false;
    if(isset($_FILES["dp"])) {
      if($_FILES["dp"]["name"] == "" && $_FILES["dp"]["error"] == 4) {
        Utilities::showAlertAndExit("There is an error to the image.", "/user/profile", 403);
      }
      $dp = $_FILES["dp"];
    }

    $id = $_SESSION["signed_in_acc"]["id"];
    $user = new User();

    if($name !== false) {
      if(!$user->updateNameById($id, $name)) {
        Utilities::showAlertAndExit("An error occured on updating name.", "/user/profile", 500);
      }

      $_SESSION["signed_in_acc"]["name"] = $name;
    }
    
    if($username != false) {
      $username = strtolower($username);
      if(!$user->updateUsernameById($id, $username)) {
        Utilities::showAlertAndExit("An error occured on updating username.", "/user/profile", 500);
      }

      $path = Utilities::getPath() . "/public/uploads/users_images/";
      if(!rename($path . $_SESSION["signed_in_acc"]["username"], $path . $username)) {
        if(!$user->updateUsernameById($id, $_SESSION["signed_in_acc"]["username"])) {
          Utilities::showAlertAndExit("An error occured on updating username.", "/user/profile", 500);
        }

        Utilities::showAlertAndExit("An error occured on updating username.", "/user/profile", 500);
      }

      $_SESSION["signed_in_acc"]["username"] = $username;
      $_SESSION["signed_in_acc"]["profile_image"] = $user->getProfileImageByEmail($_SESSION["signed_in_acc"]["email"]);
    }
    
    if($email !== false) {
      $_SESSION["verify_user"]["id"] = $_SESSION["signed_in_acc"]["id"];
      $_SESSION["verify_user"]["type"] = "user";
      Authentication::generateOTP($email, $_SESSION["signed_in_acc"]["name"]);
    }

    if($dp !== false) {
      $_SESSION["signed_in_acc"]["profile_image"] = $user->updateDPById($_SESSION["signed_in_acc"]["id"], $dp);
    }

    Utilities::showAlertAndExit("Update profile successfully!", "/user/profile", 201);
  }

  public static function resetDP() {
    $user = new User();

    if(($user->getUserByEmail($_SESSION["signed_in_acc"]["email"]))["default_image"] == 0) {
      Utilities::showAlertAndExit("Your image is already default.", "/user/profile", 400);
    }

    $dp = $user->resetDPById($_SESSION["signed_in_acc"]["id"]);
    if($dp === false) {
      Utilities::showAlertAndExit("An error occured on resetting your image.", "/user/profile", 500);
    }

    $_SESSION["signed_in_acc"]["profile_image"] = $dp;
    Utilities::showAlertAndExit("Reset Profile Image Successfully!", "/user/profile", 201);
  }

  public static function delAccount() {
    if(!isset($_POST["del_acc"]) && $_POST["_method"] == "DEL") {
      echo false;
      // return;
      // header("Location: /user/profile");
      // exit();
    }

    Utilities::validateCSRFToken($_POST["csrf_token"]);

    $user = new User();
    $user->deleteByEmail($_POST["email"]);

    session_unset();
    session_destroy();
  }

  public static function banUser() {
    if(!isset($_POST["ban"])) {
      header("Location: /admin/manage_users");
      exit();
    }

    // Check CSRF Token bug.
    Utilities::validateCSRFToken($_POST["csrf_token"]);

    $user = new User();
    if(
      !$user->updateStatusByEmail($_POST["email"], "ban") ||
      !$user->updateDateUpdatedByEmail($_POST["email"])
    ) {
      Utilities::showAlertAndExit("An error occured on updating user.", "/admin/manage_users", 500);
    }

    $subject = "BAN USER";
    $message = file_get_contents(Utilities::getPath() . "/view/email/ban-user-email.html");
    SMTPController::sendEmail($_POST["email"], "", $subject, $message);

    header("Location: /admin/manage_users");
    exit();
  }

  public static function unbanUser() {
    if(!isset($_POST["unban"])) {
      header("Location: /admin/manage_users");
      exit();
    }

    Utilities::validateCSRFToken($_POST["csrf_token"]);

    $user = new User();
    if(
      !$user->updateStatusByEmail($_POST["email"], "verified") ||
      !$user->updateDateUpdatedByEmail($_POST["email"])
    ) {
      Utilities::showAlertAndExit("An error occured on updating user.", "/admin/manage_users", 500);
    }

    $subject = "UNBAN USER";
    $message = file_get_contents(Utilities::getPath() . "/view/email/unban-user-email.html");
    SMTPController::sendEmail($_POST["email"], "", $subject, $message);

    header("Location: /admin/manage_users");
    exit();
  }
}