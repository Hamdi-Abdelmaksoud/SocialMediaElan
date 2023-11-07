function toggle_div_fun(id) {

    var element = document.getElementById(id);
    if (element.style.display === "none" || element.style.display === "") {
        element.style.display = "flex";
    } else {
        element.style.display = "none";
    }
}
// const checkmark = document.querySelector('.checkmark svg');

// // Ajoutez un gestionnaire d'événements pour le clic
// checkmark.addEventListener('click', function() {
//     // Appelez votre fonction pour changer le mode ici
//     // Par exemple, appelez une fonction appelée toggleDarkMode()
//     toggleDarkMode();

//     // Modifiez l'apparence du checkmark (ajoutez une classe CSS)
//     checkmark.classList.toggle('active');
// });
document.getElementById('pic_upload_input').addEventListener('input', function(event) {
    var input = event.target;
    var reader = new FileReader();
    reader.onload = function() {
        var img = document.getElementById('image-preview');
        img.src = reader.result;
        img.style.display = 'block';
    };
    reader.readAsDataURL(input.files[0]);
});