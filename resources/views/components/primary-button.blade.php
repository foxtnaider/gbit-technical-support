<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-gbit-blue-800 dark:bg-gbit-orange-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gbit-blue-700 dark:hover:bg-gbit-orange-400 focus:bg-gbit-blue-700 dark:focus:bg-gbit-orange-400 active:bg-gbit-blue-900 dark:active:bg-gbit-orange-600 focus:outline-none focus:ring-2 focus:ring-gbit-orange-500 focus:ring-offset-2 dark:focus:ring-offset-gbit-blue-800 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
