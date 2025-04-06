@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-gbit-orange-500 dark:border-gbit-orange-400 text-sm font-medium leading-5 text-gbit-blue-800 dark:text-white focus:outline-none focus:border-gbit-orange-600 transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gbit-blue-700 dark:text-gray-300 hover:text-gbit-orange-500 dark:hover:text-gbit-orange-400 hover:border-gbit-blue-300 dark:hover:border-gbit-orange-700 focus:outline-none focus:text-gbit-orange-500 dark:focus:text-gbit-orange-400 focus:border-gbit-blue-300 dark:focus:border-gbit-orange-700 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
