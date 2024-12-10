function popupCard(content) {
  $.get("../../popup_card/popup-card.html", function(response) {
    // response = response.replace("{title}", "Title Sample");
    response = response.replace("{content}", content);

    $("#popup_wrapper").html(response);
    $("#popup_wrapper").css("display", "flex");
    setTimeout(function() {
      $("#popup_wrapper").css("bottom", "1vh");
      $("#popup_wrapper").css("opacity", "1");
    }, 500);

    setTimeout(function() {
      $("#popup_wrapper").css("opacity", "0");
      $("#popup_wrapper").css("bottom", "-10vh");
      setTimeout(function() {
        $("#popup_wrapper").css("display", "none");
        $("#popup_wrapper").html("");
      }, 500);
    }, 3000);
  });
}