@extends('layouts.app')

@section('content')
  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold mb-0">Appointments</h5>
        <div class="d-flex align-items-center gap-2">
          <span class="text-muted small" id="appointmentSelectionCount"></span>
          <button type="button" class="btn btn-dark btn-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addAppointmentModal">
            <i class="fas fa-plus"></i> Add Appointment
          </button>
        </div>
      </div>

      <div class="nav nav-tabs mb-3">
        <a href="{{ route('appointments.index', array_merge(request()->query(), ['status' => null])) }}" class="nav-link {{ empty($filters['status']) ? 'active' : '' }}">
          All <span class="badge bg-secondary ms-1">{{ $statusCounts['all'] }}</span>
        </a>
        <a href="{{ route('appointments.index', array_merge(request()->query(), ['status' => 'confirmed'])) }}" class="nav-link {{ ($filters['status'] ?? '') === 'confirmed' ? 'active' : '' }}">
          Confirmed <span class="badge ms-1" style="background:#d1faf3;color:#0a8c6a;">{{ $statusCounts['confirmed'] }}</span>
        </a>
        <a href="{{ route('appointments.index', array_merge(request()->query(), ['status' => 'pending'])) }}" class="nav-link {{ ($filters['status'] ?? '') === 'pending' ? 'active' : '' }}">
          Pending <span class="badge ms-1" style="background:#fff3cd;color:#856404;">{{ $statusCounts['pending'] }}</span>
        </a>
        <a href="{{ route('appointments.index', array_merge(request()->query(), ['status' => 'cancelled'])) }}" class="nav-link {{ ($filters['status'] ?? '') === 'cancelled' ? 'active' : '' }}">
          Cancelled <span class="badge ms-1" style="background:#fdecea;color:#c0392b;">{{ $statusCounts['cancelled'] }}</span>
        </a>
      </div>

      <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <form method="GET" class="input-group input-group-sm" style="max-width:280px;">
          <input type="hidden" name="status" value="{{ $filters['status'] ?? '' }}">
          <input type="hidden" name="doctor_id" value="{{ $filters['doctor_id'] ?? '' }}">
          <input type="hidden" name="department_id" value="{{ $filters['department_id'] ?? '' }}">
          <input type="hidden" name="appointment_date" value="{{ $filters['appointment_date'] ?? '' }}">
          <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted small"></i></span>
          <input type="text" class="form-control border-start-0 shadow-none" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search name, doctor, treatment...">
        </form>

        <div class="d-flex gap-2 flex-wrap">
          <div class="dropdown">
            <button class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1" data-bs-toggle="dropdown">
              <i class="fas fa-filter" style="font-size:.72rem;"></i> Filter
              <i class="fas fa-chevron-down ms-1" style="font-size:.6rem;"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end shadow-sm border-0 p-3" style="min-width:280px;">
              <form method="GET" class="d-flex flex-column gap-2">
                <input type="hidden" name="status" value="{{ $filters['status'] ?? '' }}">
                <input type="hidden" name="search" value="{{ $filters['search'] ?? '' }}">

                <div>
                  <label class="form-label small fw-semibold mb-1">Doctor</label>
                  <select name="doctor_id" class="form-select form-select-sm">
                    <option value="">All Doctors</option>
                    @foreach ($doctors as $doctor)
                      <option value="{{ $doctor->id }}" @selected(($filters['doctor_id'] ?? '') == $doctor->id)>{{ $doctor->name }}</option>
                    @endforeach
                  </select>
                </div>

                <div>
                  <label class="form-label small fw-semibold mb-1">Department</label>
                  <select name="department_id" class="form-select form-select-sm">
                    <option value="">All Departments</option>
                    @foreach ($departments as $department)
                      <option value="{{ $department->id }}" @selected(($filters['department_id'] ?? '') == $department->id)>{{ $department->name }}</option>
                    @endforeach
                  </select>
                </div>

                <div>
                  <label class="form-label small fw-semibold mb-1">Date</label>
                  <input type="date" name="appointment_date" class="form-control form-control-sm" value="{{ $filters['appointment_date'] ?? '' }}">
                </div>

                <div class="d-flex gap-2 pt-1">
                  <button class="btn btn-dark btn-sm flex-grow-1">Apply</button>
                  <a href="{{ route('appointments.index', array_merge(request()->except(['doctor_id', 'department_id', 'appointment_date', 'page']), ['doctor_id' => '', 'department_id' => '', 'appointment_date' => ''])) }}" class="btn btn-outline-secondary btn-sm flex-grow-1">
                    Reset
                  </a>
                </div>
              </form>
            </div>
          </div>

          <button class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1" type="button" id="exportAppointmentsBtn">
            <i class="fas fa-download" style="font-size:.72rem;"></i> Export
          </button>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th style="width:36px;">
                <input type="checkbox" class="form-check-input" id="checkAllAppointments">
              </th>
              <th class="text-muted fw-semibold small">Name</th>
              <th class="text-muted fw-semibold small">Date</th>
              <th class="text-muted fw-semibold small">Time</th>
              <th class="text-muted fw-semibold small">Doctor</th>
              <th class="text-muted fw-semibold small">Department</th>
              <th class="text-muted fw-semibold small">Treatment</th>
              <th class="text-muted fw-semibold small">Status</th>
              <th class="text-muted fw-semibold small">Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($appointments as $appointment)
              @php
                $statusClasses = [
                    'confirmed' => 'background:#d1faf3;color:#0a8c6a;',
                    'pending' => 'background:#fff3cd;color:#856404;',
                    'cancelled' => 'background:#fdecea;color:#c0392b;',
                ];
              @endphp
              <tr>
                <td>
                  <input type="checkbox" class="form-check-input appointment-row-check" value="{{ $appointment->id }}">
                </td>
                <td class="small fw-medium">{{ $appointment->patient?->name }}</td>
                <td class="text-muted small">{{ $appointment->appointment_date?->format('Y-m-d') }}</td>
                <td class="text-muted small">{{ $appointment->start_time }} - {{ $appointment->end_time }}</td>
                <td class="small">{{ $appointment->doctor?->name }}</td>
                <td class="small">{{ $appointment->department?->name ?? '-' }}</td>
                <td class="small">{{ $appointment->treatment }}</td>
                <td><span class="badge px-3 py-2" style="{{ $statusClasses[$appointment->status] ?? 'background:#e9ecef;color:#495057;' }}">{{ ucfirst($appointment->status) }}</span></td>
                <td>
                  <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary border-0 p-1" data-bs-toggle="dropdown">
                      <i class="fas fa-ellipsis-h text-muted" style="font-size:.7rem;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="font-size:.8rem;">
                      <li>
                        <a class="dropdown-item" href="{{ route('appointments.show', $appointment) }}">
                          <i class="fas fa-eye me-2 text-muted"></i>Open
                        </a>
                      </li>
                      <li>
                        <a class="dropdown-item" href="{{ route('appointments.edit', $appointment) }}">
                          <i class="fas fa-calendar-alt me-2 text-muted"></i>Reschedule
                        </a>
                      </li>
                      <li>
                        <a class="dropdown-item" href="{{ route('doctors.show', $appointment->doctor) }}">
                          <i class="fas fa-user-md me-2 text-muted"></i>View Doctor
                        </a>
                      </li>
                      @if(auth()->user()->isAdmin())
                      <li><hr class="dropdown-divider"></li>
                      <li>
                        <form action="{{ route('appointments.destroy', $appointment) }}" method="POST" onsubmit="return confirm('Delete this appointment?')">
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
                <td colspan="9" class="text-center text-muted py-4 small">No appointments found.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @include('partials.pagination', ['paginator' => $appointments])
    </div>
  </div>

  <div class="modal fade" id="addAppointmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content border-0 shadow">
        <div class="modal-header border-0">
          <h6 class="modal-title fw-bold">Add Appointment</h6>
          <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal"></button>
        </div>
        <form action="{{ route('appointments.store') }}" method="POST">
          @csrf
          <input type="hidden" name="quick_form" value="appointment">

          <div class="modal-body pt-0">
            <div class="row g-3">
              <div class="col-6">
                <label class="form-label small fw-semibold">Patient</label>
                <select class="form-select form-select-sm @error('patient_id') is-invalid @enderror" name="patient_id" required>
                  <option value="">Select patient</option>
                  @foreach ($patients as $patient)
                    <option value="{{ $patient->id }}" @selected(old('quick_form') === 'appointment' && old('patient_id') == $patient->id)>{{ $patient->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-6">
                <label class="form-label small fw-semibold">Doctor</label>
                <select class="form-select form-select-sm @error('doctor_id') is-invalid @enderror" name="doctor_id" required>
                  <option value="">Select doctor</option>
                  @foreach ($doctors as $doctor)
                    <option value="{{ $doctor->id }}" @selected(old('quick_form') === 'appointment' && old('doctor_id') == $doctor->id)>{{ $doctor->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-12">
                <label class="form-label small fw-semibold">Department</label>
                <select class="form-select form-select-sm @error('department_id') is-invalid @enderror" name="department_id">
                  <option value="">Auto from doctor</option>
                  @foreach ($departments as $department)
                    <option value="{{ $department->id }}" @selected(old('quick_form') === 'appointment' && old('department_id') == $department->id)>{{ $department->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-6">
                <label class="form-label small fw-semibold">Date</label>
                <input type="date" class="form-control form-control-sm @error('appointment_date') is-invalid @enderror" name="appointment_date" value="{{ old('quick_form') === 'appointment' ? old('appointment_date', now()->toDateString()) : now()->toDateString() }}" required>
              </div>
              <div class="col-6">
                <label class="form-label small fw-semibold">Start Time</label>
                <input type="time" class="form-control form-control-sm @error('start_time') is-invalid @enderror" name="start_time" value="{{ old('quick_form') === 'appointment' ? old('start_time', '09:00') : '09:00' }}" required>
              </div>
              <div class="col-12">
                <label class="form-label small fw-semibold">Treatment</label>
                <input type="text" class="form-control form-control-sm @error('treatment') is-invalid @enderror" name="treatment" value="{{ old('quick_form') === 'appointment' ? old('treatment') : '' }}" placeholder="e.g. Routine Check-Up" required>
              </div>
              <div class="col-6">
                <label class="form-label small fw-semibold">Status</label>
                <select class="form-select form-select-sm" name="status">
                  @foreach (\App\Models\Appointment::statusOptions() as $status)
                    <option value="{{ $status }}" @selected(old('quick_form') === 'appointment' ? old('status', \App\Models\Appointment::STATUS_PENDING) === $status : \App\Models\Appointment::STATUS_PENDING === $status)>{{ ucfirst($status) }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-6">
                <label class="form-label small fw-semibold">Fee</label>
                <input type="number" min="0" step="0.01" class="form-control form-control-sm" name="fee" value="{{ old('quick_form') === 'appointment' ? old('fee', 0) : 0 }}">
              </div>
            </div>
          </div>

          <div class="modal-footer border-0 pt-0">
            <a href="{{ route('appointments.create') }}" class="btn btn-outline-secondary btn-sm">Open Full Form</a>
            <button type="submit" class="btn btn-dark btn-sm">Save Appointment</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    (() => {
      const selectionCount = document.getElementById('appointmentSelectionCount');
      const exportButton = document.getElementById('exportAppointmentsBtn');
      const checkAll = document.getElementById('checkAllAppointments');
      const rowChecks = () => Array.from(document.querySelectorAll('.appointment-row-check'));

      const updateSelection = () => {
        const checked = rowChecks().filter((checkbox) => checkbox.checked).length;
        selectionCount.textContent = checked > 0 ? `${checked} selected` : '';
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

      exportButton?.addEventListener('click', () => {
        const rows = Array.from(document.querySelectorAll('table tbody tr'))
          .filter((row) => row.querySelectorAll('td').length > 1)
            .map((row) => Array.from(row.querySelectorAll('td'))
            .slice(1, 8)
            .map((cell) => `"${cell.textContent.trim().replaceAll('"', '""')}"`)
            .join(','));

        if (!rows.length) {
          return;
        }

        const csv = ['Name,Date,Time,Doctor,Department,Treatment,Status', ...rows].join('\n');
        const link = document.createElement('a');

        link.href = `data:text/csv;charset=utf-8,${encodeURIComponent(csv)}`;
        link.download = 'appointments.csv';
        link.click();
      });

      @if (old('quick_form') === 'appointment' && $errors->any())
        new bootstrap.Modal(document.getElementById('addAppointmentModal')).show();
      @endif
    })();
  </script>
@endpush
