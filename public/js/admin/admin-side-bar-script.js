$(document).ready(function () {
  let nav = window.location.pathname.split("/").pop();

  switch(nav) {
    case "": case "dashboard":
      $("#dashboard_nav").toggleClass("selected");
      break;

    case "manage_genres": case "edit_genre": case "delete_genre":
      $("#manage_genres_nav").toggleClass("selected");
      break;

    case "manage_games": case "add_game": case "preview_game": case "edit_game": case "delete_game":
      $("#manage_games_nav").toggleClass("selected");
      break;

    case "manage_users":
      $("#manage_users_nav").toggleClass("selected");
      break;

    case "manage_smtp":
      $("#manage_smtp_nav").toggleClass("selected");
      break;

    case "profile":
      $("#profile_nav").toggleClass("selected");
      break;
  }
});