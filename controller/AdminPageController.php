<?php

namespace App\Controller;

use App\Core\Utilities;
use App\Model\User;
use App\Model\Game;
use App\Model\GameGenre;
use App\Model\GameReview;
use App\Model\Genre;
use App\Model\SMTPUser;
use App\View\AdminPageView;

class AdminPageController {
  public static function dashboard() {
    Authentication::checkVerifyOTPOnGoing();
    Authentication::checkAccountNotSignedIn();
    Authentication::redirectToUser();


    $user = new User();
    $total_users = $user->getTotalUser();
    $recent_users = $user->getRecentJoined();
    $total_recent_users = $recent_users === 0 ? 0 : count($recent_users);
    for($i = 0; $i < $total_recent_users; $i++) {
      $recent_users[$i]["dp"] = $user->getProfileImageByEmail($recent_users[$i]["email"]);
    }

    $game = new Game();
    $total_games = $game->getTotalGame();
    $recent_games = $game->getRecentAdded();
    $total_recent_games = $recent_games === 0 ? 0 : count($recent_games);
    for($i = 0; $i < $total_recent_games; $i++) {
      $recent_games[$i]["icon"] = $game->getIconById($recent_games[$i]["id"]);
    }

    $genre = new Genre();
    $total_genres = $genre->getTotalGenre();

    $data = [
      "total_users" => $total_users,
      "recent_users" => $recent_users,
      "total_recent_users" => $total_recent_users,
      "total_games" => $total_games,
      "recent_games" => $recent_games,
      "total_recent_games" => $total_recent_games,
      "total_genres" => $total_genres
    ];

    AdminPageView::dashboardPage($data);
  }

  public static function manageGenres() {
    Authentication::checkVerifyOTPOnGoing();
    Authentication::checkAccountNotSignedIn();
    Authentication::redirectToUser();
    Utilities::generateCSRFToken();

    $genre = new Genre();
    $data = [
      "total_genre" => $genre->getTotalGenre(),
      "genres" => $genre->getAllGenreNames()
    ];

    AdminPageView::manageGenresPage($data, $_SESSION["csrf_token"]);
  }

  public static function editGenre() {
    Authentication::checkVerifyOTPOnGoing();
    Authentication::checkAccountNotSignedIn();
    Authentication::redirectToUser();
    Utilities::generateCSRFToken();

    $genre = new Genre();
    $total_genre = $genre->getTotalGenre();
    if($total_genre <= 0 || $total_genre == false) {
      Utilities::showAlertAndExit("No Genre Available.", "/admin/manage_genres", 400);
    }

    $data = [
      "total_genre" => $total_genre,
      "genres" => $genre->getAllGenre()
    ];

    AdminPageView::editGenrePage($data, $_SESSION["csrf_token"]);
  }

  public static function deleteGenre() {
    Authentication::checkVerifyOTPOnGoing();
    Authentication::checkAccountNotSignedIn();
    Authentication::redirectToUser();
    Utilities::generateCSRFToken();

    $genre = new Genre();
    $total_genre = $genre->getTotalGenre();
    if($total_genre <= 0 || $total_genre == false) {
      Utilities::showAlertAndExit("No Genre Available.", "/admin/manage_genres", 400);
    }

    $data = [
      "total_genre" => $total_genre,
      "genres" => $genre->getAllGenre()
    ];

    AdminPageView::deleteGenrePage($data, $_SESSION["csrf_token"]);
  }

  public static function manageGames() {
    Authentication::checkVerifyOTPOnGoing();
    Authentication::checkAccountNotSignedIn();
    Authentication::redirectToUser();
    Utilities::generateCSRFToken();

    $game = new Game();
    $game_review = new GameReview();

    $games = $game_review->getAverageRatingForAllGames();
    $total_games = $game->getTotalGame();
    for($i = 0; $i < $total_games; $i++) {
      $games[$i]["icon"] = $game->getIconById($games[$i]["game_id"]);
      if($games[$i]["average_rating"] == null) {
        $games[$i]["average_rating"] = 0;
      }
    }

    $data = [
      "total_games" => $game->getTotalGame(),
      "games" => $games
    ];

    AdminPageView::manageGamesPage($data);
  }

  public static function addGame() {
    Authentication::checkVerifyOTPOnGoing();
    Authentication::checkAccountNotSignedIn();
    Authentication::redirectToUser();
    Utilities::generateCSRFToken();

    $genre = new Genre();

    $data = [
      "total_genres" => $genre->getTotalGenre(),
      "genres" => $genre->getAllGenre()
    ];

    AdminPageView::addGamePage($data, $_SESSION["csrf_token"]);
  }

  public static function previewGame() {
    Authentication::checkVerifyOTPOnGoing();
    Authentication::checkAccountNotSignedIn();
    Authentication::redirectToUser();
    Utilities::generateCSRFToken();

    if(!isset($_GET["id"])) {
      header("Location: /admin/manage_games");
      exit();
    }

    $game = new Game();
    $game_info = $game->getGameById($_GET["id"]);
    if($game_info == false) {
      Utilities::showAlertAndExit("An error occured on getting game.", "/admin/manage_games", 500);
    }

    if($game_info == 0) {
      Utilities::showAlertAndExit("No Game Found.", "/admin/manage_games", 404);
    }

    $game_review = new GameReview();
    $game_info["rating"] = $game_review->getAverageRatingByGameId($_GET["id"]);
    if($game_info["rating"] == null) {
      $game_info["rating"] = 0;
    }

    $game_info["img"] = $game->getImagesById($_GET["id"]);
    if($game_info["img"] == false) {
      Utilities::showAlertAndExit("An error occured on getting game images.", "/admin/manage_games", 500);
    }

    if($game_info["img"] == 0) {
      Utilities::showAlertAndExit("No Game Images Found.", "/admin/manage_games", 404);
    }

    $game_genre = new GameGenre();
    $game_info["genres"] = $game_genre->getGenreNamesByGameId($_GET["id"]);
    if($game_info == false) {
      Utilities::showAlertAndExit("An error occured on getting genre names.", "/admin/manage_games", 500);
    }

    if($game_info["genres"] == 0) {
      $game_info["genres"] = "<i>No genres.</i>";
    }

    $data = $game_info;

    AdminPageView::previewGamePage($data);
  }

  public static function editGame() {
    Authentication::checkVerifyOTPOnGoing();
    Authentication::checkAccountNotSignedIn();
    Authentication::redirectToUser();
    Utilities::generateCSRFToken();

    $game = new Game();
    $games = $game->getAllGames();
    $total_games = $game->getTotalGame();
    if($games === false) {
      Utilities::showAlertAndExit("An error occured on getting all games.", "/admin/manage_games", 500);
    }

    if($games === 0) {
      Utilities::showAlertAndExit("No Games Available.", "/admin/manage_games", 403);
    }

    $genre = new Genre();
    $genres = $genre->getAllGenre();
    if($genres === false) {
      Utilities::showAlertAndExit("An error occured on getting all genres.", "/admin/manage_games", 500);
    }

    $data = [
      "total_genres" => $genre->getTotalGenre(),
      "total_games" => $total_games,
      "genres" => $genres,
      "games" => $games
    ];

    AdminPageView::editGamePage($data, $_SESSION["csrf_token"]);
  }

  public static function deleteGame() {
    Authentication::checkVerifyOTPOnGoing();
    Authentication::checkAccountNotSignedIn();
    Authentication::redirectToUser();
    Utilities::generateCSRFToken();

    $game = new Game();
    $total_games = $game->getTotalGame();
    if($total_games <= 0 || $total_games == false) {
      Utilities::showAlertAndExit("No Games Available.", "/admin/manage_games", 400);
    }

    $data = [
      "total_games" => $total_games,
      "games" => $game->getAllGames()
    ];


    AdminPageView::deleteGamePage($data, $_SESSION["csrf_token"]);
  }

  public static function manageUsers() {
    Authentication::checkVerifyOTPOnGoing();
    Authentication::checkAccountNotSignedIn();
    Authentication::redirectToUser();
    Utilities::generateCSRFToken();

    $user = new User();
    $users = $user->getAllUsers();
    $users1 = [];
    $total_users = $user->getTotalUser();
    for($i = 0; $i < $total_users; $i++) {
      if($users[$i]["status"] == "ban" || $users[$i]["status"] == "verified") {
        $users1[$i] = $users[$i];
      }
    }

    $data = ["users" => $users1];

    AdminPageView::manageUsersPage($data, $_SESSION["csrf_token"]);
  }

  public static function manageSMTP() {
    Authentication::checkVerifyOTPOnGoing();
    Authentication::checkAccountNotSignedIn();
    Authentication::redirectToUser();
    Utilities::generateCSRFToken();

    $smtp_user = new SMTPUser();
    $data = $smtp_user->getUserById(1);

    AdminPageView::manageSMTPPage($data, $_SESSION["csrf_token"]);
  }

  public static function updateSMTP() {
    Authentication::checkVerifyOTPOnGoing();
    Authentication::checkAccountNotSignedIn();
    Authentication::redirectToUser();
    Utilities::generateCSRFToken();

    AdminPageView::updateSMTPPage($_SESSION["csrf_token"]);
  }

  public static function profile() {
    Authentication::checkVerifyOTPOnGoing();
    Authentication::checkAccountNotSignedIn();
    Authentication::redirectToUser();
    Utilities::generateCSRFToken();

    AdminPageView::profilePage();
  }

  public static function updateProfile() {
    Authentication::checkVerifyOTPOnGoing();
    Authentication::checkAccountNotSignedIn();
    Authentication::redirectToUser();
    Utilities::generateCSRFToken();

    if(!isset($_GET["edit"])) {
      header("Location: /admin/profile");
      exit();
    }

    $data = [];
    switch($_GET["edit"]) {
      case "dp":
        $data["type"] = "file";
        $data["name"] = "dp";
        break;

      case "name":
        $data["type"] = "text";
        $data["name"] = "name";
        break;

      case "email":
        $data["type"] = "email";
        $data["name"] = "email";
        break;

      default:
        header("Location: /admin/profile");
        exit();
    }

    AdminPageView::updateProfilePage($data, $_SESSION["csrf_token"]);
  }

  public static function resetPassword() {
    Authentication::checkVerifyOTPOnGoing();
    Authentication::checkAccountNotSignedIn();
    Authentication::redirectToUser();

    if(!isset($_POST["reset_pass"])) {
      header("Location: /admin/profile");
      exit();
    }

    Utilities::validateCSRFToken($_POST["csrf_token"]);
    Utilities::generateCSRFToken();

    AdminPageView::resetPasswordPage($_SESSION["csrf_token"]);
  }
}