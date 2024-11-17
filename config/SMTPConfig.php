<?php
require_once __DIR__ . "/Database.php";
require_once __DIR__ . "/../vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class SMTPConfig {
  private $email;
  private $name;
  private $password;
  private $pdo;

  public function __construct() {
    $this->pdo = (new Database())->connect();

    $stmt = $this->pdo->prepare("SELECT * FROM smtp_user");
    $stmt->execute();
    $smtp_user = $stmt->fetch(PDO::FETCH_ASSOC);

    $this->email = $smtp_user['email'];
    $this->name = $smtp_user['name'];
    $this->password = $smtp_user['password'];
  }

  public function getEmail() { return $this->email; }

  public function getName() { return $this->name; }

  public function getPassword() { return $this->password; }

  public function sendEmail($receiver_email, $receiver_name, $subject, $message) {
    $php_mailer = new PHPMailer(true);
    try {
      // Server Settings.
      $php_mailer->isSMTP();
      $php_mailer->SMTPDebug = 0;
      $php_mailer->SMTPAuth = true;
      $php_mailer->SMTPSecure = "tls";
      $php_mailer->Host = "smtp.gmail.com";
      $php_mailer->Port = 587;
      $php_mailer->Username = $this->email;
      $php_mailer->Password = $this->password;

      // Sender Settings.
      $php_mailer->setFrom($this->email, $this->name);

      // Receiver Settings.
      $php_mailer->addAddress($receiver_email, $receiver_name);

      // Message Settings.
      $php_mailer->Subject = $subject;
      $php_mailer->msgHTML($message);

      // Send Email.
      $php_mailer->send();
    } catch(Exception $php_mailer_err) {
      echo "
        <script>
          alert(\"Send Email Failed.\");
          alert(\"{$php_mailer_err->getMessage()}\");
          window.location.href = \"../page/index.php\";
        </script>
      ";
      exit();
    }
  }
}