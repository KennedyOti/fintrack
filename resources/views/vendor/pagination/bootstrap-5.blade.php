@if ($paginator->hasPages())
<ul class="pagination pagination-sm mb-0">

    {{-- Previous --}}
    @if ($paginator->onFirstPage())
        <li class="page-item disabled" aria-disabled="true">
            <span class="page-link"><i class="fas fa-chevron-left" style="font-size:10px;"></i></span>
        </li>
    @else
        <li class="page-item">
            <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">
                <i class="fas fa-chevron-left" style="font-size:10px;"></i>
            </a>
        </li>
    @endif

    {{-- Page Numbers --}}
    @foreach ($elements as $element)
        @if (is_string($element))
            <li class="page-item disabled" aria-disabled="true">
                <span class="page-link">{{ $element }}</span>
            </li>
        @endif

        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <li class="page-item active" aria-current="page">
                        <span class="page-link">{{ $page }}</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                    </li>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- Next --}}
    @if ($paginator->hasMorePages())
        <li class="page-item">
            <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">
                <i class="fas fa-chevron-right" style="font-size:10px;"></i>
            </a>
        </li>
    @else
        <li class="page-item disabled" aria-disabled="true">
            <span class="page-link"><i class="fas fa-chevron-right" style="font-size:10px;"></i></span>
        </li>
    @endif

</ul>
@endif
