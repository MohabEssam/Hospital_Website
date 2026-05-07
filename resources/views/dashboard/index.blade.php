@extends('layouts.app')

@section('content')
  @php
    $availableDoctorRatio = $stats['doctors'] > 0
        ? (int) round(($stats['available_doctors'] / $stats['doctors']) * 100)
        : 0;

    $miniCalendarDate = now();
    $miniCalendarStart = $miniCalendarDate->copy()->startOfMonth()->startOfWeek();
    $miniCalendarEnd = $miniCalendarDate->copy()->endOfMonth()->endOfWeek();
    $miniCalendarDays = [];
    $miniCursor = $miniCalendarStart->copy();

    while ($miniCursor <= $miniCalendarEnd) {
        $miniCalendarDays[] = $miniCursor->copy();
        $miniCursor->addDay();
    }
  @endphp

  <div class="row g-3">
    <div class="col-xl-9">
      <div class="row g-3 mb-3">
        @if(auth()->user()->isAdmin())
        <div class="col-6 col-xl-3">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body py-3">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="d-flex align-items-center gap-2">
                  <span class="rounded-2 bg-light d-inline-flex align-items-center justify-content-center" style="width:32px;height:32px;"><i class="fas fa-user-md"></i></span>
                  <span class="text-muted small">Active Doctors</span>
                </div>
                <div class="dropdown">
                  <button class="btn btn-sm btn-outline-secondary border-0 p-0" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-h text-muted" style="font-size:.7rem;"></i></button>
                  <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="font-size:.8rem;">
                    <li><a class="dropdown-item" href="{{ route('doctors.index') }}">View Details</a></li>
                    <li><a class="dropdown-item" href="{{ route('doctors.index', ['availability_status' => 'available']) }}">Available Doctors</a></li>
                  </ul>
                </div>
              </div>
              <h4 class="fw-bold mb-1">{{ $stats['available_doctors'] }}</h4>
              <div class="d-flex align-items-center gap-2">
                <span class="badge rounded-pill fw-semibold d-inline-flex align-items-center gap-1" style="background:#e6f9f0;color:#1a9c5b;font-size:.68rem;">
                  <i class="fas fa-arrow-up" style="font-size:.5rem;"></i>{{ $availableDoctorRatio }}%
                </span>
                <span class="text-muted" style="font-size:.7rem;">of all doctors ready for booking</span>
              </div>
            </div>
          </div>
        </div>
        @endif

        @if(auth()->user()->isDoctor() && $doctor)
        <div class="col-6 col-xl-3">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body py-3">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="d-flex align-items-center gap-2">
                  <span class="rounded-2 bg-light d-inline-flex align-items-center justify-content-center" style="width:32px;height:32px;"><i class="fas fa-stethoscope"></i></span>
                  <span class="text-muted small">My Availability</span>
                </div>
              </div>
              <form action="{{ route('availability.update') }}" method="POST" class="d-flex align-items-center gap-2">
                @csrf
                <select name="availability_status" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                  <option value="available" @selected($doctor->availability_status === 'available')>Available</option>
                  <option value="unavailable" @selected($doctor->availability_status === 'unavailable')>Unavailable</option>
                </select>
                <span class="badge {{ $doctor->isAvailable() ? 'bg-success' : 'bg-danger' }} rounded-pill px-2" style="font-size:.68rem;">{{ ucfirst($doctor->availability_status) }}</span>
              </form>
            </div>
          </div>
        </div>
        @endif

        <div class="col-6 col-xl-3">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body py-3">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="d-flex align-items-center gap-2">
                  <span class="rounded-2 bg-light d-inline-flex align-items-center justify-content-center" style="width:32px;height:32px;"><i class="fas fa-user-injured text-muted small"></i></span>
                  <span class="text-muted small">Total Patients</span>
                </div>
                <div class="dropdown">
                  <button class="btn btn-sm btn-outline-secondary border-0 p-0" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-h text-muted" style="font-size:.7rem;"></i></button>
                  <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="font-size:.8rem;">
                    <li><a class="dropdown-item" href="{{ route('patients.index') }}">View All Patients</a></li>
                    <li><a class="dropdown-item" href="{{ route('patients.index', ['period' => 'month']) }}">This Month</a></li>
                  </ul>
                </div>
              </div>
              <h4 class="fw-bold mb-1">{{ $stats['patients'] }}</h4>
              <div class="d-flex align-items-center gap-2">
                <span class="badge rounded-pill fw-semibold d-inline-flex align-items-center gap-1" style="background:#e6f9f0;color:#1a9c5b;font-size:.68rem;">
                  <i class="fas fa-arrow-up" style="font-size:.5rem;"></i>{{ $stats['patients_this_month'] }}
                </span>
                <span class="text-muted" style="font-size:.7rem;">new patients added this month</span>
              </div>
            </div>
          </div>
        </div>

        <div class="col-6 col-xl-3">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body py-3">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="d-flex align-items-center gap-2">
                  <span class="rounded-2 bg-light d-inline-flex align-items-center justify-content-center" style="width:32px;height:32px;"><i class="fas fa-calendar-check text-muted small"></i></span>
                  <span class="text-muted small">Appointments</span>
                </div>
                <div class="dropdown">
                  <button class="btn btn-sm btn-outline-secondary border-0 p-0" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-h text-muted" style="font-size:.7rem;"></i></button>
                  <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="font-size:.8rem;">
                    <li><a class="dropdown-item" href="{{ route('appointments.index') }}">View All Appointments</a></li>
                    <li><a class="dropdown-item" href="{{ route('appointments.index', ['appointment_date' => now()->toDateString()]) }}">Today's Schedule</a></li>
                  </ul>
                </div>
              </div>
              <h4 class="fw-bold mb-1">{{ $stats['appointments'] }}</h4>
              <div class="d-flex align-items-center gap-2">
                <span class="badge rounded-pill fw-semibold d-inline-flex align-items-center gap-1" style="background:#fff3cd;color:#856404;font-size:.68rem;">
                  <i class="fas fa-clock" style="font-size:.5rem;"></i>{{ $stats['appointments_today'] }}
                </span>
                <span class="text-muted" style="font-size:.7rem;">appointments scheduled for today</span>
              </div>
            </div>
          </div>
        </div>

        @if(auth()->user()->isAdmin())
        <div class="col-6 col-xl-3">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body py-3">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="d-flex align-items-center gap-2">
                  <span class="rounded-2 bg-light d-inline-flex align-items-center justify-content-center" style="width:32px;height:32px;"><i class="fas fa-stethoscope text-muted small"></i></span>
                  <span class="text-muted small">Total Doctors</span>
                </div>
                <div class="dropdown">
                  <button class="btn btn-sm btn-outline-secondary border-0 p-0" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-h text-muted" style="font-size:.7rem;"></i></button>
                  <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="font-size:.8rem;">
                    <li><a class="dropdown-item" href="{{ route('doctors.index') }}">Doctor Directory</a></li>
                    <li><a class="dropdown-item" href="{{ route('departments.index') }}">Departments</a></li>
                  </ul>
                </div>
              </div>
              <h4 class="fw-bold mb-1">{{ $stats['doctors'] }}</h4>
              <div class="d-flex align-items-center gap-2">
                <span class="badge rounded-pill fw-semibold d-inline-flex align-items-center gap-1" style="background:#eef5ff;color:#1a5fa8;font-size:.68rem;">
                  <i class="fas fa-building" style="font-size:.5rem;"></i>{{ $stats['departments'] }}
                </span>
                <span class="text-muted" style="font-size:.7rem;">departments represented across the hospital</span>
              </div>
            </div>
          </div>
        </div>
        @endif
      </div>

      <div class="row g-3 mb-3">
        <div class="col-lg-6">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start mb-1">
                <div>
                  <h6 class="fw-bold mb-0">Patient Overview</h6>
                  <p class="text-muted mb-2" style="font-size:.72rem;">by Age Stages</p>
                </div>
                <div class="dropdown" id="patientOverviewWidget" data-endpoint="{{ route('dashboard.patient-overview') }}">
                  <button class="btn btn-dark btn-sm d-flex align-items-center gap-1 dropdown-toggle" id="overviewDropdown" data-bs-toggle="dropdown" style="font-size:.75rem;">
                    Current Snapshot
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="font-size:.8rem;">
                    <li><button class="dropdown-item" type="button" data-range="current" data-label="Current Snapshot">Current Snapshot</button></li>
                    <li><button class="dropdown-item" type="button" data-range="quarter" data-label="This Quarter">This Quarter</button></li>
                    <li><button class="dropdown-item" type="button" data-range="year" data-label="This Year">This Year</button></li>
                  </ul>
                </div>
              </div>
              <div class="d-flex gap-3 mb-2">
                <span class="d-flex align-items-center gap-1" style="font-size:.7rem;"><span class="rounded-circle d-inline-block flex-shrink-0" style="width:10px;height:10px;background:#1a2e4a;"></span> Child <strong class="ms-1" data-overview-type="child">{{ $patientAgeGroups['child'] }}</strong></span>
                <span class="d-flex align-items-center gap-1" style="font-size:.7rem;"><span class="rounded-circle d-inline-block flex-shrink-0" style="width:10px;height:10px;background:#3eb8b0;"></span> Adult <strong class="ms-1" data-overview-type="adult">{{ $patientAgeGroups['adult'] }}</strong></span>
                <span class="d-flex align-items-center gap-1" style="font-size:.7rem;"><span class="rounded-circle d-inline-block flex-shrink-0" style="width:10px;height:10px;background:#b2e0f5;"></span> Elderly <strong class="ms-1" data-overview-type="elderly">{{ $patientAgeGroups['elderly'] }}</strong></span>
              </div>
              <div id="patientOverviewChart" style="min-height:240px;max-height:280px;position:relative;"></div>
            </div>
          </div>
        </div>

        <div class="col-lg-6">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <h6 class="fw-bold mb-0">Revenue</h6>
                <div class="btn-group btn-group-sm" id="revenueToggle">
                  <button class="btn btn-dark" style="font-size:.72rem;padding:3px 10px;" type="button" onclick="switchRevenue(this, 'week')">Week</button>
                  <button class="btn btn-outline-secondary" style="font-size:.72rem;padding:3px 10px;" type="button" onclick="switchRevenue(this, 'month')">Month</button>
                  <button class="btn btn-outline-secondary" style="font-size:.72rem;padding:3px 10px;" type="button" onclick="switchRevenue(this, 'year')">Year</button>
                </div>
              </div>
              <div class="d-flex gap-3 mb-2">
                <span class="d-flex align-items-center gap-1" style="font-size:.7rem;"><span class="rounded-circle d-inline-block flex-shrink-0" style="width:10px;height:10px;background:#1a2e4a;"></span> Confirmed</span>
                <span class="d-flex align-items-center gap-1" style="font-size:.7rem;"><span class="rounded-circle d-inline-block flex-shrink-0" style="width:10px;height:10px;background:#3eb8b0;"></span> Pending</span>
              </div>
              <div id="revenueChart"></div>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-3 mb-3">
        <div class="col-lg-7">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                  <h6 class="fw-bold mb-0">Appointments This Week</h6>
                  <p class="text-muted mb-0" style="font-size:.7rem;">Last 7 days</p>
                </div>
                <a href="{{ route('appointments.index') }}" class="text-primary small text-decoration-none">View All</a>
              </div>
              <div id="appointmentsPerDayChart"></div>
            </div>
          </div>
        </div>
        <div class="col-lg-5">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="fw-bold mb-0">Status Distribution</h6>
              </div>
              @if (array_sum($statusDistribution) > 0)
                <div id="statusDistributionChart"></div>
              @else
                <div class="rounded-3 bg-light text-center py-5">
                  <p class="text-muted small mb-0">No appointments yet.</p>
                </div>
              @endif
            </div>
          </div>
        </div>
      </div>

      @if(auth()->user()->isAdmin())
      @if($stats['pending_service_bookings'] > 0)
      <div class="alert border-0 shadow-sm d-flex align-items-center gap-3 mb-3" style="background:#eef5ff;border-radius:12px;">
        <span class="rounded-circle d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width:40px;height:40px;background:#1a5fa8;">
          <i class="fas fa-concierge-bell text-white"></i>
        </span>
        <div class="flex-grow-1">
          <p class="fw-semibold mb-0" style="font-size:.85rem;">{{ $stats['pending_service_bookings'] }} Pending Service Booking{{ $stats['pending_service_bookings'] > 1 ? 's' : '' }}</p>
          <p class="text-muted mb-0" style="font-size:.72rem;">Patient Care bookings awaiting admin review.</p>
        </div>
        <a href="{{ route('service-bookings.index', ['status' => 'pending']) }}" class="btn btn-dark btn-sm">Review</a>
      </div>
      @endif

      <div class="row g-3 mb-3">

        <div class="col-lg-12">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0">Doctors Schedule</h6>
                <a href="{{ route('doctors.index') }}" class="text-primary small text-decoration-none">View All</a>
              </div>
              <div class="d-flex flex-column gap-3">
                @forelse ($topDoctors as $doctor)
                  <div class="d-flex align-items-center gap-2" style="cursor:pointer;" onclick="window.location='{{ route('doctors.show', $doctor) }}'">
                    <span class="rounded-circle bg-primary bg-opacity-25 text-primary fw-bold d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width:34px;height:34px;font-size:.68rem;">{{ $doctor->initials() }}</span>
                    <div class="flex-grow-1 min-width-0">
                      <p class="fw-semibold small mb-0 text-truncate">{{ $doctor->name }}</p>
                      <p class="text-muted mb-0" style="font-size:.68rem;">{{ $doctor->department?->name ?? 'No Department' }}</p>
                    </div>
                    <span class="badge fw-semibold border" style="font-size:.62rem;{{ $doctor->isAvailable() ? 'background:#d1faf3;color:#0a8c6a;border-color:#a7f3e0 !important;' : 'background:#fdecea;color:#c0392b;border-color:#fbbcbc !important;' }}">{{ ucfirst($doctor->availability_status) }}</span>
                  </div>
                @empty
                  <p class="text-muted small mb-0">No doctors available.</p>
                @endforelse
              </div>
            </div>
          </div>
        </div>
      </div>
      @endif

      @php
        $pendingCount = collect($recentAppointments)->where('status', 'pending')->count();
      @endphp
      @if($pendingCount > 0)
      <div class="alert border-0 shadow-sm d-flex align-items-center gap-3 mb-3" style="background:#fff8e1;border-radius:12px;">
        <span class="rounded-circle d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width:40px;height:40px;background:#ffc107;">
          <i class="fas fa-clock text-white"></i>
        </span>
        <div class="flex-grow-1">
          <p class="fw-semibold mb-0" style="font-size:.85rem;">{{ $pendingCount }} Pending Appointment{{ $pendingCount > 1 ? 's' : '' }} Awaiting Action</p>
          <p class="text-muted mb-0" style="font-size:.72rem;">Review and confirm or reject pending bookings from patients.</p>
        </div>
        <a href="{{ route('appointments.index', ['status' => 'pending']) }}" class="btn btn-dark btn-sm">Review Now</a>
      </div>
      @endif

      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold mb-0">Patient Appointment</h6>
            <a href="{{ route('appointments.index') }}" class="text-primary small text-decoration-none">View All</a>
          </div>

          <div class="d-flex align-items-center gap-2 mb-3">
            <button class="btn btn-sm btn-outline-secondary border-0 p-1" type="button" disabled><i class="fas fa-chevron-left text-muted small"></i></button>
            <div class="d-flex align-items-center gap-1 flex-grow-1 overflow-auto pb-1">
              @foreach ($appointmentDateStrip as $date)
                <a href="{{ route('appointments.index', ['appointment_date' => $date['full_date']]) }}" class="text-decoration-none" style="color:inherit;">
                  <div class="text-center px-2 py-1 rounded-2 {{ $date['is_today'] ? 'bg-dark text-white' : 'border bg-white' }}" style="min-width:56px;cursor:pointer;transition:transform .15s;">
                    <p class="mb-0 {{ $date['is_today'] ? 'text-white' : 'text-muted' }}" style="font-size:.65rem;">{{ $date['label'] }}</p>
                    <p class="fw-semibold mb-0 small {{ $date['is_today'] ? 'text-white' : '' }}">{{ $date['day'] }}</p>
                    <p class="mb-0 {{ $date['is_today'] ? 'text-white-50' : 'text-muted' }}" style="font-size:.58rem;">{{ $date['count'] }}</p>
                  </div>
                </a>
              @endforeach
            </div>
            <button class="btn btn-sm btn-outline-secondary border-0 p-1" type="button" disabled><i class="fas fa-chevron-right text-muted small"></i></button>
          </div>

          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th class="text-muted fw-semibold small">Name</th>
                  <th class="text-muted fw-semibold small">Date</th>
                  <th class="text-muted fw-semibold small">Time</th>
                  <th class="text-muted fw-semibold small">Doctor</th>
                  <th class="text-muted fw-semibold small">Treatment</th>
                  <th class="text-muted fw-semibold small">Status</th>
                  <th class="text-muted fw-semibold small"></th>
                </tr>
              </thead>
              <tbody>
                @forelse ($recentAppointments as $appointment)
                  @php
                    $statusClasses = [
                        'confirmed' => 'bg-success bg-opacity-25 text-success',
                        'pending' => 'bg-warning bg-opacity-25 text-warning',
                        'cancelled' => 'bg-danger bg-opacity-25 text-danger',
                    ];
                  @endphp
                  <tr>
                    <td class="small fw-medium">{{ $appointment->patient?->name }}</td>
                    <td class="text-muted small">{{ $appointment->appointment_date?->format('Y-m-d') }}</td>
                    <td class="text-muted small">{{ $appointment->start_time }}</td>
                    <td class="small">{{ $appointment->doctor?->name }}</td>
                    <td class="small">{{ $appointment->treatment }}</td>
                    <td><span class="badge px-3 py-2 {{ $statusClasses[$appointment->status] ?? 'bg-secondary text-white' }}">{{ ucfirst($appointment->status) }}</span></td>
                    <td>
                      <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary border-0 p-1" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-h text-muted" style="font-size:.7rem;"></i></button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="font-size:.8rem;">
                          <li><a class="dropdown-item" href="{{ route('appointments.show', $appointment) }}">Open</a></li>
                          @if(auth()->user()->isAdmin())
                          <li><a class="dropdown-item" href="{{ route('appointments.edit', $appointment) }}">Reschedule</a></li>
                          @endif
                          @if($appointment->status === 'pending')
                          <li><hr class="dropdown-divider"></li>
                          <li>
                            <form action="{{ route('appointments.status', $appointment) }}" method="POST">
                              @csrf @method('PATCH')
                              <input type="hidden" name="status" value="confirmed">
                              <button type="submit" class="dropdown-item text-success"><i class="fas fa-check me-2"></i>Confirm</button>
                            </form>
                          </li>
                          <li>
                            <form action="{{ route('appointments.status', $appointment) }}" method="POST">
                              @csrf @method('PATCH')
                              <input type="hidden" name="status" value="cancelled">
                              <button type="submit" class="dropdown-item text-danger"><i class="fas fa-times me-2"></i>Reject</button>
                            </form>
                          </li>
                          @endif
                        </ul>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="7" class="text-center text-muted py-4 small">No appointments found.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-3">
      <div class="d-flex flex-column gap-3" style="position:sticky;top:70px;max-height:calc(100vh - 90px);overflow-y:auto;overflow-x:hidden;scrollbar-width:thin;">
        <div class="card border-0 shadow-sm">
          <div class="card-body pb-2">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h6 class="fw-bold mb-0">{{ now()->format('F Y') }}</h6>
              <div class="d-flex gap-1">
                <button class="btn btn-sm btn-outline-secondary border-0 p-1" type="button" disabled><i class="fas fa-chevron-left text-muted" style="font-size:.65rem;"></i></button>
                <button class="btn btn-sm btn-outline-secondary border-0 p-1" type="button" disabled><i class="fas fa-chevron-right text-muted" style="font-size:.65rem;"></i></button>
              </div>
            </div>

            <table class="table table-borderless text-center align-middle mb-0">
              <thead>
                <tr>
                  @foreach (['S', 'M', 'T', 'W', 'T', 'F', 'S'] as $weekday)
                    <th class="text-muted fw-semibold px-1 py-1" style="font-size:.6rem;">{{ $weekday }}</th>
                  @endforeach
                </tr>
              </thead>
              <tbody>
                @foreach (array_chunk($miniCalendarDays, 7) as $week)
                  <tr>
                    @foreach ($week as $day)
                      @php
                        $count = $miniCalendarCounts[$day->toDateString()] ?? 0;
                        $dayUrl = route('appointments.index', ['appointment_date' => $day->toDateString()]);
                      @endphp
                      <td class="px-1 py-1">
                        <a href="{{ $dayUrl }}" class="d-block text-decoration-none" style="color:inherit;" title="{{ $count }} appointment{{ $count === 1 ? '' : 's' }} on {{ $day->format('M d') }}">
                          <div class="rounded-2 {{ $day->isToday() ? 'bg-dark text-white' : ($day->month !== $miniCalendarDate->month ? 'bg-light text-muted' : '') }} mini-cal-cell" style="min-height:40px;padding:.25rem;cursor:pointer;transition:background .15s;">
                            <p class="fw-semibold mb-0" style="font-size:.68rem;">{{ $day->day }}</p>
                            @if ($count > 0)
                              <p class="mb-0 {{ $day->isToday() ? 'text-white-50' : 'text-primary' }}" style="font-size:.52rem;">{{ $count }}</p>
                            @endif
                          </div>
                        </a>
                      </td>
                    @endforeach
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>

        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h6 class="fw-semibold mb-0">Today Schedule</h6>
              <a href="{{ route('appointments.create') }}" class="btn btn-dark rounded-circle p-0 d-inline-flex align-items-center justify-content-center" style="width:24px;height:24px;"><i class="fas fa-plus" style="font-size:.55rem;"></i></a>
            </div>

            @forelse ($todaySchedule as $appointment)
              <div class="d-flex gap-2 border-top py-2 align-items-start">
                <span class="text-muted flex-shrink-0" style="font-size:.66rem;width:40px;padding-top:2px;">{{ $appointment->start_time }}</span>
                <div class="rounded-2 px-2 py-2 flex-grow-1" style="background:{{ $loop->odd ? '#b2f0ea' : '#cde8ff' }};color:{{ $loop->odd ? '#0a6e68' : '#1a5fa8' }};">
                  <p class="fw-bold mb-0" style="font-size:.7rem;">{{ $appointment->treatment }}</p>
                  <p class="mb-0" style="font-size:.63rem;opacity:.85;">{{ $appointment->patient?->name }} with {{ $appointment->doctor?->name }}</p>
                </div>
              </div>
            @empty
              <p class="text-muted small mb-0">No schedules for today.</p>
            @endforelse
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    const revenueDatasets = @json($revenueDatasets);

    const patientOverviewChart = new ApexCharts(document.querySelector('#patientOverviewChart'), {
      chart: { type: 'donut', height: 240, toolbar: { show: false } },
      labels: ['Child', 'Adult', 'Elderly'],
      series: @json(array_values($patientAgeGroups)),
      colors: ['#1a2e4a', '#3eb8b0', '#b2e0f5'],
      legend: { show: false },
      dataLabels: { enabled: false },
      stroke: { width: 0 }
    });

    patientOverviewChart.render();

    const revenueChart = new ApexCharts(document.querySelector('#revenueChart'), {
      chart: { type: 'line', height: 240, toolbar: { show: false } },
      series: [
        { name: 'Confirmed', data: revenueDatasets.week.income },
        { name: 'Pending', data: revenueDatasets.week.pending },
      ],
      xaxis: { categories: revenueDatasets.week.labels },
      colors: ['#1a2e4a', '#3eb8b0'],
      stroke: { curve: 'smooth', width: 3 },
      dataLabels: { enabled: false },
      legend: { show: false },
      grid: { borderColor: '#f0f0f0' },
      yaxis: { labels: { formatter: (v) => '$' + Number(v).toFixed(0) } }
    });

    revenueChart.render();

    const deptChartElement = document.querySelector('#deptDonutChart');

    if (deptChartElement) {
      new ApexCharts(deptChartElement, {
        chart: { type: 'donut', height: 220, toolbar: { show: false } },
        series: @json($departmentDistribution->pluck('doctors_count')->values()),
        labels: @json($departmentDistribution->pluck('name')->values()),
        colors: ['#1a2e4a', '#3eb8b0', '#b2e0f5', '#dee2e6'],
        legend: { show: false },
        dataLabels: { enabled: false },
        stroke: { width: 0 }
      }).render();
    }

    window.switchRevenue = (button, key) => {
      const dataset = revenueDatasets[key];

      document.querySelectorAll('#revenueToggle button').forEach((toggleButton) => {
        toggleButton.className = 'btn btn-outline-secondary';
        toggleButton.style.fontSize = '.72rem';
        toggleButton.style.padding = '3px 10px';
      });

      button.className = 'btn btn-dark';
      button.style.fontSize = '.72rem';
      button.style.padding = '3px 10px';

      revenueChart.updateOptions({
        xaxis: { categories: dataset.labels }
      });

      revenueChart.updateSeries([
        { name: 'Confirmed', data: dataset.income },
        { name: 'Pending', data: dataset.pending }
      ]);
    };

    // Appointments per day (last 7 days) — real data
    const perDayEl = document.querySelector('#appointmentsPerDayChart');
    if (perDayEl) {
      new ApexCharts(perDayEl, {
        chart: { type: 'bar', height: 180, toolbar: { show: false }, sparkline: { enabled: false } },
        plotOptions: { bar: { borderRadius: 4, columnWidth: '55%' } },
        series: [{ name: 'Appointments', data: @json($appointmentsPerDaySeries) }],
        xaxis: { categories: @json($revenueLabels) },
        colors: ['#3eb8b0'],
        dataLabels: { enabled: false },
        grid: { borderColor: '#f0f0f0' },
        legend: { show: false }
      }).render();
    }

    // Status distribution — real data
    const statusEl = document.querySelector('#statusDistributionChart');
    if (statusEl) {
      new ApexCharts(statusEl, {
        chart: { type: 'donut', height: 200, toolbar: { show: false } },
        series: @json(array_values($statusDistribution)),
        labels: ['Confirmed', 'Pending', 'Cancelled'],
        colors: ['#0a8c6a', '#f0b429', '#c0392b'],
        legend: { position: 'bottom', fontSize: '11px' },
        dataLabels: { enabled: false },
        stroke: { width: 0 }
      }).render();
    }

    // --- Patient Overview: dynamic range switcher ---
    (function () {
      const widget = document.getElementById('patientOverviewWidget');
      if (!widget) return;

      const endpoint = widget.dataset.endpoint;
      const labelEl = document.getElementById('overviewDropdown');
      let inFlight = null;

      const applyData = (data) => {
        const child = Number(data.child) || 0;
        const adult = Number(data.adult) || 0;
        const elderly = Number(data.elderly) || 0;

        widget.closest('.card').querySelectorAll('[data-overview-type]').forEach((el) => {
          const type = el.dataset.overviewType;
          el.textContent = { child, adult, elderly }[type] ?? 0;
        });

        // Guard against chart crash when all values are zero.
        const series = (child + adult + elderly) === 0 ? [0, 0, 0] : [child, adult, elderly];
        patientOverviewChart.updateSeries(series);
      };

      widget.querySelectorAll('[data-range]').forEach((button) => {
        button.addEventListener('click', async (e) => {
          e.preventDefault();
          const range = button.dataset.range;
          const label = button.dataset.label;

          labelEl.textContent = label;
          labelEl.classList.add('disabled');

          try {
            if (inFlight) inFlight.abort();
            inFlight = new AbortController();

            const response = await fetch(endpoint + '?range=' + encodeURIComponent(range), {
              headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
              credentials: 'same-origin',
              signal: inFlight.signal,
            });

            if (!response.ok) throw new Error('Request failed: ' + response.status);
            const data = await response.json();
            applyData(data);
          } catch (err) {
            if (err.name !== 'AbortError') console.error('Patient overview fetch failed:', err);
          } finally {
            labelEl.classList.remove('disabled');
          }
        });
      });
    })();
  </script>
@endpush
