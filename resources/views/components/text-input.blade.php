@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-gbit-orange-500 focus:ring-gbit-orange-500 rounded-md shadow-sm']) }}>
