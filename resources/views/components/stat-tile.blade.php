@props(['value', 'label'])
<div {{ $attributes->merge(['class' => 'app-stat-tile']) }}>
    <p class="font-serif text-2xl md:text-3xl text-navy tabular-nums">{{ $value }}</p>
    <p class="text-xs text-muted-foreground mt-1 leading-snug">{{ $label }}</p>
</div>
