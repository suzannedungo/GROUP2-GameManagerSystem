let is_toast_open = false;

function showToast(toast, result) {
  if(!is_toast_open) {
    toast.toggleClass(result);
    toast.css("display", "block")
    is_toast_open = true;
    setTimeout(() => {
      toast.css("opacity", "1");

      hideToast(toast, result);
    }, 250);
  }
}

function hideToast(toast, result) {
  setTimeout(() => {
    toast.css("opacity", "0");
    setTimeout(() => {
      toast.css("display", "none")
      toast.toggleClass(result);
      toast.html("");
      is_toast_open = false;
    }, 500);
  }, 2000);
}