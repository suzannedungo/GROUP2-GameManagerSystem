<?php

namespace App\View;

use App\Core\Utilities;

class AdminPageView {
  public static function dashboardPage($data) {
    $page = file_get_contents(Utilities::getPath() . "/view/page/admin/dashboard.html");

    $side_bar = file_get_contents(Utilities::getPath() . "/view/page/admin/side-bar.html");
    $side_bar = str_replace("{logo}", "../res/img/logo-white.png", $side_bar);
    $page = str_replace("{side_bar}", $side_bar, $page);

    $page = str_replace("{total_genre}", $data["total_genres"], $page);
    $page = str_replace("{total_game}", $data["total_games"], $page);
    $page = str_replace("{total_user}", $data["total_users"], $page);

    $users = $data["recent_users"];
    if($users <= 0) {
      $page = str_replace("{user}", "<i>No user added recently.</i>", $page);
    } else {
      $total_users = count($users);
      for($i = 0; $i < $total_users; $i++) {
        $user = "<div class='user'>";
        $user = $user . "\n  <img src='../{$users[$i]['dp']}' alt='profile image' />";
        $user = $user . "\n  <div class='user-info'>";
        $user = $user . "\n   <h2>{$users[$i]['name']}</h2>";
        $user = $user . "\n   <h3>{$users[$i]['username']}</h3>";
        $user = $user . "\n  </div>";
        $user = $user . "\n</div>";

        $placeholder = "{user}";

        if($i < ($total_users - 1)) {
          $user = $user . $placeholder;
        }

        $page = str_replace("{user}", $user, $page);
      }
    }

    $games = $data["recent_games"];
    if($games <= 0) {
      $page = str_replace("{game}", "<i>No game added recently.</i>", $page);
    } else {
      $total_games = count($games);
      for($i = 0; $i < $total_games; $i++) {
        $game = "<div class='game'>";
        $game = $game . "\n  <img src='../{$games[$i]['icon']}' alt='icon' />";
        $game = $game . "\n  <div class='game-info'>";
        $game = $game . "\n   <h2>{$games[$i]['name']}</h2>";
        $game = $game . "\n  </div>";
        $game = $game . "\n</div>";

        $placeholder = "{game}";

        if($i < ($total_games - 1)) {
          $game = $game . $placeholder;
        }

        $page = str_replace("{game}", $game, $page);
      }
    }

    echo $page;
  }

  public static function manageGenresPage($data, $csrf_token) {
    $page = file_get_contents(Utilities::getPath() . "/view/page/admin/manage-genres.html");
    $side_bar = file_get_contents(Utilities::getPath() . "/view/page/admin/side-bar.html");
    $side_bar = str_replace("{logo}", "../res/img/logo-white.png", $side_bar);
    $page = str_replace("{side_bar}", $side_bar, $page);
    $page = str_replace("{csrf_token}", $csrf_token, $page);

    $total_genre = $data["total_genre"];
    $genre_names = $data["genres"];

    if($total_genre === 0) {
      $page = str_replace("{genre}", "<i>No available genres yet.</i>", $page);
    } else {
      for($i = 0; $i < $total_genre; $i++) {
        $genres = "<div class='genre'>{$genre_names[$i]['name']}</div>";
        $placeholder = "{genre}";

        if($i < ($total_genre - 1)) {
          $genres = $genres . "\n" . $placeholder;
        }

        $page = str_replace("{genre}", $genres, $page);
      }
    }

    echo $page;
  }

  public static function editGenrePage($data, $csrf_token) {
    $page = file_get_contents(Utilities::getPath() . "/view/page/admin/manage-genres-edit.html");
    $side_bar = file_get_contents(Utilities::getPath() . "/view/page/admin/side-bar.html");
    $side_bar = str_replace("{logo}", "../../res/img/logo-white.png", $side_bar);
    $page = str_replace("{side_bar}", $side_bar, $page);
    $page = str_replace("{csrf_token}", $csrf_token, $page);

    $total_genre = $data["total_genre"];
    $genres = $data["genres"];
    for($i = 0; $i < $total_genre; $i++) {
      $option = "<option value='{$genres[$i]['id']}'>{$genres[$i]['name']}</option>";
      $placeholder = "{option}";

      if($i < ($total_genre - 1)) {
        $option = $option . "\n" . $placeholder;
      }

      $page = str_replace("{option}", $option, $page);
    }
    
    echo $page;
  }

  public static function deleteGenrePage($data, $csrf_token) {
    $page = file_get_contents(Utilities::getPath() . "/view/page/admin/manage-genres-delete.html");
    $side_bar = file_get_contents(Utilities::getPath() . "/view/page/admin/side-bar.html");
    $side_bar = str_replace("{logo}", "../../res/img/logo-white.png", $side_bar);
    $page = str_replace("{side_bar}", $side_bar, $page);
    $page = str_replace("{csrf_token}", $csrf_token, $page);

    $total_genre = $data["total_genre"];
    $genres = $data["genres"];
    for($i = 0; $i < $total_genre; $i++) {
      $option = "<option value='{$genres[$i]['id']}'>{$genres[$i]['name']}</option>";
      $placeholder = "{option}";

      if($i < ($total_genre - 1)) {
        $option = $option . "\n" . $placeholder;
      }

      $page = str_replace("{option}", $option, $page);
    }
    
    echo $page;
  }

  public static function manageGamesPage($data) {
    $page = file_get_contents(Utilities::getPath() . "/view/page/admin/manage-games.html");
    $side_bar = file_get_contents(Utilities::getPath() . "/view/page/admin/side-bar.html");
    $side_bar = str_replace("{logo}", "../res/img/logo-white.png", $side_bar);
    $page = str_replace("{side_bar}", $side_bar, $page);

    $total_games = $data["total_games"];
    $games = $data["games"];
    if($total_games <= 0) {
      $page = str_replace("{game}", "<i>No games available yet.</i>", $page);
    } else {
      for($i = 0; $i < $total_games; $i++) {
        // $game = "<div class='game'>";
        $game = "<a href='/admin/manage_games/preview_game?id={$games[$i]['game_id']}'>";
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

        // $game = $game . "\n      <a href='/admin/manage_games/preview_game?id={$games[$i]['game_id']}'>View</a>";
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

  public static function addGamePage($data, $csrf_token) {
    $page = file_get_contents(Utilities::getPath() . "/view/page/admin/manage-games-add.html");
    $side_bar = file_get_contents(Utilities::getPath() . "/view/page/admin/side-bar.html");
    $side_bar = str_replace("{logo}", "../../res/img/logo-white.png", $side_bar);
    $page = str_replace("{side_bar}", $side_bar, $page);
    $page = str_replace("{csrf_token}", $csrf_token, $page);

    $total_genres = $data["total_genres"];
    $genres = $data["genres"];

    if($total_genres <= 0) {
      $page = str_replace("{genre}", "", $page);
    } else {
      $input_genres = "<label>Pick At Least 1 Genre:</label>";
      // $input_genres = $input_genres . "\n<br />";

      for($i = 0; $i < $total_genres ; $i++) {
        $input_genres = $input_genres . 
        "\n
        <label>
          <input type='checkbox' name='genres[]' class='genres' value='{$genres[$i]['id']}'/>
          {$genres[$i]['name']}
        <label>\n<br />";
      }

      $page = str_replace("{genre}", $input_genres, $page);
    }

    echo $page;
  }

  public static function previewGamePage($data) {
    $page = file_get_contents(Utilities::getPath() . "/view/page/admin/manage-games-preview.html");
    $side_bar = file_get_contents(Utilities::getPath() . "/view/page/admin/side-bar.html");
    $side_bar = str_replace("{logo}", "../../res/img/logo-white.png", $side_bar);
    $page = str_replace("{side_bar}", $side_bar, $page);

    $page = str_replace("{game_id}", $data["id"], $page);
    $page = str_replace("{game_bg}", "../../" . $data["img"]["bg"], $page);
    $page = str_replace("{game_name}", $data["name"], $page);
    $page = str_replace("{game_dev}", $data["developer"], $page);
    $page = str_replace("{game_icon}", "../../" . $data["img"]["icon"], $page);
    $page = str_replace("{rating}", intval($data["rating"]) . "/5", $page);

    for($i = 0; $i < 5; $i++) {
      $star = "<i class='bx bxs-star'></i>";
      $star1 = "<i class='bx bx-star'></i>";
      $placeholder = "{rating_star}";

      $star2 = $i < $data["rating"] ? $star : $star1;

      if($i < (5 - 1)) {
        $star2 = $star2 . $placeholder;
      }

      $page = str_replace("{rating_star}", $star2, $page);
    }

    $page = str_replace("{game_dl_link}", $data["dl_link"], $page);
    $page = str_replace("{game_genre}", "Genre: " . $data["genres"], $page);
    $page = str_replace("{game_info}", $data["info"], $page);

    $i = 0;
    foreach($data["img"]["samples"] as $sample) {
      $page = str_replace("{game_sample$i}", "../../" . $sample, $page);
      $i++;
    }
    echo $page;
  }

  public static function editGamePage($data, $csrf_token) {
    $page = file_get_contents(Utilities::getPath() . "/view/page/admin/manage-games-edit.html");
    $side_bar = file_get_contents(Utilities::getPath() . "/view/page/admin/side-bar.html");
    $side_bar = str_replace("{logo}", "../../res/img/logo-white.png", $side_bar);
    $page = str_replace("{side_bar}", $side_bar, $page);
    $page = str_replace("{csrf_token}", $csrf_token, $page);

    $total_games = $data["total_games"];
    $games = $data["games"];
    $game_options = "";
    for($i = 0; $i < $total_games; $i++) {
      $game_options = $game_options . 
      "\n
      <option value='{$games[$i]['id']}'>{$games[$i]['name']}</option>\n";
    }
    $page = str_replace("{game_option}", $game_options, $page);

    $total_genres = $data["total_genres"];
    $genres = $data["genres"];

    if($total_genres <= 0) {
      $page = str_replace("{genre}", "", $page);
    } else {
      $input_genres = "<label>Pick At Least 1 Genre:</label>";

      for($i = 0; $i < $total_genres ; $i++) {
        $input_genres = $input_genres . 
        "\n
        <label>
          <input type='checkbox' name='genres[]' class='genres' value='{$genres[$i]['id']}'/>
          {$genres[$i]['name']}
        <label>\n<br />";
      }

      $page = str_replace("{genre}", $input_genres, $page);
    }

    echo $page;
  }

  public static function deleteGamePage($data, $csrf_token) {
    $page = file_get_contents(Utilities::getPath() . "/view/page/admin/manage-games-delete.html");
    $side_bar = file_get_contents(Utilities::getPath() . "/view/page/admin/side-bar.html");
    $side_bar = str_replace("{logo}", "../../res/img/logo-white.png", $side_bar);
    $page = str_replace("{side_bar}", $side_bar, $page);
    $page = str_replace("{csrf_token}", $csrf_token, $page);

    $total_games = $data["total_games"];
    $games = $data["games"];
    for($i = 0; $i < $total_games; $i++) {
      $option = "<option value='{$games[$i]['id']}'>{$games[$i]['name']}</option>";
      $placeholder = "{option}";

      if($i < ($total_games- 1)) {
        $option = $option . "\n" . $placeholder;
      }

      $page = str_replace("{option}", $option, $page);
    }
    
    echo $page;
  }

  public static function manageUsersPage($data, $csrf_token) {
    $page = file_get_contents(Utilities::getPath() . "/view/page/admin/manage-users.html");
    $side_bar = file_get_contents(Utilities::getPath() . "/view/page/admin/side-bar.html");
    $side_bar = str_replace("{logo}", "../res/img/logo-white.png", $side_bar);
    $page = str_replace("{side_bar}", $side_bar, $page);


    $total_users = $data["total_users"];
    $users = $data["users"];
    if($total_users <= 0) {
      $page = str_replace("{table}", "<i>No verified and banned users yet.</i>", $page);
    } else {
      $table = "<table id='user_table'>";
      $table = $table . "\n  <thead>";
      $table = $table . "\n    <tr>";
      $table = $table . "\n     <td>UID</td>";
      $table = $table . "\n     <td>Name</td>";
      $table = $table . "\n     <td>Username</td>";
      $table = $table . "\n     <td>Email</td>";
      $table = $table . "\n     <td>Action</td>";
      $table = $table . "\n    </tr>";
      $table = $table . "\n  </thead>";

      $table = $table . "\n  <tbody>";

      for($i = 0; $i < $total_users; $i++) {
        $table = $table . "\n    <tr>";
        $table = $table . "\n     <td>{$users[$i]["id"]}</td>";
        $table = $table . "\n     <td>{$users[$i]["name"]}</td>";
        $table = $table . "\n     <td>{$users[$i]["username"]}</td>";
        $table = $table . "\n     <td>{$users[$i]["email"]}</td>";

        $table = $table . "\n     <td>";

        if($users[$i]["status"] == "ban") {
          $table = $table . "\n       <form action='/admin/manage_users/unban_user' method='post'>";
          $table = $table . "\n         <input type='hidden' name='csrf_token' value='{$csrf_token}' />";
          $table = $table . "\n         <input type='hidden' name='email' value='{$users[$i]['email']}' />";
          $table = $table . "\n         <input type='submit' name='unban' value='Unban' />";
          $table = $table . "\n       </form>";
        } else {
          $table = $table . "\n       <form action='/admin/manage_users/ban_user' method='post'>";
          $table = $table . "\n         <input type='hidden' name='csrf_token' value='{$csrf_token}' />";
          $table = $table . "\n         <input type='hidden' name='email' value='{$users[$i]['email']}' />";
          $table = $table . "\n         <input type='submit' name='ban' value='Ban' />";
          $table = $table . "\n       </form>";
        }
        $table = $table . "\n     </td>";
        $table = $table . "\n    </tr>";
      }
      $table = $table . "\n  </tbody>";
      $table = $table . "\n</table>";

      $page = str_replace("{table}", $table, $page);
    }

    echo $page;
  }

  public static function manageSMTPPage($data, $csrf_token) {
    $page = file_get_contents(Utilities::getPath() . "/view/page/admin/manage-smtp.html");
    $side_bar = file_get_contents(Utilities::getPath() . "/view/page/admin/side-bar.html");
    $side_bar = str_replace("{logo}", "../res/img/logo-white.png", $side_bar);
    $page = str_replace("{side_bar}", $side_bar, $page);
    $page = str_replace("{csrf_token}", $csrf_token, $page);
    $page = str_replace("{admin_email}", $_SESSION["signed_in_acc"]["email"], $page);

    $page = str_replace("{smtp_user_name}", $data["name"], $page);
    $page = str_replace("{smtp_user_email}", $data["email"], $page);
    $page = str_replace("{smtp_user_password}", $data["password"], $page);

    $date_updated = !$data["date_updated"] ? "<i>Not Yet Updated.</i>" : $data["date_updated"];
    $page = str_replace("{smtp_user_date_updated}", $date_updated, $page);

    echo $page;
  }

  public static function updateSMTPPage($csrf_token) {
    $page = file_get_contents(Utilities::getPath() . "/view/page/admin/manage-smtp-update.html");
    $side_bar = file_get_contents(Utilities::getPath() . "/view/page/admin/side-bar.html");
    $side_bar = str_replace("{logo}", "../../res/img/logo-white.png", $side_bar);
    $page = str_replace("{side_bar}", $side_bar, $page);
    $page = str_replace("{csrf_token}", $csrf_token, $page);

    echo $page;
  }

  public static function profilePage() {
    $page = file_get_contents(Utilities::getPath() . "/view/page/admin/profile.html");

    $side_bar = file_get_contents(Utilities::getPath() . "/view/page/admin/side-bar.html");
    $side_bar = str_replace("{logo}", "../res/img/logo-white.png", $side_bar);
    $page = str_replace("{side_bar}", $side_bar, $page);

    $page = str_replace("{admin_dp}", "../" . $_SESSION["signed_in_acc"]["profile_image"], $page);
    $page = str_replace("{csrf_token}", $_SESSION["csrf_token"], $page);
    $page = str_replace("{admin_name}", $_SESSION["signed_in_acc"]["name"], $page);
    $page = str_replace("{admin_email}", $_SESSION["signed_in_acc"]["email"], $page);

    echo $page;
  }

  public static function updateProfilePage($data, $csrf_token) {
    $page = file_get_contents(Utilities::getPath() . "/view/page/admin/profile-edit.html");

    $side_bar = file_get_contents(Utilities::getPath() . "/view/page/admin/side-bar.html");
    $side_bar = str_replace("{logo}", "../../res/img/logo-white.png", $side_bar);
    $page = str_replace("{side_bar}", $side_bar, $page);
    $page = str_replace("{csrf_token}", $csrf_token, $page);
    $page = str_replace("{id}", $_SESSION["signed_in_acc"]["id"], $page);
    $page = str_replace("{type}", $data["type"], $page);
    $page = str_replace("{edit}", $data["name"], $page);

    if($data["name"] == "dp") {
      $page = str_replace("{enctype}", "enctype='multipart/form-data'", $page);
    } else {
      $page = str_replace("{enctype}", "", $page);
    }

    echo $page;
  }

  public static function resetPasswordPage($csrf_token) {
    $page = file_get_contents(Utilities::getPath() . "/view/page/admin/resetpass.html");
    $page = str_replace("{csrf_token}", $csrf_token, $page);

    echo $page;
  }
}