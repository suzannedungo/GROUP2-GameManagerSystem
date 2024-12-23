<?php

namespace App\Controller;

use App\Core\Utilities;
use App\Model\Genre;

class GenreController {
  public static function addGenre() {
    if(!isset($_POST["add_genre"])) {
      header("Location: /admin/manage_genres");
      exit();
    }

    Utilities::validateCSRFToken($_POST["csrf_token"]);

    $name = trim($_POST["name"]);
    $name = strtolower($name);
    Authentication::checkInputEmpty($name, "genre name", "/admin/manage_genres");

    $genre = new Genre();
    $genre_name = $genre->getNameByName($name);
    if($genre_name != false || $genre_name != 0) {
      Utilities::showAlertAndExit("Genre {$name} is already in the system.", "/admin/manage_genres", 400);
    }

    if(!$genre->add($name)) {
      Utilities::showAlertAndExit("An error occured on adding genre.", "/admin/manage_genres", 500);
    }

    Utilities::showAlertAndExit("Genre {$name} added successfully!", "/admin/manage_genres", 201);
  }

  public static function editGenre() {
    if(!isset($_POST["edit_genre"]) && $_POST["_method"] != "PUT") {
      header("Location: /admin/manage_genres");
      exit();
    }

    Utilities::validateCSRFToken($_POST["csrf_token"]);

    $name = trim($_POST["name"]);
    $name = strtolower($name);
    Authentication::checkInputEmpty($name, "genre name", "/admin/manage_genres");

    $id = $_POST["genre"];

    $genre = new Genre();
    $genre_name = $genre->getNameByName($name);
    if($genre_name != false || $genre_name != 0) {
      Utilities::showAlertAndExit("Genre {$name} is already in the system.", "/admin/manage_genres/edit_genre", 400);
    }

    if(!$genre->updateById($id, $name)) {
      Utilities::showAlertAndExit("An error occured on updating genre.", "/admin/manage_genres", 500);
    }

    Utilities::showAlertAndExit("Genre {$name} updated successfully!", "/admin/manage_genres", 201);
  }

  public static function deleteGenre() {
    if(!isset($_POST["edit_genre"]) && $_POST["_method"] != "DEL") {
      header("Location: /admin/manage_genres");
      exit();
    }

    Utilities::validateCSRFToken($_POST["csrf_token"]);

    $id = $_POST["genre"];
    $genre = new Genre();
    if(!$genre->deleteById($id)) {
      Utilities::showAlertAndExit("An error occured on deleting genre.", "/admin/manage_genres", 500);
    }

    Utilities::showAlertAndExit("Genre deleted successfully!", "/admin/manage_genres", 201);
  }
}