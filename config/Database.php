<?php

class Database {
  public static function connect() {
    $pdo = null;

    try {
      $pdo = new PDO("mysql:host=localhost;dbname=game_manager", "root", "");
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(Exception $pdo_err) {
      echo 
      "<script>
        alert(\"{$pdo_err->getMessage()}\");
        window.location.href = \"../page/index.php\";
      </script>";
      exit();
    }

    return $pdo;
  }
}