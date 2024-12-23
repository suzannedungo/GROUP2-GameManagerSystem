<?php

namespace App\View;

use App\Core\Utilities;

class UserPageView {
  public static function dashboardPage($data) {
    $page = file_get_contents(Utilities::getPath() . "/view/page/user/dashboard.html");
    $top_bar = file_get_contents(Utilities::getPath() . "/view/page/user/top-bar.html");
    $top_bar = str_replace("{logo}", "../../res/img/logo-white.png", $top_bar);
    $top_bar = str_replace("{profile_image}", "../../" . $_SESSION["signed_in_acc"]["profile_image"], $top_bar);
    $page = str_replace("{top_bar}", $top_bar, $page);

    $featured_game = "";
    if($data["featured_game"] > 0 && $data["featured_game"] !== false) {
      $featured_game = 
        "<div class='featured-content'". "style='background: linear-gradient(to bottom, rgba(0,0,0,0), #151515), url(../{$data['featured_game_img']}')no-repeat center center /cover;'></div>";
    }
    $page = str_replace("{featured_game}", $featured_game, $page);

    // Most Favorite Games.
    $fav_games = $data["favorite_games"];
    if($fav_games === 0 || $fav_games === false) {
      $page = str_replace("{fav_game}", "<i>No user has added their favorites yet.</i>", $page);
    } else {
      $total_games = count($fav_games);
      for($i = 0; $i < $total_games; $i++) {
        $game = "<div class='game-list-item'>";
        $game = $game . "\n  <img class='game-list-item-img' src='../../{$fav_games[$i]['icon']}'/>";
        $game = $game . "\n  <span class='game-list-item-title'>{$fav_games[$i]['name']}</span>";
        $game = $game . "\n  <a href='/user/visit_game?id={$fav_games[$i]['id']}'> <button class='game-list-item-button'>View</button></a>";
        $game = $game . "\n</div>";

        $placeholder = "{fav_game}";

        if($i < ($total_games - 1)) {
          $game = $game . $placeholder;
        }

        $page = str_replace("{fav_game}", $game, $page);
      }
    }

    // Most Visited Games.
    $visited_games = $data["visited_games"];
    if($visited_games === 0 || $visited_games === false) {
      $page = str_replace("{visited_game}", "<i>No user has visit a game yet.</i>", $page);
    } else {
      $total_games = count($visited_games);
      for($i = 0; $i < $total_games; $i++) {
        $game = "<div class='game-list-item'>";
        $game = $game . "\n  <img class='game-list-item-img' src='../../{$visited_games[$i]['icon']}'/>";
        $game = $game . "\n  <span class='game-list-item-title'>{$visited_games[$i]['name']}</span>";
        $game = $game . "\n  <a href='/user/visit_game?id={$visited_games[$i]['id']}'> <button class='game-list-item-button'>View</button></a>";
        $game = $game . "\n</div>";

        $placeholder = "{visited_game}";

        if($i < ($total_games - 1)) {
          $game = $game . $placeholder;
        }

        $page = str_replace("{visited_game}", $game, $page);
      }
    }

    // All Genres.
    $genres = $data["genres"];
    if($genres === 0 || $genres === false) {
      $page = str_replace("{genre}", "<i>No genres available yet.</i>", $page);
    } else {
      $total_genres = count($genres);
      for($i = 0; $i < $total_genres; $i++) {
        $genre = "<a class='genre' href='/user/game_genre?id={$genres[$i]['id']}'>{$genres[$i]['name']}</a>";

        $placeholder = "{genre}";
        if($i < ($total_genres - 1)) {
          $genre = $genre . $placeholder;
        }

        $page = str_replace("{genre}", $genre, $page);
      }
    }

    // All Games.
    $total_games = $data["total_games"];
    $games = $data["games"];
    if($total_games <= 0) {
      $page = str_replace("{game}", "<i>No games available yet.</i>", $page);
    } else {
      for($i = 0; $i < $total_games; $i++) {
        $game = "<a href='/user/visit_game?id={$games[$i]['game_id']}'>";
        $game = $game . "\n  <div class='game'>";
        $game = $game . "\n    <img src='../{$games[$i]['icon']}' alt='game icon' />";
        $game = $game . "\n    <div class='game-info'>";
        $game = $game . "\n      <p>{$games[$i]['game_name']}</p>";
        $game = $game . "\n      <div class='rating'>";
        $game = $game . "\n        {rating_star}";
        $game = $game . "\n      </div>";

        $star2 = "";
        for($j = 0; $j < 5; $j++) {
          $star = "<i class='bx bxs-star'></i>";
          $star1 = "<i class='bx bx-star'></i>";
          $star2 = $j < $games[$i]["average_rating"] ? $star2 . $star : $star2 . $star1;
        }
        $game = str_replace("{rating_star}", $star2, $game);

        $game = $game . "\n    </div>";
        $game = $game . "\n  </div>";
        $game = $game . "\n</a>";

        $placeholder = "{game}";

        if($i < ($total_games - 1)) {
          $game = $game . $placeholder;
        }

        $page = str_replace("{game}", $game, $page);
      }
    }

    echo $page;
  }

  public static function visitGamePage($data) {
    $page = file_get_contents(Utilities::getPath() . "/view/page/user/visit-game.html");
    $top_bar = file_get_contents(Utilities::getPath() . "/view/page/user/top-bar.html");
    $top_bar = str_replace("{logo}", "../../res/img/logo-white.png", $top_bar);
    $top_bar = str_replace("{profile_image}", "../../" . $_SESSION["signed_in_acc"]["profile_image"], $top_bar);
    $page = str_replace("{top_bar}", $top_bar, $page);
    $page = str_replace("{csrf_token}", $data["csrf_token"], $page);

    $game = $data["game"];
    $page = str_replace("{game_id}", $game["id"], $page);
    $page = str_replace("{game_bg}", "../../" . $game["img"]["bg"], $page);
    $page = str_replace("{game_name}", $game["name"], $page);
    $page = str_replace("{game_dev}", $game["developer"], $page);
    $page = str_replace("{game_icon}", "../../" . $game["img"]["icon"], $page);
    $page = str_replace("{rating}", intval($game["rating"]) . "/5", $page);

    for($i = 0; $i < 5; $i++) {
      $star = "<i class='bx bxs-star'></i>";
      $star1 = "<i class='bx bx-star'></i>";
      $placeholder = "{rating_star}";

      $star2 = $i < $game["rating"] ? $star : $star1;

      if($i < (5 - 1)) {
        $star2 = $star2 . $placeholder;
      }

      $page = str_replace("{rating_star}", $star2, $page);
    }

    $page = str_replace("{game_dl_link}", $game["dl_link"], $page);
    $page = str_replace("{game_genre}", "Genre: " . $game["genres"], $page);
    $page = str_replace("{game_info}", $game["info"], $page);

    $i = 0;
    foreach($game["img"]["samples"] as $sample) {
      $page = str_replace("{game_sample$i}", "../../" . $sample, $page);
      $i++;
    }

    $page = str_replace("{game_id}", $_GET["id"], $page);
    $page = str_replace("{user_id}", $_SESSION["signed_in_acc"]["id"], $page);
    if($game["is_fav"]) {
      $page = str_replace("{is_fav}", "checked", $page);
      $page = str_replace("{fav_btn}", "<i id='fav_icon' class='bx bxs-bookmark'></i> Favorite", $page);
    } else {
      $page = str_replace("{is_fav}", "", $page);
      $page = str_replace("{fav_btn}", "<i id='fav_icon' class='bx bx-bookmark'></i> Favorite", $page);
    }

    $show_form = true;
    $reviews = $game["reviews"];
    if($reviews <= 0) {
      $page = str_replace("{review}", "<i>No reviews yet.</i>", $page);
    } else {
      $total = count($reviews);
      for($i = 0; $i < $total; $i++) {
        $review = "<li>";
        $review = $review . "\n  <div class='profile'>";
        $review = $review . "\n    <img src='../../{$reviews[$i]['dp']}' alt='dp' />";
        $review = $review . "\n  </div>";

        $review = $review . "\n  <div class='review-content'>";
        $review = $review . "\n    <div class='username'>{$reviews[$i]['username']}</div>";
        $review = $review . "\n    <span>{$reviews[$i]['comment']}</span>";
        $review = $review . "\n    <div class='stars'>{rating_star}</div>";

        for($j = 0; $j < 5; $j++) {
          $star = "<i class='bx bxs-star'></i>";
          $star1 = "<i class='bx bx-star'></i>";
          $placeholder = "{rating_star}";

          $star2 = $j < $reviews[$i]["rating"] ? $star : $star1;

          if($j < (5 - 1)) {
            $star2 = $star2 . $placeholder;
          }

          $review = str_replace("{rating_star}", $star2, $review);
        }

        $review = $review . "\n  </div>";
        if($_SESSION["signed_in_acc"]["id"] == $reviews[$i]["user_id"]) {
          $show_form = false;
          $review = $review . "\n    <form action='/user/visit_game/del_review' method='post'>";
          $review = $review . "\n      <input type='hidden' name='csrf_token' value='{$data['csrf_token']}' />";
          $review = $review . "\n      <input type='hidden' name='game_id' value='{$reviews[$i]['game_id']}' />";
          $review = $review . "\n      <input type='hidden' name='user_id' value='{$reviews[$i]['user_id']}' />";
          $review = $review . "\n      <input type='submit' id='del_review' name='del_review' value='Delete' />";
          $review = $review . "\n    </form>";
        }
        $review = $review . "\n</li>";
        // Code...
        $placeholder = "{review}";
        if($i < ($total - 1)) {
          $review = $review . "\n" . $placeholder;
        }

        $page = str_replace("{review}", $review, $page);
      }
    }

    if(!$show_form) {
      $page = str_replace("{review_form}", "", $page);
    } else {
      $form = "
      <form class='review-form'>
        <h3>Leave a Review</h3>
        <textarea id='comment' placeholder='Write your comment here...' rows='5'></textarea>
        <div class='star-rating'>
          <span class='rating-label'>Rating:</span>
          <i class='bx bx-star' data-value='1'></i>
          <i class='bx bx-star' data-value='2'></i>
          <i class='bx bx-star' data-value='3'></i>
          <i class='bx bx-star' data-value='4'></i>
          <i class='bx bx-star' data-value='5'></i>
        </div>
        <button id='submit_review' type='submit'>Submit Review</button>
      </form>
      ";

      $page = str_replace("{review_form}", $form, $page);
    }

    echo $page;
  }

  public static function gameGenrePage($data) {
    $page = file_get_contents(Utilities::getPath() . "/view/page/user/browse-by-genre.html");

    $top_bar = file_get_contents(Utilities::getPath() . "/view/page/user/top-bar.html");
    $top_bar = str_replace("{logo}", "../../res/img/logo-white.png", $top_bar);
    $top_bar = str_replace("{profile_image}", "../../" . $_SESSION["signed_in_acc"]["profile_image"], $top_bar);
    $page = str_replace("{top_bar}", $top_bar, $page);

    $page = str_replace("{genre_name}", $data["genre_name"], $page);

    $total_games = $data["total_games"];
    $games = $data["games"];
    if($total_games <= 0) {
      $page = str_replace("{game}", "<i>No games available yet.</i>", $page);
    } else {
      for($i = 0; $i < $total_games; $i++) {
        $game = "<a href='/user/visit_game?id={$games[$i]['id']}'>";
        $game = $game . "\n  <div class='game'>";
        $game = $game . "\n    <img src='../{$games[$i]['icon']}' alt='game icon' />";
        $game = $game . "\n    <div class='game-info'>";
        $game = $game . "\n      <p>{$games[$i]['name']}</p>";
        $game = $game . "\n      <div class='rating'>";
        $game = $game . "\n        {rating_star}";
        $game = $game . "\n      </div>";

        $star2 = "";
        for($j = 0; $j < 5; $j++) {
          $star = "<i class='bx bxs-star'></i>";
          $star1 = "<i class='bx bx-star'></i>";
          $star2 = $j < $games[$i]["rating"] ? $star2 . $star : $star2 . $star1;
        }
        $game = str_replace("{rating_star}", $star2, $game);

        $game = $game . "\n    </div>";
        $game = $game . "\n  </div>";
        $game = $game . "\n</a>";

        $placeholder = "{game}";

        if($i < ($total_games - 1)) {
          $game = $game . $placeholder;
        }

        $page = str_replace("{game}", $game, $page);
      }
    }


    echo $page;
  }

  public static function profilePage($data) {
    $page = file_get_contents(Utilities::getPath() . "/view/page/user/profile.html");

    $top_bar = file_get_contents(Utilities::getPath() . "/view/page/user/top-bar.html");
    $top_bar = str_replace("{logo}", "../../res/img/logo-white.png", $top_bar);
    $top_bar = str_replace("{profile_image}", "../../" . $data["user"]["profile_image"], $top_bar);
    $page = str_replace("{top_bar}", $top_bar, $page);

    $page = str_replace("{user_dp}", "../../" . $data["user"]["profile_image"], $page);
    $page = str_replace("{user_name}", $data["user"]["name"], $page);
    $page = str_replace("{user_username}", $data["user"]["username"], $page);
    $page = str_replace("{user_email}", $data["user"]["email"], $page);
    $page = str_replace("{csrf_token}", $data["csrf_token"], $page);

    $fav_games = $data["fav_games"];
    if($fav_games == 0 || $fav_games == false) {
      $page = str_replace("{fav_game}", "<i>No favorite games added yet.</i>", $page);
    } else {
      $total_games = count($fav_games);
      for($i = 0; $i < $total_games; $i++) {
        $game = "<a href='/user/visit_game?id={$fav_games[$i]['id']}'>";
        $game = $game . "\n  <div class='game'>";
        $game = $game . "\n    <img src='../{$fav_games[$i]['icon']}' alt='icon' />";
        $game = $game . "\n    <div class='game-info'>";
        $game = $game . "\n     <h2>{$fav_games[$i]['name']}</h2>";
        $game = $game . "\n    </div>";
        $game = $game . "\n  </div>";
        $game = $game . "\n</a>";

        $placeholder = "{fav_game}";

        if($i < ($total_games - 1)) {
          $game = $game . $placeholder;
        }

        $page = str_replace("{fav_game}", $game, $page);
      }
    }

    $visited_games = $data["visited_games"];
    if($visited_games == 0 || $visited_games == false) {
      $page = str_replace("{visited_game}", "<i>No visited games yet.</i>", $page);
    } else {
      $total_games = count($visited_games);
      for($i = 0; $i < $total_games; $i++) {
        $game = "<a href='/user/visit_game?id={$visited_games[$i]['id']}'>";
        $game = $game . "\n  <div class='game'>";
        $game = $game . "\n    <img src='../{$visited_games[$i]['icon']}' alt='icon' />";
        $game = $game . "\n    <div class='game-info'>";
        $game = $game . "\n     <h2>{$visited_games[$i]['name']}</h2>";
        $game = $game . "\n    </div>";
        $game = $game . "\n  </div>";
        $game = $game . "\n</a>";

        $placeholder = "{visited_game}";

        if($i < ($total_games - 1)) {
          $game = $game . $placeholder;
        }

        $page = str_replace("{visited_game}", $game, $page);
      }
    }



    echo $page;
  }
}