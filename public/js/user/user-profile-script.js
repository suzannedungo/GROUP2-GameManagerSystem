$(document).ready(function () {
  $("#del_acc").click(function(event) {
    event.preventDefault();

    if(confirm('Your account will be deleted, do you still want to proceed?')) {
      $.post("/user/del_acc", { 
        del_acc: true,
        _method: $("#_method").val(),
        csrf_token: $("#csrf_token").val(),
        email: $("#del_email").val()
      });

      alert('Your account is deleted successfully.');
      window.location.href = '/signout';
    } else {
      window.location.href = '/user/profile';
    }
  });

  $("#edit_dp").click(function() {
    $("#modal_content_body label").attr("for", "dp");
    $("#modal_content_body label").html("New Image");
    $("#input").attr("type", "file");
    $("#input").attr("name", "dp");
    $("#input").attr("accept", ".jpg, .jpeg, .png, .webp, .avif");
    openModal();
  });

  $("#edit_name").click(function() {
    $("#modal_content_body label").attr("for", "name");
    $("#modal_content_body label").html("New Name");
    $("#input").attr("type", "text");
    $("#input").attr("name", "name");
    openModal();
  });

  $("#edit_username").click(function() {
    $("#modal_content_body label").attr("for", "username");
    $("#modal_content_body label").html("New Username");
    $("#input").attr("type", "text");
    $("#input").attr("name", "username");
    openModal();
  });

  $("#edit_email").click(function() {
    $("#modal_content_body label").attr("for", "email");
    $("#modal_content_body label").html("New Email");
    $("#input").attr("type", "email");
    $("#input").attr("name", "email");
    openModal();
  });

  $("#close_modal").click(function(event) {
    event.preventDefault();
    $("#input").removeAttr("accept");
    $("#input").val("");
    closeModal();
  });
});