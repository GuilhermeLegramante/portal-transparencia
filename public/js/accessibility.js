// Gerenciamento de Temas
const btnDark = document.getElementById('toggle-dark-mode');
const iconDark = document.getElementById('dark-mode-icon');
const textDark = document.getElementById('dark-mode-text');
const btnContrast = document.getElementById('toggle-contrast'); // Botão de contraste

function updateDarkUI(isDark) {
    if (!btnDark || !iconDark || !textDark) return; // Segurança caso os IDs mudem

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

// Verifica se o botão Dark existe antes de adicionar o evento
if (btnDark) {
    btnDark.addEventListener('click', () => {
        const isDark = document.body.classList.toggle('dark-mode');
        document.body.classList.remove('high-contrast');
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
        updateDarkUI(isDark);
    });
}

// Verifica se o botão de Contraste existe antes de adicionar o evento
if (btnContrast) {
    btnContrast.addEventListener('click', () => {
        document.body.classList.toggle('high-contrast');
        document.body.classList.remove('dark-mode');
        updateDarkUI(false);
    });
}

// Inicialização (Melhorada)
window.addEventListener('DOMContentLoaded', () => {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        document.body.classList.add('dark-mode');
        updateDarkUI(true);
    }
});