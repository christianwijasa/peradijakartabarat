@props(['variant' => 'primary', 'as' => 'button'])
@php
    $styles = [
        'primary' => 'bg-primary text-white border border-transparent hover:bg-navy-light',
        'done' => 'bg-tag-ok-bg text-tag-ok-fg border border-[#cfe0d5] cursor-default',
        'ghost' => 'bg-white text-[#4a4c57] border border-[#e0e2e9] hover:bg-[#f7f8fa]',
        'danger-outline' => 'bg-white text-[#93321f] border border-[#e7cfc7] hover:bg-[#f9ebe7]',
    ];
    $classes = 'inline-flex items-center justify-center gap-1.5 rounded-lg px-3.5 py-2 text-[12.5px] font-medium whitespace-nowrap transition disabled:opacity-50 disabled:cursor-not-allowed '
        . ($styles[$variant] ?? $styles['primary']);
@endphp
@if ($as === 'a')
    <a {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button {{ $attributes->merge(['type' => 'submit', 'class' => $classes]) }}>{{ $slot }}</button>
@endif
