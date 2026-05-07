@php
  $windowStart = max(1, $paginator->currentPage() - 2);
  $windowEnd = min($paginator->lastPage(), $paginator->currentPage() + 2);
@endphp

@if ($paginator->lastPage() > 1)
<div class="d-flex justify-content-between align-items-center px-3 py-3 border-top flex-wrap gap-2">
  <span class="text-muted" style="font-size:.78rem;">
    Showing {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} of {{ $paginator->total() }} results
  </span>

  <nav aria-label="Pagination">
    <ul class="dash-pagination">
      <li class="dash-page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
        <a class="dash-page-link dash-page-arrow" href="{{ $paginator->onFirstPage() ? '#' : $paginator->previousPageUrl() }}" aria-label="Previous">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        </a>
      </li>

      @for ($page = $windowStart; $page <= $windowEnd; $page++)
        <li class="dash-page-item {{ $page === $paginator->currentPage() ? 'active' : '' }}">
          <a class="dash-page-link" href="{{ $paginator->url($page) }}">{{ $page }}</a>
        </li>
      @endfor

      <li class="dash-page-item {{ $paginator->hasMorePages() ? '' : 'disabled' }}">
        <a class="dash-page-link dash-page-arrow" href="{{ $paginator->hasMorePages() ? $paginator->nextPageUrl() : '#' }}" aria-label="Next">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
        </a>
      </li>
    </ul>
  </nav>
</div>
@elseif ($paginator->total() > 0)
<div class="px-3 py-2 border-top">
  <span class="text-muted" style="font-size:.78rem;">Showing {{ $paginator->total() }} result{{ $paginator->total() > 1 ? 's' : '' }}</span>
</div>
@endif
