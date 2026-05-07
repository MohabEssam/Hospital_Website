@if ($paginator->hasPages())
<nav role="navigation" aria-label="Pagination" class="mc-pagination-wrap">
  <span class="mc-pagination-info">
    Showing {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} of {{ $paginator->total() }} results
  </span>

  <ul class="mc-pagination">
    {{-- Previous --}}
    <li class="mc-page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
      @if ($paginator->onFirstPage())
        <span class="mc-page-link mc-page-arrow" aria-disabled="true" aria-label="Previous">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        </span>
      @else
        <a class="mc-page-link mc-page-arrow" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        </a>
      @endif
    </li>

    {{-- Page Numbers --}}
    @foreach ($elements as $element)
      @if (is_string($element))
        <li class="mc-page-item disabled">
          <span class="mc-page-link mc-page-dots">{{ $element }}</span>
        </li>
      @endif

      @if (is_array($element))
        @foreach ($element as $page => $url)
          <li class="mc-page-item {{ $page == $paginator->currentPage() ? 'active' : '' }}">
            @if ($page == $paginator->currentPage())
              <span class="mc-page-link" aria-current="page">{{ $page }}</span>
            @else
              <a class="mc-page-link" href="{{ $url }}">{{ $page }}</a>
            @endif
          </li>
        @endforeach
      @endif
    @endforeach

    {{-- Next --}}
    <li class="mc-page-item {{ $paginator->hasMorePages() ? '' : 'disabled' }}">
      @if ($paginator->hasMorePages())
        <a class="mc-page-link mc-page-arrow" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
        </a>
      @else
        <span class="mc-page-link mc-page-arrow" aria-disabled="true" aria-label="Next">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
        </span>
      @endif
    </li>
  </ul>
</nav>
@endif
