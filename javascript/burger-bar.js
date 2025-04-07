const burger = document.getElementById('burger');
const dropdown = document.getElementById('dropdownMenu');

burger.addEventListener('click', () => {
  burger.classList.toggle('active');
  dropdown.classList.toggle('show');
});
