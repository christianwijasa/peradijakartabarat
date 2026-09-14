@props(['eyebrow', 'title', 'description' => null])
<div {{ $attributes->merge(['class' => 'rounded-2xl bg-gradient-to-br from-navy via-navy to-navy-dark text-white p-6 shadow-card']) }}>
    <p class="text-[11px] font-medium tracking-[0.14em] uppercase text-accent-muted">{{ $eyebrow }}</p>
    <p class="mt-2 font-serif text-xl leading-snug">{{ $title }}</p>
    @if ($description)
        <p class="text-sm text-sidebar-muted mt-2 leading-relaxed">{{ $description }}</p>
    @elseif (trim($slot))
        <div class="text-sm text-sidebar-muted mt-2 leading-relaxed">{{ $slot }}</div>
    @endif
    @isset($actions)
        <div>{{ $actions }}</div>
    @endisset
</div>
