<div class="bg-white shadow-md rounded-lg overflow-hidden border border-gray-200 w-64">
    <div class="px-6 py-4 bg-blue-600">
        <h3 class="text-lg font-medium text-white">Panel de Comandos</h3>
    </div>
    <nav class="p-4">
        <ul class="space-y-2">
            @foreach($menuItems as $item)
                <li>
                    <button 
                        class="w-full text-left px-4 py-2 rounded-md transition-colors duration-200 flex items-center {{ $activeMenu === $item['id'] ? 'bg-blue-100 text-blue-700' : 'hover:bg-gray-100' }}"
                        wire:click="setActiveMenu('{{ $item['id'] }}')"
                    >
                        <i class="{{ $item['icon'] }} mr-3"></i>
                        <span>{{ $item['label'] }}</span>
                    </button>
                </li>
            @endforeach
        </ul>
    </nav>
</div>
