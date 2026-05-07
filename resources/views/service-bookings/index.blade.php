@extends('layouts.app')

@section('content')
  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold mb-0">Service Bookings</h5>
      </div>

      <div class="nav nav-tabs mb-3">
        <a href="{{ route('service-bookings.index') }}" class="nav-link {{ $statusFilter === 'all' ? 'active' : '' }}">
          All <span class="badge bg-secondary ms-1">{{ $counts['all'] }}</span>
        </a>
        <a href="{{ route('service-bookings.index', ['status' => 'pending']) }}" class="nav-link {{ $statusFilter === 'pending' ? 'active' : '' }}">
          Pending <span class="badge ms-1" style="background:#fff3cd;color:#856404;">{{ $counts['pending'] }}</span>
        </a>
        <a href="{{ route('service-bookings.index', ['status' => 'confirmed']) }}" class="nav-link {{ $statusFilter === 'confirmed' ? 'active' : '' }}">
          Confirmed <span class="badge ms-1" style="background:#d1faf3;color:#0a8c6a;">{{ $counts['confirmed'] }}</span>
        </a>
        <a href="{{ route('service-bookings.index', ['status' => 'rejected']) }}" class="nav-link {{ $statusFilter === 'rejected' ? 'active' : '' }}">
          Rejected <span class="badge ms-1" style="background:#fdecea;color:#c0392b;">{{ $counts['rejected'] }}</span>
        </a>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th class="text-muted fw-semibold small">Patient</th>
              <th class="text-muted fw-semibold small">Service</th>
              <th class="text-muted fw-semibold small">Date</th>
              <th class="text-muted fw-semibold small">Time</th>
              <th class="text-muted fw-semibold small">Phone</th>
              <th class="text-muted fw-semibold small">Status</th>
              <th class="text-muted fw-semibold small">Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($bookings as $booking)
              @php
                $statusStyles = [
                    'pending' => 'background:#fff3cd;color:#856404;',
                    'confirmed' => 'background:#d1faf3;color:#0a8c6a;',
                    'rejected' => 'background:#fdecea;color:#c0392b;',
                ];
              @endphp
              <tr>
                <td class="small fw-medium">{{ $booking->patient?->name ?? 'N/A' }}</td>
                <td class="small">{{ $booking->service?->name ?? 'N/A' }}</td>
                <td class="text-muted small">{{ $booking->booking_date?->format('Y-m-d') }}</td>
                <td class="text-muted small">{{ $booking->booking_time }}</td>
                <td class="small">{{ $booking->phone_number }}</td>
                <td><span class="badge px-3 py-2" style="{{ $statusStyles[$booking->status] ?? 'background:#e9ecef;color:#495057;' }}">{{ ucfirst($booking->status) }}</span></td>
                <td>
                  @if($booking->status === 'pending')
                    <div class="d-flex gap-1">
                      <form action="{{ route('service-bookings.status', $booking) }}" method="POST">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="confirmed">
                        <button type="submit" class="btn btn-success btn-sm" title="Confirm"><i class="fas fa-check"></i></button>
                      </form>
                      <form action="{{ route('service-bookings.status', $booking) }}" method="POST" onsubmit="return confirm('Reject this booking?')">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="rejected">
                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Reject"><i class="fas fa-times"></i></button>
                      </form>
                    </div>
                  @else
                    <span class="text-muted small">—</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center text-muted py-4 small">No service bookings found.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @include('partials.pagination', ['paginator' => $bookings])
    </div>
  </div>
@endsection
