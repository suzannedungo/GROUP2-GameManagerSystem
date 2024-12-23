$(document).ready(function() {
  $("#favorite").click(function() {
    if($(this).is(":checked")) {
      $.post(
        "/user/visit_game/add_fav",
        {
          add_fav: true,
          gid: $("#game_id").val(),
          uid: $("#user_id").val()
        },
        function(response) {
          alert(response);
          $("#fav_icon").removeClass("bx-bookmark");
          $("#fav_icon").addClass("bxs-bookmark");
        }
      );
    } else {
      $.post(
        "/user/visit_game/del_fav",
        {
          del_fav: true,
          gid: $("#game_id").val(),
          uid: $("#user_id").val()
        },
        function(response) {
          alert(response);
          $("#fav_icon").removeClass("bxs-bookmark");
          $("#fav_icon").addClass("bx-bookmark");
        }
      );
    }
  });

  let selectedRating = 0;

  const stars = document.querySelectorAll('.star-rating i');
  stars.forEach((star, index) => {
    star.addEventListener('click', () => {
      selectedRating = index + 1;
      stars.forEach((s, i) => {
        if (i < selectedRating) {
          s.classList.add('active');
          s.classList.replace('bx-star', 'bxs-star');
        } else {
          s.classList.remove('active');
          s.classList.replace('bxs-star', 'bx-star');
        }
      });
    });
  });

  $("#submit_review").click(function(event) {
    event.preventDefault();

    if(selectedRating <= 0) {
      alert("Do not leave rating field blank.");
    } else {
      $.post(
        "/user/visit_game/add_review",
        {
          submit_review: true,
          gid: $("#game_id").val(),
          uid: $("#user_id").val(),
          rating: selectedRating,
          comment: $("#comment").val()
        },
        function(response) {
          alert(response);
          location.reload();
        }
      );
    }
  });
});