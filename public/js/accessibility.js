// Gerenciamento de Temas
const btnDark = document.getElementById('toggle-dark-mode');
const iconDark = document.getElementById('dark-mode-icon');
const textDark = document.getElementById('dark-mode-text');

function updateDarkUI(isDark) {
    if (isDark) {
        iconDark.classList.replace('bi-moon-stars', 'bi-sun-fill');
        textDark.textContent = 'Modo Claro';
        btnDark.classList.replace('btn-outline-dark', 'btn-light');
    } else {
        iconDark.classList.replace('bi-sun-fill', 'bi-moon-stars');
        textDark.textContent = 'Modo Escuro';
        btnDark.classList.replace('btn-light', 'btn-outline-dark');
    }
}

btnDark.addEventListener('click', () => {
    const isDark = document.body.classList.toggle('dark-mode');
    document.body.classList.remove('high-contrast');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
    updateDarkUI(isDark);
});

document.getElementById('toggle-contrast').addEventListener('click', () => {
    document.body.classList.toggle('high-contrast');
    document.body.classList.remove('dark-mode');
    updateDarkUI(false);
});

// Controle de Fonte
function changeFontSize(action) {
    const el = document.documentElement;
    let size = parseFloat(window.getComputedStyle(el).fontSize);
    if (action === 'increase' && size < 24) el.style.fontSize = (size + 2) + 'px';
    if (action === 'decrease' && size > 12) el.style.fontSize = (size - 2) + 'px';
}

// Inicialização
window.addEventListener('DOMContentLoaded', () => {
    if (localStorage.getItem('theme') === 'dark') {
        document.body.classList.add('dark-mode');
        updateDarkUI(true);
    }
});