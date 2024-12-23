<?php

namespace App\Core;

class Request {
  public static function getPath() {
    $path = $_SERVER["REQUEST_URI"] ?? "/";

    $position = strpos($path, "?") ?? false;
    if($position === false) {
      return $path;
    }

    $path = substr($path, 0, $position);

    return $path;
  }

  public static function getMethod() {
    return strtoupper($_SERVER["REQUEST_METHOD"]);
  }
}