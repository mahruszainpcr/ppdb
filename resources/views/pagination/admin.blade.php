@if ($paginator->hasPages())
    <nav class="app-pagination" aria-label="Navigasi halaman">
        <p class="app-pagination-summary">
            Menampilkan <strong>{{ $paginator->firstItem() }}</strong>–<strong>{{ $paginator->lastItem() }}</strong>
            dari <strong>{{ $paginator->total() }}</strong> data
        </p>
        <ul class="pagination mb-0">
            <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                @if ($paginator->onFirstPage())
                    <span class="page-link" aria-disabled="true" aria-label="Halaman sebelumnya"><span class="pagination-chevron previous" aria-hidden="true"></span></span>
                @else
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Halaman sebelumnya"><span class="pagination-chevron previous" aria-hidden="true"></span></a>
                @endif
            </li>
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="page-item disabled"><span class="page-link" aria-hidden="true">{{ $element }}</span></li>
                @else
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active"><span class="page-link" aria-current="page" aria-label="Halaman {{ $page }}">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $url }}" aria-label="Halaman {{ $page }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach
            <li class="page-item {{ !$paginator->hasMorePages() ? 'disabled' : '' }}">
                @if ($paginator->hasMorePages())
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Halaman berikutnya"><span class="pagination-chevron next" aria-hidden="true"></span></a>
                @else
                    <span class="page-link" aria-disabled="true" aria-label="Halaman berikutnya"><span class="pagination-chevron next" aria-hidden="true"></span></span>
                @endif
            </li>
        </ul>
    </nav>
@endif
