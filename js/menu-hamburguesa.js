const toggleButton = document.getElementById("menu-toggle");
const menuHamburguesa = document.getElementById("menu-hamburguesa");
const closeButton = document.getElementById("menu-close");

toggleButton.addEventListener("click", () => {
  menuHamburguesa.classList.toggle("activo");
});

closeButton.addEventListener("click", () => {
  menuHamburguesa.classList.remove("activo");
});

document.querySelectorAll("#menu-hamburguesa a").forEach((link) => {
  link.addEventListener("click", () => {
    menuHamburguesa.classList.remove("activo");
  });
});
