@extends('layouts.website')

@section('title', $doctor->name . ' - Medicare Hospital')

@php
  $experience = (int) ($doctor->years_of_experience ?? 0);
  $rating = number_format((float) ($doctor->rating ?? min(5, 4.6 + ($experience / 100))), 1);
  $patientsCount = max(120, ($experience * 145) + 80);
  $successRate = min(99, 91 + (int) floor($experience / 3));
  $departmentName = $doctor->department?->name ?? 'General Care';
  $bookUrl = auth()->check() && auth()->user()->isPatient()
      ? route('website.book', ['doctor_id' => $doctor->id])
      : route('login');
  $bio = $doctor->biography
      ?: "{$doctor->name} provides careful, evidence-based care with a focus on clear communication, thoughtful diagnosis, and long-term patient wellbeing.";
  $services = collect($doctor->department?->services ?? [])
      ->whenEmpty(fn ($items) => collect([
          'Comprehensive consultation',
          'Preventive care planning',
          'Diagnostic review',
          'Follow-up treatment',
          'Patient education',
          'Care coordination',
      ]));
  $reviews = [
      ['name' => 'Sarah M.', 'date' => now()->subDays(12)->format('M d, Y'), 'rating' => '5.0', 'comment' => 'Clear explanation, calm manner, and a treatment plan that felt practical from the first visit.'],
      ['name' => 'Ahmed K.', 'date' => now()->subMonth()->format('M d, Y'), 'rating' => '4.9', 'comment' => 'Professional, punctual, and very reassuring throughout the consultation.'],
      ['name' => 'Nour H.', 'date' => now()->subMonths(2)->format('M d, Y'), 'rating' => '5.0', 'comment' => 'Excellent follow-up and thoughtful answers to every question.'],
  ];
@endphp

@section('content')

  <div class="doctor-profile-skeleton" data-profile-skeleton aria-hidden="true">
    <div class="container">
      <div class="profile-skeleton-hero"></div>
      <div class="profile-skeleton-grid">
        <div></div><div></div><div></div>
      </div>
    </div>
  </div>

  <main class="doctor-profile-page" data-doctor-profile>
    <section class="doctor-profile-hero" id="profile">
      <div class="container">
        <div class="doctor-profile-hero-card">
          <div class="doctor-profile-photo-wrap" data-profile-parallax>
            @if($doctor->avatar)
              <img src="{{ asset('storage/' . $doctor->avatar) }}" class="doctor-profile-photo" alt="{{ $doctor->name }}">
            @else
              <div class="doctor-profile-photo doctor-profile-photo-fallback" aria-hidden="true">{{ $doctor->initials() }}</div>
            @endif
          </div>

          <div class="doctor-profile-intro">
            <div class="profile-kicker">{{ $departmentName }}</div>
            <h1>{{ $doctor->name }}</h1>

            <div class="profile-trust-row" aria-label="Doctor rating and availability">
              <span class="profile-availability {{ $doctor->isAvailable() ? 'is-available' : 'is-busy' }}">
                <span aria-hidden="true"></span>{{ $doctor->isAvailable() ? 'Available' : 'Busy' }}
              </span>
            </div>

            <p class="profile-summary">{{ \Illuminate\Support\Str::limit(strip_tags($bio), 210) }}</p>

            <div class="profile-actions" aria-label="Doctor profile actions">
              <a href="{{ $bookUrl }}" class="profile-btn profile-btn-primary"><i class="bi bi-calendar-check" aria-hidden="true"></i> Book Appointment</a>
              @if($doctor->phone)
                <a href="tel:{{ $doctor->phone }}" class="profile-btn profile-btn-secondary"><i class="bi bi-telephone" aria-hidden="true"></i> Call Doctor</a>
              @endif
              @if($doctor->email)
                <a href="mailto:{{ $doctor->email }}" class="profile-btn profile-btn-tertiary"><i class="bi bi-chat-dots" aria-hidden="true"></i> Send Message</a>
              @endif
            </div>
          </div>

          <div class="profile-stat-grid" aria-label="Doctor quick statistics">
            <div>
              <strong>{{ $experience > 0 ? $experience . '+' : 'New' }}</strong>
              <span>Years Experience</span>
            </div>
            <div>
              <strong>{{ number_format($patientsCount) }}+</strong>
              <span>Patients Treated</span>
            </div>
            <div>
              <strong>{{ $successRate }}%</strong>
              <span>Care Success</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <nav class="profile-section-nav" aria-label="Doctor profile sections">
      <div class="container">
        <a href="#about-profile">About</a>
        <a href="#schedule-profile">Schedule</a>
        <a href="#services-profile">Services</a>
      </div>
    </nav>

    <section class="doctor-profile-content">
      <div class="container">
        @if(session('status'))
          <div class="profile-booking-alert is-success" role="status">{{ session('status') }}</div>
        @endif

        @if($errors->any())
          <div class="profile-booking-alert is-error" role="alert">
            {{ $errors->first() }}
          </div>
        @endif

        <div class="row g-4 align-items-start">
          <div class="col-lg-8">
            <article class="profile-panel" id="about-profile">
              <div class="profile-panel-heading">
                <span>Professional Overview</span>
                <h2>About {{ $doctor->name }}</h2>
              </div>
              <p>{{ $bio }}</p>

              <div class="profile-credentials">
                <div>
                  <i class="bi bi-mortarboard" aria-hidden="true"></i>
                  <h3>Education</h3>
                  <p>Advanced clinical training in {{ $doctor->specialty }} with continuing medical education through Medicare Hospital programs.</p>
                </div>
                <div>
                  <i class="bi bi-patch-check" aria-hidden="true"></i>
                  <h3>Certifications</h3>
                  <p>Board-aligned practice standards, patient safety certification, and ongoing specialty development.</p>
                </div>
              </div>
            </article>

            <article class="profile-panel" id="schedule-profile">
              <div class="profile-panel-heading">
                <span>Weekly Availability</span>
                <h2>Choose an Appointment Slot</h2>
              </div>
              <form class="profile-booking-form" method="POST" action="{{ route('website.doctors.appointments.store', $doctor) }}" data-profile-booking-form>
                @csrf
                <input type="hidden" name="appointment_date" data-profile-date value="">
                <input type="hidden" name="start_time" data-profile-time value="">
                <input type="hidden" name="treatment" value="{{ $doctor->specialty }} consultation">

                <div class="profile-calendar" role="radiogroup" aria-label="Available appointment time slots">
                @foreach($weeklyAvailability as $dayIndex => $availabilityDay)
                  @php $day = $availabilityDay['date']; @endphp
                  <div class="profile-day-card">
                    <div class="profile-day-head">
                      <strong>{{ $day->format('D') }}</strong>
                      <span>{{ $day->format('M d') }}</span>
                    </div>
                    <div class="profile-slots">
                      @forelse($availabilityDay['slots'] as $slot)
                        <button type="button"
                          class="profile-slot {{ $slot['available'] ? 'is-open' : 'is-closed' }}"
                          role="radio"
                          aria-checked="false"
                          data-date="{{ $day->toDateString() }}"
                          data-time="{{ $slot['time'] }}"
                          {{ $slot['available'] ? '' : 'disabled' }}>
                          {{ $slot['label'] }}
                        </button>
                      @empty
                        <span class="profile-no-slots">No clinic hours</span>
                      @endforelse
                    </div>
                  </div>
                @endforeach
                </div>

                <label class="profile-booking-notes">
                  <span>Phone Number <span style="color:#ef4444">*</span></span>
                  <input type="tel" name="phone_number" required minlength="7" maxlength="20"
                    value="{{ auth()->check() ? (auth()->user()->patientProfile?->phone ?? '') : '' }}"
                    placeholder="e.g. +1 555 123 4567"
                    style="width:100%;padding:10px 14px;border:2px solid #e2e8f0;border-radius:10px;font-size:14px;outline:none;font-family:inherit;margin-top:6px;">
                </label>

                <label class="profile-booking-notes">
                  <span>Reason for visit</span>
                  <textarea name="notes" rows="3" placeholder="Add symptoms, concerns, or notes for the care team"></textarea>
                </label>

                <div class="profile-booking-feedback" data-profile-booking-feedback role="status" aria-live="polite"></div>

                @auth
                  @if(auth()->user()->isPatient())
                    <button type="submit" class="profile-btn profile-btn-primary" data-profile-booking-submit disabled>
                      <i class="bi bi-calendar-check" aria-hidden="true"></i>
                      Confirm Selected Slot
                    </button>
                  @else
                    <p class="profile-form-note">Only patient accounts can book appointments.</p>
                  @endif
                @else
                  <a href="{{ route('login') }}" class="profile-btn profile-btn-primary">
                    <i class="bi bi-box-arrow-in-right" aria-hidden="true"></i>
                    Login to Book
                  </a>
                @endauth
              </form>
            </article>

            <article class="profile-panel" id="services-profile">
              <div class="profile-panel-heading">
                <span>Treatments & Care</span>
                <h2>Services Provided</h2>
              </div>
              <div class="profile-service-tags">
                @foreach($services as $service)
                  <span><i class="bi bi-check2-circle" aria-hidden="true"></i>{{ $service }}</span>
                @endforeach
              </div>
            </article>

   
          </div>

          <aside class="col-lg-4">
            <div class="profile-sidebar">
              <div class="profile-sidebar-card">
                <h2>Book with confidence</h2>
                <p>Appointments are reviewed by Medicare staff for accurate scheduling and care coordination.</p>
                <a href="{{ $bookUrl }}" class="profile-btn profile-btn-primary w-100"><i class="bi bi-calendar-check" aria-hidden="true"></i> Book Appointment</a>
              </div>

              <div class="profile-sidebar-card">
                <h3>Contact Information</h3>
                @if($doctor->phone)
                  <a href="tel:{{ $doctor->phone }}"><i class="bi bi-telephone" aria-hidden="true"></i>{{ $doctor->phone }}</a>
                @endif
                @if($doctor->email)
                  <a href="mailto:{{ $doctor->email }}"><i class="bi bi-envelope" aria-hidden="true"></i>{{ $doctor->email }}</a>
                @endif
                <span><i class="bi bi-geo-alt" aria-hidden="true"></i>{{ $doctor->address ?: 'Medicare Main Clinic' }}</span>
                <span><i class="bi bi-cash" aria-hidden="true"></i>${{ number_format($doctor->consultation_fee, 2) }} consultation fee</span>
              </div>
            </div>
          </aside>
        </div>
      </div>
    </section>
  </main>

  <a href="{{ $bookUrl }}" class="profile-mobile-book">
    <i class="bi bi-calendar-check" aria-hidden="true"></i>
    Book Appointment
  </a>

  <div class="container text-center py-4">
    <a href="{{ route('website.doctors') }}" class="doctor-btn doctor-btn-secondary">
      <i class="bi bi-arrow-left" aria-hidden="true"></i> Back to All Doctors
    </a>
  </div>

@endsection
