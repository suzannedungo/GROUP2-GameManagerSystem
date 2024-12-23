$(document).ready(function() {
  let is_swipe = false;
  let login_is_show_pass = false;
  let register_is_show_pass = false;

  $("#login_show_pass").click(function() {
    login_is_show_pass = login_is_show_pass ? hidePassword("#login_pass") : showPassword("#login_pass");
  });

  $("#register_show_pass").click(function() {
    register_is_show_pass = register_is_show_pass ? hidePassword("#register_pass") : showPassword("#register_pass");
  });

  $(".loginBtn").click(function() {
    login();
  });

  $(".registerBtn").click(function() {
    register();
  });

  function register() {
    if(!is_swipe) {
      $("#login_container").toggleClass("opacity-1");
      $("#login_container").toggleClass("opacity-0");
      $("#login_container").toggleClass("translatex-0");
      $("#login_container").toggleClass("translatex-n100pc");
      setTimeout(() => {
        $("#login_container").toggleClass("d-flex");
        $("#login_container").toggleClass("d-none");

        $("#register_container").toggleClass("d-none");
        $("#register_container").toggleClass("d-flex");
        
        setTimeout(() => {
          $("#register_container").toggleClass("opacity-0");
          $("#register_container").toggleClass("opacity-1");
          $("#register_container").toggleClass("translatex-100pc");
          $("#register_container").toggleClass("translatex-0");
        }, 500);
      }, 500);

      is_swipe = true;
    }
  }

  function login() {
    if(is_swipe) {
      $("#register_container").toggleClass("opacity-1");
      $("#register_container").toggleClass("opacity-0");
      $("#register_container").toggleClass("translatex-0");
      $("#register_container").toggleClass("translatex-100pc");
      setTimeout(() => {
        $("#register_container").toggleClass("d-flex");
        $("#register_container").toggleClass("d-none");

        $("#login_container").toggleClass("d-none");
        $("#login_container").toggleClass("d-flex");
        
        setTimeout(() => {
          $("#login_container").toggleClass("opacity-0");
          $("#login_container").toggleClass("opacity-1");
          $("#login_container").toggleClass("translatex-n100pc");
          $("#login_container").toggleClass("translatex-0");
        }, 500);
      }, 500);

      is_swipe = false;
    }
  }
});