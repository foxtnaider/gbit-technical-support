<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gbit-blue-800 dark:text-white leading-tight">
                {{ __('Gestión de Usuarios') }}
            </h2>
            <a href="{{ route('users.create') }}" class="px-4 py-2 bg-gbit-blue-800 hover:bg-gbit-blue-700 text-white rounded-md transition">
                Nuevo Usuario
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gbit-blue-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gbit-blue-800 dark:text-white">
                    
                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-100 rounded-md">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 p-4 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-100 rounded-md">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white dark:bg-gbit-blue-800 border border-gray-200 dark:border-gbit-blue-700">
                            <thead>
                                <tr class="bg-gray-100 dark:bg-gbit-blue-700">
                                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nombre</th>
                                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Rol</th>
                                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gbit-blue-700">
                                @forelse($users as $user)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gbit-blue-700">
                                        <td class="py-4 px-6 text-sm">{{ $user->name }}</td>
                                        <td class="py-4 px-6 text-sm">{{ $user->email }}</td>
                                        <td class="py-4 px-6 text-sm">
                                            <span class="px-2 py-1 text-xs rounded-full {{ $user->role === 'admin' ? 'bg-gbit-orange-100 text-gbit-orange-800 dark:bg-gbit-orange-800 dark:text-gbit-orange-100' : 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100' }}">
                                                {{ $user->role === 'admin' ? 'Administrador' : 'Soporte' }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 text-sm flex space-x-2">
                                            <a href="{{ route('users.edit', $user) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-200">
                                                Editar
                                            </a>
                                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este usuario?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-200">
                                                    Eliminar
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-4 px-6 text-sm text-center text-gray-500 dark:text-gray-400">
                                            No hay usuarios registrados.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
