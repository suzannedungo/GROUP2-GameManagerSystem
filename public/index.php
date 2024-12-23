<?php

require_once __DIR__ . "/../vendor/autoload.php";

use App\Core\Router;
use App\Controller\Authentication;
use App\Controller\AdminController;
use App\Controller\GameController;
use App\Controller\GenreController;
use App\Controller\UserController;
use App\Controller\SMTPController;
use App\Controller\AdminPageController;
use App\Controller\FavoriteGameController;
use App\Controller\GameReviewController;
use App\Controller\UserPageController;
use App\Controller\LandingPageController;

session_start();

ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
error_reporting(E_ALL);

$router = new Router();

// $router->get("/haha", [LandingPageController::class, "haha"]);

/* === LANDING PAGE === */
$router->get("/", [LandingPageController::class, "signUpIn"]);
$router->get("/signin", [LandingPageController::class, "signUpIn"]);
$router->get("/signup", [LandingPageController::class, "signUpIn"]);
$router->get("/verifyotp", [LandingPageController::class, "verifyOTP"]);
$router->get("/forgotpass", [LandingPageController::class, "forgotPass"]);
$router->get("/resetpass", [LandingPageController::class, "resetPass"]);
$router->get("/_404", [LandingPageController::class, "pageNotFound"]);

/* === AUTHENTICATION === */
$router->post("/signup", [Authentication::class, "signUp"]);
$router->post("/signin", [Authentication::class, "signIn"]);
$router->get("/signout", [Authentication::class, "signOut"]);
$router->post("/verifyotp", [Authentication::class, "verifyOTP"]);
$router->post("/forgotpass", [Authentication::class, "forgotPass"]);
$router->post("/resetpass", [Authentication::class, "resetPass"]);



/* === ADMIN PAGE === */
$router->get("/admin/", [AdminPageController::class, "dashboard"]);
$router->get("/admin/dashboard", [AdminPageController::class, "dashboard"]);
$router->get("/admin/manage_genres", [AdminPageController::class, "manageGenres"]);
$router->get("/admin/manage_games", [AdminPageController::class, "manageGames"]);
$router->get("/admin/manage_users", [AdminPageController::class, "manageUsers"]);
$router->get("/admin/manage_smtp", [AdminPageController::class, "manageSMTP"]);
$router->get("/admin/profile", [AdminPageController::class, "profile"]);

/* === MANAGE GENRES ROUTES === */
$router->post("/admin/manage_genres/add_genre", [GenreController::class, "addGenre"]);
$router->get("/admin/manage_genres/edit_genre", [AdminPageController::class, "editGenre"]);
$router->post("/admin/manage_genres/edit_genre", [GenreController::class, "editGenre"]);
$router->get("/admin/manage_genres/delete_genre", [AdminPageController::class, "deleteGenre"]);
$router->post("/admin/manage_genres/delete_genre", [GenreController::class, "deleteGenre"]);

/* === MANAGE GAMES ROUTES === */
$router->get("/admin/manage_games/add_game", [AdminPageController::class, "addGame"]);
$router->post("/admin/manage_games/add_game", [GameController::class, "addGame"]);
$router->get("/admin/manage_games/preview_game", [AdminPageController::class, "previewGame"]);
$router->get("/admin/manage_games/edit_game", [AdminPageController::class, "editGame"]);
$router->post("/admin/manage_games/edit_game", [GameController::class, "editGame"]);
$router->get("/admin/manage_games/delete_game", [AdminPageController::class, "deleteGame"]);
$router->post("/admin/manage_games/delete_game", [GameController::class, "deleteGame"]);

/* === MANAGE USER BAN ROUTES === */
$router->post("/admin/manage_users/ban_user", [UserController::class, "banUser"]);
$router->post("/admin/manage_users/unban_user", [UserController::class, "unbanUser"]);

/* === MANAGE SMTP ROUTES === */
$router->get("/admin/manage_smtp/update_smtp", [AdminPageController::class, "updateSMTP"]);
$router->post("/admin/manage_smtp/update_smtp", [SMTPController::class, "updateSMTP"]);
$router->post("/admin/manage_smtp/testing_smtp", [SMTPController::class, "testingSMTP"]);

/* === MANAGE PROFILE ROUTES === */
$router->get("/admin/profile/update", [AdminPageController::class, "updateProfile"]);
$router->post("/admin/profile/update", [AdminController::class, "updateProfile"]);
$router->get("/admin/profile/reset_dp", [AdminController::class, "resetDP"]);
$router->post("/admin/profile/reset_pass", [AdminPageController::class, "resetPassword"]);
$router->post("/admin/change_pass", [AdminController::class, "changePassword"]);



/* === USER PAGE === */
$router->get("/user/", [UserPageController::class, "dashboard"]);
$router->get("/user/profile", [UserPageController::class, "profile"]);
$router->get("/user/visit_game", [UserPageController::class, "visitGame"]);
$router->get("/user/game_genre", [UserPageController::class, "gameGenre"]);
$router->post("/user/visit_game/add_fav", [FavoriteGameController::class, "addFavorite"]);
$router->post("/user/visit_game/del_fav", [FavoriteGameController::class, "delFavorite"]);
$router->post("/user/visit_game/add_review", [GameReviewController::class, "addReview"]);
$router->post("/user/visit_game/del_review", [GameReviewController::class, "delReview"]);

/* === MANAGE PROFILE ROUTES === */
$router->post("/user/profile/update", [UserController::class, "updateProfile"]);
$router->get("/user/profile/reset_dp", [UserController::class, "resetDP"]);
$router->post("/user/profile/reset_pass", [UserController::class, "updateProfile"]);
$router->post("/user/del_acc", [UserController::class, "delAccount"]);

// echo $router->listen();
$router->listen();