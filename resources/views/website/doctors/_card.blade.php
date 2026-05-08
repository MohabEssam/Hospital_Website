@php
  $experience = (int) ($doctor->years_of_experience ?? 0);
  $rating = number_format((float) ($doctor->rating ?? min(5, 4.6 + ($experience / 100))), 1);
  $isFeatured = $experience >= 10 || ($featured ?? false);
  $availabilityClass = $doctor->isAvailable() ? 'available' : 'busy';
  $availabilityText = $doctor->isAvailable() ? 'Available' : 'Busy';
  $departmentName = $doctor->department?->name ?? 'General Care';
  $bookUrl = auth()->check() && auth()->user()->isPatient()
      ? route('website.book', ['doctor_id' => $doctor->id])
      : route('login');
  $summary = $doctor->biography
      ? \Illuminate\Support\Str::limit(strip_tags($doctor->biography), 92)
      : 'Compassionate clinician focused on clear guidance, careful diagnosis, and patient-centered treatment.';
@endphp

<article class="doctor-card"
  data-doctor-card
  data-name="{{ \Illuminate\Support\Str::lower($doctor->name) }}"
  data-specialty="{{ \Illuminate\Support\Str::lower($doctor->specialty) }}"
  data-department="{{ $doctor->department_id ?? '' }}"
  data-rating="{{ $rating }}"
  data-experience="{{ $experience }}">


  <div class="doctor-card-header">
    <div class="doctor-avatar-wrap">
      @if($doctor->avatar)
        <img src="{{ asset('storage/' . $doctor->avatar) }}" class="doctor-avatar" alt="{{ $doctor->name }}" loading="lazy">
      @else
        <div class="doctor-avatar doctor-avatar-fallback" aria-hidden="true">
          {{ $doctor->initials() }}
        </div>
      @endif
    </div>

    <div class="doctor-status doctor-status-{{ $availabilityClass }}">
      <span aria-hidden="true"></span>
      {{ $availabilityText }}
    </div>
  </div>

  <div class="doctor-card-body">
    <p class="doctor-department">{{ $departmentName }}</p>
    <h3>{{ $doctor->name }}</h3>
    <p class="doctor-summary">{{ $summary }}</p>


  </div>

  <div class="doctor-card-actions">
    <div class="doctor-quick-actions" aria-label="Quick actions">
      @if($doctor->phone)
        <a href="tel:{{ $doctor->phone }}" aria-label="Call {{ $doctor->name }}"><i class="bi bi-telephone" aria-hidden="true"></i></a>
      @endif
      @if($doctor->email)
        <a href="mailto:{{ $doctor->email }}" aria-label="Message {{ $doctor->name }}"><i class="bi bi-chat-dots" aria-hidden="true"></i></a>
      @endif
      <a href="{{ route('website.doctors.show', $doctor) }}" aria-label="View {{ $doctor->name }} profile"><i class="bi bi-eye" aria-hidden="true"></i></a>
    </div>

    <div class="doctor-buttons">
      <a href="{{ $bookUrl }}" class="doctor-btn doctor-btn-primary">Book Appointment</a>
      <a href="{{ route('website.doctors.show', $doctor) }}" class="doctor-btn doctor-btn-secondary">View Profile</a>
    </div>
  </div>
</article>
