@props(['label' => null, 'column' => null, 'current' => null, 'direction' => 'desc'])

@php
    $active = $current === $column;
    $nextDirection = $active && $direction === 'asc' ? 'desc' : 'asc';
    $query = array_merge(request()->query(), ['sort' => $column, 'direction' => $nextDirection]);
@endphp

<th class="dz-sortable">
    <a href="{{ request()->fullUrlWithQuery($query) }}" class="inline-flex items-center gap-1 hover:text-secondary">
        {{ $label }}
        @if ($active)
            <span aria-hidden="true">{{ $direction === 'asc' ? '▲' : '▼' }}</span>
        @endif
    </a>
    @if ($active)
        <span class="sr-only">Diurutkan {{ $direction === 'asc' ? 'menaik' : 'menurun' }} menurut {{ $label }}</span>
    @endif
</th>
