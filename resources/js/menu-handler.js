function initializeMenu() {
    const menuItems = document.querySelectorAll('.menu-item');
    const contentSections = document.querySelectorAll('.content-section');

    if (menuItems.length === 0) {
        return; // No hay menú en esta página
    }

    menuItems.forEach(item => {
        item.addEventListener('click', function() {
            // Remover clase activa de todos los botones
            menuItems.forEach(btn => {
                btn.classList.remove('bg-blue-50');
            });

            // Agregar clase activa al botón seleccionado
            this.classList.add('bg-blue-50');

            // Ocultar todas las secciones de contenido
            contentSections.forEach(section => {
                section.classList.add('hidden');
            });

            // Mostrar la sección de contenido correspondiente
            const targetId = this.getAttribute('data-target');
            const targetSection = document.getElementById(targetId);
            if (targetSection) {
                targetSection.classList.remove('hidden');
            }
        });
    });
}

// Ejecutar en la carga inicial
document.addEventListener('DOMContentLoaded', initializeMenu);

// Ejecutar en navegaciones de Livewire/Turbo
document.addEventListener('livewire:navigated', initializeMenu);
