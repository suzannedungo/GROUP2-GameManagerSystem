function showPassword(input_id) {
  $(input_id).attr("type", "text");
  return true;
}

function hidePassword(input_id) {
  $(input_id).attr("type", "password");
  return false;
}