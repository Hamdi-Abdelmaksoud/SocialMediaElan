function toggle_div_fun(id) {

    var element = document.getElementById(id);
    if (element.style.display === "none" || element.style.display === "") {
        element.style.display = "flex";
    } else {
        element.style.display = "none";
    }
}
document.addEventListener('DOMContentLoaded',
    function() {
        var btn = document.getElementById("mode");
        btn.addEventListener('click', function() {
            document.body.classList.toggle("darkmode");
        });
    });