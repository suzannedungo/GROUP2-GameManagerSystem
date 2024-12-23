<?php

namespace App\View;

use App\Core\Utilities;

class LandingPageView {
  public static function signUpInPage($csrf_token) {
    $page = file_get_contents(Utilities::getPath() . "/view/page/auth/signupin.html");
    $page = str_replace("{csrf_token}", $csrf_token, $page);
    echo $page;
  }

  public static function verifyOTPPage($csrf_token) {
    $page = file_get_contents(Utilities::getPath() . "/view/page/auth/verifyotp.html");
    $page = str_replace("{csrf_token}", $csrf_token, $page);
    echo $page;
  }

  public static function forgotPassPage($csrf_token) {
    $page = file_get_contents(Utilities::getPath() . "/view/page/auth/forgotpass.html");
    $page = str_replace("{csrf_token}", $csrf_token, $page);
    echo $page;
  }

  public static function resetPassPage($csrf_token, $id, $tokencode) {
    $page = file_get_contents(Utilities::getPath() . "/view/page/auth/resetpass.html");
    $page = str_replace("{csrf_token}", $csrf_token, $page);
    $page = str_replace("{id}", $id, $page);
    $page = str_replace("{tokencode}", $tokencode, $page);
    echo $page;
  }

  public static function pageNotFoundPage() {
    echo file_get_contents(Utilities::getPath() . "/view/page/_404.html");
  }
}