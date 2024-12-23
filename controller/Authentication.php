<?php

namespace App\Controller;

use App\Model\Admin;
use App\Model\User;
use App\Core\Utilities;
use App\Model\SMTPUser;

class Authentication {
  public static function signUp() {
    if(!isset($_POST["signup"])) {
      header("Location: /");
      exit();
    }

    // Check CSRF Token Bug mamaya.
    Utilities::validateCSRFToken($_POST["csrf_token"]);

    $name = trim($_POST["name"]);
    self::checkInputEmpty($name, "name", "/");

    $username = trim($_POST["username"]);
    $username = strtolower($username);
    self::checkInputEmpty($username, "username", "/");

    $email = trim($_POST["email"]);
    self::checkInputEmpty($email, "email", "/");
    
    $admin = new Admin();
    $smtp_user = new SMTPUser();
    $user = new User();

    $admin_info = $admin->getEmailByEmail($email);
    $smtp_user_info = $smtp_user->getEmailByEmail($email);
    $user_info = $user->getEmailByEmail($email);

    if(
      $admin_info  != false || $admin_info != 0 ||
      $smtp_user_info != false || $smtp_user_info != 0 ||
      $user_info != false || $user_info != 0
    ) {
      Utilities::showAlertAndExit("Email  is already in our records.", "/", 400);
    }

    $password = trim($_POST["password"]);
    self::checkInputEmpty($password, "password", "/");
    self::checkPasswordLength($password, "/");
    $password = self::hashPass($password);

    $user_info = $user->getUsernameByUsername($username);
    if($user_info != false || $user_info != 0) {
      Utilities::showAlertAndExit("Username is already in our records.", "/", 400);
    }

    if(!($user->add($name, $username, $email, $password, self::generateTC()))) {
      Utilities::showAlertAndExit("An error occured on adding user.", "/", 500);
    }

    self::generateOTP($email, $username);
  }

  public static function signIn() {
    if(!isset($_POST["signin"])) {
      header("Location: /");
      exit();
    }

    // Check mamaya CSRF Token bug.
    Utilities::validateCSRFToken($_POST["csrf_token"]);
    
    $email = trim($_POST["email"]);
    self::checkInputEmpty($email, "email", "/");
    
    $password = trim($_POST["password"]);
    self::checkInputEmpty($password, "password", "/");
    self::checkPasswordLength($password, "/");
    $password = self::hashPass($password);

    $admin = new Admin();
    $admin_info = $admin->getAdminByEmail($email);
    if($admin_info  != false || $admin_info != 0) {
      if($password != $admin_info["password"]) {
        Utilities::showAlertAndExit("Password did not match.", "/", 400);
      }

      $_SESSION["signed_in_acc"] = $admin_info;
      $_SESSION["signed_in_acc"]["profile_image"] = $admin->getProfileImageByEmail($email);

      header("Location: /admin/");
      exit();
    }

    $user = new User();
    $user_info = $user->getUserByEmail($email);
    if($user_info == false || $user_info == 0) {
      Utilities::showAlertAndExit("User is not yet in our records.", "/", 400);
    }

    if($password != $user_info["password"]) {
      Utilities::showAlertAndExit("Password did not match.", "/", 400);
    }

    if($user_info["status"] == "not_verified") {
      self::generateOTP($user_info["email"], $user_info["username"]);
      exit();
    }

    if($user_info["status"] == "ban") {
      Utilities::showAlert("Unfortunately, your account is still banned.");
      Utilities::showAlertAndExit("You still cannot sign in your account.", "/", 403);
    }

    $_SESSION["signed_in_acc"] = $user_info;
    $_SESSION["signed_in_acc"]["profile_image"] = $user->getProfileImageByEmail($email);

    header("Location: /user/");
    exit();
  }

  public static function signOut() {
    if(isset($_SESSION["signed_in_acc"]) && !empty($_SESSION["signed_in_acc"])) {
      session_unset();
      session_destroy();
    }

    header("Location: /");
    exit();
  }

  public static function verifyOTP() {
    if(!isset($_POST["otp_sent"])) {
      header("Location: /");
      exit();
    }

    // Check mamaya CSRF Token bug.
    Utilities::validateCSRFToken($_POST["csrf_token"]);

    if($_SESSION["otp"] != $_POST["otp"]) {
      Utilities::showAlertAndExit("{$_POST['otp']} is not the OTP.", "/verifyotp", 403);
    }

    $receiver_email = $_SESSION["verify_user"]["email"];
    $receiver_name = $_SESSION["verify_user"]["username"];

    if(isset($_SESSION["verify_user"]["smtp"])) {
      $smtp_user = new SMTPUser();
      if(!($smtp_user->update($receiver_name, $receiver_email, $_SESSION["verify_user"]["password"]))) {
        Utilities::showAlertAndExit("An error occured on updating smtp user.", "/admin/manage_smtp", 500);
      }

      unset($_SESSION["otp"]);
      unset($_SESSION["verify_user"]);

      Utilities::showAlert("Updating SMTP done!");
      SMTPController::sendEmail($_SESSION["signed_in_acc"]["email"], "Admin", "SMTP Test", "Hello <b>Admin</b>! This is a testing message!");
      Utilities::showAlertAndExit("To test, system will send a testing message.", "/admin/manage_smtp", 201);
    } 

    if(isset($_SESSION["verify_user"]["type"])) {
      if($_SESSION["verify_user"]["type"] == "admin") {
        $admin = new Admin();
        if(!($admin->updateEmailById($_SESSION["verify_user"]["id"], $receiver_email))) {
          Utilities::showAlertAndExit("An error occured on updating your email.", "/admin/profile", 500);
        }
      }

      if($_SESSION["verify_user"]["type"] == "user") {
        $user = new User();
        if(!($user->updateEmailById($_SESSION["verify_user"]["id"], $receiver_email))) {
          Utilities::showAlertAndExit("An error occured on updating your email.", "/user/profile", 500);
        }
      }

      session_unset();
      session_destroy();

      Utilities::showAlertAndExit("Email updated successfully. Login your account with you new email.", "/", 201);
    }

    $user = new User();
    if(
      !($user->updateStatusByEmail($receiver_email, "verified")) ||
      !$user->updateDateUpdatedByEmail($receiver_email)
    ) {
      Utilities::showAlertAndExit("An error occured on updating status of your user.", "/", 500);
    }

    $user->updateDateUpdatedByEmail($receiver_email);

    session_unset();
    session_destroy();

    $subject = "VERIFICATION SUCCESS";

    $message = file_get_contents(Utilities::getPath() . "/view/email/verification-success-email.html");
    $message = str_replace("{receiver_name}", $receiver_name, $message);

    SMTPController::sendEmail($receiver_email, $receiver_name, $subject, $message);
    
    $user_dir = Utilities::getPath() . "/public/uploads/users_images/" . $receiver_name;
    if(!is_dir($user_dir)) {
      mkdir($user_dir, 0777, true);
    }

    Utilities::showAlertAndExit("User {$receiver_email} Verified Successfully.", "/", 201);
    exit();
  }

  public static function forgotPass() {
    if(!isset($_POST["forgotpass"])) {
      header("Location: /");
      exit();
    }

    // Check CSRF Tken mamaya.
    Utilities::validateCSRFToken($_POST["csrf_token"]);

    $email = trim($_POST["email"]);
    self::checkInputEmpty($email, "email", "/forgotpass");

    $user = new User();
    $user_info = $user->getUserByEmail($email);
    if($user_info == false || $user_info == 0) {
      Utilities::showAlertAndExit("Account is not yet in our records.", "/forgotpass", 404);
    }

    $subject = "RESET PASSWORD";

    // $link = "https://thevoid.website/resetpass?id={$user_info['id']}&tokencode={$user_info['tokencode']}";
    $link = "http://localhost:8080/resetpass?id={$user_info['id']}&tokencode={$user_info['tokencode']}";
    $message = file_get_contents(Utilities::getPath() . "/view/email/rp-email.html");
    $message = str_replace("{link}", $link, $message);

    SMTPController::sendEmail($user_info["email"], $user_info["username"], $subject, $message);

    $uri = isset($_SESSION["signed_in_acc"]) ? "/user/profile" : "/forgotpass";

    Utilities::showAlertAndExit("We Have Sent You A Reset Password Link.", $uri, 201);
    exit();
  }

  public static function resetPass() {
    if(!isset($_POST["resetpass"])) {
      header("Location: /");
      exit();
    }

    // Check CSRF Token bug.

    $password = trim($_POST["password"]);
    self::checkInputEmpty($password, "password", "/resetpass?id={$_POST['id']}&tokencode={$_POST['tokencode']}");
    self::checkPasswordLength($password, "/resetpass?id={$_POST['id']}&tokencode={$_POST['tokencode']}");
    $password = self::hashPass($password);

    $user = new User();
    if(
      !($user->updatePasswordById($_POST["id"], $password)) ||
      !($user->updateTCById($_POST["id"], self::generateTC())) ||
      !($user->updateDateUpdatedById($_POST["id"]))
    ) {
      Utilities::showAlertAndExit("An error occured on updating your password.", "/resetpass?id={$_POST['id']}&tokencode={$_POST['tokencode']}", 500);
    }

    Utilities::showAlertAndCloseTab("Reset Password Successfully.", 201);
  }

  public static function checkAccountSignedIn() {
    if(isset($_SESSION["signed_in_acc"]) && !empty($_SESSION["signed_in_acc"])) {
      header("Location: /user/");
      exit();
    }
  }

  public static function checkAccountNotSignedIn() {
    if(!isset($_SESSION["signed_in_acc"])) {
      header("Location: /");
      exit();
    }
  }

  public static function checkVerifyOTPOnGoing() {
    if(isset($_SESSION["otp"]) && isset($_SESSION["verify_user"])) {
      unset($_SESSION["otp"]);
      unset($_SESSION["verify_user"]);
    }
  }

  public static function checkVerifyOTPNotOnGoing() {
    if(!isset($_SESSION["otp"]) && !isset($_SESSION["verify_user"])) {
      header("Location: /");
      exit();
    }
  }

  public static function redirectToUser() {
    $admin = new Admin();
    $email = $admin->getEmailByEmail($_SESSION["signed_in_acc"]["email"]);
    if($email == false || $email == 0) {
      header("Location: /user/");
      exit();
    }
  }

  public static function redirectToAdmin() {
    $admin = new Admin();
    $email = $admin->getEmailByEmail($_SESSION["signed_in_acc"]["email"]);
    if($email != false || $email != 0) {
      header("Location: /admin/");
      exit();
    }
  }

  public static function checkInputEmpty($input, $input_name, $redirect) {
    if($input === null || !isset($input) || $input === "") {
      Utilities::showAlertAndExit("Do not leave {$input_name} blank.", $redirect, 400);
    }
  }

  public static function generateOTP($email, $username) {
    $_SESSION["verify_user"]["email"] = $email;
    $_SESSION["verify_user"]["username"] = $username;
    $_SESSION["otp"] = rand(100000, 999999);
    self::sendOTP();
    Utilities::showAlertAndExit("We have sent an OTP to {$email}.", "/verifyotp", 201);
  }

  private static function sendOTP() {
    $receiver_email = $_SESSION["verify_user"]["email"];
    $receiver_name = $_SESSION["verify_user"]["username"];

    $subject = "OTP VERIFICATION";

    $message = file_get_contents(Utilities::getPath() . "/view/email/otp-verification-email.html");
    $message = str_replace("{receiver_name}", $receiver_name, $message);
    $message = str_replace("{otp}", $_SESSION["otp"], $message);
    SMTPController::sendEmail($receiver_email, $receiver_name, $subject, $message);
  }

  private static function generateTC() { return md5(uniqid(rand())); }

  public static function hashPass($password) { return md5($password); }

  private static function checkPasswordLength($password, $redirect) {
    if(strlen($password) < 8) {
      Utilities::showAlertAndExit("Password must be at least 8 characters.", $redirect, 400);
    }
  }
}