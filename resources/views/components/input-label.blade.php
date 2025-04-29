@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-gbit-blue-800']) }}>
    {{ $value ?? $slot }}
</label>
