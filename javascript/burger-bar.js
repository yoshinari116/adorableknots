const burger = document.querySelector('.burger');
const dropdown = document.querySelector('.dropdown-menu');

burger.addEventListener('click', () => {
  burger.classList.toggle('active');
  dropdown.classList.toggle('show');
});
