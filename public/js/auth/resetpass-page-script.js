$(document).ready(function () {
  let is_show_pass = false;

  $("#show_pass").click(function() {
    is_show_pass = is_show_pass ? hidePassword(".input-field") : showPassword(".input-field");
  })
});