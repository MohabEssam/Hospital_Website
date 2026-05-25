@extends('layouts.website')

@section('title', 'Medicare Hospital')

@section('content')

    <!-- Hero Section -->
    <section id="hero" class="hero hero-video-section section" aria-label="Medicare Hospital introduction">
      <video class="hero-bg-video" autoplay muted loop playsinline preload="metadata" poster="{{ asset('website-assets/img/medicare.png') }}">
        <source src="{{ asset('videos/CHUVTTV_TEASER.mp4') }}" type="video/mp4">
      </video>
      <div class="hero-video-overlay" aria-hidden="true"></div>

      <div class="container hero-video-content">
        <div class="hero-copy">
          <h1>Medicare</h1>
          <h2>Hospital</h2>

        </div>
      </div>

      <a href="#about" class="hero-scroll-indicator" aria-label="Scroll to About section">
        <i class="bi bi-chevron-down" aria-hidden="true"></i>
      </a>
    </section>

    <div class="hero-video-modal" id="heroVideoModal" role="dialog" aria-modal="true" aria-label="Medicare full video" hidden>
      <div class="hero-video-modal__backdrop" data-hero-video-close></div>
      <div class="hero-video-modal__dialog">
        <button class="hero-video-modal__close" type="button" aria-label="Close video" data-hero-video-close>
          <i class="bi bi-x-lg" aria-hidden="true"></i>
        </button>
        <video id="heroFullVideo" class="hero-video-modal__video" controls playsinline preload="metadata">
          <source src="{{ asset('videos/CHUVTTV_TEASER.mp4') }}" type="video/mp4">
        </video>
      </div>
    </div>

    <!-- About Section -->
    <section id="about" class="about section">
      <div class="container">
        <div class="row gy-4 gx-5">
          <div class="col-lg-6 position-relative align-self-start reveal" style="transition-delay: 200ms">
            <img src="{{ asset('website-assets/img/about.jpg') }}" class="img-fluid" alt="About Medicare">
          </div>
          <div class="col-lg-6 content reveal" style="transition-delay: 100ms">
            <h3>About Medicare Hospital</h3>
            <p>Medicare is a leading healthcare institution dedicated to providing exceptional medical services
               across a wide range of specialties. Our team of experienced doctors and state-of-the-art facilities
               ensure the highest quality of patient care.</p>
            <ul>
              <li>
                <i class="bi bi-chat-left-text"></i>
                <div>
                  <h5>Our Mission</h5>
                  <p>To deliver compassionate, accessible, and high-quality healthcare to every patient who walks through our doors.</p>
                </div>
              </li>
              <li>
                <i class="bi bi-eye"></i>
                <div>
                  <h5>Our Vision</h5>
                  <p>To be the most trusted hospital, recognized for clinical excellence, innovation, and patient-centered care.</p>
                </div>
              </li>
              <li>
                <i class="bi bi-graph-up-arrow"></i>
                <div>
                  <h5>Our Values</h5>
                  <p>Integrity, compassion, innovation, and excellence in everything we do.</p>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </section>

    <!-- Stats Section -->
    <section id="stats" class="stats section light-background stats-bg">
      <div class="stats-overlay">
        <div class="container reveal" style="transition-delay: 100ms">
          <div class="row gy-4 justify-content-center">
            <div class="col-lg-3 col-md-6 d-flex flex-column align-items-center">
              <i class="fa-solid fa-hospital"></i>
              <div class="stats-item">
                <span data-purecounter-start="0" data-purecounter-end="{{ $stats['departments'] }}" data-purecounter-duration="1" class="purecounter"></span>
                <p>Departments</p>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 d-flex flex-column align-items-center">
              <i class="fa-solid fa-user-doctor"></i>
              <div class="stats-item">
                <span data-purecounter-start="0" data-purecounter-end="{{ $stats['doctors'] }}" data-purecounter-duration="1" class="purecounter"></span>
                <p>Doctors</p>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 d-flex flex-column align-items-center">
              <i class="fa-solid fa-users"></i>
              <div class="stats-item">
                <span data-purecounter-start="0" data-purecounter-end="{{ $stats['patients'] }}" data-purecounter-duration="1" class="purecounter"></span>
                <p>Patients Served</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Medical Center Services Section -->
    <section id="services" class="mcs-section">
      <div class="container">
        <div class="mcs-heading">
          <h2 class="mcs-title">Our Medical Center Services</h2>
          <div class="mcs-dots">
            <span class="mcs-dot"></span>
            <span class="mcs-dot active"></span>
            <span class="mcs-dot"></span>
          </div>
        </div>

        <div class="mcs-track-wrapper">
          <button class="mcs-arrow mcs-arrow-left" aria-label="Previous services">
            <i class="bi bi-chevron-left"></i>
          </button>

          <div class="mcs-track">
            @foreach($patientCareServices as $pcService)
            <div class="mcs-card">
              <div class="mcs-card-img">
                @if($pcService->image)
                  <img src="{{ asset($pcService->image) }}" alt="{{ $pcService->name }}">
                @else
                  <div class="mcs-card-placeholder">
                    <i class="{{ $pcService->icon_class ?? 'bi bi-heart-pulse' }}"></i>
                  </div>
                @endif
              </div>
              <h5 class="mcs-card-name">{{ $pcService->name }}</h5>
              <p class="mcs-card-desc">{{ Str::limit($pcService->description, 100) }}</p>
            </div>
            @endforeach
          </div>

          <button class="mcs-arrow mcs-arrow-right" aria-label="Next services">
            <i class="bi bi-chevron-right"></i>
          </button>
        </div>

        <div class="text-center mt-4">
          <a href="{{ route('website.patient-care') }}" class="mcs-readmore-btn">Read More</a>
        </div>
      </div>
    </section>

    <!-- Departments Section -->
    <section id="departments" class="homepage-depts-section">
      <div class="homepage-depts-wrapper">
        {{-- Left image --}}
        <div class="homepage-depts-image reveal right">
          <img src="{{ asset('assets/images/Department/imagehome.jpg') }}" alt="Our Departments">
        </div>
        {{-- Right grid --}}
        <div class="homepage-depts-grid reveal">
          @foreach($departments as $index => $department)
            <a href="{{ route('website.departments.show', $department) }}" class="homepage-dept-cell">
              <div class="homepage-dept-icon">
                @if($department->icon)
                  <img src="{{ asset('storage/' . $department->icon) }}" alt="{{ $department->name }}">
                @else
                  <i class="bi bi-hospital"></i>
                @endif
              </div>
              <p class="homepage-dept-name">{{ $department->name }}</p>
            </a>
          @endforeach
        </div>
      </div>
    </section>

    <!-- Doctors Section -->
    <section id="doctors" class="doctors section">
      <div class="container section-title reveal">
        <h2>Our Doctors</h2>
        <p>Meet trusted specialists focused on precise, compassionate care</p>
      </div>
      <div class="container">
        <div class="doctor-tools reveal" style="transition-delay: 50ms">
          <label class="doctor-search">
            <i class="bi bi-search" aria-hidden="true"></i>
            <span class="visually-hidden">Search doctors</span>
            <input type="search" data-doctor-search placeholder="Search doctors or specialties">
          </label>
          <select data-doctor-department aria-label="Filter doctors by department">
            <option value="">All departments</option>
            @foreach($departments as $department)
              <option value="{{ $department->id }}">{{ $department->name }}</option>
            @endforeach
          </select>
        </div>

        <div class="doctor-card-skeletons" data-doctor-skeletons aria-hidden="true">
          <div></div><div></div><div></div>
        </div>

        <div class="row g-4 doctor-card-grid" data-doctor-grid>
          @foreach($doctors as $doctor)
            @php $isExtra = $loop->index >= 6; @endphp
            <div class="col-md-6 col-xl-4 doctor-card-col reveal {{ $isExtra ? 'active' : '' }}"
                 style="transition-delay: {{ 80 + (($loop->index % 6) * 80) }}ms"
                 data-doctor-default="{{ $isExtra ? 'extra' : 'visible' }}"
                 @if($isExtra) hidden @endif>
              @include('website.doctors._card', ['doctor' => $doctor, 'featured' => $loop->first && ! $isExtra])
            </div>
          @endforeach
        </div>

        <div class="doctor-empty-state" data-doctor-empty hidden>
          <i class="bi bi-search-heart" aria-hidden="true"></i>
          <p>No doctors match your search right now.</p>
        </div>
      </div>
      <div class="container text-center mt-4">
        <a href="{{ route('website.doctors') }}" class="doctor-section-link">View All Doctors <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
      </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="faq section light-background">
      <div class="container section-title reveal">
        <h2>Frequently Asked Questions</h2>
      </div>
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-lg-10 reveal" style="transition-delay: 100ms">
            <div class="faq-container">
              <div class="faq-item faq-active">
                <h3>How do I book an appointment?</h3>
                <div class="faq-content">
                  <p>Register or login as a patient, then go to the "Book Appointment" page. Select your preferred doctor,
                     choose a date and time, and submit your booking. You'll receive a confirmation once it's approved.</p>
                </div>
                <i class="faq-toggle bi bi-chevron-right"></i>
              </div>
              <div class="faq-item">
                <h3>What departments are available?</h3>
                <div class="faq-content">
                  <p>We offer a wide range of departments including General Medicine, Cardiology, Pediatrics, Dermatology,
                     Neurology, and Orthopedics. Each department is staffed with experienced specialists.</p>
                </div>
                <i class="faq-toggle bi bi-chevron-right"></i>
              </div>
              <div class="faq-item">
                <h3>Can I see my booking history?</h3>
                <div class="faq-content">
                  <p>Yes! After logging in, visit "My Bookings" from the navigation menu to view all your past and upcoming appointments.</p>
                </div>
                <i class="faq-toggle bi bi-chevron-right"></i>
              </div>
              <div class="faq-item">
                <h3>What should I bring to my appointment?</h3>
                <div class="faq-content">
                  <p>Please bring a valid ID, any previous medical records, and your insurance details if applicable.
                     Arrive at least 15 minutes before your scheduled appointment time.</p>
                </div>
                <i class="faq-toggle bi bi-chevron-right"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact section">
      <div class="container section-title reveal">
        <h2>Contact Us</h2>
      </div>
      <div class="container reveal" style="transition-delay: 100ms">
        <div class="row gy-4">
          <div class="col-md-4">
            <div class="info-item d-flex reveal" style="transition-delay: 300ms">
              <i class="bi bi-geo-alt flex-shrink-0"></i>
              <div>
                <h3>Location</h3>
                <p>456 Elm Street, Springfield</p>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="info-item d-flex reveal" style="transition-delay: 400ms">
              <i class="bi bi-telephone flex-shrink-0"></i>
              <div>
                <h3>Call Us</h3>
                <p>+20 2 1234 5678</p>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="info-item d-flex reveal" style="transition-delay: 500ms">
              <i class="bi bi-envelope flex-shrink-0"></i>
              <div>
                <h3>Email Us</h3>
                <p>info@medicare.test</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('heroVideoModal');
    const closeButtons = document.querySelectorAll('[data-hero-video-close]');
    const fullVideo = document.getElementById('heroFullVideo');

    if (!modal || !openButton || !fullVideo) {
      return;
    }

    function openHeroVideo() {
      modal.hidden = false;
      document.body.classList.add('hero-video-modal-open');
      fullVideo.currentTime = 0;
      fullVideo.play().catch(function () {});
    }

    function closeHeroVideo() {
      fullVideo.pause();
      fullVideo.currentTime = 0;
      modal.hidden = true;
      document.body.classList.remove('hero-video-modal-open');
      openButton.focus();
    }

    openButton.addEventListener('click', openHeroVideo);
    closeButtons.forEach(function (button) {
      button.addEventListener('click', closeHeroVideo);
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape' && !modal.hidden) {
        closeHeroVideo();
      }
    });
  });
</script>
@endpush
