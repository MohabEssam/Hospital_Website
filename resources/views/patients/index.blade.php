@extends('layouts.app')

@section('content')
  @php
    $statusClasses = [
        'active' => 'badge bg-dark px-3 py-2',
        'new_patient' => 'badge bg-success bg-opacity-25 text-success px-3 py-2',
        'inactive' => 'badge bg-danger bg-opacity-25 text-danger px-3 py-2',
    ];

    $periodLabels = [
        '' => 'All Time',
        'month' => 'This Month',
        'last_30_days' => 'Last 30 Days',
    ];

    $sortLabels = [
        '' => 'Latest First',
        'name' => 'Name A-Z',
        'code' => 'Patient ID',
        'age' => 'Age',
    ];

    $hasActiveFilters = filled($filters['period'] ?? null)
        || filled($filters['status'] ?? null)
        || filled($filters['treatment'] ?? null)
        || filled($filters['search'] ?? null)
        || filled($filters['sort'] ?? null);
  @endphp

  <div class="d-flex align-items-center justify-content-between mb-4">
    <h4 class="fw-bold mb-0">Patients</h4>
    <span class="text-muted small" id="patientSelectionInfo">{{ $patients->total() }} total records</span>
  </div>

  <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
    <div class="dropdown">
      <button class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2 dropdown-toggle" data-bs-toggle="dropdown">
        <i class="fas fa-calendar-alt"></i>
        <span>{{ $periodLabels[$filters['period'] ?? ''] ?? 'All Time' }}</span>
      </button>
      <ul class="dropdown-menu shadow-sm border-0" style="font-size:.82rem;">
        @foreach ($periodLabels as $value => $label)
          <li>
            <a class="dropdown-item {{ ($filters['period'] ?? '') === $value ? 'active' : '' }}" href="{{ route('patients.index', array_merge(request()->except('page'), ['period' => $value])) }}">
              {{ $label }}
            </a>
          </li>
        @endforeach
      </ul>
    </div>

    <div class="dropdown">
      <button class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2 dropdown-toggle" data-bs-toggle="dropdown">
        <i class="fas fa-filter"></i>
        <span>{{ $filters['treatment'] ?? 'All Treatment' }}</span>
      </button>
      <ul class="dropdown-menu shadow-sm border-0" style="font-size:.82rem;">
        <li>
          <a class="dropdown-item {{ blank($filters['treatment'] ?? null) ? 'active' : '' }}" href="{{ route('patients.index', array_merge(request()->except('page'), ['treatment' => ''])) }}">
            All Treatment
          </a>
        </li>
        @foreach ($treatmentOptions as $treatment)
          <li>
            <a class="dropdown-item {{ ($filters['treatment'] ?? '') === $treatment ? 'active' : '' }}" href="{{ route('patients.index', array_merge(request()->except('page'), ['treatment' => $treatment])) }}">
              {{ $treatment }}
            </a>
          </li>
        @endforeach
      </ul>
    </div>

    <div class="dropdown">
      <button class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2 dropdown-toggle" data-bs-toggle="dropdown">
        <i class="fas fa-filter"></i>
        <span>{{ filled($filters['status'] ?? null) ? str($filters['status'])->replace('_', ' ')->title() : 'All Status' }}</span>
      </button>
      <ul class="dropdown-menu shadow-sm border-0" style="font-size:.82rem;">
        <li>
          <a class="dropdown-item {{ blank($filters['status'] ?? null) ? 'active' : '' }}" href="{{ route('patients.index', array_merge(request()->except('page'), ['status' => ''])) }}">
            All Status
          </a>
        </li>
        @foreach (\App\Models\Patient::statusOptions() as $status)
          <li>
            <a class="dropdown-item {{ ($filters['status'] ?? '') === $status ? 'active' : '' }}" href="{{ route('patients.index', array_merge(request()->except('page'), ['status' => $status])) }}">
              {{ str($status)->replace('_', ' ')->title() }}
            </a>
          </li>
        @endforeach
      </ul>
    </div>

    <form method="GET" class="input-group input-group-sm flex-grow-1" style="max-width:300px;">
      <input type="hidden" name="period" value="{{ $filters['period'] ?? '' }}">
      <input type="hidden" name="status" value="{{ $filters['status'] ?? '' }}">
      <input type="hidden" name="treatment" value="{{ $filters['treatment'] ?? '' }}">
      <input type="hidden" name="sort" value="{{ $filters['sort'] ?? '' }}">
      <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
      <input type="text" class="form-control border-start-0 shadow-none" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search ID, name, phone, email, doctor...">
    </form>

    <div class="dropdown">
      <button class="btn btn-outline-secondary btn-sm px-2 dropdown-toggle" data-bs-toggle="dropdown" title="Sort">
        <i class="fas fa-sliders-h"></i>
      </button>
      <ul class="dropdown-menu shadow-sm border-0" style="font-size:.82rem;">
        <li><h6 class="dropdown-header">Sort by</h6></li>
        @foreach ($sortLabels as $value => $label)
          <li>
            <a class="dropdown-item {{ ($filters['sort'] ?? '') === $value ? 'active' : '' }}" href="{{ route('patients.index', array_merge(request()->except('page'), ['sort' => $value])) }}">
              {{ $label }}
            </a>
          </li>
        @endforeach
      </ul>
    </div>

    @if ($hasActiveFilters)
      <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
    @endif

    @if (auth()->user()->isAdmin())
      <button type="button" class="btn btn-dark btn-sm d-flex align-items-center gap-2 ms-auto" data-bs-toggle="modal" data-bs-target="#addPatientModal">
        <i class="fas fa-plus"></i> Add Patient
      </button>
    @endif
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th style="width:36px;">
                <input type="checkbox" class="form-check-input" id="checkAllPatients">
              </th>
              <th class="text-muted fw-semibold small">Name</th>
              <th class="text-muted fw-semibold small">ID</th>
              <th class="text-muted fw-semibold small">Age</th>
              <th class="text-muted fw-semibold small">Check In</th>
              <th class="text-muted fw-semibold small">Treatment</th>
              <th class="text-muted fw-semibold small">Doctor Assigned</th>
              <th class="text-muted fw-semibold small">Room</th>
              <th class="text-muted fw-semibold small">Status</th>
              <th class="text-muted fw-semibold small">Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($patients as $patient)
              <tr>
                <td>
                  <input type="checkbox" class="form-check-input patient-row-check" value="{{ $patient->id }}">
                </td>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <span class="rounded-circle bg-primary bg-opacity-25 text-primary fw-bold d-inline-flex align-items-center justify-content-center" style="width:34px;height:34px;font-size:.72rem;">{{ $patient->initials() }}</span>
                    <span class="fw-medium small">{{ $patient->name }}</span>
                  </div>
                </td>
                <td class="text-muted small">{{ $patient->patient_code }}</td>
                <td class="text-muted small">{{ $patient->age() ?? '--' }}</td>
                <td class="text-muted small">{{ $patient->check_in_date?->format('d M Y') ?? '--' }}</td>
                <td class="small">{{ $patient->treatment ?? '--' }}</td>
                <td class="small">{{ $patient->doctor?->name ?? '--' }}</td>
                <td class="text-muted small">{{ $patient->room_number ?? '--' }}</td>
                <td>
                  <span class="{{ $statusClasses[$patient->status] ?? 'badge bg-secondary px-3 py-2' }}">
                    {{ str($patient->status)->replace('_', ' ')->title() }}
                  </span>
                </td>
                <td>
                  <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary border-0 p-1" data-bs-toggle="dropdown">
                      <i class="fas fa-ellipsis-h text-muted" style="font-size:.7rem;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="font-size:.8rem;">
                      <li>
                        <a class="dropdown-item" href="{{ route('patients.show', $patient) }}">
                          <i class="fas fa-eye me-2 text-muted"></i>View
                        </a>
                      </li>
                      <li>
                        <a class="dropdown-item" href="{{ route('appointments.create', ['patient_id' => $patient->id]) }}">
                          <i class="fas fa-calendar-plus me-2 text-muted"></i>Book Appointment
                        </a>
                      </li>
                      @if (auth()->user()->isAdmin())
                        <li>
                          <a class="dropdown-item" href="{{ route('patients.edit', $patient) }}">
                            <i class="fas fa-pen me-2 text-muted"></i>Edit
                          </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                          <form action="{{ route('patients.destroy', $patient) }}" method="POST" onsubmit="return confirm('Delete this patient?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="dropdown-item text-danger">
                              <i class="fas fa-trash me-2"></i>Delete
                            </button>
                          </form>
                        </li>
                      @endif
                    </ul>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="10" class="text-center text-muted py-4 small">No patients found.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @include('partials.pagination', ['paginator' => $patients])
    </div>
  </div>

  @if (auth()->user()->isAdmin())
    <div class="modal fade" id="addPatientModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
          <div class="modal-header border-0">
            <h6 class="modal-title fw-bold">Add Patient</h6>
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal"></button>
          </div>
          <form action="{{ route('patients.store') }}" method="POST">
            @csrf
            <input type="hidden" name="quick_form" value="patient">

            <div class="modal-body pt-0">
              <div class="row g-3">
                <div class="col-6">
                  <label class="form-label small fw-semibold">Full Name</label>
                  <input type="text" class="form-control form-control-sm @error('name') is-invalid @enderror" name="name" value="{{ old('quick_form') === 'patient' ? old('name') : '' }}" placeholder="Full name" required>
                </div>
                <div class="col-6">
                  <label class="form-label small fw-semibold">Age</label>
                  <input type="number" min="0" max="120" class="form-control form-control-sm" name="age" value="{{ old('quick_form') === 'patient' ? old('age') : '' }}" placeholder="Age">
                </div>
                <div class="col-6">
                  <label class="form-label small fw-semibold">Treatment</label>
                  <input type="text" class="form-control form-control-sm" name="treatment" value="{{ old('quick_form') === 'patient' ? old('treatment') : '' }}" placeholder="Treatment">
                </div>
                <div class="col-6">
                  <label class="form-label small fw-semibold">Doctor</label>
                  <select class="form-select form-select-sm" name="doctor_id">
                    <option value="">Select doctor</option>
                    @foreach ($doctors as $doctor)
                      <option value="{{ $doctor->id }}" @selected(old('quick_form') === 'patient' && old('doctor_id') == $doctor->id)>{{ $doctor->name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-6">
                  <label class="form-label small fw-semibold">Room</label>
                  <input type="text" class="form-control form-control-sm" name="room_number" value="{{ old('quick_form') === 'patient' ? old('room_number') : '' }}" placeholder="e.g. Single - 313">
                </div>
                <div class="col-6">
                  <label class="form-label small fw-semibold">Status</label>
                  <select class="form-select form-select-sm" name="status">
                    @foreach (\App\Models\Patient::statusOptions() as $status)
                      <option value="{{ $status }}" @selected(old('quick_form') === 'patient' ? old('status', \App\Models\Patient::STATUS_ACTIVE) === $status : \App\Models\Patient::STATUS_ACTIVE === $status)>
                        {{ str($status)->replace('_', ' ')->title() }}
                      </option>
                    @endforeach
                  </select>
                </div>
                <div class="col-6">
                  <label class="form-label small fw-semibold">Email</label>
                  <input type="email" class="form-control form-control-sm @error('email') is-invalid @enderror" name="email" value="{{ old('quick_form') === 'patient' ? old('email') : '' }}" placeholder="name@example.com">
                </div>
                <div class="col-6">
                  <label class="form-label small fw-semibold">Check In Date</label>
                  <input type="date" class="form-control form-control-sm" name="check_in_date" value="{{ old('quick_form') === 'patient' ? old('check_in_date', now()->toDateString()) : now()->toDateString() }}">
                </div>
              </div>
            </div>

            <div class="modal-footer border-0 pt-0">
              <a href="{{ route('patients.create') }}" class="btn btn-outline-secondary btn-sm">Open Full Form</a>
              <button type="submit" class="btn btn-dark btn-sm">Save Patient</button>
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
      const selectionInfo = document.getElementById('patientSelectionInfo');
      const checkAll = document.getElementById('checkAllPatients');
      const rowChecks = () => Array.from(document.querySelectorAll('.patient-row-check'));

      const updateSelection = () => {
        const checked = rowChecks().filter((checkbox) => checkbox.checked).length;

        if (checked > 0) {
          selectionInfo.textContent = `${checked} patient${checked > 1 ? 's' : ''} selected`;
          return;
        }

        selectionInfo.textContent = '{{ $patients->total() }} total records';
      };

      checkAll?.addEventListener('change', () => {
        rowChecks().forEach((checkbox) => {
          checkbox.checked = checkAll.checked;
        });

        updateSelection();
      });

      rowChecks().forEach((checkbox) => {
        checkbox.addEventListener('change', () => {
          checkAll.checked = rowChecks().every((rowCheckbox) => rowCheckbox.checked);
          updateSelection();
        });
      });

      @if (auth()->user()->isAdmin() && old('quick_form') === 'patient' && $errors->any())
        new bootstrap.Modal(document.getElementById('addPatientModal')).show();
      @endif
    })();
  </script>
@endpush
