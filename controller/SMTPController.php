<?php

namespace App\Controller;

use App\Model\SMTPUser;
use App\Core\Utilities;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class SMTPController {
  public static function sendEmail($receiver_email, $receiver_name, $subject, $message) {
    $smtp_user = new SMTPUser();
    $smtp_user_info = $smtp_user->getUserById(1);
    if($smtp_user_info === false) {
      Utilities::showAlertAndExit("SMTP User not found in our records.", "/", 404);
    }

    $php_mailer = new PHPMailer(true);
    try {
      // Server Settings.
      $php_mailer->isSMTP();
      $php_mailer->SMTPDebug = 0;
      $php_mailer->SMTPAuth = true;
      $php_mailer->SMTPSecure = "tls";
      $php_mailer->Host = "smtp.gmail.com";
      $php_mailer->Port = 587;
      $php_mailer->Username = $smtp_user_info["email"];
      $php_mailer->Password = $smtp_user_info["password"];

      // Sender Settings.
      $php_mailer->setFrom($smtp_user_info["email"], $smtp_user_info["name"]);

      // Receiver Settings.
      $php_mailer->addAddress($receiver_email, $receiver_name);

      // Message Settings.
      $php_mailer->Subject = $subject;
      $php_mailer->msgHTML($message);

      // Send Email.
      $php_mailer->send();
    } catch (Exception $phpmailer_err) {
      Utilities::showAlertAndExit($phpmailer_err->getMessage(), "/", 500);
      // return $phpmailer_err->getMessage();
    }
  }

  public static function updateSMTP() {
    if(!isset($_POST["update_smtp"])) {
      header("Location: /admin/manage_smtp");
      exit();
    }

    Utilities::validateCSRFToken($_POST["csrf_token"]);

    $name = trim($_POST["name"]);
    Authentication::checkInputEmpty($name, "name", "/admin/manage_smtp");
    $email = trim($_POST["email"]);
    Authentication::checkInputEmpty($email, "email", "/admin/manage_smtp");
    $password = trim($_POST["password"]);
    Authentication::checkInputEmpty($password, "password", "/admin/manage_smtp");

    $_SESSION["verify_user"]["smtp"] = true;
    $_SESSION["verify_user"]["password"] = $password;
    Authentication::generateOTP($email, $name);
  }

  public static function testingSMTP() {
    if(!isset($_POST["testing_smtp"])) {
      header("Location: /admin/manage_smtp");
      exit();
    }

    Utilities::validateCSRFToken($_POST["csrf_token"]);

    self::sendEmail($_POST["email"], "Admin", "SMTP Test", "Hello <b>Admin</b>! This is a testing message!");

    Utilities::showAlertAndExit("Testing Message Sent.", "/admin/manage_smtp", 201);
  }
}