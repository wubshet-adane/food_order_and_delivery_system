const toggleButton = document.getElementById('darkModeToggle');
const body = document.body;

if (localStorage.getItem('darkMode') === 'enabled') {
    body.classList.add('dark-mode');
    toggleButton.innerHtml = '<i class="fa-regular fa-sun"></i>';
}

toggleButton.onclick = () => {
    body.classList.toggle('dark-mode');
    if (body.classList.contains('dark-mode')) {
        localStorage.setItem('darkMode', 'enabled');
        toggleButton.innerHTML = '<i class="fa-regular fa-sun"></i>';
    } else {
        localStorage.setItem('darkMode', 'disabled');
        toggleButton.textContent = '🌙';
    }
};