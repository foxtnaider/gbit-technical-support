<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-gbit-blue-800">Soporte Técnico</h2>
        <p class="text-gray-600">Ingresa tus credenciales para acceder al sistema</p>
    </div>

    <form wire:submit="login">
        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Correo Electrónico')" class="text-gbit-blue-800 font-semibold" />
            <x-text-input wire:model="form.email" id="email" class="block mt-1 w-full border-gray-300 focus:border-gbit-orange-500 focus:ring-gbit-orange-500 rounded-md shadow-sm" type="email" name="email" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Contraseña')" class="text-gbit-blue-800 font-semibold" />

            <x-text-input wire:model="form.password" id="password" class="block mt-1 w-full border-gray-300 focus:border-gbit-orange-500 focus:ring-gbit-orange-500 rounded-md shadow-sm"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember" class="inline-flex items-center">
                <input wire:model="form.remember" id="remember" type="checkbox" class="rounded border-gray-300 text-gbit-orange-500 focus:ring-gbit-orange-500" name="remember">
                <span class="ms-2 text-sm text-gbit-blue-800 font-medium">{{ __('Recordarme') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-6">
            @if (Route::has('password.request'))
                <a class="text-sm text-gbit-blue-700 hover:text-gbit-orange-500 font-medium" href="{{ route('password.request') }}" wire:navigate>
                    {{ __('¿Olvidaste tu contraseña?') }}
                </a>
            @endif

            <button type="submit" class="bg-gbit-orange-500 hover:bg-gbit-orange-400 text-white font-bold py-2 px-6 rounded-md transition-colors duration-300 flex items-center">
                <span>{{ __('Ingresar') }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
    </form>
    
    <div class="mt-8 pt-6 border-t border-gray-200">
        <div class="flex items-center justify-center">
            <div class="text-sm text-gray-600 font-medium">
                Soporte técnico especializado para empresas
            </div>
        </div>
    </div>
</div>
