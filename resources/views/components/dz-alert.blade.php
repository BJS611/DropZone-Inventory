@props(['tone' => 'info'])

@php
    $tones = [
        'success' => 'dz-badge-success',
        'warning' => 'dz-badge-warning',
        'error' => 'dz-badge-error',
        'info' => 'dz-badge-info',
    ];
    $borders = [
        'success' => 'border-success/40',
        'warning' => 'border-warning/40',
        'error' => 'border-error/40',
        'info' => 'border-info/40',
    ];
@endphp

@if (session($tone))
    <div
        role="status"
        class="mb-6 flex items-start gap-3 border-l-4 {{ $borders[$tone] }} bg-surface px-5 py-4 text-sm text-ink"
    >
        <span class="dz-badge {{ $tones[$tone] }}">{{ strtoupper($tone) }}</span>
        <p class="flex-1">{{ session($tone) }}</p>
    </div>
@endif
