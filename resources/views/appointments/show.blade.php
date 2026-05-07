@extends('layouts.app')

@section('content')
  <div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('appointments.index') }}" class="btn btn-outline-secondary btn-sm rounded-circle p-2 lh-1">
      <i class="fas fa-arrow-left"></i>
    </a>
    <div>
      <p class="mb-0 text-muted small">Appointment summary</p>
      <h4 class="fw-bold mb-0">{{ $appointment->patient?->name }} - {{ $appointment->appointment_date?->format('d M Y') }}</h4>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-6">
              <p class="text-muted small mb-1">Patient</p>
              <p class="fw-semibold mb-0">{{ $appointment->patient?->name }}</p>
            </div>
            <div class="col-md-6">
              <p class="text-muted small mb-1">Doctor</p>
              <p class="fw-semibold mb-0">{{ $appointment->doctor?->name }}</p>
            </div>
            <div class="col-md-6">
              <p class="text-muted small mb-1">Department</p>
              <p class="fw-semibold mb-0">{{ $appointment->department?->name ?? '-' }}</p>
            </div>
            <div class="col-md-4">
              <p class="text-muted small mb-1">Date</p>
              <p class="fw-semibold mb-0">{{ $appointment->appointment_date?->format('d M Y') }}</p>
            </div>
            <div class="col-md-4">
              <p class="text-muted small mb-1">Start Time</p>
              <p class="fw-semibold mb-0">{{ $appointment->start_time }}</p>
            </div>
            <div class="col-md-4">
              <p class="text-muted small mb-1">End Time</p>
              <p class="fw-semibold mb-0">{{ $appointment->end_time }}</p>
            </div>
            <div class="col-md-6">
              <p class="text-muted small mb-1">Treatment</p>
              <p class="fw-semibold mb-0">{{ $appointment->treatment }}</p>
            </div>
            <div class="col-md-3">
              <p class="text-muted small mb-1">Status</p>
              <p class="fw-semibold mb-0 text-capitalize">{{ $appointment->status }}</p>
            </div>
            <div class="col-md-3">
              <p class="text-muted small mb-1">Fee</p>
              <p class="fw-semibold mb-0">${{ number_format((float) $appointment->fee, 2) }}</p>
            </div>
            <div class="col-12">
              <p class="text-muted small mb-1">Notes</p>
              <p class="mb-0">{{ $appointment->notes ?: 'No notes attached to this appointment.' }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <h6 class="fw-semibold mb-3">Quick Actions</h6>
          <div class="d-grid gap-2">
            @if($appointment->status === 'pending')
              <form action="{{ route('appointments.status', $appointment) }}" method="POST">
                @csrf @method('PATCH')
                <input type="hidden" name="status" value="confirmed">
                <button type="submit" class="btn btn-success w-100 mb-1"><i class="fas fa-check me-1"></i> Confirm Appointment</button>
              </form>
              <form action="{{ route('appointments.status', $appointment) }}" method="POST">
                @csrf @method('PATCH')
                <input type="hidden" name="status" value="cancelled">
                <button type="submit" class="btn btn-outline-danger w-100" onclick="return confirm('Reject this appointment?')"><i class="fas fa-times me-1"></i> Reject Appointment</button>
              </form>
              <hr class="my-1">
            @endif
            <a href="{{ route('appointments.edit', $appointment) }}" class="btn btn-dark">Edit Appointment</a>
            @if ($appointment->doctor)
              <a href="{{ route('doctors.show', $appointment->doctor) }}" class="btn btn-outline-secondary">View Doctor</a>
            @endif
            @if ($appointment->patient)
              <a href="{{ route('patients.show', $appointment->patient) }}" class="btn btn-outline-secondary">View Patient</a>
            @endif
            @if(auth()->user()->isAdmin())
            <form action="{{ route('appointments.destroy', $appointment) }}" method="POST">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-outline-danger w-100" onclick="return confirm('Delete this appointment?')">Delete Appointment</button>
            </form>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
