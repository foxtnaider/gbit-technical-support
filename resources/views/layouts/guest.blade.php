<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'GBIT Technical Support') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Estilos y Scripts compilados -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-gbit-blue-800 to-gbit-blue-900">
            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                <div class="flex justify-center mb-6">
                    <a href="/">
                        <img src="https://gbit.com.ve/web/assets/img/gbit-nbg.png" alt="GBIT Logo" class="w-40">
                    </a>
                </div>
                
                {{ $slot }}
            </div>
            
            <div class="mt-8 text-center text-sm text-white">
                <p>&copy; {{ date('Y') }} GBIT. Todos los derechos reservados.</p>
            </div>
        </div>

        <script>
            // Esperar a que el DOM esté completamente cargado
            document.addEventListener('DOMContentLoaded', function() {
                console.log('Script de recarga cargado');
                console.log('Ruta actual:', window.location.pathname);
                
                // Force reload when accessing /olt-commands to ensure styles load correctly
                if (window.location.pathname === '/olt-commands') {
                    console.log('Ruta /olt-commands detectada');
                    if (!sessionStorage.getItem('hasReloaded')) {
                        console.log('Primera carga - Forzando recarga...');
                        sessionStorage.setItem('hasReloaded', 'true');
                        window.location.reload();
                    } else {
                        console.log('Recarga ya realizada en esta sesión');
                        sessionStorage.removeItem('hasReloaded');
                    }
                } else {
                    console.log('No es la ruta /olt-commands, limpiando estado');
                    sessionStorage.removeItem('hasReloaded');
                }
            });
        </script>
    </body>
</html>
