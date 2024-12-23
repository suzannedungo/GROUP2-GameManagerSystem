function closeModal() {
  $("#modal").css("opacity", "0");
  setTimeout(() => {
    $("#modal").css("display", "none");
    $("body").css("overflow", "scroll");
  }, 300);
}

function openModal() {
  $("body").css("overflow", "hidden");
  $("#modal").css("display", "flex");
  setTimeout(() => {
    $("#modal").css("opacity", "1");
  }, 300);
}