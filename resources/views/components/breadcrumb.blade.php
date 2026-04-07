@props(['items' => []])

<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb bg-transparent p-0 mb-0">
        <li class="breadcrumb-item">
            <a href="/" class="text-decoration-none text-muted">
                <i class="fas fa-home small"></i> Início
            </a>
        </li>

        @foreach ($items as $label => $link)
            @if (!$loop->last)
                <li class="breadcrumb-item">
                    <a href="{{ $link }}" class="text-decoration-none text-muted">{{ $label }}</a>
                </li>
            @else
                <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">
                    {{ $label }}
                </li>
            @endif
        @endforeach
    </ol>
</nav>
