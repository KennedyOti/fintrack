@if ($paginator->hasPages())
<nav class="blog-pag-nav" aria-label="Blog pagination">
    {{-- Previous --}}
    @if ($paginator->onFirstPage())
        <span class="blog-pag-btn blog-pag-disabled"><i class="fa-solid fa-chevron-left"></i></span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" class="blog-pag-btn"><i class="fa-solid fa-chevron-left"></i></a>
    @endif

    {{-- Pages --}}
    @foreach ($elements as $element)
        @if (is_string($element))
            <span class="blog-pag-dots">…</span>
        @endif
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="blog-pag-btn blog-pag-active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="blog-pag-btn">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- Next --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" class="blog-pag-btn"><i class="fa-solid fa-chevron-right"></i></a>
    @else
        <span class="blog-pag-btn blog-pag-disabled"><i class="fa-solid fa-chevron-right"></i></span>
    @endif
</nav>
@endif
