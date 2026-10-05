@props([
    'label' => null,
    'helper' => null,
])

@php
    $id = $attributes->get('id', 'dz-textarea-' . substr(\Illuminate\Support\Str::uuid()->toString(), 0, 8));
    $name = $attributes->get('name');
    $error = $name ? $errors->first($name) : null;
@endphp

@if ($label !== null)
    <label for="{{ $id }}" class="dz-label">{{ $label }}</label>
@endif

<textarea id="{{ $id }}" {{ $attributes->merge(['class' => 'dz-input'])->except('id') }} @if ($error) aria-invalid="true" @endif>{{ $slot }}</textarea>

@if ($helper && ! $error)
    <p class="dz-helper">{{ $helper }}</p>
@endif

@if ($error)
    <p class="dz-error" role="alert">{{ $error }}</p>
@endif
