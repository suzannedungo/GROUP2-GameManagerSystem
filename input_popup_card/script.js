$(document).on("click", "#edit_profile", function(event) {
  event.preventDefault();

  $(this).attr("disabled", "");
  $("#cancel_form").attr("disabled", "");
  $("#popup_input").attr("disabled", "");

  inputPopdownCard();
});

$(document).on("click", "#cancel_form", function(event) {
  event.preventDefault();

  $(this).attr("disabled", "");
  $("#edit_profile").attr("disabled", "");
  $("#popup_input").attr("disabled", "");

  inputPopdownCard();
});

function replacePlaceholders(source, input_type, input_name) {
  source = source.replace("{input_type}", input_type);
  source = source.replace("{input_name}", input_name);
  source = source.replace("{input_name}", "Enter " + input_name);

  return source;
}

function inputPopupCard(content) {
  $("#input_popup_wrapper").html(content);
  $("#input_popup_wrapper").css("display", "flex");
  setTimeout(function() {
    $("#input_popup_wrapper").css("bottom", "1vh");
    $("#input_popup_wrapper").css("opacity", "1");
  }, 100);
}

function inputPopdownCard() {
  setTimeout(function() {
    $("#input_popup_wrapper").css("opacity", "0");
    $("#input_popup_wrapper").css("bottom", "-10vh");
    setTimeout(function() {
      $("#input_popup_wrapper").css("display", "none");
      $("#input_popup_wrapper").html("");
    }, 500);
  }, 100);
}