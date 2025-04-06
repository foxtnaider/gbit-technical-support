<?php

namespace App\Livewire\Components;

use Livewire\Component;
use Illuminate\Support\Facades\Cookie;

class ThemeSwitcher extends Component
{
    public $darkMode = false;

    public function mount()
    {
        $this->darkMode = Cookie::get('darkMode') === 'true';
    }

    public function toggleTheme()
    {
        $this->darkMode = !$this->darkMode;
        
        // Emitir evento para que JavaScript actualice el tema
        $this->dispatch('theme-changed', $this->darkMode);
        
        // Guardar preferencia en cookie
        Cookie::queue('darkMode', $this->darkMode ? 'true' : 'false', 60 * 24 * 365);
    }

    public function render()
    {
        return view('livewire.components.theme-switcher');
    }
}
