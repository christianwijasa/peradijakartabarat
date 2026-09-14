@props(['variant' => 'primary', 'as' => 'button'])
@php
    $styles = [
        'primary' => 'bg-primary text-white border border-transparent hover:bg-navy-light shadow-sm',
        'done' => 'bg-tag-ok-bg text-tag-ok-fg border border-[#cfe0d5] cursor-default',
        'ghost' => 'bg-white text-ink-secondary border border-line hover:bg-muted',
        'danger-outline' => 'bg-white text-tag-bad-fg border border-[#e7cfc7] hover:bg-tag-bad-bg',
    ];
    $classes = 'inline-flex min-h-[44px] sm:min-h-0 items-center justify-center gap-1.5 rounded-xl px-4 py-2.5 text-[13px] font-medium whitespace-nowrap transition active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed disabled:active:scale-100 '
        . ($styles[$variant] ?? $styles['primary']);
@endphp
@if ($as === 'a')
    <a {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button {{ $attributes->merge(['type' => 'submit', 'class' => $classes]) }}>{{ $slot }}</button>
@endif
