$(document).ready(function() {
  $("#favorite").click(function() {
    if($(this).is(":checked")) {
      $.post(
        "../../dashboard/user/game-class.php",
        {
          addFav: true,
          gid: $("#game_id").val(),
          uid: $("#user_id").val()
        },
        function() {
          alert("Added to your favorites.");
        }
      );
    } else {
      $.post(
        "../../dashboard/user/game-class.php",
        {
          _method: "DELETE",
          delFav: true,
          gid: $("#game_id").val(),
          uid: $("#user_id").val()
        },
        function() {
          alert("Removed from your favorites.");
        }
      );
    }
  });

  let has_review = Boolean($("#has_review").val());
  getAllReviews();

  $("#submit_review").click(function(event) {
    event.preventDefault();

    let rating = $("#rating");

    if(rating.val() == null || rating.val() <= 0 || rating.val() == "") {
      $("#error_message").html("Do not leave rating field blank.");
    } else {
      $("#error_message").html("");
      $.post(
        "../../dashboard/user/game-class.php",
        {
          submit_review: true,
          gid: $("#game_id").val(),
          uid: $("#user_id").val(),
          rating: $("#rating").val(),
          comment: $("#comment").val()
        },
        function() {
          has_review = true;
        }
      );
    }

    $("#rating").val("");
    $("#comment").val("");

    getAllReviews();
  });

  $(document).on("click", "#delete_review", function() {
    $.post(
      "../../dashboard/user/game-class.php",
      {
        _method: "DELETE",
        delReview: true,
        gid: $("#game_id").val(),
        uid: $("#user_id").val()
      },
      function() {
        has_review = false;
      }
    );

    getAllReviews();
  });

  function getAllReviews() {
    $.get(
      "../../dashboard/user/game-class.php", 
      {
        gid: $("#game_id").val(),
        uid: $("#user_id").val()
      },
      function(response) {
        $("#reviews_container").html(response);

        if(has_review) {
          $("#review_form").css("display", "none");
        } else {
          $("#review_form").css("display", "block");
        }
      }
    );
  }
});