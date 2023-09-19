const menu = document.querySelector(".menu")
const sideBar = document.querySelector(".sidebar_options")
menu.addEventListener('click', () => {
        sideBar.classList.toggle('menu_show')
    })
    // Fonction pour afficher la durée écoulée
function afficherDureeEcoulee(dateString) {
    const postDate = new Date(dateString);
    const now = new Date();
    const differenceEnMillisecondes = now - postDate;
    const differenceEnSecondes = Math.floor(differenceEnMillisecondes / 1000);
    const differenceEnMinutes = Math.floor(differenceEnSecondes / 60);
    const differenceEnHeures = Math.floor(differenceEnMinutes / 60);
    const differenceEnJours = Math.floor(differenceEnHeures / 24);

    if (differenceEnSecondes < 60) {
        return `il y a ${differenceEnSecondes} secondes`;
    } else if (differenceEnMinutes < 60) {
        return `il y a ${differenceEnMinutes} minutes`;
    } else if (differenceEnHeures < 24) {
        return `il y a ${differenceEnHeures} heures`;
    } else {
        return `il y a ${differenceEnJours} jours`;
    }
}

// Récupérez le contenu de la balise span et appelez la fonction
const postTimeSpan = document.getElementById('postTime');
const dateString = postTimeSpan.textContent.trim();
postTimeSpan.textContent = afficherDureeEcoulee(dateString);