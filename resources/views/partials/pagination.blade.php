@php
  $windowStart = max(1, $paginator->currentPage() - 2);
  $windowEnd = min($paginator->lastPage(), $paginator->currentPage() + 2);
@endphp

<div class="d-flex justify-content-between align-items-center px-3 py-2 border-top flex-wrap gap-2">
  <span class="text-muted small">
    @if ($paginator->total() > 0)
      Showing {{ $paginator->firstItem() }}-{{ $paginator->lastItem() }} of {{ $paginator->total() }}
    @else
      Showing 0 of 0
    @endif
  </span>

  @if ($paginator->lastPage() > 1)
    <nav>
      <ul class="pagination pagination-sm mb-0">
        <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
          <a class="page-link" href="{{ $paginator->onFirstPage() ? '#' : $paginator->previousPageUrl() }}" aria-label="Previous">
            &lsaquo;
          </a>
        </li>

        @for ($page = $windowStart; $page <= $windowEnd; $page++)
          <li class="page-item {{ $page === $paginator->currentPage() ? 'active' : '' }}">
            <a class="page-link" href="{{ $paginator->url($page) }}">{{ $page }}</a>
          </li>
        @endfor

        <li class="page-item {{ $paginator->hasMorePages() ? '' : 'disabled' }}">
          <a class="page-link" href="{{ $paginator->hasMorePages() ? $paginator->nextPageUrl() : '#' }}" aria-label="Next">
            &rsaquo;
          </a>
        </li>
      </ul>
    </nav>
  @endif
</div>
