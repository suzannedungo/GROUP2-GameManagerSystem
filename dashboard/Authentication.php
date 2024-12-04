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
    $this->checkInputEmpty($name, "name");
    $this->checkInputEmpty($username, "username");
    $this->checkInputEmpty($email, "email");
    $this->checkInputEmpty($password, "password");

    $this->checkPwdLen($password);
    $username = strtolower($username);

    $stmt = $this->database->prepare("SELECT email FROM account WHERE email = :email");
    $stmt->execute([
      ":email" => $email
    ]);

    // Update this with pop up window instead of alert.
    if($stmt->rowCount() > 0) {
      echo
      "<script>
        alert(\"Account is already in our records.\");
        window.location.href = \"../page/index.php\";
      </script>";
      exit();
    }

    $stmt = $this->database->prepare("SELECT username FROM account WHERE username = :username");
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

    $stmt = $this->database->prepare("INSERT INTO account(name, username, email, password, tokencode) VALUES(:name, :username, :email, :password, :tokencode)");
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
      alert(\"Account Added Successfully!\");
    </script>";

    $_SESSION['verifying_acc']['email'] = $email;
    $_SESSION['verifying_acc']['username'] = $username;
    $_SESSION['otp'] = rand(100000, 999999);
    
    // Sending OTP.
    $this->sendOTP();

    // Update this with pop up window instead of alert.
    echo "
      <script>
        alert(\"We have sent you an OTP to {$_SESSION['verifying_acc']['email']}.\"); 
        window.location.href = \"../page/otpverification.php\";
      </script>
    ";
  }

  public function signin($email, $password) {
    $this->checkInputEmpty($email, "email");
    $this->checkInputEmpty($password, "password");

    $this->checkPwdLen($password);

    $stmt = $this->database->prepare("SELECT * FROM account WHERE email = :email");
    $stmt->execute([
      ":email" => $email
    ]);

    // Update this with pop up window instead of alert.
    if($stmt->rowCount() <= 0) {
      echo
      "<script>
        alert(\"Account is not in our records.\");
        window.location.href = \"../page/index.php\";
      </script>";
      exit();
    }

    $account_info = $stmt->fetch(PDO::FETCH_ASSOC);

    // Update this with pop up window instead of alert.
    $password = $this->hashPwd($password);
    if($password !== $account_info["password"]) {
      echo
      "<script>
        alert(\"Password Incorrect.\");
        window.location.href = \"../page/index.php\";
      </script>";
      exit();
    }

    // Update this with pop up window instead of alert.
    if($account_info["status"] === "not_verified") {
      echo "
        <script>
          alert(\"Account Is Not Yet Verified.\"); 
        </script>
      ";

      $_SESSION['verifying_acc']['email'] = $email;
      $_SESSION['verifying_acc']['username'] = $account_info["username"];
      $_SESSION['otp'] = rand(100000, 999999);
      $this->sendOTP();

      echo "
        <script>
          alert(\"We have sent you an OTP to {$account_info['email']}.\"); 
          window.location.href = \"../page/otpverification.php\";
        </script>
      ";

      exit();
    }

    $_SESSION["signed_in_acc"] = $account_info;
    switch($_SESSION["signed_in_acc"]["type"]) {
      case 'user':
        header("Location: ./user/dashboard.php");
        break;
      
      case 'admin':
        header("Location: ./admin/dashboard.php");
        break;
    }
  }

  public function signout() {
    if(!isset($_SESSION["signed_in_acc"])) exit();

    session_unset();
    session_destroy();

    header("Location: ../page/index.php");
  }

  public function sendOTP() {
    $receiver_email = $_SESSION['verifying_acc']['email'];
    $receiver_name = $_SESSION['verifying_acc']['username'];

    $subject = "OTP VERIFICATION";

    $message = file_get_contents("../email/otp-verification-email.html");
    $message = str_replace("{receiver_name}", $receiver_name, $message);
    $message = str_replace("{otp}", $_SESSION['otp'], $message);

    $this->smtp->sendEmail($receiver_email, $receiver_name, $subject, $message);
  }

  public function verifyAdmin($otp) {
    if($_SESSION['otp'] != $otp) {
      echo "
        <script>
          alert(\"{$otp} is not the OTP.\");
          window.location.href = \"../page/otpverification.php\";
        </script>
      ";
      exit();
    }

    $receiver_email = $_SESSION['verifying_acc']['email'];
    $receiver_name = $_SESSION['verifying_acc']['username'];

    $stmt = $this->database->prepare("UPDATE account SET status = :status, tokencode = :tokencode WHERE email = :email");
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
    
    echo "
      <script>
        alert(\"Account {$receiver_email} Verified Successfully.\");
        window.location.href = \"../page/index.php\";
      </script>
    ";
  }

  public function sendRPLink($email) {
    $stmt = $this->database->prepare("SELECT * FROM account WHERE email = :email");
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

    $account_info = $stmt->fetch(PDO::FETCH_ASSOC);

    $subject = "RESET PASSWORD";

    $link = "http://localhost/projects/ITELEC2/GROUP2-GameManagerSystem/page/resetpassword.php?id={$account_info['id']}&tokencode={$account_info['tokencode']}";
    $message = file_get_contents("../email/rp-email.html");
    $message = str_replace("{link}", $link, $message);

    $this->smtp->sendEmail($account_info['email'], $account_info['username'], $subject, $message);
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

    $stmt = $this->database->prepare("UPDATE account SET password = :password, tokencode = :tokencode WHERE id = :id");
    $stmt->execute([
      ":password" => $this->hashPwd($new_pass),
      ":tokencode" => $this->generateTC(),
      ":id" => $id
    ]);

    echo "
      <script>
        alert('Reset Password Successfully.');
        window.location.href = '../page/index.php';
      </script>
    ";
  }

  public static function checkAccLoggedIn() {
    if(isset($_SESSION["signed_in_acc"])) {
      echo 
      "<script>
        alert(\"Account is signed in yet.\");
        window.location.href = \"../dashboard/{$_SESSION['signed_in_acc']['type']}/dashboard.php\";
      </script>";
      exit();
    }
  }

  public static function checkAccNotLoggedIn($redirect) {
    if(!isset($_SESSION["signed_in_acc"])) {
      echo 
      "<script>
        alert(\"No account is signed in.\");
        window.location.href = \"{$redirect}\";
      </script>";
      exit();
    }
  }

  public static function checkOTPVerifyOnGoing() {
    if(isset($_SESSION['otp']) && isset($_SESSION['verifying_acc'])) {
      unset($_SESSION['otp']);
      unset($_SESSION['verifying_acc']);
    }
  }

  public static function checkOTPVerifyNotOnGoing() {
    if(!isset($_SESSION['otp']) && !isset($_SESSION['verifying_acc'])) {
      echo 
      "<script>
        alert(\"No OTP Set.\");
        window.location.href = \"../page/index.php\";
      </script>";
      exit();
    }
  }

  public static function redirectAdminPage() {
    if($_SESSION["signed_in_acc"]["type"] === "admin") {
      header("Location: ../admin/dashboard.php");
      exit();
    }
  }

  public static function redirectUserPage() {
    if($_SESSION["signed_in_acc"]["type"] === "user") {
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
  private function checkInputEmpty($str_input, $input_name) {
    if(strlen($str_input) <= 0 || $str_input === "" || $str_input === null) {
      echo
      "<script>
        alert(\"Do not leave '{$input_name}' input blank.\");
        window.location.href = \"../page/index.php\";
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
  (new Authentication())->verifyAdmin($_POST['otp']);
}

if(isset($_POST['find_email'])) {
  (new Authentication())->sendRPLink(trim($_POST['email']));
}

if(isset($_POST['reset_pass'])) {
  (new Authentication())->resetPassword($_POST['id'], trim($_POST['password']), $_POST['tokencode']);
}