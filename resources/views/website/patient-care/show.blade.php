@extends('layouts.website')

@section('title', $service->name . ' — Patient Care — Medicare Hospital')

@section('content')

  {{-- Hero Banner --}}
  <section class="pc-hero">
    <div class="container position-relative" style="z-index:2;">
      <nav aria-label="breadcrumb">
        <ol class="pc-breadcrumb">
          <li><a href="{{ route('home') }}">Home</a></li>
          <li><a href="{{ route('website.patient-care') }}">Patient Care</a></li>
          <li class="active">{{ $service->name }}</li>
        </ol>
      </nav>
      <h1 class="pc-hero-title">{{ $service->name }}</h1>
      @if($service->description)
        <p class="pc-hero-desc">{{ Str::limit($service->description, 200) }}</p>
      @endif
      <div class="pc-hero-badges">
        @if($service->is_bookable)
          <span class="pc-hero-badge bookable"><i class="bi bi-calendar-check-fill"></i> Bookable Service</span>
        @endif
        <span class="pc-hero-badge"><i class="{{ $service->icon_class ?? 'bi bi-heart-pulse' }}"></i> Patient Care</span>
      </div>
    </div>
  </section>

  {{-- Main Content --}}
  <section class="pc-content">
    <div class="container">
      <div class="row g-4">

        {{-- Sidebar --}}
        <div class="col-lg-3">

          {{-- Mobile toggle --}}
          <button class="btn pc-sidebar-toggle d-lg-none w-100 mb-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#pcSidebar">
            <i class="bi bi-list-ul me-2"></i> Browse Services
          </button>

          {{-- Offcanvas sidebar for mobile --}}
          <div class="offcanvas offcanvas-start d-lg-none" id="pcSidebar">
            <div class="offcanvas-header">
              <h5 class="offcanvas-title">Patient Care</h5>
              <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
            </div>
            <div class="offcanvas-body p-0">
              <ul class="pc-sidebar-list">
                @foreach($allServices as $svc)
                <li>
                  <a href="{{ route('website.patient-care.show', $svc) }}"
                     class="pc-sidebar-item {{ $svc->id === $service->id ? 'active' : '' }}">
                    <div class="pc-sidebar-icon">
                      <i class="{{ $svc->icon_class ?? 'bi bi-circle' }}"></i>
                    </div>
                    <div class="flex-grow-1">
                      <div class="pc-sidebar-name">{{ $svc->name }}</div>
                    </div>
                    <i class="bi bi-chevron-right pc-sidebar-arrow"></i>
                  </a>
                </li>
                @endforeach
              </ul>
            </div>
          </div>

          {{-- Desktop sidebar --}}
          <div class="pc-sidebar-card d-none d-lg-block">
            <h5 class="pc-sidebar-title">
              <i class="bi bi-grid-fill me-2"></i>Patient Care
            </h5>
            <ul class="pc-sidebar-list">
              @foreach($allServices as $svc)
              <li>
                <a href="{{ route('website.patient-care.show', $svc) }}"
                   class="pc-sidebar-item {{ $svc->id === $service->id ? 'active' : '' }}">
                  <div class="pc-sidebar-icon">
                    <i class="{{ $svc->icon_class ?? 'bi bi-circle' }}"></i>
                  </div>
                  <div class="flex-grow-1">
                    <div class="pc-sidebar-name">{{ $svc->name }}</div>
                  </div>
                  <i class="bi bi-chevron-right pc-sidebar-arrow"></i>
                </a>
              </li>
              @endforeach
            </ul>
          </div>
        </div>

        {{-- Main Content Area --}}
        <div class="col-lg-9">

          {{-- Success alert --}}
          @if(session('status'))
          <div class="alert alert-success alert-dismissible fade show" style="border-radius:12px; border:none; background:#ecfdf5; color:#065f46; font-weight:500;" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
          @endif

          {{-- About Card --}}
          <div class="pc-detail-card">
            <div class="pc-detail-card-header">
              <i class="{{ $service->icon_class ?? 'bi bi-info-circle-fill' }}"></i>
              <h4>About {{ $service->name }}</h4>
            </div>
            <div class="pc-detail-card-body">
              @if($service->image)
                <div class="pc-about-img mb-4">
                  <img src="{{ asset($service->image) }}" alt="{{ $service->name }}">
                </div>
              @endif

              @if($service->description)
                <p class="pc-about-text">{{ $service->description }}</p>
              @endif

              @if($service->content)
                <div class="pc-full-content mt-3">
                  {!! nl2br(e($service->content)) !!}
                </div>
              @endif
            </div>
          </div>

          {{-- Booking Form (only for bookable services) --}}
          @if($service->is_bookable)
          <div class="pc-detail-card" id="booking">
            <div class="pc-detail-card-header">
              <i class="bi bi-calendar-check-fill"></i>
              <h4>Book {{ $service->name }}</h4>
              <span class="pc-detail-badge">Online Booking</span>
            </div>
            <div class="pc-detail-card-body">
              @auth
                @if(auth()->user()->isPatient())
                <form method="POST" action="{{ route('website.patient-care.book', $service) }}" class="pc-booking-form">
                  @csrf
                  <div class="row g-3">
                    <div class="col-md-6">
                      <label class="pc-form-label" for="pc_patient_name">Patient Name</label>
                      <input type="text" id="pc_patient_name" class="pc-form-input" value="{{ auth()->user()->name }}" disabled>
                    </div>
                    <div class="col-md-6">
                      <label class="pc-form-label" for="pc_phone_number">Phone Number <span class="text-danger">*</span></label>
                      <input type="tel" name="phone_number" id="pc_phone_number" class="pc-form-input @error('phone_number') is-invalid @enderror"
                             value="{{ old('phone_number') }}" placeholder="e.g. +961 71 234 567" required>
                      @error('phone_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                    <div class="col-md-6">
                      <label class="pc-form-label" for="pc_booking_date">Preferred Date <span class="text-danger">*</span></label>
                      <input type="date" name="booking_date" id="pc_booking_date" class="pc-form-input @error('booking_date') is-invalid @enderror"
                             value="{{ old('booking_date') }}" min="{{ date('Y-m-d') }}" required>
                      @error('booking_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                    <div class="col-md-6">
                      <label class="pc-form-label" for="pc_booking_time">Preferred Time <span class="text-danger">*</span></label>
                      <input type="time" name="booking_time" id="pc_booking_time" class="pc-form-input @error('booking_time') is-invalid @enderror"
                             value="{{ old('booking_time') }}" required>
                      @error('booking_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                    <div class="col-12">
                      <label class="pc-form-label" for="pc_notes">Notes <span class="text-muted">(optional)</span></label>
                      <textarea name="notes" id="pc_notes" class="pc-form-input @error('notes') is-invalid @enderror"
                                rows="3" placeholder="Any additional information or special requirements...">{{ old('notes') }}</textarea>
                      @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                    <div class="col-12">
                      <button type="submit" class="pc-btn-primary">
                        <i class="bi bi-calendar-check"></i> Submit Booking Request
                      </button>
                    </div>
                  </div>
                </form>
                @else
                  <div class="text-center py-4">
                    <i class="bi bi-person-lock" style="font-size:40px;color:#cbd5e1;"></i>
                    <p class="mt-3 mb-0" style="color:#64748b;">Booking is available for patients only.</p>
                  </div>
                @endif
              @else
                <div class="text-center py-4">
                  <i class="bi bi-box-arrow-in-right" style="font-size:40px;color:#cbd5e1;"></i>
                  <p class="mt-3 mb-2" style="color:#64748b;">Please log in to book this service.</p>
                  <a href="{{ route('login') }}" class="pc-btn-primary" style="display:inline-flex;">
                    <i class="bi bi-box-arrow-in-right"></i> Log In to Book
                  </a>
                </div>
              @endauth
            </div>
          </div>
          @endif

          {{-- Back --}}
          <div class="text-center mt-4">
            <a href="{{ route('website.patient-care') }}" class="pc-btn-outline" style="padding:12px 32px;">
              <i class="bi bi-arrow-left"></i> Back to All Services
            </a>
          </div>

        </div>
      </div>
    </div>
  </section>

@endsection

@push('styles')
<style>
/* ── Patient Care Show Page ────────────────────────────────── */

/* Hero */
.pc-hero {
  background: linear-gradient(135deg, #3f4047 0%, #2d2e33 50%, #1a1b1f 100%);
  padding: 56px 0 48px;
  position: relative;
  overflow: hidden;
}
.pc-hero::before {
  content: '';
  position: absolute;
  top: -40%;
  right: -15%;
  width: 500px;
  height: 500px;
  background: rgba(255,255,255,0.04);
  border-radius: 50%;
}
.pc-hero::after {
  content: '';
  position: absolute;
  bottom: -30%;
  left: -8%;
  width: 350px;
  height: 350px;
  background: rgba(255,255,255,0.03);
  border-radius: 50%;
}
.pc-hero-title {
  font-family: 'Poppins', sans-serif;
  font-size: 42px;
  font-weight: 700;
  color: #fff;
  margin-bottom: 12px;
  line-height: 1.2;
}
.pc-hero-desc {
  color: rgba(255,255,255,0.85);
  font-size: 17px;
  max-width: 640px;
  line-height: 1.7;
  margin-bottom: 20px;
}
.pc-breadcrumb {
  list-style: none;
  display: flex;
  gap: 8px;
  padding: 0;
  margin: 0 0 16px;
  font-size: 14px;
}
.pc-breadcrumb li + li::before {
  content: '/';
  margin-right: 8px;
  color: rgba(255,255,255,0.4);
}
.pc-breadcrumb a { color: rgba(255,255,255,0.7); text-decoration: none; transition: color 0.2s; }
.pc-breadcrumb a:hover { color: #fff; }
.pc-breadcrumb .active { color: rgba(255,255,255,0.95); font-weight: 500; }
.pc-hero-badges { display: flex; gap: 12px; flex-wrap: wrap; }
.pc-hero-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: rgba(255,255,255,0.12);
  backdrop-filter: blur(4px);
  color: #fff;
  padding: 8px 16px;
  border-radius: 99px;
  font-size: 13px;
  font-weight: 500;
}
.pc-hero-badge.bookable {
  background: rgba(16, 185, 129, 0.25);
}

@media (max-width: 767px) {
  .pc-hero { padding: 36px 0 32px; }
  .pc-hero-title { font-size: 28px; }
  .pc-hero-desc { font-size: 15px; }
}

/* Content wrapper */
.pc-content {
  padding: 40px 0 64px;
  background: #f8f9fa;
}

/* Sidebar */
.pc-sidebar-card {
  background: #fff;
  border-radius: 16px;
  box-shadow: 0 2px 20px rgba(0,0,0,0.06);
  overflow: hidden;
  position: sticky;
  top: 90px;
}
.pc-sidebar-title {
  font-family: 'Poppins', sans-serif;
  font-weight: 600;
  font-size: 16px;
  padding: 20px 20px 12px;
  color: #1e293b;
  margin: 0;
  border-bottom: 1px solid #f1f5f9;
}
.pc-sidebar-list {
  list-style: none;
  padding: 8px;
  margin: 0;
}
.pc-sidebar-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
  border-radius: 12px;
  text-decoration: none;
  color: #475569;
  transition: all 0.25s ease;
  margin-bottom: 2px;
}
.pc-sidebar-item:hover {
  background: #e8e8ea;
  color: #3f4047;
  transform: translateX(4px);
}
.pc-sidebar-item.active {
  background: linear-gradient(135deg, #3f4047, #2d2e33);
  color: #fff;
  box-shadow: 0 4px 16px rgba(63,64,71,0.25);
}
.pc-sidebar-item.active .pc-sidebar-arrow {
  color: rgba(255,255,255,0.8);
}
.pc-sidebar-icon {
  width: 36px;
  height: 36px;
  background: #e8e8ea;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 15px;
  color: #3f4047;
  flex-shrink: 0;
  transition: all 0.25s;
}
.pc-sidebar-item.active .pc-sidebar-icon {
  background: rgba(255,255,255,0.2);
  color: #fff;
}
.pc-sidebar-item:hover .pc-sidebar-icon {
  background: #3f4047;
  color: #fff;
}
.pc-sidebar-name {
  font-weight: 600;
  font-size: 13px;
  line-height: 1.3;
}
.pc-sidebar-arrow {
  font-size: 11px;
  color: #cbd5e1;
  transition: transform 0.25s;
}
.pc-sidebar-item:hover .pc-sidebar-arrow {
  transform: translateX(3px);
  color: #3f4047;
}
.pc-sidebar-item.active .pc-sidebar-arrow {
  transform: translateX(3px);
}

/* Sidebar toggle (mobile) */
.pc-sidebar-toggle {
  background: #fff;
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  padding: 12px 20px;
  font-weight: 600;
  font-size: 15px;
  color: #3f4047;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.25s;
}
.pc-sidebar-toggle:hover {
  background: #e8e8ea;
  border-color: #3f4047;
}

/* Detail cards */
.pc-detail-card {
  background: #fff;
  border-radius: 16px;
  box-shadow: 0 2px 20px rgba(0,0,0,0.06);
  margin-bottom: 24px;
  overflow: hidden;
  transition: box-shadow 0.3s;
}
.pc-detail-card:hover {
  box-shadow: 0 4px 28px rgba(0,0,0,0.09);
}
.pc-detail-card-header {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 20px 24px;
  border-bottom: 1px solid #f1f5f9;
}
.pc-detail-card-header i {
  font-size: 20px;
  color: #3f4047;
}
.pc-detail-card-header h4 {
  font-family: 'Poppins', sans-serif;
  font-weight: 600;
  font-size: 18px;
  margin: 0;
  color: #1e293b;
}
.pc-detail-badge {
  margin-left: auto;
  background: #e8e8ea;
  color: #3f4047;
  padding: 5px 14px;
  border-radius: 99px;
  font-size: 12px;
  font-weight: 600;
}
.pc-detail-card-body {
  padding: 24px;
}

/* About */
.pc-about-img {
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}
.pc-about-img img {
  width: 100%;
  height: 280px;
  object-fit: cover;
  display: block;
}
.pc-about-text {
  color: #475569;
  font-size: 15px;
  line-height: 1.8;
}
.pc-full-content {
  color: #475569;
  font-size: 15px;
  line-height: 1.9;
  border-top: 1px solid #f1f5f9;
  padding-top: 16px;
}

/* Booking Form */
.pc-booking-form {
  max-width: 100%;
}
.pc-form-label {
  font-family: 'Poppins', sans-serif;
  font-size: 13px;
  font-weight: 600;
  color: #374151;
  margin-bottom: 6px;
  display: block;
}
.pc-form-input {
  width: 100%;
  padding: 11px 16px;
  border: 1.5px solid #e5e7eb;
  border-radius: 10px;
  font-size: 14px;
  font-family: 'Poppins', sans-serif;
  color: #374151;
  background: #fff;
  transition: all 0.25s ease;
}
.pc-form-input:focus {
  outline: none;
  border-color: #3f4047;
  box-shadow: 0 0 0 3px rgba(63, 64, 71, 0.08);
}
.pc-form-input::placeholder {
  color: #9ca3af;
}
.pc-form-input:disabled {
  background: #f9fafb;
  color: #6b7280;
}
.pc-form-input.is-invalid {
  border-color: #ef4444;
}
textarea.pc-form-input {
  resize: vertical;
  min-height: 80px;
}

/* Buttons */
.pc-btn-primary {
  background: #3f4047;
  color: #fff;
  border: none;
  border-radius: 10px;
  padding: 12px 28px;
  font-size: 14px;
  font-weight: 600;
  font-family: 'Poppins', sans-serif;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  transition: all 0.25s ease;
  cursor: pointer;
}
.pc-btn-primary:hover {
  background: #2d2e33;
  color: #fff;
  transform: translateY(-2px);
  box-shadow: 0 4px 16px rgba(63,64,71,0.3);
}
.pc-btn-outline {
  background: transparent;
  color: #3f4047;
  border: 1.5px solid #3f4047;
  border-radius: 10px;
  padding: 9px 18px;
  font-size: 13px;
  font-weight: 600;
  font-family: 'Poppins', sans-serif;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  transition: all 0.25s ease;
}
.pc-btn-outline:hover {
  background: #3f4047;
  color: #fff;
  transform: translateY(-2px);
}
</style>
@endpush
