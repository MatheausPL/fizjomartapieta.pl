document.addEventListener("DOMContentLoaded", () => {

    // automatyczne podświetlanie aktywnej strony
    const links = document.querySelectorAll(".sidebar__nav a");
    const current = window.location.pathname.split("/").pop();

    links.forEach(link => {
        if (link.getAttribute("href") === current) {
            link.classList.add("active");
        }
    });

    // toggle sidebar (mobile)
    const navToggle = document.querySelector('.sidebar__toggle');
    const sidebar = document.querySelector('.sidebar');

    if (navToggle && sidebar) {
        navToggle.addEventListener('click', () => {
            sidebar.classList.toggle('sidebar--open');

            // blokada scrolla na mobile
            document.body.style.overflow =
                sidebar.classList.contains('sidebar--open') ? 'hidden' : '';
        });
    }

});
