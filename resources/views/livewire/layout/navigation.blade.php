<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" wire:navigate>
                        <x-application-logo class="block h-12 w-auto" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate class="text-gbit-blue-800 hover:text-gbit-orange-500">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    
                    <x-nav-link :href="route('network-devices.index')" :active="request()->routeIs('network-devices.*')" wire:navigate class="text-gbit-blue-800 hover:text-gbit-orange-500">
                        {{ __('OLTs') }}
                    </x-nav-link>
                    
                    <x-nav-link :href="route('naps.index')" :active="request()->routeIs('naps.*')" wire:navigate class="text-gbit-blue-800 hover:text-gbit-orange-500">
                        {{ __('NAPs') }}
                    </x-nav-link>
                    
                    <x-nav-link :href="route('customers.index')" :active="request()->routeIs('customers.*')" wire:navigate class="text-gbit-blue-800 hover:text-gbit-orange-500">
                        {{ __('Clientes (ONTs)') }}
                    </x-nav-link>
                    
                    <x-nav-link :href="route('monitoring.index')" :active="request()->routeIs('monitoring.*')" wire:navigate class="text-gbit-blue-800 hover:text-gbit-orange-500">
                        {{ __('Monitoreo OLTs') }}
                    </x-nav-link>
                    
                    <x-nav-link :href="route('olt-commands.index')" :active="request()->routeIs('olt-commands.*')" wire:navigate class="text-gbit-blue-800 hover:text-gbit-orange-500">
                        {{ __('Comandos OLT') }}
                    </x-nav-link>
                    
                    {{-- <x-nav-link :href="route('olt-api.index')" :active="request()->routeIs('olt-api.*')" wire:navigate class="text-gbit-blue-800 hover:text-gbit-orange-500">
                        {{ __('API OLT') }}
                    </x-nav-link> --}}

                    <x-nav-link :href="route('olt-performance.index')" :active="request()->routeIs('olt-performance.*')" wire:navigate class="text-gbit-blue-800 hover:text-gbit-orange-500">
                        {{ __('Rendimiento OLT/ONU') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gbit-blue-800 bg-white hover:text-gbit-orange-500 focus:outline-none transition ease-in-out duration-150">
                            <div x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile')" wire:navigate class="hover:text-gbit-orange-500">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <button wire:click="logout" class="w-full text-start">
                            <x-dropdown-link class="hover:text-gbit-orange-500">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </button>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gbit-blue-500 hover:text-gbit-blue-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gbit-blue-800 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate class="text-gbit-blue-800 hover:text-gbit-orange-500">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            
            <x-responsive-nav-link :href="route('network-devices.index')" :active="request()->routeIs('network-devices.*')" wire:navigate class="text-gbit-blue-800 hover:text-gbit-orange-500">
                {{ __('OLTs') }}
            </x-responsive-nav-link>
            
            <x-responsive-nav-link :href="route('naps.index')" :active="request()->routeIs('naps.*')" wire:navigate class="text-gbit-blue-800 hover:text-gbit-orange-500">
                {{ __('NAPs') }}
            </x-responsive-nav-link>
            
            <x-responsive-nav-link :href="route('customers.index')" :active="request()->routeIs('customers.*')" wire:navigate class="text-gbit-blue-800 hover:text-gbit-orange-500">
                {{ __('Clientes (ONTs)') }}
            </x-responsive-nav-link>
            
            <x-responsive-nav-link :href="route('monitoring.index')" :active="request()->routeIs('monitoring.*')" wire:navigate class="text-gbit-blue-800 hover:text-gbit-orange-500">
                {{ __('Monitoreo OLTs') }}
            </x-responsive-nav-link>
            
            <x-responsive-nav-link :href="route('olt-commands.index')" :active="request()->routeIs('olt-commands.*')" wire:navigate class="text-gbit-blue-800 hover:text-gbit-orange-500">
                {{ __('Comandos OLT') }}
            </x-responsive-nav-link>
            
            {{-- <x-responsive-nav-link :href="route('olt-api.index')" :active="request()->routeIs('olt-api.*')" wire:navigate class="text-gbit-blue-800 hover:text-gbit-orange-500">
                {{ __('API OLT') }}
            </x-responsive-nav-link> --}}

            <x-responsive-nav-link :href="route('olt-performance.index')" :active="request()->routeIs('olt-performance.*')" wire:navigate class="text-gbit-blue-800 hover:text-gbit-orange-500">
                {{ __('Rendimiento OLT/ONU') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gbit-blue-800" x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>
                <div class="font-medium text-sm text-gray-500">{{ auth()->user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile')" wire:navigate class="hover:text-gbit-orange-500">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <button wire:click="logout" class="w-full text-start">
                    <x-responsive-nav-link class="hover:text-gbit-orange-500">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </button>
            </div>
        </div>
    </div>
</nav>
