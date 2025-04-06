@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-gbit-orange-500 dark:border-gbit-orange-400 text-start text-base font-medium text-gbit-blue-800 dark:text-white bg-gbit-orange-50 dark:bg-gbit-blue-700 focus:outline-none focus:text-gbit-blue-900 dark:focus:text-white focus:bg-gbit-orange-100 dark:focus:bg-gbit-blue-600 focus:border-gbit-orange-600 dark:focus:border-gbit-orange-300 transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gbit-blue-700 dark:text-gray-300 hover:text-gbit-blue-800 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gbit-blue-700 hover:border-gbit-orange-300 dark:hover:border-gbit-orange-600 focus:outline-none focus:text-gbit-blue-800 dark:focus:text-white focus:bg-gray-50 dark:focus:bg-gbit-blue-700 focus:border-gbit-orange-300 dark:focus:border-gbit-orange-600 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
