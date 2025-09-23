@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-300 focus:border-[#3075BF] focus:ring-[#3075BF] rounded-lg shadow-sm transition-colors duration-200']) !!}>
