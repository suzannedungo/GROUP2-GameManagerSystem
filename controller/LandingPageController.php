<?php

namespace App\Controller;

use App\Model\User;
use App\View\LandingPageView;
use App\Core\Utilities;

class LandingPageController {
  public static function haha() {
    echo json_encode([
      "status" => "error",
      "message" => "Ewan"
    ]);
  }

  public static function signUpIn() {
    Authentication::checkVerifyOTPOnGoing();
    Authentication::checkAccountSignedIn();
    Utilities::generateCSRFToken();

    LandingPageView::signUpInPage($_SESSION["csrf_token"]);
  }

  public static function verifyOTP() {
    Authentication::checkVerifyOTPNotOnGoing();
    // Authentication::checkAccountSignedIn();
    Utilities::generateCSRFToken();

    LandingPageView::verifyOTPPage($_SESSION["csrf_token"]);
  }

  public static function forgotPass() {
    Authentication::checkVerifyOTPOnGoing();
    Authentication::checkAccountSignedIn();
    Utilities::generateCSRFToken();

    LandingPageView::forgotPassPage($_SESSION["csrf_token"]);
  }

  public static function resetPass() {
    Authentication::checkVerifyOTPOnGoing();
    // Authentication::checkAccountSignedIn();
    Utilities::generateCSRFToken();

    if(!isset($_GET["id"]) && !isset($_GET["tokencode"])) {
      http_response_code(403);
      header("Location: /");
      exit();
    }

    $user = new User();
    $user_info = $user->getUserById($_GET["id"]);
    if($user_info === false) {
      Utilities::showAlertAndCloseTab("No User ID Found in our records.", 404);
    }

    if($user_info["tokencode"] !== $_GET["tokencode"]) {
      Utilities::showAlertAndCloseTab("Invalid Tokencode", 404);
    }

    LandingPageView::resetPassPage($_SESSION["csrf_token"], $_GET["id"], $_GET["tokencode"]);
  }

  public static function pageNotFound() {
    LandingPageView::pageNotFoundPage();
  }
}