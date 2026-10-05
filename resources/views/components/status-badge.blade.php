@props([
    'label' => null,
    'tone' => 'neutral',
])

@php
    $tones = [
        'success' => 'dz-badge-success',
        'warning' => 'dz-badge-warning',
        'error' => 'dz-badge-error',
        'info' => 'dz-badge-info',
        'neutral' => 'dz-badge-neutral',
    ];
    // status tidak pernah disampaikan lewat warna saja
    $text = $label ?? match ($tone) {
        'success' => 'Normal',
        'warning' => 'Low Stock',
        'error' => 'Out Of Stock',
        'info' => 'Info',
        default => 'Neutral',
    };
@endphp

<span class="dz-badge {{ $tones[$tone] ?? 'dz-badge-neutral' }}">
    {{ $text }}
</span>
