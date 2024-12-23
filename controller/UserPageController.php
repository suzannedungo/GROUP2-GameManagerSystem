<?php

namespace App\Controller;

use App\Core\Utilities;
use App\Model\Game;
use App\Model\GameReview;
use App\Model\FavoriteGame;
use App\Model\Genre;
use App\Model\User;
use App\Model\GameGenre;
use App\Model\VisitedGame;
use App\View\UserPageView;

class UserPageController {
  private static function checkUserBan() {
    $user = new User();
    if(($user->getStatusByEmail($_SESSION["signed_in_acc"]["email"])) == "ban") {
      session_unset();
      session_destroy();
      Utilities::showAlert("Unfortunately, your account is banned by the Admin.");
      Utilities::showAlertAndExit("You will be signout.", "/", 403);
    }
  }

  public static function dashboard() {
    Authentication::checkVerifyOTPOnGoing();
    Authentication::checkAccountNotSignedIn();
    Authentication::redirectToAdmin();
    Utilities::generateCSRFToken();
    self::checkUserBan();

    $genre = new Genre();
    $game = new Game();
    $game_review = new GameReview();
    $favorite_game = new FavoriteGame();
    $visited_game = new VisitedGame();

    $featured_game = $game_review->getHighestRatingGame();
    $featured_game_img = false;
    if($featured_game !== 0 && $featured_game !== false) {
      $featured_game_img = $game->getBGById($featured_game["game_id"]);
    }

    $total_games = $game->getTotalGame();
    $games = $game_review->getAverageRatingForAllGames();
    for($i = 0; $i < $total_games; $i++) {
      $games[$i]["icon"] = $game->getIconById($games[$i]["game_id"]);
      if($games[$i]["average_rating"] == null) {
        $games[$i]["average_rating"] = 0;
      }
    }

    $total_fav_games = $favorite_game->getTotalMostFavoriteGames();
    $fav_games = $favorite_game->getMostFavoriteGames();
    for($i = 0; $i < $total_fav_games; $i++) {
      $fav_games[$i]["icon"] = $game->getIconById($fav_games[$i]["id"]);
    }

    $total_v_games = $visited_game->getTotalMostVisitedGames();
    $v_games = $visited_game->getMostVisitedGames();
    for($i = 0; $i < $total_v_games; $i++) {
      $v_games[$i]["icon"] = $game->getIconById($v_games[$i]["id"]);
    }

    $data = [
      'genres' => $genre->getAllGenre(),
      "favorite_games" => $fav_games,
      "featured_game" => $featured_game,
      "featured_game_img" => $featured_game_img,
      "visited_games" => $v_games,
      "total_games" => $game->getTotalGame(),
      "games" => $games
    ];

    UserPageView::dashboardPage($data);
  }

  public static function visitGame() {
    Authentication::checkVerifyOTPOnGoing();
    Authentication::checkAccountNotSignedIn();
    Authentication::redirectToAdmin();
    Utilities::generateCSRFToken();
    self::checkUserBan();

    if(!isset($_GET["id"])) {
      header("Location: /user/");
      exit();
    }


    $game = new Game();
    $game_info = $game->getGameById($_GET["id"]);
    if($game_info === false) {
      Utilities::showAlertAndExit("An error occured on getting game.", "/user/", 500);
    }

    if($game_info === 0) {
      Utilities::showAlertAndExit("No Game Found.", "/user/", 404);
    }

    $v_game = new VisitedGame();
    if(!$v_game->isGameVisitedByUserId($_SESSION["signed_in_acc"]["id"], $_GET["id"])) {
      $v_game->add($_SESSION["signed_in_acc"]["id"], $_GET["id"]);
    } else {
      $v_game->update($_SESSION["signed_in_acc"]["id"], $_GET["id"]);
    }

    $game_review = new GameReview();
    $game_info["rating"] = $game_review->getAverageRatingByGameId($_GET["id"]);
    if($game_info["rating"] == null) {
      $game_info["rating"] = 0;
    }

    $user = new User();
    $tot = $game_review->getTotalAllReviewsByGameId($_GET["id"]);
    $game_info["reviews"] = $game_review->getAllReviewsByGameId($_GET["id"]);
    if($tot !== 0 || $tot !== false) {
      for($i = 0; $i < $tot; $i++) {
        $game_info["reviews"][$i]["username"] = $user->getUsernameById($game_info["reviews"][$i]["user_id"]);
        $game_info["reviews"][$i]["dp"] = $user->getProfileImageById($game_info["reviews"][$i]["user_id"]);
      }
    }

    $game_info["img"] = $game->getImagesById($_GET["id"]);
    if($game_info["img"] == false) {
      Utilities::showAlertAndExit("An error occured on getting game images.", "/user/", 500);
    }

    if($game_info["img"] == 0) {
      Utilities::showAlertAndExit("No Game Images Found.", "/user/", 404);
    }

    $game_genre = new GameGenre();
    $game_info["genres"] = $game_genre->getGenreNamesByGameId($_GET["id"]);
    if($game_info == false) {
      Utilities::showAlertAndExit("An error occured on getting genre names.", "/user/", 500);
    }

    if($game_info["genres"] == 0) {
      $game_info["genres"] = "<i>No genres.</i>";
    }

    $favorite_game = new FavoriteGame();
    $game_info["is_fav"] = $favorite_game->isGameFavoriteByUserId($_SESSION["signed_in_acc"]["id"], $game_info["id"]);

    $data = [
      "csrf_token" => $_SESSION["csrf_token"],
      "game" => $game_info
    ];

    UserPageView::visitGamePage($data);
  }

  public static function profile() {
    Authentication::checkVerifyOTPOnGoing();
    Authentication::checkAccountNotSignedIn();
    Authentication::redirectToAdmin();
    Utilities::generateCSRFToken();
    self::checkUserBan();

    $game = new Game();

    $favorite_game = new FavoriteGame();
    $tot = $favorite_game->getTotalFavoriteGamesByUserId($_SESSION["signed_in_acc"]["id"]);
    $fav_games = $favorite_game->getAllFavoriteGamesByUserId($_SESSION["signed_in_acc"]["id"]);
    for($i = 0; $i < $tot; $i++) {
      $fav_games[$i]["icon"] = $game->getIconById($fav_games[$i]["id"]);
    }

    $visited_game = new VisitedGame();
    $tot = $visited_game->getTotalRecentGamesByUserId($_SESSION["signed_in_acc"]["id"]);
    $v_games = $visited_game->getAllRecentGamesByUserId($_SESSION["signed_in_acc"]["id"]);
    for($i = 0; $i < $tot; $i++) {
      $v_games[$i]["icon"] = $game->getIconById($v_games[$i]["id"]);
    }

    $data = [
      "csrf_token" => $_SESSION["csrf_token"],
      "user" => $_SESSION["signed_in_acc"],
      "fav_games" => $fav_games,
      "visited_games" => $v_games
    ];

    UserPageView::profilePage($data);
  }

  public static function gameGenre() {
    Authentication::checkVerifyOTPOnGoing();
    Authentication::checkAccountNotSignedIn();
    Authentication::redirectToAdmin();
    Utilities::generateCSRFToken();
    self::checkUserBan();

    if(!isset($_GET["id"])) {
      header("Location: /user/");
      exit();
    }

    $genre = new Genre();
    $genre1 = $genre->getGenreById($_GET["id"]);
    if($genre1 === false) {
      Utilities::showAlertAndExit("An error occured on fetching genre.", "/user/", 500);
    }

    if($genre1 === 0) {
      Utilities::showAlertAndExit("No genre found.", "/user/", 404);
    }

    $game = new Game();
    $game_genre = new GameGenre();
    $game_review = new GameReview();

    $total = $game_genre->getTotalGamesByGenreId($_GET["id"]);
    $games = $game_genre->getAllGamesByGenreId($_GET["id"]);
    for($i = 0; $i < $total; $i++) {
      $games[$i]["icon"] = $game->getIconById($games[$i]["id"]);
      $games[$i]["rating"] = $game_review->getAverageRatingByGameId($games[$i]["id"]);
    }

    $data = [
      "genre_name" => $genre->getNameById($_GET["id"]),
      "total_games" => $total,
      "games" => $games
    ];

    UserPageView::gameGenrePage($data);
  }
}