@props(['quantity' => 0, 'minimum' => 0])

@php
    $status = $quantity === 0 ? 'OUT_OF_STOCK' : ($quantity <= $minimum ? 'LOW' : 'NORMAL');
    $tone = $status === 'OUT_OF_STOCK' ? 'error' : ($status === 'LOW' ? 'warning' : 'success');
    $labels = [
        'OUT_OF_STOCK' => 'Out Of Stock',
        'LOW' => 'Low Stock',
        'NORMAL' => 'Normal',
    ];
@endphp

<span class="dz-badge {{ $tone === 'error' ? 'dz-badge-error' : ($tone === 'warning' ? 'dz-badge-warning' : 'dz-badge-success') }}">
    {{ $labels[$status] }}
</span>
