@extends('layouts.app')

@section('content')
  @php
    $hasActiveFilters = filled($filters['department_id'] ?? null)
        || filled($filters['specialty'] ?? null)
        || filled($filters['availability_status'] ?? null)
        || filled($filters['search'] ?? null);
  @endphp

  <h4 class="fw-bold mb-4">Doctors</h4>

  <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
    <form method="GET" class="input-group input-group-sm" style="max-width:260px;">
      <input type="hidden" name="department_id" value="{{ $filters['department_id'] ?? '' }}">
      <input type="hidden" name="specialty" value="{{ $filters['specialty'] ?? '' }}">
      <input type="hidden" name="availability_status" value="{{ $filters['availability_status'] ?? '' }}">
      <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
      <input type="text" class="form-control border-start-0 shadow-none" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search name, ID, specialist...">
    </form>

    <div class="dropdown">
      <button class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2 dropdown-toggle" data-bs-toggle="dropdown">
        <i class="fas fa-filter"></i>
        <span>{{ $departments->firstWhere('id', $filters['department_id'] ?? null)?->name ?? 'Department' }}</span>
      </button>
      <ul class="dropdown-menu shadow-sm border-0" style="font-size:.82rem;">
        <li>
          <a class="dropdown-item {{ blank($filters['department_id'] ?? null) ? 'active' : '' }}" href="{{ route('doctors.index', array_merge(request()->except('page'), ['department_id' => ''])) }}">
            All Departments
          </a>
        </li>
        @foreach ($departments as $department)
          <li>
            <a class="dropdown-item {{ ($filters['department_id'] ?? '') == $department->id ? 'active' : '' }}" href="{{ route('doctors.index', array_merge(request()->except('page'), ['department_id' => $department->id])) }}">
              {{ $department->name }}
            </a>
          </li>
        @endforeach
      </ul>
    </div>

    <div class="dropdown">
      <button class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2 dropdown-toggle" data-bs-toggle="dropdown">
        <i class="fas fa-filter"></i>
        <span>{{ $filters['specialty'] ?? 'Specialist' }}</span>
      </button>
      <ul class="dropdown-menu shadow-sm border-0" style="font-size:.82rem;">
        <li>
          <a class="dropdown-item {{ blank($filters['specialty'] ?? null) ? 'active' : '' }}" href="{{ route('doctors.index', array_merge(request()->except('page'), ['specialty' => ''])) }}">
            All Specialists
          </a>
        </li>
        @foreach ($specialties as $specialty)
          <li>
            <a class="dropdown-item {{ ($filters['specialty'] ?? '') === $specialty ? 'active' : '' }}" href="{{ route('doctors.index', array_merge(request()->except('page'), ['specialty' => $specialty])) }}">
              {{ $specialty }}
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
          <a class="dropdown-item {{ blank($filters['availability_status'] ?? null) ? 'active' : '' }}" href="{{ route('doctors.index', array_merge(request()->except('page'), ['availability_status' => ''])) }}">
            All Statuses
          </a>
        </li>
        @foreach (\App\Models\Doctor::availabilityOptions() as $status)
          <li>
            <a class="dropdown-item {{ ($filters['availability_status'] ?? '') === $status ? 'active' : '' }}" href="{{ route('doctors.index', array_merge(request()->except('page'), ['availability_status' => $status])) }}">
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

  <div class="card border-0 shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th class="text-muted fw-semibold small">Name</th>
              <th class="text-muted fw-semibold small">ID</th>
              <th class="text-muted fw-semibold small">Department</th>
              <th class="text-muted fw-semibold small">Specialist</th>
              <th class="text-muted fw-semibold small">Total Patients</th>
              <th class="text-muted fw-semibold small">Today's Appointment</th>
              <th class="text-muted fw-semibold small">Availability</th>
              <th class="text-muted fw-semibold small">Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($doctors as $doctor)
              <tr style="cursor:pointer;" onclick="window.location='{{ route('doctors.show', $doctor) }}'">
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <span class="rounded-circle bg-primary bg-opacity-25 text-primary fw-bold d-inline-flex align-items-center justify-content-center" style="width:34px;height:34px;font-size:.72rem;">{{ $doctor->initials() }}</span>
                    <span class="fw-medium small">{{ $doctor->name }}</span>
                  </div>
                </td>
                <td class="text-muted small">{{ $doctor->doctor_code }}</td>
                <td class="small">{{ $doctor->department?->name ?? '--' }}</td>
                <td class="small">{{ $doctor->specialty }}</td>
                <td class="small">{{ $doctor->patients_count }}</td>
                <td class="small">{{ $doctor->today_appointments_count }}</td>
                <td>
                  <span class="badge border {{ $doctor->isAvailable() ? 'border-info text-info' : 'border-danger text-danger' }} bg-transparent px-3 py-2">
                    {{ ucfirst($doctor->availability_status) }}
                  </span>
                </td>
                <td>
                  <div class="d-flex gap-1" onclick="event.stopPropagation()">
                    <a href="{{ route('doctors.show', $doctor) }}" class="btn btn-sm btn-outline-secondary border-0 p-1" title="View">
                      <i class="fas fa-eye text-muted" style="font-size:.75rem;"></i>
                    </a>
                    @if (auth()->user()->isAdmin())
                      <a href="{{ route('doctors.edit', $doctor) }}" class="btn btn-sm btn-outline-secondary border-0 p-1" title="Edit">
                        <i class="fas fa-edit text-muted" style="font-size:.75rem;"></i>
                      </a>
                      <form action="{{ route('doctors.destroy', $doctor) }}" method="POST" onsubmit="return confirm('Delete this doctor?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-secondary border-0 p-1" title="Delete">
                          <i class="fas fa-trash text-danger" style="font-size:.75rem;"></i>
                        </button>
                      </form>
                    @endif
                    <a href="{{ route('doctors.schedule', $doctor) }}" class="btn btn-sm btn-outline-secondary border-0 p-1" title="Schedule">
                      <i class="fas fa-calendar-day text-muted" style="font-size:.75rem;"></i>
                    </a>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="text-center text-muted py-4 small">No doctors found.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @include('partials.pagination', ['paginator' => $doctors])
    </div>
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
      @if (auth()->user()->isAdmin() && old('quick_form') === 'doctor' && $errors->any())
        new bootstrap.Modal(document.getElementById('addDoctorModal')).show();
      @endif
    })();
  </script>
@endpush
