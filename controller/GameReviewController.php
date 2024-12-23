<?php

namespace App\Controller;

use App\Core\Utilities;
use App\Model\GameReview;

class GameReviewController {
  public static function addReview() {
    if(!isset($_POST["submit_review"])) {
      echo false;
    }

    $game_review = new GameReview();
    if(!$game_review->add($_POST["gid"], $_POST["uid"], $_POST["rating"], $_POST["comment"])) {
      echo "An error occured on adding review.";
    }

    echo "Review Added.";
  }

  public static function delReview() {
    if(!isset($_POST["del_review"])) {
      header("Location: /user/");
      exit();
    }

    Utilities::validateCSRFToken($_POST["csrf_token"]);

    $game_review = new GameReview();
    if(!$game_review->delete($_POST["user_id"], $_POST["game_id"])) {
      Utilities::showAlertAndExit("An error occured on deleting review.", "/user/visit_game=id={$_POST['game_id']}", 500);
    }

    Utilities::showAlertAndExit("Review Deleted.", "/user/visit_game?id={$_POST['game_id']}", 201);
  }
}