@extends('layouts.website')

@section('title', 'My Bookings - Medicare Hospital')

@section('content')

<section class="section">
  <div class="container section-title reveal">
    <h2>My Bookings</h2>
    <p>View and manage your appointments</p>
  </div>

  <div class="container reveal" style="transition-delay: 100ms">

    {{-- Session Messages --}}
    @if(session('status'))
      <div class="alert alert-success d-flex align-items-center gap-2 mb-4" role="alert" style="border-radius:12px;">
        <i class="bi bi-check-circle-fill fs-5"></i>
        <span>{{ session('status') }}</span>
      </div>
    @endif

    @if(session('error'))
      <div class="alert alert-danger d-flex align-items-center gap-2 mb-4" role="alert" style="border-radius:12px;">
        <i class="bi bi-exclamation-circle-fill fs-5"></i>
        <span>{{ session('error') }}</span>
      </div>
    @endif

    {{-- Phone Search for Guests --}}
    @guest
      <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
        <div class="card-body p-4">
          <h5 class="mb-3"><i class="bi bi-search me-2"></i>Find Your Bookings</h5>
          <p class="text-muted mb-3">Enter your phone number to view your appointments.</p>
          <form action="{{ route('my-bookings') }}" method="GET" class="d-flex gap-2 flex-wrap">
            <div class="flex-grow-1" style="min-width: 200px;">
              <input type="tel"
                     name="phone"
                     class="form-control"
                     placeholder="Enter your phone number"
                     value="{{ old('phone', $phone ?? '') }}"
                     style="border-radius: 10px;"
                     required>
            </div>
            <button type="submit" class="btn btn-primary" style="border-radius: 10px;">
              <i class="bi bi-search me-1"></i> Find My Bookings
            </button>
          </form>
          @error('phone')
            <div class="text-danger small mt-2">{{ $message }}</div>
          @enderror
        </div>
      </div>
    @endguest

    {{-- User Info for Authenticated Users --}}
    @auth
      <div class="alert alert-info d-flex align-items-center gap-2 mb-4" role="alert" style="border-radius:12px;">
        <i class="bi bi-person-check-fill fs-5"></i>
        <span>Showing bookings for <strong>{{ auth()->user()->name }}</strong></span>
      </div>
    @endauth

    {{-- Status Filter Buttons --}}
    @if($appointments instanceof \Illuminate\Pagination\LengthAwarePaginator && $appointments->total() > 0)
      <div class="d-flex flex-wrap gap-2 mb-4">
        <a href="{{ route('my-bookings', request()->except('status')) }}"
           class="btn {{ $statusFilter === 'all' ? 'btn-primary' : 'btn-outline-secondary' }}"
           style="border-radius: 20px;">
          All
        </a>
        <a href="{{ route('my-bookings', array_merge(request()->except('status'), ['status' => 'pending'])) }}"
           class="btn {{ $statusFilter === 'pending' ? 'btn-warning' : 'btn-outline-secondary' }}"
           style="border-radius: 20px;">
          <i class="bi bi-clock me-1"></i> Pending
        </a>
        <a href="{{ route('my-bookings', array_merge(request()->except('status'), ['status' => 'confirmed'])) }}"
           class="btn {{ $statusFilter === 'confirmed' ? 'btn-success' : 'btn-outline-secondary' }}"
           style="border-radius: 20px;">
          <i class="bi bi-check-circle me-1"></i> Confirmed
        </a>
        <a href="{{ route('my-bookings', array_merge(request()->except('status'), ['status' => 'cancelled'])) }}"
           class="btn {{ $statusFilter === 'cancelled' ? 'btn-danger' : 'btn-outline-secondary' }}"
           style="border-radius: 20px;">
          <i class="bi bi-x-circle me-1"></i> Cancelled
        </a>
      </div>
    @endif

    {{-- Bookings List --}}
    @if($appointments instanceof \Illuminate\Pagination\LengthAwarePaginator && $appointments->count() > 0)
      <div class="row g-4">
        @foreach($appointments as $appointment)
          <div class="col-lg-6 col-12">
            <div class="card h-100 shadow-sm border-0 booking-card" style="border-radius: 16px; transition: all 0.3s ease;">
              <div class="card-body p-4">

                {{-- Header: Doctor + Status --}}
                <div class="d-flex justify-content-between align-items-start mb-3">
                  <div class="d-flex align-items-center gap-3">
                    <div class="doctor-avatar d-flex align-items-center justify-content-center text-white fw-bold"
                         style="width: 48px; height: 48px; border-radius: 50%; font-size: 1.1rem;">
                      {{ $appointment->doctor ? $appointment->doctor->initials() : 'DR' }}
                    </div>
                    <div>
                      <h6 class="mb-0 fw-bold">{{ $appointment->doctor?->name ?? 'Unknown Doctor' }}</h6>
                      <small class="text-muted">{{ $appointment->doctor?->specialty ?? $appointment->doctor?->department?->name ?? 'General' }}</small>
                    </div>
                  </div>
                  <span class="badge {{ $appointment->status === 'confirmed' ? 'bg-success' : ($appointment->status === 'pending' ? 'bg-warning text-dark' : 'bg-danger') }}"
                        style="border-radius: 8px; font-size: 0.75rem; padding: 0.5em 0.8em;">
                    {{ ucfirst($appointment->status) }}
                  </span>
                </div>

                {{-- Appointment Details --}}
                <div class="row g-2 mb-3">
                  <div class="col-6">
                    <div class="d-flex align-items-center gap-2 text-muted small">
                      <i class="bi bi-calendar3"></i>
                      <span>{{ $appointment->appointment_date->format('M d, Y') }}</span>
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="d-flex align-items-center gap-2 text-muted small">
                      <i class="bi bi-clock"></i>
                      <span>{{ $appointment->start_time }} - {{ $appointment->end_time }}</span>
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="d-flex align-items-center gap-2 text-muted small">
                      <i class="bi bi-telephone"></i>
                      <span>{{ $appointment->phone_number ?? '—' }}</span>
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="d-flex align-items-center gap-2 text-muted small">
                      <i class="bi bi-cash"></i>
                      <span>${{ number_format($appointment->fee, 2) }}</span>
                    </div>
                  </div>
                </div>

                {{-- Treatment --}}
                <div class="mb-3">
                  <small class="text-muted d-block mb-1">Treatment</small>
                  <p class="mb-0 fw-medium text-truncate">{{ $appointment->treatment }}</p>
                </div>

                {{-- Notes (if any) --}}
                @if($appointment->notes)
                  <div class="mb-3">
                    <small class="text-muted d-block mb-1">Notes</small>
                    <p class="mb-0 small text-muted">{{ Str::limit($appointment->notes, 100) }}</p>
                  </div>
                @endif

                {{-- Actions --}}
                <div class="d-flex gap-2 mt-auto pt-3 border-top">
                  <a href="{{ route('website.booking.status', $appointment) }}"
                     class="btn btn-sm btn-outline-primary flex-fill"
                     style="border-radius: 8px;">
                    <i class="bi bi-eye me-1"></i> View Details
                  </a>
                  @if($appointment->status === 'pending')
                    <form action="{{ route('website.booking.cancel', $appointment) }}" method="POST" class="flex-fill">
                      @csrf
                      <button type="submit"
                              class="btn btn-sm btn-outline-danger w-100"
                              style="border-radius: 8px;"
                              onclick="return confirm('Are you sure you want to cancel this appointment?')">
                        <i class="bi bi-x-lg me-1"></i> Cancel
                      </button>
                    </form>
                  @endif
                </div>

              </div>
            </div>
          </div>
        @endforeach
      </div>

      {{-- Pagination --}}
      @if($appointments->hasPages())
      <div class="mt-5">
        {{ $appointments->withQueryString()->links('vendor.pagination.medicare') }}
      </div>
      @endif

    @else
      {{-- Empty State --}}
      <div class="text-center py-5">
        <div class="mb-4">
          <i class="bi bi-calendar-x display-1 text-muted"></i>
        </div>
        <h4 class="text-muted mb-2">No bookings found</h4>
        @guest
          @if($searched ?? false)
            <p class="text-muted mb-4">No appointments found for this phone number.</p>
          @else
            <p class="text-muted mb-4">Enter your phone number above to find your bookings.</p>
          @endif
        @else
          <p class="text-muted mb-4">You don't have any appointments yet.</p>
        @endguest
        @auth
          <a href="{{ route('website.book') }}" class="btn btn-primary" style="border-radius: 25px;">
            <i class="bi bi-calendar-plus me-2"></i> Book an Appointment
          </a>
        @else
          <a href="{{ route('login') }}" class="btn btn-primary" style="border-radius: 25px;">
            <i class="bi bi-box-arrow-in-right me-2"></i> Login to Book
          </a>
        @endauth
      </div>
    @endif

    {{-- Back to Home --}}
    <div class="text-center mt-5">
      <a href="{{ route('home') }}" class="btn btn-outline-primary" style="border-radius: 25px;">
        <i class="bi bi-arrow-left me-2"></i> Back to Home
      </a>
    </div>

  </div>
</section>

@endsection

@push('styles')
<style>
  .booking-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
  }
  .doctor-avatar {
    background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
  }
</style>
@endpush
