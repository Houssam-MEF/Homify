@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-semibold text-sm text-[#405F80] mb-1']) }}>
    {{ $value ?? $slot }}
</label>
