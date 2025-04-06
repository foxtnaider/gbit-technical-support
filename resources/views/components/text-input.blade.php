@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 dark:border-gbit-blue-600 dark:bg-gbit-blue-900 dark:text-white focus:border-gbit-orange-500 dark:focus:border-gbit-orange-400 focus:ring-gbit-orange-500 dark:focus:ring-gbit-orange-400 rounded-md shadow-sm']) }}>
