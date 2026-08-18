@props(['variant' => 'mute'])
@php
    $styles = [
        'ok' => 'bg-tag-ok-bg text-tag-ok-fg',
        'wait' => 'bg-tag-wait-bg text-tag-wait-fg',
        'info' => 'bg-tag-info-bg text-tag-info-fg',
        'bad' => 'bg-tag-bad-bg text-tag-bad-fg',
        'mute' => 'bg-tag-mute-bg text-tag-mute-fg',
    ];
@endphp
<span {{ $attributes->merge(['class' => 'inline-block text-[11.5px] font-medium tracking-wide uppercase rounded-md px-2.5 py-1 whitespace-nowrap justify-self-start ' . ($styles[$variant] ?? $styles['mute'])]) }}>
    {{ $slot }}
</span>
