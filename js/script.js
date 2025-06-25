const menuToggle = document.getElementById("menuToggle");
const mobileMenu = document.getElementById("mobileMenu");
const menuIcon = document.getElementById("menuIcon");

menuToggle.addEventListener("click", () => {
    const isOpen = mobileMenu.classList.contains("max-h-0");
    mobileMenu.classList.toggle("max-h-0", !isOpen);
    mobileMenu.classList.toggle("max-h-100", isOpen);
    menuIcon.classList.toggle("fa-bars", !isOpen);
    menuIcon.classList.toggle("fa-times", isOpen);
});