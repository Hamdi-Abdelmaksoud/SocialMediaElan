function toggle_div_fun(id) {
    var element = document.getElementById(id);
    if (element.style.display === "none" || element.style.display === "") {
        element.style.display = "flex";
    } else {
        element.style.display = "none";
    }
}