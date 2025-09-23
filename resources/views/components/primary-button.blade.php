<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-6 py-3 bg-[#3075BF] border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-wide hover:bg-[#405F80] focus:bg-[#405F80] active:bg-[#405F80] focus:outline-none focus:ring-2 focus:ring-[#3075BF] focus:ring-offset-2 transition ease-in-out duration-200 shadow-lg hover:shadow-xl']) }}>
    {{ $slot }}
</button>
