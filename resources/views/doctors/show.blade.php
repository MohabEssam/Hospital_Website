@extends('layouts.app')

@section('content')
  @php
    $weekDays = collect(range(0, 5))
        ->map(fn (int $offset) => now()->copy()->startOfWeek()->addDays($offset));
  @endphp

  <div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('doctors.index') }}" class="btn btn-outline-secondary btn-sm rounded-circle p-2 lh-1">
      <i class="fas fa-arrow-left"></i>
    </a>
    <div>
      <p class="mb-0 text-muted small">Back to Doctor List</p>
      <h4 class="fw-bold mb-0">Doctor Details</h4>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-lg-3">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body text-center pt-4">
          <img src="{{ $doctor->avatar_path ? asset($doctor->avatar_path) : asset('assets/images/profile/user-1.jpg') }}" alt="{{ $doctor->name }}"
            class="rounded-3 mb-3 object-fit-cover"
            style="width:130px;height:130px;">

          <h5 class="fw-bold mb-0">{{ $doctor->name }}</h5>
          <p class="text-muted small mb-2">{{ $doctor->doctor_code }}</p>
          <span class="badge border {{ $doctor->isAvailable() ? 'border-info text-info' : 'border-danger text-danger' }} bg-transparent px-3 py-2 mb-3">
            <i class="fas fa-circle me-1" style="font-size:.45rem;vertical-align:middle;"></i> {{ ucfirst($doctor->availability_status) }}
          </span>

          <hr>

          <div class="text-start mb-3">
            <p class="text-muted small mb-1">Specialist</p>
            <p class="fw-medium mb-0">{{ $doctor->specialty }}</p>
          </div>

          <div class="text-start mb-3">
            <p class="text-muted small mb-1">About</p>
            <p class="small mb-0 text-secondary">
              {{ $doctor->biography ?: 'No biography has been added for this doctor yet.' }}
            </p>
          </div>

          <hr>

          <div class="text-start d-flex flex-column gap-2">
            <div class="d-flex align-items-center gap-2 small text-secondary">
              <i class="fas fa-phone text-info"></i> {{ $doctor->phone ?? 'No phone' }}
            </div>
            <div class="d-flex align-items-center gap-2 small text-secondary">
              <i class="fas fa-envelope text-info"></i> {{ $doctor->email ?? 'No email' }}
            </div>
            <div class="d-flex align-items-center gap-2 small text-secondary">
              <i class="fas fa-map-marker-alt text-info"></i> {{ $doctor->address ?? 'No address' }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="row g-3 mb-3">
        <div class="col-6">
          <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center justify-content-between py-3">
              <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-info bg-opacity-10 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                  <i class="fas fa-user-injured text-info fs-5"></i>
                </div>
                <div>
                  <p class="text-muted small mb-0">Total Patients</p>
                  <h3 class="fw-bold mb-0">{{ $doctor->patients_count }}</h3>
                </div>
              </div>
              <button class="btn btn-sm btn-outline-secondary border-0 p-1 align-self-start" type="button"><i class="fas fa-ellipsis-v text-muted"></i></button>
            </div>
          </div>
        </div>
        <div class="col-6">
          <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center justify-content-between py-3">
              <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-info bg-opacity-10 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                  <i class="fas fa-calendar-check text-info fs-5"></i>
                </div>
                <div>
                  <p class="text-muted small mb-0">Total Appointments</p>
                  <h3 class="fw-bold mb-0">{{ $doctor->appointments_count }}</h3>
                </div>
              </div>
              <button class="btn btn-sm btn-outline-secondary border-0 p-1 align-self-start" type="button"><i class="fas fa-ellipsis-v text-muted"></i></button>
            </div>
          </div>
        </div>
      </div>

      <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="fw-semibold mb-0">Appointment Stats</h6>
            <a href="{{ route('doctors.schedule', $doctor) }}" class="btn btn-dark btn-sm d-flex align-items-center gap-1">
              View Schedule <i class="fas fa-chevron-right ms-1"></i>
            </a>
          </div>
          <div id="appointmentChart"></div>

          <div class="row g-2 mt-2">
            <div class="col-4">
              <div class="card border-0 bg-light">
                <div class="card-body py-2 px-3">
                  <div class="d-flex align-items-center gap-2">
                    <div class="rounded-1 bg-dark" style="width:4px;height:36px;"></div>
                    <div>
                      <h5 class="fw-bold mb-0">{{ $doctor->appointments_count }}</h5>
                      <p class="text-muted small mb-0">Total Appointments</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-4">
              <div class="card border-0 bg-light">
                <div class="card-body py-2 px-3">
                  <div class="d-flex align-items-center gap-2">
                    <div class="rounded-1 bg-dark" style="width:4px;height:36px;"></div>
                    <div>
                      <h5 class="fw-bold mb-0">{{ array_sum($newPatientSeries) }}</h5>
                      <p class="text-muted small mb-0">New Patients</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-4">
              <div class="card border-0 bg-light">
                <div class="card-body py-2 px-3">
                  <div class="d-flex align-items-center gap-2">
                    <div class="rounded-1 bg-dark" style="width:4px;height:36px;"></div>
                    <div>
                      <h5 class="fw-bold mb-0">{{ array_sum($followUpSeries) }}</h5>
                      <p class="text-muted small mb-0">Follow-Up Patients</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-semibold mb-0">Feedback</h6>
            <button class="btn btn-sm btn-outline-secondary border-0 p-1" type="button"><i class="fas fa-ellipsis-h text-muted"></i></button>
          </div>
          <div class="row g-3">
            @forelse ($feedbackPatients as $appointment)
              <div class="col-6 col-xl-3">
                <div class="card border-0 bg-light">
                  <div class="card-body p-3">
                    <span class="rounded-circle d-inline-flex align-items-center justify-content-center bg-secondary bg-opacity-25 mb-2" style="width:42px;height:42px;">
                      <i class="fas fa-user text-secondary"></i>
                    </span>
                    <p class="fw-semibold mb-0 small">{{ $appointment->patient?->name }}</p>
                    <p class="text-muted mb-1" style="font-size:.7rem;">{{ $appointment->appointment_date?->format('Y-m-d') }}</p>
                    <p class="small text-secondary mb-0">Consultation completed successfully.</p>
                  </div>
                </div>
              </div>
            @empty
              <p class="text-muted small mb-0">No recent feedback yet.</p>
            @endforelse
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-3">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-semibold mb-0">Schedule</h6>
            <a href="{{ route('appointments.create', ['doctor_id' => $doctor->id]) }}" class="btn btn-sm btn-outline-secondary border-0 p-1"><i class="fas fa-plus text-muted"></i></a>
          </div>

          <div class="d-flex align-items-center justify-content-between mb-3">
            <button class="btn btn-sm btn-outline-secondary border-0 p-1" type="button"><i class="fas fa-chevron-left text-muted"></i></button>
            <div class="d-flex gap-1">
              @foreach ($weekDays as $day)
                <div class="text-center px-2 py-1 rounded-2 {{ $day->isToday() ? 'bg-dark text-white' : '' }}">
                  <p class="mb-0 {{ $day->isToday() ? 'text-white' : 'text-muted' }}" style="font-size:.65rem;">{{ $day->format('D') }}</p>
                  <p class="fw-semibold mb-0 small {{ $day->isToday() ? 'text-white' : '' }}">{{ $day->format('d') }}</p>
                </div>
              @endforeach
            </div>
            <button class="btn btn-sm btn-outline-secondary border-0 p-1" type="button"><i class="fas fa-chevron-right text-muted"></i></button>
          </div>

          <p class="text-muted small mb-3">{{ $todaySchedule->count() }} schedules today</p>

          @forelse ($todaySchedule as $appointment)
            <div class="d-flex gap-2 border-top py-2 align-items-start">
              <span class="text-muted flex-shrink-0" style="font-size:.66rem;width:55px;padding-top:2px;">{{ $appointment->start_time }}</span>
              <div class="rounded-2 px-2 py-2 flex-grow-1" style="background:{{ $loop->odd ? '#b2f0ea' : '#cde8ff' }};color:{{ $loop->odd ? '#0a6e68' : '#1a5fa8' }};">
                <p class="fw-bold mb-0" style="font-size:.7rem;">{{ $appointment->treatment }}</p>
                <p class="mb-0" style="font-size:.63rem;opacity:.85;">{{ $appointment->patient?->name }}</p>
              </div>
            </div>
          @empty
            <p class="text-muted small mb-0">No appointments scheduled today.</p>
          @endforelse
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    new ApexCharts(document.querySelector('#appointmentChart'), {
      chart: { type: 'bar', height: 220, toolbar: { show: false }, stacked: false },
      series: [
        { name: 'New Patient', data: @json($newPatientSeries) },
        { name: 'Follow-Up Patient', data: @json($followUpSeries) }
      ],
      colors: ['#1a2e4a', '#7ec8c8'],
      plotOptions: {
        bar: { columnWidth: '45%', borderRadius: 4, grouped: true }
      },
      dataLabels: { enabled: false },
      xaxis: {
        categories: @json($chartLabels),
        labels: { style: { fontSize: '11px' } }
      },
      yaxis: { min: 0 },
      legend: { show: false },
      grid: { borderColor: '#f0f0f0' },
      tooltip: { shared: true, intersect: false }
    }).render();
  </script>
@endpush
