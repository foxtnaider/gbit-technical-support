<div>
    {{-- The Master doesn't talk, he acts. --}}
    <button 
        wire:click="toggleTheme"
        data-theme-toggle
        type="button" 
        class="flex items-center justify-center p-2 rounded-md transition-colors duration-200 bg-gray-100 text-gbit-blue-800 hover:bg-gray-200"
        aria-label="Cambiar modo de visualización"
    >
        <!-- Icono de sol (visible en modo oscuro) -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd" />
        </svg>
        
        <!-- Icono de luna (visible en modo claro) -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 block" viewBox="0 0 20 20" fill="currentColor">
            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
        </svg>
    </button>

    <script>
        // Escuchar eventos de Livewire para cambiar el tema
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('theme-changed', (darkMode) => {
                // Llamar a la función global de cambio de tema
                if (window.toggleDarkMode) {
                    // No llamamos a toggleDarkMode directamente para evitar un bucle infinito
                    // ya que toggleDarkMode también dispara eventos
                    if (darkMode) {
                        document.documentElement.classList.add('dark');
                        document.cookie = 'darkMode=true; max-age=' + (60 * 24 * 365) + '; path=/;';
                    } else {
                        document.documentElement.classList.remove('dark');
                        document.cookie = 'darkMode=false; max-age=' + (60 * 24 * 365) + '; path=/;';
                    }
                }
            });
        });
    </script>
</div>
