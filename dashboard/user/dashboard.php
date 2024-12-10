<?php
include_once __DIR__ . "/../../config/settings.php";
include_once __DIR__ . "/../Authentication.php";

Authentication::checkAccNotLoggedIn("../../page/index.php");
Authentication::redirectAdminPage();

require_once __DIR__ . "/../../config/Database.php";
$db = Database::connect();

$account = $_SESSION["signed_in_acc"];

$dp_path = "../../src/uploads/users_images/{$account['profile_image']}";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../src/css/userdashboard.css">
    <link rel="icon" href="../../src/img/voidlogo.png.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
    <title>Home | Void</title>
</head>
<body>
    <div class="Section-top">
        <div class="navbar">
            <div class="navbar-container">
                <div class="logo-container">
                   <!-- <img src="../src/img/logo.png" alt="">-->
                </div>
                <div class="logo-name">
                   <!-- <h1>VOID</h1>-->
                </div>
                <div class="menu-container">
                    <ul class="menu-list">
                        <li class="menu-list-item active"><a href="#">Home</a></li>
                        <li class="menu-list-item"><a href="#">Favorites</a></li>
                        <li class="menu-list-item">
                            <a href="#">Genres <i class="fas fa-caret-down"></i> </a>
                            <ul class="dropdown">
                                <li><a href="#role_play_games">RPG</a></li>
                                <li><a href="#horror_games">Horror</a></li>
                                <li><a href="#shooting_games">Shooting</a></li>
                                <li><a href="#racing_games">Racing</a></li>
                            </ul>
                        </li>
                </div>
                <div class="profile-container">
                    <a href="./userprofile.php"><img class="profile-picture" src="<?= $dp_path ?>" alt="Profile Picture"></a>
                    <div class="profile-text-container">
                        <span class="profile-text">Profile</span>
                        <i class="fas fa-caret-down"></i>
                    </div>
                    <div class="toggle">
                        <i class="fas fa-moon toggle-icon"></i>
                        <i class="fas fa-sun toggle-icon"></i>
                        <div class="toggle-ball"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="sidebar">
            <i class="left-menu-icon fas fa-home"></i>
            <i class="left-menu-icon fas fa-users"></i>
            <i class="left-menu-icon fas fa-bookmark"></i>

        </div>
        <div class="container">
            <div class="content-container">
                <div class="featured-content"
                    style="background: linear-gradient(to bottom, rgba(0,0,0,0), #151515), url('../../src/uploads/games_images/wuwa1.jpg')no-repeat center center /cover;">
                    <img class="featured-title" src="../../src/uploads/games_images/wuwatitle.png" alt="wuwa">
                </div>
                <div class="game-list-container">
                    <h1 class="game-list-title" id="role_play_games">ROLE PLAY GAMES</h1>
                    <div class="game-list-wrapper">
                        <div class="game-list">
                          <?php
                            $stmt = $db->prepare(
                              "SELECT `game`.`id`, `game`.`name`, `game`.`info`, `game`.`game_image` AS 'image' FROM `game`
                              INNER JOIN `game_genre` ON `game`.`id` = `game_genre`.`game_id`
                              INNER JOIN `genre` ON `game_genre`.`genre_id` = `genre`.`id`
                              WHERE `genre`.`name` = 'role play';"
                            );
                            if(!($stmt->execute())) {
                              http_response_code(500);
                              echo
                              "<script>
                                alert(\"An error occured on fetching all games.\");
                                window.location.href = \"./\";
                              </script>";
                              exit();
                            }

                            $games = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            for($i = 0; $i < $stmt->rowCount(); $i++) {
                              echo
                              "<div class='game-list-item'>
                                <img class='game-list-item-img' src=\"../../src/uploads/games_images/{$games[$i]['image']}\" alt=\"{$games[$i]['image']}\">
                                <span class='game-list-item-title'>{$games[$i]['name']}</span>
                                <p class='game-list-item-desc'>{$games[$i]['info']}</p>
                                <a href=\"./game.php?id={$games[$i]['id']}\">
                                  <button class='game-list-item-button'>View</button>
                                </a>
                              </div>";
                            }
                          ?>
                        </div>
                        <i class="fas fa-chevron-right arrow"></i>
                    </div>
                </div>
                <div class="game-list-container">
                    <h1 class="game-list-title" id="horror_games">HORROR</h1>
                    <div class="game-list-wrapper">
                        <div class="game-list">
                          <?php
                            $stmt = $db->prepare(
                              "SELECT `game`.`id`, `game`.`name`, `game`.`info`, `game`.`game_image` AS 'image' FROM `game`
                              INNER JOIN `game_genre` ON `game`.`id` = `game_genre`.`game_id`
                              INNER JOIN `genre` ON `game_genre`.`genre_id` = `genre`.`id`
                              WHERE `genre`.`name` = 'horror';"
                            );
                            if(!($stmt->execute())) {
                              http_response_code(500);
                              echo
                              "<script>
                                alert(\"An error occured on fetching all games.\");
                                window.location.href = \"./\";
                              </script>";
                              exit();
                            }

                            $games = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            for($i = 0; $i < $stmt->rowCount(); $i++) {
                              echo
                              "<div class='game-list-item'>
                                <img class='game-list-item-img' src=\"../../src/uploads/games_images/{$games[$i]['image']}\" alt=\"{$games[$i]['image']}\">
                                <span class='game-list-item-title'>{$games[$i]['name']}</span>
                                <p class='game-list-item-desc'>{$games[$i]['info']}</p>
                                <a href=\"./game.php?id={$games[$i]['id']}\">
                                  <button class='game-list-item-button'>View</button>
                                </a>
                              </div>";
                            }
                          ?>
                            
                        </div>
                        <i class="fas fa-chevron-right arrow"></i>
                    </div>
                </div>
                <div class="featured-content"
                    style="background: linear-gradient(to bottom, rgba(0,0,0,0), #151515), url('../../src/uploads/games_images/valorant.avif') no-repeat center center /cover;">
                    <img class="featured-title" src="../../src/uploads/games_images/valorant1.webp" alt="Valorant">
                </div>
                <div class="game-list-container">
                    <h1 class="game-list-title" id="shooting_games">SHOOTING</h1>
                    <div class="game-list-wrapper">
                        <div class="game-list">
                          <?php
                            $stmt = $db->prepare(
                              "SELECT `game`.`id`, `game`.`name`, `game`.`info`, `game`.`game_image` AS 'image' FROM `game`
                              INNER JOIN `game_genre` ON `game`.`id` = `game_genre`.`game_id`
                              INNER JOIN `genre` ON `game_genre`.`genre_id` = `genre`.`id`
                              WHERE `genre`.`name` = 'shooting';"
                            );
                            if(!($stmt->execute())) {
                              http_response_code(500);
                              echo
                              "<script>
                                alert(\"An error occured on fetching all games.\");
                                window.location.href = \"./\";
                              </script>";
                              exit();
                            }

                            $games = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            for($i = 0; $i < $stmt->rowCount(); $i++) {
                              echo
                              "<div class='game-list-item'>
                                <img class='game-list-item-img' src=\"../../src/uploads/games_images/{$games[$i]['image']}\" alt=\"{$games[$i]['image']}\">
                                <span class='game-list-item-title'>{$games[$i]['name']}</span>
                                <p class='game-list-item-desc'>{$games[$i]['info']}</p>
                                <a href=\"./game.php?id={$games[$i]['id']}\">
                                  <button class='game-list-item-button'>View</button>
                                </a>
                              </div>";
                            }
                          ?>
                        </div>
                        <i class="fas fa-chevron-right arrow"></i>
                    </div>
                </div>
                <div class="game-list-container">
                    <h1 class="game-list-title" id="racing_games">RACING</h1>
                    <div class="game-list-wrapper">
                        <div class="game-list">
                          <?php
                            $stmt = $db->prepare(
                              "SELECT `game`.`id`, `game`.`name`, `game`.`info`, `game`.`game_image` AS 'image' FROM `game`
                              INNER JOIN `game_genre` ON `game`.`id` = `game_genre`.`game_id`
                              INNER JOIN `genre` ON `game_genre`.`genre_id` = `genre`.`id`
                              WHERE `genre`.`name` = 'racing';"
                            );
                            if(!($stmt->execute())) {
                              http_response_code(500);
                              echo
                              "<script>
                                alert(\"An error occured on fetching all games.\");
                                window.location.href = \"./\";
                              </script>";
                              exit();
                            }

                            $games = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            for($i = 0; $i < $stmt->rowCount(); $i++) {
                              echo
                              "<div class='game-list-item'>
                                <img class='game-list-item-img' src=\"../../src/uploads/games_images/{$games[$i]['image']}\" alt=\"{$games[$i]['image']}\">
                                <span class='game-list-item-title'>{$games[$i]['name']}</span>
                                <p class='game-list-item-desc'>{$games[$i]['info']}</p>
                                <a href=\"./game.php?id={$games[$i]['id']}\">
                                  <button class='game-list-item-button'>View</button>
                                </a>
                              </div>";
                            }
                          ?>
                        </div>
                        <i class="fas fa-chevron-right arrow"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../../src/js/user-dashboard.js"></script>
</body>
</html>