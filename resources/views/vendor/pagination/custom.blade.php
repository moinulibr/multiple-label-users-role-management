@if ($paginator->hasPages())
    <nav class="cdbc-pagination-links">
        <ul class="cdbc-pagination">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="cdbc-page-item disabled" aria-disabled="true">
                    <span class="cdbc-page-link">« Previous</span>
                </li>
            @else
                <li class="cdbc-page-item">
                    <a class="cdbc-page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">« Previous</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="cdbc-page-item disabled" aria-disabled="true"><span class="cdbc-page-link">{{ $element }}</span></li>
                @endif

                {{-- Array Of Page Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="cdbc-page-item active" aria-current="page"><span class="cdbc-page-link">{{ $page }}</span></li>
                        @else
                            <li class="cdbc-page-item"><a class="cdbc-page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="cdbc-page-item">
                    <a class="cdbc-page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">Next »</a>
                </li>
            @else
                <li class="cdbc-page-item disabled" aria-disabled="true">
                    <span class="cdbc-page-link">Next »</span>
                </li>
            @endif
        </ul>
    </nav>
@endif