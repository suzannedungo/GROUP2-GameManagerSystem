$(document).ready(function() {
  disableGenreButtons();

  $(document).on("click", ".genre", function() {
    enableGenreButtons();
  });

  $("#edit_genre_btn").click(function() {
    $.get("../../input_popup_card/popup-card.html", function(response) {
      inputPopupCard(replacePlaceholders(response, "text", "name"));
    });
  });

  $(document).on("click", "#edit_profile", function(event) {
    event.preventDefault();
    $.post(
      "../game-class.php", 
      {
        edit_genre : true,
        id : $()
        name : $("#popup_input").val()
      },
      function(response) {
        popupCard(response);
      }
    );
  });

  function enableGenreButtons() {
    $("#edit_genre_btn").removeAttr("disabled");
    $("#delete_genre_btn").removeAttr("disabled");
  }

  function disableGenreButtons() {
    $("#edit_genre_btn").attr("disabled", "");
    $("#delete_genre_btn").attr("disabled", "");
  }
});