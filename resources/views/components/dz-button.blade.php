@props([
    'variant' => 'primary',
    'size' => 'md',
    'as' => 'button',
    'href' => null,
])

@php
    $classes = ['dz-btn'];
    $classes[] = match ($variant) {
        'secondary' => 'dz-btn-secondary',
        'ghost' => 'dz-btn-ghost',
        'danger' => 'dz-btn-danger',
        default => 'dz-btn-primary',
    };
    $classes[] = match ($size) {
        'sm' => 'dz-btn-sm',
        'lg' => 'dz-btn-lg',
        default => '',
    };
    $class = implode(' ', $classes);
@endphp

@if ($as === 'a' || $href !== null)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $class]) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['class' => $class]) }}>
        {{ $slot }}
    </button>
@endif
