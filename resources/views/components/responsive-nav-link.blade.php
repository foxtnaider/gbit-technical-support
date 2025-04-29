@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-gbit-orange-500 text-start text-base font-medium text-gbit-blue-800 focus:outline-none focus:text-gbit-blue-900 focus:bg-gbit-orange-100 focus:border-gbit-orange-600 transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gbit-blue-700 hover:text-gbit-orange-500 focus:outline-none focus:text-gbit-orange-500 focus:bg-gbit-orange-100 focus:border-gbit-orange-300 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
