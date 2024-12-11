<?php
include_once __DIR__ . "/../../config/settings.php";
include_once __DIR__ . "/../Authentication.php";

Authentication::checkAccNotLoggedIn("../../page/index.php");
Authentication::redirectUserPage();

$account = $_SESSION["signed_in_user"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
</head>
<body>
  <h1>Hi Admin <?= $account['name'] ?>!</h1>
  <a href="../Authentication.php?logout">Logout</a>

  <div>
    <h1>Add Game</h1>
    <form action="../game-class.php" method="POST" enctype="multipart/form-data">
      <label for="name">Name: </label>
      <input type="text" name="name" id="name" placeholder="Name" required />
      <br/>
      <label for="developer">Developer: </label>
      <input type="text" name="developer" id="developer" placeholder="Developer" required />
      <br/>
      <label for="info">Info: </label>
      <textarea name="info" id="info" placeholder="Information" required></textarea>
      <br/>
      <label for="dl_link">Download Link: </label>
      <input type="text" name="dl_link" id="dl_link" placeholder="Download Link" required />
      <br />
      <label for="icon">Choose Icon: </label>
      <input type="file" name="icon" id="icon" accept=".jpg,.jpeg,.png" required />
      <br />
      <label for="bg">Choose Background Image: </label>
      <input type="file" name="bg" id="bg" accept=".jpg,.jpeg,.png" required />
      <br />
      <label for="samples[]">Choose 5 Samples: </label>
      <input type="file" name="samples[]" id="samples" multiple accept=".jpg,.jpeg,.png" required />
      <br />
      <input type="submit" name="add_game" id="add_game" value="Add Game" />
    </form>
  </div>

  <hr />
</body>
</html>