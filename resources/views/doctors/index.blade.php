@extends('layouts.app')

@section('content')
  @php
    $hasActiveFilters = filled($filters['department_id'] ?? null)
        || filled($filters['availability_status'] ?? null)
        || filled($filters['search'] ?? null);
  @endphp

  <h4 class="fw-bold mb-4">Doctors</h4>

  <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
    <form method="GET" action="{{ route('doctors.index') }}" class="input-group input-group-sm" style="max-width:260px;" data-doctor-search-form>
      <input type="hidden" name="department_id" value="{{ $filters['department_id'] ?? '' }}">
      <input type="hidden" name="availability_status" value="{{ $filters['availability_status'] ?? '' }}">
      <button type="submit" class="input-group-text bg-white border-end-0 border-end-0" aria-label="Search doctors"><i class="fas fa-search text-muted"></i></button>
      <input type="search" class="form-control border-start-0 shadow-none" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search doctors..." autocomplete="off" data-doctor-search-input>
    </form>

    <div class="dropdown">
      <button class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2 dropdown-toggle" data-bs-toggle="dropdown">
        <i class="fas fa-filter"></i>
        <span>{{ $departments->firstWhere('id', $filters['department_id'] ?? null)?->name ?? 'Department' }}</span>
      </button>
      <ul class="dropdown-menu shadow-sm border-0" style="font-size:.82rem;">
        <li>
          <a class="dropdown-item {{ blank($filters['department_id'] ?? null) ? 'active' : '' }}" href="{{ route('doctors.index', array_merge(request()->except('page', 'specialty'), ['department_id' => ''])) }}">
            All Departments
          </a>
        </li>
        @foreach ($departments as $department)
          <li>
            <a class="dropdown-item {{ ($filters['department_id'] ?? '') == $department->id ? 'active' : '' }}" href="{{ route('doctors.index', array_merge(request()->except('page', 'specialty'), ['department_id' => $department->id])) }}">
              {{ $department->name }}
            </a>
          </li>
        @endforeach
      </ul>
    </div>

    <div class="dropdown">
      <button class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2 dropdown-toggle" data-bs-toggle="dropdown">
        <i class="fas fa-filter"></i>
        <span>{{ filled($filters['availability_status'] ?? null) ? ucfirst($filters['availability_status']) : 'Status' }}</span>
      </button>
      <ul class="dropdown-menu shadow-sm border-0" style="font-size:.82rem;">
        <li>
          <a class="dropdown-item {{ blank($filters['availability_status'] ?? null) ? 'active' : '' }}" href="{{ route('doctors.index', array_merge(request()->except('page', 'specialty'), ['availability_status' => ''])) }}">
            All Statuses
          </a>
        </li>
        @foreach (\App\Models\Doctor::availabilityOptions() as $status)
          <li>
            <a class="dropdown-item {{ ($filters['availability_status'] ?? '') === $status ? 'active' : '' }}" href="{{ route('doctors.index', array_merge(request()->except('page', 'specialty'), ['availability_status' => $status])) }}">
              {{ ucfirst($status) }}
            </a>
          </li>
        @endforeach
      </ul>
    </div>

    @if ($hasActiveFilters)
      <a href="{{ route('doctors.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
    @endif

    @if (auth()->user()->isAdmin())
      <button type="button" class="btn btn-dark btn-sm d-flex align-items-center gap-2 ms-auto" data-bs-toggle="modal" data-bs-target="#addDoctorModal">
        <i class="fas fa-plus"></i> Add Doctor
      </button>
    @endif
  </div>

  <div data-doctors-results-wrapper>
    @include('doctors._table', ['doctors' => $doctors])
  </div>

  @if (auth()->user()->isAdmin())
    <div class="modal fade" id="addDoctorModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
          <div class="modal-header border-0">
            <h6 class="modal-title fw-bold">Add Doctor</h6>
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal"></button>
          </div>
          <form action="{{ route('doctors.store') }}" method="POST">
            @csrf
            <input type="hidden" name="quick_form" value="doctor">

            <div class="modal-body pt-0">
              <div class="row g-3">
                <div class="col-6">
                  <label class="form-label small fw-semibold">Full Name</label>
                  <input type="text" class="form-control form-control-sm @error('name') is-invalid @enderror" name="name" value="{{ old('quick_form') === 'doctor' ? old('name') : '' }}" placeholder="Dr. Full Name" required>
                </div>
                <div class="col-6">
                  <label class="form-label small fw-semibold">Doctor ID</label>
                  <input type="text" class="form-control form-control-sm" name="doctor_code" value="{{ old('quick_form') === 'doctor' ? old('doctor_code') : '' }}" placeholder="WNH-XX-001">
                </div>
                <div class="col-6">
                  <label class="form-label small fw-semibold">Department</label>
                  <select class="form-select form-select-sm @error('department_id') is-invalid @enderror" name="department_id" required>
                    <option value="">Select department</option>
                    @foreach ($departments as $department)
                      <option value="{{ $department->id }}" @selected(old('quick_form') === 'doctor' && old('department_id') == $department->id)>{{ $department->name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-6">
                  <label class="form-label small fw-semibold">Specialty</label>
                  <input type="text" class="form-control form-control-sm @error('specialty') is-invalid @enderror" name="specialty" value="{{ old('quick_form') === 'doctor' ? old('specialty') : '' }}" placeholder="e.g. Heart Specialist" required>
                </div>
                <div class="col-6">
                  <label class="form-label small fw-semibold">Availability</label>
                  <select class="form-select form-select-sm" name="availability_status">
                    @foreach (\App\Models\Doctor::availabilityOptions() as $status)
                      <option value="{{ $status }}" @selected(old('quick_form') === 'doctor' ? old('availability_status', \App\Models\Doctor::STATUS_AVAILABLE) === $status : \App\Models\Doctor::STATUS_AVAILABLE === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-6">
                  <label class="form-label small fw-semibold">Consultation Fee</label>
                  <input type="number" min="0" step="0.01" class="form-control form-control-sm" name="consultation_fee" value="{{ old('quick_form') === 'doctor' ? old('consultation_fee', 0) : 0 }}">
                </div>
                <div class="col-6">
                  <label class="form-label small fw-semibold">Email</label>
                  <input type="email" class="form-control form-control-sm @error('email') is-invalid @enderror" name="email" value="{{ old('quick_form') === 'doctor' ? old('email') : '' }}" placeholder="name@example.com">
                </div>
                <div class="col-6">
                  <label class="form-label small fw-semibold">Phone</label>
                  <input type="text" class="form-control form-control-sm" name="phone" value="{{ old('quick_form') === 'doctor' ? old('phone') : '' }}" placeholder="+1 555-234-5678">
                </div>
              </div>
            </div>

            <div class="modal-footer border-0 pt-0">
              <a href="{{ route('doctors.create') }}" class="btn btn-outline-secondary btn-sm">Open Full Form</a>
              <button type="submit" class="btn btn-dark btn-sm">Save Doctor</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  @endif
@endsection

@push('scripts')
  <script>
    (() => {
      const searchForm = document.querySelector('[data-doctor-search-form]');
      const searchInput = document.querySelector('[data-doctor-search-input]');
      const resultsWrapper = document.querySelector('[data-doctors-results-wrapper]');
      let searchTimer = null;
      let inFlight = null;

      const updateDoctors = async (url) => {
        if (!resultsWrapper) return;

        try {
          if (inFlight) inFlight.abort();
          inFlight = new AbortController();

          const response = await fetch(url, {
            headers: {
              'Accept': 'application/json',
              'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
            signal: inFlight.signal,
          });

          if (!response.ok) throw new Error('Request failed: ' + response.status);

          const data = await response.json();
          resultsWrapper.innerHTML = data.html;
          window.history.replaceState({}, '', data.url);
        } catch (error) {
          if (error.name !== 'AbortError') console.error('Doctor search failed:', error);
        }
      };

      if (searchForm && searchInput && resultsWrapper) {
        searchForm.addEventListener('submit', (event) => {
          event.preventDefault();
          updateDoctors(searchForm.action + '?' + new URLSearchParams(new FormData(searchForm)).toString());
        });

        searchInput.addEventListener('input', () => {
          window.clearTimeout(searchTimer);
          searchTimer = window.setTimeout(() => {
            updateDoctors(searchForm.action + '?' + new URLSearchParams(new FormData(searchForm)).toString());
          }, 300);
        });

        resultsWrapper.addEventListener('click', (event) => {
          const link = event.target.closest('.pagination a');

          if (!link) return;

          event.preventDefault();
          updateDoctors(link.href);
        });
      }

      @if (auth()->user()->isAdmin() && old('quick_form') === 'doctor' && $errors->any())
        new bootstrap.Modal(document.getElementById('addDoctorModal')).show();
      @endif
    })();
  </script>
@endpush
