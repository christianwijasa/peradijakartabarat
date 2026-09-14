@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'app-alert-success']) }} role="status">
        {{ $status }}
    </div>
@endif
