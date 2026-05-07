@extends('layouts.website')

@section('title', 'Departments - Medicare Hospital')

@section('content')

  {{-- Section Header --}}
  <section class="dept-section-header">
    <div class="container position-relative" style="z-index:2;">
      <div class="text-center reveal">
        <h2 class="dept-section-title">Our Departments</h2>
        <p class="dept-section-subtitle">Comprehensive medical specialties for your care</p>
        <div class="dept-section-divider"></div>
      </div>
    </div>
  </section>

  <section class="dept-section-content">
    <div class="container">
      {{-- Search Toolbar --}}
      <div class="dept-toolbar reveal" style="transition-delay: 100ms">
        <div class="dept-search" role="search">
          <span class="dept-search-submit" aria-hidden="true">
            <i class="bi bi-search"></i>
          </span>
          <input
            type="search"
            id="dept-live-search"
            value="{{ $filters['search'] ?? '' }}"
            placeholder="Search by department, specialty, or keyword..."
            aria-label="Search departments"
            autocomplete="off">
          <span class="dept-search-clear" id="dept-search-clear" aria-label="Clear search" style="display:none">
            <i class="bi bi-x-circle-fill"></i>
          </span>
          <span class="dept-search-spinner" id="dept-spinner" aria-hidden="true"></span>
        </div>
      </div>

      <div class="row g-4" id="dept-grid">
        @include('website.departments._grid', ['departments' => $departments])
      </div>
    </div>
  </section>

@endsection

@push('styles')
<style>
/* ── Departments Section ───────────────────────────────────── */
.dept-section-header {
  background: #fff;
  padding: 64px 0 32px;
  position: relative;
}
.dept-section-title {
  font-family: 'Poppins', sans-serif;
  font-size: 36px;
  font-weight: 700;
  color: #3f4047;
  margin-bottom: 8px;
  letter-spacing: -0.5px;
}
.dept-section-subtitle {
  font-family: 'Poppins', sans-serif;
  font-size: 16px;
  color: #6b7280;
  margin-bottom: 20px;
}
.dept-section-divider {
  width: 60px;
  height: 3px;
  background: #3f4047;
  border-radius: 2px;
  margin: 0 auto;
}

.dept-section-content {
  padding: 24px 0 80px;
  background: #f8f9fa;
}

/* ── Department Card ─────────────────────────────────────── */
.dept-card {
  background: #fff;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 2px 12px rgba(0,0,0,0.04);
  transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
  height: 100%;
}
.dept-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 40px rgba(63, 64, 71, 0.12);
}
.dept-card-link {
  text-decoration: none;
  display: block;
  height: 100%;
  color: inherit;
}
.dept-card-link:hover {
  color: inherit;
}

/* Card Image */
.dept-card-image {
  position: relative;
  overflow: hidden;
  height: 200px;
}
.dept-card-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  transition: transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}
.dept-card:hover .dept-card-image img {
  transform: scale(1.08);
}

/* Gradient Overlay */
.dept-card-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to top, rgba(63, 64, 71, 0.75) 0%, rgba(63, 64, 71, 0.2) 40%, transparent 100%);
  opacity: 0.7;
  transition: opacity 0.4s ease;
}
.dept-card:hover .dept-card-overlay {
  opacity: 0.9;
}

/* Icon Badge on Image */
.dept-card-icon {
  position: absolute;
  bottom: 16px;
  left: 20px;
  width: 48px;
  height: 48px;
  background: rgba(255,255,255,0.95);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  transition: all 0.35s ease;
}
.dept-card-icon img {
  width: 24px;
  height: 24px;
  object-fit: contain;
  transition: none;
}
.dept-card-icon i {
  font-size: 22px;
  color: #3f4047;
}
.dept-card:hover .dept-card-icon {
  background: #3f4047;
  transform: translateY(-4px);
}
.dept-card:hover .dept-card-icon i {
  color: #fff;
}

/* Card Body */
.dept-card-body {
  padding: 24px;
  display: flex;
  flex-direction: column;
}

.dept-card-title {
  font-family: 'Poppins', sans-serif;
  font-weight: 700;
  font-size: 18px;
  color: #1f2937;
  margin-bottom: 10px;
  line-height: 1.3;
}

.dept-card-desc {
  color: #6b7280;
  font-size: 14px;
  line-height: 1.65;
  margin-bottom: 20px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  flex: 1;
}

/* CTA Button */
.dept-card-cta {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-family: 'Poppins', sans-serif;
  font-size: 13px;
  font-weight: 600;
  color: #3f4047;
  background: #f3f4f6;
  padding: 10px 20px;
  border-radius: 10px;
  transition: all 0.35s ease;
  align-self: flex-start;
}
.dept-card-cta i {
  transition: transform 0.35s ease;
  font-size: 14px;
}
.dept-card:hover .dept-card-cta {
  background: #3f4047;
  color: #fff;
  gap: 12px;
}
.dept-card:hover .dept-card-cta i {
  transform: translateX(4px);
}

/* Toolbar */
.dept-toolbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  margin-bottom: 32px;
  flex-wrap: wrap;
}
.dept-search {
  position: relative;
  flex: 1;
  min-width: 260px;
  max-width: 400px;
}
.dept-search input {
  width: 100%;
  padding: 12px 16px 12px 44px;
  border: 1.5px solid #e5e7eb;
  border-radius: 12px;
  font-size: 14px;
  font-family: 'Poppins', sans-serif;
  color: #374151;
  background: #fff;
  transition: all 0.3s ease;
}
.dept-search input:focus {
  outline: none;
  border-color: #3f4047;
  box-shadow: 0 0 0 3px rgba(63, 64, 71, 0.08);
}
.dept-search input::placeholder {
  color: #9ca3af;
}
.dept-search input[type="search"]::-webkit-search-cancel-button {
  -webkit-appearance: none;
  appearance: none;
}

/* Search icon */
.dept-search-submit {
  position: absolute;
  left: 8px;
  top: 50%;
  transform: translateY(-50%);
  padding: 4px 8px;
  color: #9ca3af;
  font-size: 16px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  z-index: 1;
  pointer-events: none;
}

/* Clear (×) button */
.dept-search-clear {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: #9ca3af;
  font-size: 16px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  text-decoration: none;
  cursor: pointer;
  transition: color 0.2s ease;
}
.dept-search-clear:hover {
  color: #3f4047;
}

/* Loading spinner */
.dept-search-spinner {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  width: 16px;
  height: 16px;
  border: 2px solid #e5e7eb;
  border-top-color: #3f4047;
  border-radius: 50%;
  animation: dept-spin 0.6s linear infinite;
  display: none;
}
.dept-search-spinner.active {
  display: block;
}
@keyframes dept-spin {
  to { transform: translateY(-50%) rotate(360deg); }
}

/* Loading state on the grid */
#dept-grid.is-loading {
  opacity: 0.5;
  pointer-events: none;
  transition: opacity 0.2s ease;
}

/* Card Meta */
.dept-card-meta {
  margin-bottom: 10px;
}
.dept-card-count {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  font-size: 12px;
  font-weight: 500;
  color: #6b7280;
  background: #f3f4f6;
  padding: 4px 10px;
  border-radius: 99px;
}
.dept-card-count i {
  color: #3f4047;
  font-size: 11px;
}

/* Empty State */
.dept-empty-state {
  padding: 40px 20px;
}
.dept-empty-state i {
  font-size: 64px;
  color: #d1d5db;
  margin-bottom: 16px;
  display: block;
}
.dept-empty-state p {
  color: #9ca3af;
  font-size: 16px;
  margin: 0;
}

/* Responsive */
@media (max-width: 767px) {
  .dept-section-title {
    font-size: 28px;
  }
  .dept-toolbar {
    flex-direction: column;
    align-items: stretch;
  }
  .dept-search {
    max-width: 100%;
  }
  .dept-card-image {
    height: 180px;
  }
  .dept-card-cta {
    width: 100%;
    justify-content: center;
  }
}

@media (min-width: 992px) and (max-width: 1199px) {
  .dept-card-title {
    font-size: 16px;
  }
}
</style>
@endpush

@push('scripts')
<script>
(function () {
  'use strict';

  var input   = document.getElementById('dept-live-search');
  var grid    = document.getElementById('dept-grid');
  var spinner = document.getElementById('dept-spinner');
  var clearBtn = document.getElementById('dept-search-clear');
  if (!input || !grid) return;

  var endpoint = "{{ route('website.departments') }}";
  var debounceTimer;
  var abortController;

  function toggleSpinner(show) {
    if (spinner) spinner.classList.toggle('active', show);
  }

  function toggleClear(show) {
    if (clearBtn) clearBtn.style.display = show ? 'inline-flex' : 'none';
  }

  function fetchDepartments(term) {
    if (abortController) abortController.abort();
    abortController = new AbortController();

    grid.classList.add('is-loading');
    toggleSpinner(true);

    var url = endpoint + (term ? '?search=' + encodeURIComponent(term) : '');

    fetch(url, {
      headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
      signal: abortController.signal
    })
    .then(function (res) { return res.json(); })
    .then(function (data) {
      grid.innerHTML = data.html;
      grid.classList.remove('is-loading');
      toggleSpinner(false);
    })
    .catch(function (err) {
      if (err.name !== 'AbortError') {
        grid.classList.remove('is-loading');
        toggleSpinner(false);
      }
    });
  }

  input.addEventListener('input', function () {
    var term = input.value.trim();
    toggleClear(term.length > 0);

    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(function () {
      fetchDepartments(term);
    }, 350);
  });

  if (clearBtn) {
    clearBtn.addEventListener('click', function () {
      input.value = '';
      toggleClear(false);
      fetchDepartments('');
      input.focus();
    });
  }

  // Prevent accidental form submission if wrapped in a form
  input.addEventListener('keydown', function (e) {
    if (e.key === 'Enter') e.preventDefault();
  });
})();
</script>
@endpush
