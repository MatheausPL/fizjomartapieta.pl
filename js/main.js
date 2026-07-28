const navToggle = document.querySelector('.nav__toggle');
const navList = document.querySelector('.nav__list');

if (navToggle && navList) {
    navToggle.addEventListener('click', () => {
        navList.classList.toggle('nav--open');
    });
}

document.addEventListener("DOMContentLoaded", () => {
    const links = document.querySelectorAll(".sidebar__nav a");
    const current = window.location.pathname.split("/").pop();

    links.forEach(link => {
        if (link.getAttribute("href") === current) {
            link.classList.add("active");
        }
    });
});
