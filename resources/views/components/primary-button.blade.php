<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex min-h-[44px] sm:min-h-0 items-center justify-center px-5 py-2.5 bg-primary border border-transparent rounded-xl font-medium text-[13px] text-white hover:bg-navy-light focus:outline-none focus:ring-2 focus:ring-primary/30 focus:ring-offset-2 shadow-sm transition active:scale-[0.98]']) }}>
    {{ $slot }}
</button>
