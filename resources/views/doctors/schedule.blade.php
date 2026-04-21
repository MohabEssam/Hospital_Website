@extends('layouts.app')

@section('content')
  @php
    $startOfMonth = $calendarDate->copy()->startOfMonth();
    $startOfCalendar = $startOfMonth->copy()->startOfWeek();
    $endOfCalendar = $calendarDate->copy()->endOfMonth()->endOfWeek();
    $calendarDays = [];
    $cursor = $startOfCalendar->copy();

    while ($cursor <= $endOfCalendar) {
        $calendarDays[] = $cursor->copy();
        $cursor->addDay();
    }
  @endphp

  <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
    <div class="d-flex align-items-center gap-2">
      <h5 class="fw-bold mb-0">{{ $calendarDate->format('F Y') }}</h5>
      <a class="btn btn-sm btn-outline-secondary border-0 p-1" href="{{ route('doctors.schedule', [$doctor, 'month' => $calendarDate->copy()->subMonth()->month, 'year' => $calendarDate->copy()->subMonth()->year]) }}"><i class="fas fa-chevron-left text-muted"></i></a>
      <a class="btn btn-sm btn-outline-secondary border-0 p-1" href="{{ route('doctors.schedule', [$doctor, 'month' => $calendarDate->copy()->addMonth()->month, 'year' => $calendarDate->copy()->addMonth()->year]) }}"><i class="fas fa-chevron-right text-muted"></i></a>
    </div>

    <div class="d-flex align-items-center gap-2 flex-wrap">
      <div class="btn-group btn-group-sm">
        <button class="btn btn-outline-secondary" type="button">Day</button>
        <button class="btn btn-outline-secondary" type="button">Week</button>
        <button class="btn btn-dark" type="button">Month</button>
      </div>

      <div class="dropdown">
        <button class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1 dropdown-toggle" data-bs-toggle="dropdown">
          <span>{{ str($doctor->name)->limit(22) }}</span>
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="font-size:.82rem;">
          <li><h6 class="dropdown-header">Agenda</h6></li>
          @foreach ($allDoctors as $agendaDoctor)
            <li>
              <a class="dropdown-item {{ $agendaDoctor->id === $doctor->id ? 'active' : '' }}" href="{{ route('doctors.schedule', ['doctor' => $agendaDoctor, 'month' => $calendarDate->month, 'year' => $calendarDate->year]) }}">
                {{ $agendaDoctor->name }}
              </a>
            </li>
          @endforeach
        </ul>
      </div>

      <a href="{{ route('doctors.show', $doctor) }}" class="btn btn-outline-secondary btn-sm">Doctor Details</a>
      <a href="{{ route('appointments.create', ['doctor_id' => $doctor->id]) }}" class="btn btn-dark btn-sm d-flex align-items-center gap-2">
        <i class="fas fa-plus"></i> Add Schedule
      </a>
    </div>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-body p-0">
      <table class="table table-bordered mb-0" style="table-layout:fixed;">
        <thead class="table-light">
          <tr>
            @foreach (['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $weekday)
              <th class="text-center fw-semibold text-muted small py-2">{{ $weekday }}</th>
            @endforeach
          </tr>
        </thead>
        <tbody>
          @foreach (array_chunk($calendarDays, 7) as $week)
            <tr style="height:130px;">
              @foreach ($week as $day)
                @php
                  $dateKey = $day->toDateString();
                  $events = $appointmentsByDate->get($dateKey, collect());
                @endphp
                <td class="p-2 align-top {{ $day->month !== $calendarDate->month ? 'bg-light' : '' }}">
                  @if ($day->isToday())
                    <span class="d-inline-flex align-items-center justify-content-center bg-dark text-white rounded-circle fw-bold mb-1" style="width:22px;height:22px;font-size:.65rem;">{{ $day->day }}</span>
                  @else
                    <small class="fw-semibold d-block mb-1">{{ $day->day }}</small>
                  @endif

                  @foreach ($events->take(3) as $appointment)
                    <a href="{{ route('appointments.show', $appointment) }}" class="badge w-100 text-start fw-semibold mb-1 text-decoration-none" style="font-size:.62rem;white-space:normal;line-height:1.4;background:{{ $loop->odd ? '#b2f0ea' : '#cde8ff' }};color:{{ $loop->odd ? '#0d7a6f' : '#1a5fa8' }};">
                      {{ $appointment->start_time }}<br>{{ $appointment->patient?->name }}
                    </a>
                  @endforeach

                  @if ($events->count() > 3)
                    <small class="text-muted" style="font-size:.6rem;">+{{ $events->count() - 3 }} more</small>
                  @endif
                </td>
              @endforeach
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  <div class="card border-0 shadow-sm mt-3">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-semibold mb-0">Scheduled Events This Month</h6>
        <span class="text-muted small">{{ $appointmentsByDate->flatten(1)->count() }} appointments</span>
      </div>

      <div class="row g-3">
        @forelse ($appointmentsByDate as $date => $events)
          <div class="col-lg-4">
            <div class="card border-0 bg-light h-100">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <p class="fw-semibold mb-0">{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</p>
                  <span class="text-muted small">{{ $events->count() }}</span>
                </div>
                <div class="d-flex flex-column gap-2">
                  @foreach ($events as $appointment)
                    <a href="{{ route('appointments.show', $appointment) }}" class="text-decoration-none text-dark">
                      <div class="rounded-2 px-2 py-2" style="background:#fff;">
                        <p class="fw-semibold small mb-0">{{ $appointment->start_time }} - {{ $appointment->end_time }}</p>
                        <p class="small text-muted mb-0">{{ $appointment->patient?->name }} - {{ $appointment->treatment }}</p>
                      </div>
                    </a>
                  @endforeach
                </div>
              </div>
            </div>
          </div>
        @empty
          <p class="text-muted small mb-0">No appointments found for this month.</p>
        @endforelse
      </div>
    </div>
  </div>
@endsection
