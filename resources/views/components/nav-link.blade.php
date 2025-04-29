@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-gbit-orange-500 text-sm font-medium leading-5 text-gbit-blue-800 focus:outline-none focus:border-gbit-orange-600 transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gbit-blue-700 hover:text-gbit-orange-500 focus:outline-none focus:text-gbit-orange-500 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
