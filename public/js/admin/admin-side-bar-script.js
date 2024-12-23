$(document).ready(function () {
  let is_menu_open = false;
  $("#menu").click(function() {
    if(is_menu_open) {
      $("#side_nav").css("opacity", "0");
      $("#side_nav").css("transform", "translateX(-30vw)");
      setTimeout(() => {
        $("#side_nav").css("display", "none");
        $("body").css("overflow", "scroll");
      }, 200);

      is_menu_open = false;
    } else {
      $("body").css("overflow", "hidden");
      $("#side_nav").css("display", "block");
      setTimeout(() => {
        $("#side_nav").css("opacity", "1");
        $("#side_nav").css("transform", "translateX(0)");
      }, 200);

      is_menu_open = true;
    }
  });

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