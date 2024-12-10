$(document).ready(function() {
  updateProfile();

  $("#image").click(function() {
    $.get("../../input_popup_card/popup-card.html", function(response) {
      inputPopupCard(replacePlaceholders(response, "file", "profile_image"));

      $("#input_popup_container").attr("enctype", "multipart/form-data");
      $("#popup_input").attr("accept", ".jpg, .jpeg, .png");
    });
  });

  $("#name").click(function() {
    $.get("../../input_popup_card/popup-card.html", function(response) {
      inputPopupCard(replacePlaceholders(response, "text", "name"));
    });
  });

  $("#username").click(function() {
    $.get("../../input_popup_card/popup-card.html", function(response) {
      inputPopupCard(replacePlaceholders(response, "text", "username"));
    });
  });

  $("#reset_pass").click(function(event) {
    event.preventDefault();

    $.post(
      "../Authentication.php",
      {
        find_email: true,
        email: $("#email").val()
      },
      function() {
        popupCard("We have sent you a reset password link.");
      },
    );
  });

  $(document).on("click", "#edit_profile", function(event) {
    event.preventDefault();

    const formData = new FormData();
    if($("#popup_input").attr("type") === "file") {
      const file = $("#popup_input")[0].files[0];
      console.log($("#popup_input"));
      
      if (!file) {
        popupCard("Please select a file.");
        return;
      }

      formData.append('input_value', file);
    } else {
      formData.append("input_value", $("#popup_input").val());
    }

    formData.append("input_type", $("#popup_input").attr("type"));
    formData.append("input_name", $("#popup_input").attr("name"));
    formData.append("email", $("#email").val());
    formData.append("edit_profile", true);

    $.ajax({
      url: './user-class.php', 
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        popupCard(response);
      }
    });
    
    updateProfile();
  });

  function updateProfile() {
    $.get(
      "./user-class.php", 
      {
        email: $("#email").val(),
        get_name: true
      },
      function (data) {
        if(!data) {
          popupCard("An error occured upon fetching name.");
        } else {
          $("#acc_name").html(data);
        }
      }
    );

    $.get(
      "./user-class.php", 
      {
        email: $("#email").val(),
        get_username: true
      },
      function (data) {
        if(!data) {
          popupCard("An error occured upon fetching username.");
        } else {
          $("#acc_username").html(data);
        }
      }
    );

    $.get(
      "./user-class.php", 
      {
        email: $("#email").val(),
        get_profile_image: true
      },
      function (data) {
        if(!data) {
          popupCard("An error occured upon fetching profile image.");
        } else {
          $("#acc_image").attr("src", "../../src/uploads/users_images/" + data + "?t=" + new Date().getTime());
        }
      }
    );
  }
});