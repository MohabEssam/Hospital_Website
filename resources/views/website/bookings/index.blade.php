@extends('layouts.website')

@section('title', 'My Bookings - Medicare Hospital')

@section('content')

  <section class="section">
    <div class="container section-title" data-aos="fade-up">
      <h2>My Bookings</h2>
    </div>

    <div class="container" data-aos="fade-up" data-aos-delay="100">

      @if(session('status'))
        <div class="alert alert-success text-center">{{ session('status') }}</div>
      @endif

      @if($appointments instanceof \Illuminate\Pagination\LengthAwarePaginator && $appointments->count() > 0)
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th>Date</th>
                <th>Time</th>
                <th>Doctor</th>
                <th>Department</th>
                <th>Treatment</th>
                <th>Fee</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              @foreach($appointments as $appointment)
              <tr>
                <td>{{ $appointment->appointment_date->format('M d, Y') }}</td>
                <td>{{ $appointment->start_time }} - {{ $appointment->end_time }}</td>
                <td>{{ $appointment->doctor?->name ?? '—' }}</td>
                <td>{{ $appointment->doctor?->department?->name ?? '—' }}</td>
                <td>{{ $appointment->treatment }}</td>
                <td>${{ number_format($appointment->fee, 2) }}</td>
                <td>
                  @if($appointment->status === 'confirmed')
                    <span class="badge bg-success">Confirmed</span>
                  @elseif($appointment->status === 'pending')
                    <span class="badge bg-warning text-dark">Pending</span>
                  @else
                    <span class="badge bg-danger">Cancelled</span>
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <div class="d-flex justify-content-center mt-4">
          {{ $appointments->links() }}
        </div>
      @else
        <div class="text-center py-5">
          <p class="text-muted">You have no bookings yet.</p>
          <a href="{{ route('website.book') }}" class="btn btn-primary" style="border-radius: 25px;">
            <i class="bi bi-calendar-plus"></i> Book Your First Appointment
          </a>
        </div>
      @endif

      <div class="text-center mt-4">
        <a href="{{ route('home') }}" class="btn btn-outline-primary" style="border-radius: 25px;">
          <i class="bi bi-arrow-left"></i> Back to Home
        </a>
      </div>
    </div>
  </section>

@endsection
