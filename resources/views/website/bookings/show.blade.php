@extends('layouts.website')

@section('title', 'Booking Status - Medicare Hospital')

@section('content')
<style>
  .bs-section{padding-top:120px;padding-bottom:60px;background:#f4f6f9;min-height:100vh}
  .bs-card{max-width:640px;margin:0 auto;background:#fff;border-radius:18px;box-shadow:0 4px 24px rgba(0,0,0,.07);overflow:hidden}
  .bs-header{text-align:center;padding:36px 32px 20px;background:linear-gradient(135deg,#f8fafc,#f0f4f8)}
  .bs-check{width:72px;height:72px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;animation:bsPop .5s cubic-bezier(.34,1.56,.64,1)}
  .bs-check.pending{background:#fef9c3}.bs-check.confirmed{background:#d1fae5}.bs-check.cancelled{background:#fee2e2}
  .bs-check i{font-size:32px}
  .bs-check.pending i{color:#ca8a04}.bs-check.confirmed i{color:#059669}.bs-check.cancelled i{color:#dc2626}
  @keyframes bsPop{0%{transform:scale(0)}50%{transform:scale(1.2)}100%{transform:scale(1)}}
  .bs-header h2{font-weight:700;font-size:22px;color:#1e293b;margin-bottom:4px}
  .bs-header p{color:#64748b;font-size:14px;margin:0}
  .bs-badge{display:inline-block;padding:5px 16px;border-radius:20px;font-weight:600;font-size:13px;margin-top:10px}
  .bs-badge.pending{background:#fef9c3;color:#92400e}.bs-badge.confirmed{background:#d1fae5;color:#065f46}.bs-badge.cancelled{background:#fee2e2;color:#991b1b}
  .bs-body{padding:28px 32px}
  .bs-grid{display:grid;grid-template-columns:1fr 1fr;gap:0}
  .bs-item{padding:14px 0;border-bottom:1px solid #f1f5f9}
  .bs-item:nth-last-child(-n+2){border-bottom:none}
  .bs-item label{display:block;font-size:11px;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;font-weight:600}
  .bs-item strong{font-size:14px;color:#1e293b;display:block}
  .bs-footer{display:flex;justify-content:center;gap:12px;padding:0 32px 28px}
  .bs-btn{display:inline-flex;align-items:center;gap:8px;padding:11px 24px;border-radius:10px;font-weight:600;font-size:14px;border:none;cursor:pointer;text-decoration:none;transition:all .2s;font-family:inherit}
  .bs-btn-primary{background:#3f4047;color:#fff}.bs-btn-primary:hover{background:#2d2e33;color:#fff;transform:translateY(-1px);box-shadow:0 4px 12px rgba(63,64,71,.25)}
  .bs-btn-outline{background:#f1f5f9;color:#475569}.bs-btn-outline:hover{background:#e2e8f0;color:#1e293b}
  @media(max-width:575px){.bs-body{padding:20px 18px}.bs-grid{grid-template-columns:1fr}.bs-item:nth-last-child(-n+2){border-bottom:1px solid #f1f5f9}.bs-item:last-child{border-bottom:none}.bs-footer{flex-direction:column}.bs-btn{width:100%;justify-content:center}}
</style>

<section class="bs-section section">
  <div class="container">

    @if(session('status'))
    <div class="bs-card reveal">
      <div class="bs-header">
        @php
          $statusClass = match($appointment->status) {
            'confirmed' => 'confirmed',
            'cancelled' => 'cancelled',
            default => 'pending',
          };
          $statusIcon = match($appointment->status) {
            'confirmed' => 'bi-check-circle-fill',
            'cancelled' => 'bi-x-circle-fill',
            default => 'bi-clock-fill',
          };
        @endphp

        <div class="bs-check {{ $statusClass }}"><i class="bi {{ $statusIcon }}"></i></div>
        <h2>Appointment Booked!</h2>
        <p>{{ session('status') }}</p>
        <span class="bs-badge {{ $statusClass }}">{{ ucfirst($appointment->status) }}</span>
      </div>

      <div class="bs-body">
        <div class="bs-grid">
          <div class="bs-item">
            <label>Doctor</label>
            <strong>{{ $appointment->doctor?->name ?? '—' }}</strong>
          </div>
          <div class="bs-item">
            <label>Department</label>
            <strong>{{ $appointment->doctor?->department?->name ?? '—' }}</strong>
          </div>
          <div class="bs-item">
            <label>Appointment Date</label>
            <strong>{{ $appointment->appointment_date->format('l, F j, Y') }}</strong>
          </div>
          <div class="bs-item">
            <label>Appointment Time</label>
            <strong>{{ \Carbon\Carbon::parse($appointment->start_time)->format('g:i A') }} – {{ \Carbon\Carbon::parse($appointment->end_time)->format('g:i A') }}</strong>
          </div>
          <div class="bs-item">
            <label>Patient Name</label>
            <strong>{{ $appointment->patient?->name ?? auth()->user()->name }}</strong>
          </div>
          <div class="bs-item">
            <label>Phone Number</label>
            <strong>{{ $appointment->phone_number ?? '—' }}</strong>
          </div>
          <div class="bs-item">
            <label>Treatment</label>
            <strong>{{ $appointment->treatment }}</strong>
          </div>
          <div class="bs-item">
            <label>Fee</label>
            <strong>${{ number_format($appointment->fee, 2) }}</strong>
          </div>
          <div class="bs-item">
            <label>Status</label>
            <strong><span class="bs-badge {{ $statusClass }}">{{ ucfirst($appointment->status) }}</span></strong>
          </div>
          @if($appointment->notes)
          <div class="bs-item">
            <label>Notes</label>
            <strong>{{ $appointment->notes }}</strong>
          </div>
          @endif
        </div>
      </div>

      <div class="bs-footer">
        <a href="{{ route('home') }}" class="bs-btn bs-btn-outline"><i class="bi bi-house"></i> Back to Home</a>
        <a href="{{ route('my-bookings') }}" class="bs-btn bs-btn-primary"><i class="bi bi-list-check"></i> View All Bookings</a>
      </div>
    </div>
    @else
    {{-- Direct access (no flash) --}}
    <div class="bs-card reveal">
      <div class="bs-header">
        @php
          $statusClass = match($appointment->status) {
            'confirmed' => 'confirmed',
            'cancelled' => 'cancelled',
            default => 'pending',
          };
          $statusIcon = match($appointment->status) {
            'confirmed' => 'bi-check-circle-fill',
            'cancelled' => 'bi-x-circle-fill',
            default => 'bi-clock-fill',
          };
        @endphp

        <div class="bs-check {{ $statusClass }}"><i class="bi {{ $statusIcon }}"></i></div>
        <h2>Booking Details</h2>
        <p>Appointment #{{ $appointment->id }}</p>
        <span class="bs-badge {{ $statusClass }}">{{ ucfirst($appointment->status) }}</span>
      </div>

      <div class="bs-body">
        <div class="bs-grid">
          <div class="bs-item">
            <label>Doctor</label>
            <strong>{{ $appointment->doctor?->name ?? '—' }}</strong>
          </div>
          <div class="bs-item">
            <label>Department</label>
            <strong>{{ $appointment->doctor?->department?->name ?? '—' }}</strong>
          </div>
          <div class="bs-item">
            <label>Appointment Date</label>
            <strong>{{ $appointment->appointment_date->format('l, F j, Y') }}</strong>
          </div>
          <div class="bs-item">
            <label>Appointment Time</label>
            <strong>{{ \Carbon\Carbon::parse($appointment->start_time)->format('g:i A') }} – {{ \Carbon\Carbon::parse($appointment->end_time)->format('g:i A') }}</strong>
          </div>
          <div class="bs-item">
            <label>Patient Name</label>
            <strong>{{ $appointment->patient?->name ?? auth()->user()->name }}</strong>
          </div>
          <div class="bs-item">
            <label>Phone Number</label>
            <strong>{{ $appointment->phone_number ?? '—' }}</strong>
          </div>
          <div class="bs-item">
            <label>Treatment</label>
            <strong>{{ $appointment->treatment }}</strong>
          </div>
          <div class="bs-item">
            <label>Fee</label>
            <strong>${{ number_format($appointment->fee, 2) }}</strong>
          </div>
          <div class="bs-item">
            <label>Status</label>
            <strong><span class="bs-badge {{ $statusClass }}">{{ ucfirst($appointment->status) }}</span></strong>
          </div>
          @if($appointment->notes)
          <div class="bs-item">
            <label>Notes</label>
            <strong>{{ $appointment->notes }}</strong>
          </div>
          @endif
        </div>
      </div>

      <div class="bs-footer">
        <a href="{{ route('home') }}" class="bs-btn bs-btn-outline"><i class="bi bi-house"></i> Back to Home</a>
        <a href="{{ route('my-bookings') }}" class="bs-btn bs-btn-primary"><i class="bi bi-list-check"></i> View All Bookings</a>
      </div>
    </div>
    @endif

  </div>
</section>
@endsection
