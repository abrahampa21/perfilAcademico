const toggle = document.getElementById('toggle');
const menu = document.getElementById('menu');
const menuLateral = document.getElementById('menu-lateral');

// Toggle menú desplegable
toggle.addEventListener('click', () => {
  menu.classList.toggle('activo');
});

// Cerrar menú al hacer clic en un enlace
const menuLinks = menu.querySelectorAll('a');
menuLinks.forEach(link => {
  link.addEventListener('click', () => {
    menu.classList.remove('activo');
  });
});

// Cerrar menú cuando se hace clic fuera de él
document.addEventListener('click', (e) => {
  if (!menu.contains(e.target) && !toggle.contains(e.target)) {
    menu.classList.remove('activo');
  }
});
