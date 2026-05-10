@extends('layouts.app')

@section('content')
  @php
    $dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    $statusClasses = [
        'confirmed' => 'bg-success bg-opacity-25 text-success',
        'pending' => 'bg-warning bg-opacity-25 text-warning',
        'cancelled' => 'bg-danger bg-opacity-25 text-danger',
    ];
  @endphp

  <div class="d-flex align-items-center justify-content-between gap-3 mb-4">
    <div>
      <p class="mb-0 text-muted small">Doctor Dashboard</p>
      <h4 class="fw-bold mb-0">Welcome, {{ $doctor->name }}</h4>
    </div>
    <a href="{{ route('doctors.schedule', $doctor) }}" class="btn btn-dark btn-sm">
      <i class="fas fa-calendar-day me-1"></i> My Schedule
    </a>
  </div>

  <div class="row g-3 mb-3">
    <div class="col-md-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <p class="text-muted small mb-1">Upcoming Appointments</p>
          <h3 class="fw-bold mb-0">{{ $doctor->upcoming_appointments_count }}</h3>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <p class="text-muted small mb-1">Total Patients</p>
          <h3 class="fw-bold mb-0">{{ $totalPatients }}</h3>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <p class="text-muted small mb-1">Availability</p>
          <span class="badge {{ $doctor->isAvailable() ? 'bg-success' : 'bg-danger' }} rounded-pill px-3 py-2">
            {{ ucfirst($doctor->availability_status) }}
          </span>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-xl-8">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold mb-0">Upcoming Appointments</h6>
            <a href="{{ route('appointments.index') }}" class="text-primary small text-decoration-none">View All</a>
          </div>
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th class="text-muted fw-semibold small">Patient</th>
                  <th class="text-muted fw-semibold small">Date</th>
                  <th class="text-muted fw-semibold small">Time</th>
                  <th class="text-muted fw-semibold small">Treatment</th>
                  <th class="text-muted fw-semibold small">Status</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($upcomingAppointments as $appointment)
                  <tr>
                    <td class="small fw-medium">{{ $appointment->patient?->name }}</td>
                    <td class="text-muted small">{{ $appointment->appointment_date?->format('Y-m-d') }}</td>
                    <td class="text-muted small">{{ $appointment->start_time }} - {{ $appointment->end_time }}</td>
                    <td class="small">{{ $appointment->treatment }}</td>
                    <td><span class="badge px-3 py-2 {{ $statusClasses[$appointment->status] ?? 'bg-secondary text-white' }}">{{ ucfirst($appointment->status) }}</span></td>
                  </tr>
                @empty
                  <tr><td colspan="5" class="text-center text-muted py-4 small">No upcoming appointments.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-4">
      <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
          <h6 class="fw-bold mb-3">Profile Info</h6>
          <div class="d-flex align-items-center gap-3 mb-3">
            <span class="rounded-circle bg-primary bg-opacity-25 text-primary fw-bold d-inline-flex align-items-center justify-content-center" style="width:48px;height:48px;">
              {{ $doctor->initials() }}
            </span>
            <div>
              <p class="fw-semibold mb-0">{{ $doctor->name }}</p>
              <p class="text-muted small mb-0">{{ $doctor->department?->name ?? 'No Department' }}</p>
            </div>
          </div>
          <p class="small mb-1"><span class="text-muted">Email:</span> {{ $doctor->email }}</p>
          <p class="small mb-1"><span class="text-muted">Phone:</span> {{ $doctor->phone ?? 'N/A' }}</p>
          <p class="small mb-0"><span class="text-muted">Specialty:</span> {{ $doctor->specialty }}</p>
        </div>
      </div>

      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <h6 class="fw-bold mb-3">Weekly Schedule</h6>
          <div class="d-flex flex-column gap-2">
            @foreach ($dayNames as $dayNumber => $dayName)
              @php($slots = $weeklySchedule->get($dayNumber, collect()))
              <div class="border-top pt-2">
                <p class="fw-semibold small mb-1">{{ $dayName }}</p>
                @forelse ($slots as $slot)
                  <span class="badge bg-light text-dark border me-1 mb-1">{{ $slot->start_time }} - {{ $slot->end_time }}</span>
                @empty
                  <span class="text-muted small">No available slots.</span>
                @endforelse
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
