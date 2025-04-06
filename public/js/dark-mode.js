// Función para manejar el tema oscuro
function handleDarkMode() {
    // Verificar si hay una preferencia guardada en cookies
    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
    }

    // Aplicar el tema oscuro basado en la cookie
    const darkModeCookie = getCookie('darkMode');
    if (darkModeCookie === 'true') {
        document.documentElement.classList.add('dark');
    } else if (darkModeCookie === 'false') {
        document.documentElement.classList.remove('dark');
    } else {
        // Si no hay preferencia guardada, usar la preferencia del sistema
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.documentElement.classList.add('dark');
            document.cookie = 'darkMode=true; max-age=' + (60 * 24 * 365) + '; path=/;';
        }
    }

    // Función para cambiar el tema
    window.toggleDarkMode = function() {
        const isDarkMode = document.documentElement.classList.contains('dark');
        
        if (isDarkMode) {
            document.documentElement.classList.remove('dark');
            document.cookie = 'darkMode=false; max-age=' + (60 * 24 * 365) + '; path=/;';
        } else {
            document.documentElement.classList.add('dark');
            document.cookie = 'darkMode=true; max-age=' + (60 * 24 * 365) + '; path=/;';
        }

        // Disparar un evento personalizado para que los componentes Livewire puedan reaccionar
        document.dispatchEvent(new CustomEvent('dark-mode-changed', { 
            detail: { darkMode: !isDarkMode } 
        }));
    }

    // Inicializar los botones de cambio de tema
    document.addEventListener('DOMContentLoaded', function() {
        const themeToggles = document.querySelectorAll('[data-theme-toggle]');
        themeToggles.forEach(button => {
            button.addEventListener('click', window.toggleDarkMode);
        });
    });
}

// Ejecutar inmediatamente para evitar parpadeos
handleDarkMode();
