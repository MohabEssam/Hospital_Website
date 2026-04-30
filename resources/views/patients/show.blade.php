@extends('layouts.app')

@section('content')
  <div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary btn-sm rounded-circle p-2 lh-1">
      <i class="fas fa-arrow-left"></i>
    </a>
    <div>
      <p class="mb-0 text-muted small">Patient record overview</p>
      <h4 class="fw-bold mb-0">{{ $patient->name }}</h4>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-lg-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body text-center pt-4">
          <img src="{{ $patient->avatar_path ? asset($patient->avatar_path) : asset('assets/images/profile/user-1.jpg') }}" alt="{{ $patient->name }}"
            class="rounded-3 mb-3 object-fit-cover"
            style="width:130px;height:130px;">

          <h5 class="fw-bold mb-0">{{ $patient->name }}</h5>
          <p class="text-muted small mb-2">{{ $patient->patient_code }}</p>
          <span class="badge bg-dark px-3 py-2 mb-3">{{ str($patient->status)->replace('_', ' ')->title() }}</span>

          <hr>

          <div class="text-start mb-3">
            <p class="text-muted small mb-1">Assigned Doctor</p>
            <p class="fw-medium mb-0">{{ $patient->doctor?->name ?? 'Not assigned' }}</p>
          </div>

          <div class="text-start mb-3">
            <p class="text-muted small mb-1">Treatment</p>
            <p class="small mb-0 text-secondary">{{ $patient->treatment ?? 'No treatment recorded.' }}</p>
          </div>

          <div class="text-start d-flex flex-column gap-2">
            <div class="d-flex align-items-center gap-2 small text-secondary">
              <i class="fas fa-envelope text-info"></i> {{ $patient->email ?? 'No email' }}
            </div>
            <div class="d-flex align-items-center gap-2 small text-secondary">
              <i class="fas fa-phone text-info"></i> {{ $patient->phone ?? 'No phone' }}
            </div>
            <div class="d-flex align-items-center gap-2 small text-secondary">
              <i class="fas fa-bed text-info"></i> {{ $patient->room_number ?? 'No room assigned' }}
            </div>
          </div>

          @if (auth()->user()->isAdmin())
            <div class="d-grid gap-2 mt-4">
              <a href="{{ route('patients.edit', $patient) }}" class="btn btn-dark btn-sm">Edit Patient</a>
              <form action="{{ route('patients.destroy', $patient) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger btn-sm w-100" onclick="return confirm('Delete this patient?')">Delete Patient</button>
              </form>
            </div>
          @endif
        </div>
      </div>
    </div>

    <div class="col-lg-8">
      <div class="row g-3 mb-3">
        <div class="col-md-4">
          <div class="card border-0 shadow-sm">
            <div class="card-body py-3">
              <p class="text-muted small mb-1">Age</p>
              <h3 class="fw-bold mb-0">{{ $patient->age() ?? '--' }}</h3>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card border-0 shadow-sm">
            <div class="card-body py-3">
              <p class="text-muted small mb-1">Appointments</p>
              <h3 class="fw-bold mb-0">{{ $appointments->count() }}</h3>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card border-0 shadow-sm">
            <div class="card-body py-3">
              <p class="text-muted small mb-1">Check In</p>
              <h6 class="fw-bold mb-0">{{ $patient->check_in_date?->format('d M Y') ?? '--' }}</h6>
            </div>
          </div>
        </div>
      </div>

      <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
          <h6 class="fw-semibold mb-3">Patient Notes</h6>
          <p class="text-secondary mb-0">{{ $patient->notes ?: 'No notes available for this patient yet.' }}</p>
        </div>
      </div>

      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-semibold mb-0">Appointment History</h6>
            @if (auth()->user()->isAdmin())
              <a href="{{ route('appointments.create', ['patient_id' => $patient->id]) }}" class="btn btn-dark btn-sm">Book Appointment</a>
            @endif
          </div>

          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th class="text-muted fw-semibold small">Date</th>
                  <th class="text-muted fw-semibold small">Time</th>
                  <th class="text-muted fw-semibold small">Doctor</th>
                  <th class="text-muted fw-semibold small">Treatment</th>
                  <th class="text-muted fw-semibold small">Status</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($appointments as $appointment)
                  <tr>
                    <td class="small">{{ $appointment->appointment_date?->format('d M Y') }}</td>
                    <td class="small">{{ $appointment->start_time }} - {{ $appointment->end_time }}</td>
                    <td class="small">{{ $appointment->doctor?->name }}</td>
                    <td class="small">{{ $appointment->treatment }}</td>
                    <td class="small text-capitalize">{{ $appointment->status }}</td>
                  </tr>
                @empty
                  <tr><td colspan="5" class="text-center text-muted py-4 small">No appointments found.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
