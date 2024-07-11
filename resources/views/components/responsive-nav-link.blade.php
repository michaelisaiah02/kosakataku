@props(['active'])

@php
    $classes =
        $active ?? false
            ? 'nav-link active border-start border-primary ps-3 pe-4 py-2 text-start text-base fw-medium text-primary bg-primary bg-opacity-10'
            : 'nav-link border-start border-transparent ps-3 pe-4 py-2 text-start text-base fw-medium text-muted hover-text-primary hover-bg-light';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
