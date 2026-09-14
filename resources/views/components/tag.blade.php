@props(['variant' => 'mute'])
@php
    $styles = [
        'ok' => 'bg-tag-ok-bg text-tag-ok-fg ring-1 ring-inset ring-tag-ok-fg/10',
        'wait' => 'bg-tag-wait-bg text-tag-wait-fg ring-1 ring-inset ring-tag-wait-fg/10',
        'info' => 'bg-tag-info-bg text-tag-info-fg ring-1 ring-inset ring-tag-info-fg/10',
        'bad' => 'bg-tag-bad-bg text-tag-bad-fg ring-1 ring-inset ring-tag-bad-fg/10',
        'mute' => 'bg-tag-mute-bg text-tag-mute-fg ring-1 ring-inset ring-black/5',
    ];
@endphp
<span {{ $attributes->merge(['class' => 'inline-flex items-center text-[11px] font-semibold tracking-wide uppercase rounded-lg px-2.5 py-1 whitespace-nowrap ' . ($styles[$variant] ?? $styles['mute'])]) }}>
    {{ $slot }}
</span>
