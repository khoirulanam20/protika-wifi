@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-sm font-semibold text-content-primary']) }}>
    {{ $value ?? $slot }}
</label>
