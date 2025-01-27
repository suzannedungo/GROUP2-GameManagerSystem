<?php

namespace App\Core;

use PDO;
use Exception;

class Database {
  public static function connect() {
    $pdo = null;
    $server_name = null;
    $user = null;
    $pass = null;
    $db_name = null;
    if(
      $_SERVER["SERVER_NAME"] === "localhost" || 
      $_SERVER["SERVER_ADDR"] === "127.0.0.1" || 
      $_SERVER["SERVER_ADDR"] === "192.168.1.72") {
      $server_name = "localhost";
      $db_name = "game_manager";
      $user = "root";
      $pass = "";
    } else {
      $server_name = "localhost";
      $db_name = "u175342239_game_manager";
      $user = "u175342239_user";
      $pass = "MySQL_root_12345";
    }

    try {
      $pdo = new PDO("mysql:host={$server_name};dbname={$db_name}", $user, $pass);
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(Exception $pdo_err) {
      echo 
      "<script>
        alert(\"{$pdo_err->getMessage()}\");
        window.location.href = \"/\";
      </script>";

      session_unset();
      session_destroy();

      exit();
    }

    return $pdo;
  }
}