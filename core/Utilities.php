<?php

namespace App\Core;

class Utilities {
  public static function deleteFolder($folderPath) {
    if (!is_dir($folderPath)) {
      return false; // The specified path is not a folder
    }

    // Iterate through all items in the folder
    foreach (scandir($folderPath) as $item) {
      if ($item === '.' || $item === '..') {
        continue; // Skip special folder entries
      }
      
      $itemPath = $folderPath . DIRECTORY_SEPARATOR . $item;
      if (is_dir($itemPath)) {
        // Recursively delete subfolders
        self::deleteFolder($itemPath);
      } else {
        // Delete files
        unlink($itemPath);
      }
    }

    // Delete the now-empty folder
    return rmdir($folderPath);
  }


  public static function validateCSRFToken($token) {
    if(!hash_equals($_SESSION["csrf_token"], $token)) {
      Utilities::showAlertAndExit("Invalid CSRF Token.", "/", 403);
    }

    unset($_SESSION["csrf_token"]);
  }

  public static function generateCSRFToken() {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
  }

  public static function showAlert($message) {
    echo
    "<script>
      alert(\"{$message}\");
    </script>";
  }

  public static function showAlertAndExit($message, $redirect, $status_code) {
    // http_response_code($status_code);
    echo
    "<script>
      alert(\"{$message}\");
      window.location.href = \"{$redirect}\";
    </script>";
    exit();
  }

  public static function showAlertAndCloseTab($message, $status_code) {
    http_response_code($status_code);
    echo
    "<script>
      alert(\"{$message}\");
      window.close();
    </script>";
  }

  public static function getPath() {
    return dirname(__DIR__);
  }
}