<?php

namespace App\Controller;

use App\Model\FavoriteGame;

class FavoriteGameController {
  public static function addFavorite() {
    if(!isset($_POST["add_fav"])) {
      echo false;
    }

    $fav_games = new FavoriteGame();
    if(!$fav_games->add($_POST["uid"], $_POST["gid"])) {
      echo "An error occured on adding game as favorite.";
    }

    echo "Added to your favorites.";
  }

  public static function delFavorite() {
    if(!isset($_POST["del_fav"])) {
      echo false;
    }

    $fav_games = new FavoriteGame();
    if(!$fav_games->delete($_POST["uid"], $_POST["gid"])) {
      echo "An error occured on removing game as favorite.";
    }

    echo "Removed from your favorites.";
  }
}