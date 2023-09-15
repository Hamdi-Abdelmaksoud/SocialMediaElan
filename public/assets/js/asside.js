const menu = document.querySelector(".menu")
const sideBar = document.querySelector(".sidebar_options")

menu.addEventListener('click', () => {
    sideBar.classList.toggle('menu_show')
})