<?php

require_once __DIR__ . "/../config/Database.php";
require_once __DIR__ . "/../config/SMTPConfig.php";
include_once __DIR__ . "/../config/settings.php";

class Authentication {
  private $database;
  private $smtp;

  public function __construct() {
    $this->database = Database::connect();
    $this->smtp = new SMTPConfig();
  }

  public function signup($name, $username, $email, $password) {
    self::checkInputEmpty($name, "name", "../page/index.php");
    self::checkInputEmpty($username, "username", "../page/index.php");
    self::checkInputEmpty($email, "email", "../page/index.php");
    self::checkInputEmpty($password, "password", "../page/index.php");

    $this->checkPwdLen($password);

    $smtp_user = new SMTPConfig();
    if($email === $smtp_user->getEmail()) {
      echo
      "<script>
        alert(\"User is already in our records.\");
        window.location.href = \"../page/index.php\";
      </script>";
      exit();
    }

    $username = strtolower($username);

    $stmt = $this->database->prepare("SELECT email FROM user WHERE email = :email");
    $stmt->execute([
      ":email" => $email
    ]);

    // Update this with pop up window instead of alert.
    if($stmt->rowCount() > 0) {
      echo
      "<script>
        alert(\"User is already in our records.\");
        window.location.href = \"../page/index.php\";
      </script>";
      exit();
    }

    $stmt = $this->database->prepare("SELECT username FROM user WHERE username = :username");
    $stmt->execute([
      ":username" => $username
    ]);

    // Update this with pop up window instead of alert.
    if($stmt->rowCount() > 0) {
      echo
      "<script>
        alert(\"Username is already used.\");
        window.location.href = \"../page/index.php\";
      </script>";
      exit();
    }

    $stmt = $this->database->prepare("INSERT INTO user(name, username, email, password, tokencode) VALUES(:name, :username, :email, :password, :tokencode)");
    $stmt->execute([
      ":name" => $name,
      ":username" => $username,
      ":email" => $email,
      ":password" => $this->hashPwd($password),
      ":tokencode" => $this->generateTC()
    ]);

    // Update this with pop up window instead of alert.
    echo
    "<script>
      alert(\"User Added Successfully!\");
    </script>";

    $_SESSION['verifying_user']['email'] = $email;
    $_SESSION['verifying_user']['username'] = $username;
    $_SESSION['otp'] = rand(100000, 999999);
    
    // Sending OTP.
    $this->sendOTP();

    // Update this with pop up window instead of alert.
    echo "
      <script>
        alert(\"We have sent you an OTP to {$_SESSION['verifying_user']['email']}.\"); 
        window.location.href = \"../page/otpverification.php\";
      </script>
    ";
  }

  public function signin($email, $password) {
    self::checkInputEmpty($email, "email", "../page/index.php");
    self::checkInputEmpty($password, "password", "../page/index.php");

    $this->checkPwdLen($password);

    $smtp_user = new SMTPConfig();
    if($email === $smtp_user->getEmail() && $password === $smtp_user->getPassword()) {
      $admin["email"] = $smtp_user->getEmail();
      $admin["name"] = $smtp_user->getName();

      $_SESSION["signed_in_user"] = $admin;

      header("Location: ./admin/dashboard.php");
      exit();
    }

    $stmt = $this->database->prepare("SELECT * FROM user WHERE email = :email");
    $stmt->execute([
      ":email" => $email
    ]);

    // Update this with pop up window instead of alert.
    if($stmt->rowCount() <= 0) {
      echo
      "<script>
        alert(\"User is not in our records.\");
        window.location.href = \"../page/index.php\";
      </script>";
      exit();
    }

    $user_info = $stmt->fetch(PDO::FETCH_ASSOC);

    // Update this with pop up window instead of alert.
    $password = $this->hashPwd($password);
    if($password !== $user_info["password"]) {
      echo
      "<script>
        alert(\"Password Incorrect.\");
        window.location.href = \"../page/index.php\";
      </script>";
      exit();
    }

    // Update this with pop up window instead of alert.
    if($user_info["status"] === "not_verified") {
      echo "
        <script>
          alert(\"User Is Not Yet Verified.\"); 
        </script>
      ";

      $_SESSION['verifying_user']['email'] = $email;
      $_SESSION['verifying_user']['username'] = $user_info["username"];
      $_SESSION['otp'] = rand(100000, 999999);
      $this->sendOTP();

      echo "
        <script>
          alert(\"We have sent you an OTP to {$user_info['email']}.\"); 
          window.location.href = \"../page/otpverification.php\";
        </script>
      ";

      exit();
    }

    $_SESSION["signed_in_user"] = $user_info;

    $_SESSION["signed_in_user"]["profile_image"] = "src/uploads/users_images/default_dp.jpg";
    if($user_info["default_image"]) {
      $img_path = "uploads/users_images/{$user_info['username']}/dp.jpg";
      if(!is_file($img_path)) 
        $img_path = "uploads/users_images/{$user_info['username']}/dp.png";
    }

    header("Location: ./user/dashboard.php");
  }

  public function signout() {
    if(!isset($_SESSION["signed_in_user"])) exit();

    session_unset();
    session_destroy();

    header("Location: ../page/index.php");
  }

  public function sendOTP() {
    $receiver_email = $_SESSION['verifying_user']['email'];
    $receiver_name = $_SESSION['verifying_user']['username'];

    $subject = "OTP VERIFICATION";

    $message = file_get_contents("../email/otp-verification-email.html");
    $message = str_replace("{receiver_name}", $receiver_name, $message);
    $message = str_replace("{otp}", $_SESSION['otp'], $message);

    $this->smtp->sendEmail($receiver_email, $receiver_name, $subject, $message);
  }

  public function verifyUser($otp) {
    if($_SESSION['otp'] != $otp) {
      echo "
        <script>
          alert(\"{$otp} is not the OTP.\");
          window.location.href = \"../page/otpverification.php\";
        </script>
      ";
      exit();
    }

    $receiver_email = $_SESSION['verifying_user']['email'];
    $receiver_name = $_SESSION['verifying_user']['username'];

    $stmt = $this->database->prepare("UPDATE user SET status = :status, tokencode = :tokencode WHERE email = :email");
    $stmt->execute([
      ":status" => "verified",
      ":tokencode" => $this->generateTC(),
      ":email" => $receiver_email
    ]);

    $subject = "VERIFICATION SUCCESS";

    $message = file_get_contents("../email/verification-success-email.html");
    $message = str_replace("{receiver_name}", $receiver_name, $message);

    $this->smtp->sendEmail($receiver_email, $receiver_name, $subject, $message);

    session_unset();
    session_destroy();

    $user_dir = "uploads/users_images/" . $receiver_name;
    if(!is_dir($user_dir)) {
      mkdir("../{$user_dir}", 0777, true);
    }
    
    echo "
      <script>
        alert(\"User {$receiver_email} Verified Successfully.\");
        window.location.href = \"../page/index.php\";
      </script>
    ";
  }

  public function sendRPLink($email) {
    $stmt = $this->database->prepare("SELECT * FROM user WHERE email = :email");
    $stmt->execute([
      ":email" => $email
    ]);

    if($stmt->rowCount() <= 0) {
      echo "
        <script>
          alert('{$email} is not yet existing.');
          window.location.href = '../page/forgotpassword.php';
        </script>
      ";
      exit();
    }

    $user_info = $stmt->fetch(PDO::FETCH_ASSOC);

    $subject = "RESET PASSWORD";

    $link = "http://localhost/projects/ITELEC2/GROUP2-GameManagerSystem/page/resetpassword.php?id={$user_info['id']}&tokencode={$user_info['tokencode']}";
    $message = file_get_contents("../email/rp-email.html");
    $message = str_replace("{link}", $link, $message);

    $this->smtp->sendEmail($user_info['email'], $user_info['username'], $subject, $message);
    echo "
      <script>
        alert('We Have Sent You A Reset Password.');
        window.location.href = '../page/forgotpassword.php';
      </script>
    ";
  }

  public function resetPassword($id, $new_pass, $tokencode) {
    if(strlen($new_pass) < 8) {
      echo "
        <script>
          alert(\"Password Must Have At Least 8 Characters.\");
          window.location.href = \"../page/resetpassword.php?id={$id}&tokencode={$tokencode}\";
        </script>
      ";
      exit();
    }

    $stmt = $this->database->prepare("UPDATE user SET password = :password, tokencode = :tokencode WHERE id = :id");
    $stmt->execute([
      ":password" => $this->hashPwd($new_pass),
      ":tokencode" => $this->generateTC(),
      ":id" => $id
    ]);

    echo "
      <script>
        alert('Reset Password Successfully.');
        window.close();
      </script>
    ";
  }

  public static function checkAccLoggedIn() {
    if(isset($_SESSION["signed_in_user"])) {
      echo 
      "<script>
        alert(\"User is signed in yet.\");
        window.location.href = \"../dashboard/{$_SESSION['signed_in_user']['type']}/dashboard.php\";
      </script>";
      exit();
    }
  }

  public static function checkAccNotLoggedIn($redirect) {
    if(!isset($_SESSION["signed_in_user"])) {
      echo 
      "<script>
        alert(\"No user is signed in.\");
        window.location.href = \"{$redirect}\";
      </script>";
      exit();
    }
  }

  public static function checkOTPVerifyOnGoing() {
    if(isset($_SESSION['otp']) && isset($_SESSION['verifying_user'])) {
      unset($_SESSION['otp']);
      unset($_SESSION['verifying_user']);
    }
  }

  public static function checkOTPVerifyNotOnGoing() {
    if(!isset($_SESSION['otp']) && !isset($_SESSION['verifying_user'])) {
      echo 
      "<script>
        alert(\"No OTP Set.\");
        window.location.href = \"../page/index.php\";
      </script>";
      exit();
    }
  }

  public static function redirectAdminPage() {
    $smtp_user = new SMTPConfig();
    if($_SESSION["signed_in_user"]["email"] === $smtp_user->getEmail()) {
      header("Location: ../admin/dashboard.php");
      exit();
    }
  }

  public static function redirectUserPage() {
    $smtp_user = new SMTPConfig();
    if($_SESSION["signed_in_user"]["email"] !== $smtp_user->getEmail()) {
      header("Location: ../user/dashboard.php");
      exit();
    }
  }

  private function hashPwd($pass) {
    return md5($pass);
  }

  private function generateTC() {
    return md5(uniqid(rand()));
  }
  
  // Update this with pop up window instead of alert.
  public static function checkInputEmpty($str_input, $input_name, $redirect) {
    if(strlen($str_input) <= 0 || $str_input === "" || $str_input === null) {
      echo
      "<script>
        alert(\"Do not leave '{$input_name}' input blank.\");
        window.location.href = \"{$redirect}\";
      </script>";
      exit();
    }
  }

  // Update this with pop up window instead of alert.
  private function checkPwdLen($pass) {
    if(strlen($pass) < 8) {
      echo
      "<script>
        alert(\"Password must be at least 8 characters.\");
        window.location.href = \"../page/index.php\";
      </script>";
      exit();
    }
  }
}

if(isset($_POST["signup"])) {
  (new Authentication())->signup(
    trim($_POST["name"]),
    trim($_POST["username"]),
    trim($_POST["email"]),
    trim($_POST["password"])
  );
}

if(isset($_POST["signin"])) {
  (new Authentication())->signin(
    trim($_POST["email"]),
    trim($_POST["password"])
  );
}

if(isset($_GET["logout"])) {
  (new Authentication())->signout();
}

if(isset($_POST['otp_sent'])) {
  (new Authentication())->verifyUser($_POST['otp']);
}

if(isset($_POST['find_email'])) {
  (new Authentication())->sendRPLink(trim($_POST['email']));
}

if(isset($_POST['reset_pass'])) {
  (new Authentication())->resetPassword($_POST['id'], trim($_POST['password']), $_POST['tokencode']);
}