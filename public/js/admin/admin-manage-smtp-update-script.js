$(document).ready(function () {
  let is_pass_show = false;
  $("#show_pass").click(function() {
    is_pass_show = is_pass_show ? hidePassword("#password") : showPassword("#password");
  });
});