@extends('layouts.website')

@section('title', 'Medicare Hospital')

@section('content')

    <!-- Hero Section -->
    <section id="hero" class="hero hero-video-section section" aria-label="Medicare Hospital introduction">
      <video class="hero-bg-video" autoplay muted loop playsinline preload="metadata" poster="{{ asset('website-assets/img/hero-bg.jpg') }}">
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
          <div class="col-lg-6 position-relative align-self-start" data-aos="fade-up" data-aos-delay="200">
            <img src="{{ asset('website-assets/img/about.jpg') }}" class="img-fluid" alt="About Medicare">
          </div>
          <div class="col-lg-6 content" data-aos="fade-up" data-aos-delay="100">
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
    <section id="stats" class="stats section light-background">
      <div class="container" data-aos="fade-up" data-aos-delay="100">
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
    </section>

    <!-- Departments Section -->
    <section id="departments" class="dpt-section">

      {{-- Section Header --}}
      <div class="dpt-section-header">
        <div class="container position-relative" style="z-index:2;">
          <div data-aos="fade-right">
            <span class="dpt-label"><i class="bi bi-building me-1"></i> Our Specialties</span>
            <h2 class="dpt-heading">Explore Our Departments</h2>
            <p class="dpt-subheading">Specialized medical care across multiple disciplines, delivered by experienced professionals.</p>
          </div>
        </div>
      </div>

      <div class="container dpt-body">
        <div class="row g-4">

          {{-- Sidebar --}}
          <div class="col-lg-3">
            {{-- Mobile toggle --}}
            <button class="btn dpt-mobile-toggle d-lg-none w-100 mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#dptSidebarCollapse">
              <i class="bi bi-list-ul me-2"></i> Select Department
            </button>
            <div class="collapse d-lg-block" id="dptSidebarCollapse">
              <div class="dpt-sidebar" data-aos="fade-right">
                <ul class="nav nav-tabs flex-column dpt-nav" role="tablist">
                  @foreach($departments as $index => $department)
                  <li class="nav-item" role="presentation">
                    <a class="dpt-nav-link {{ $index === 0 ? 'active show' : '' }}" data-bs-toggle="tab" href="#dept-tab-{{ $department->id }}" role="tab">
                      <span class="dpt-nav-icon">
                        @if($department->icon)
                          <img src="{{ asset('storage/' . $department->icon) }}" alt="" style="width:20px;height:20px;">
                        @else
                          <i class="bi bi-hospital"></i>
                        @endif
                      </span>
                      <span class="dpt-nav-text">
                        <span class="dpt-nav-name">{{ $department->name }}</span>
                        <span class="dpt-nav-count">{{ $department->doctors_count }} doctor{{ $department->doctors_count !== 1 ? 's' : '' }}</span>
                      </span>
                      <i class="bi bi-chevron-right dpt-nav-arrow"></i>
                    </a>
                  </li>
                  @endforeach
                </ul>
              </div>
            </div>
          </div>

          {{-- Tab Content --}}
          <div class="col-lg-9">
            <div class="tab-content" data-aos="fade-up">
              @foreach($departments as $index => $department)
              <div class="tab-pane fade {{ $index === 0 ? 'active show' : '' }}" id="dept-tab-{{ $department->id }}" role="tabpanel">

                {{-- Department Overview Card --}}
                <div class="dpt-overview-card">
                  @if($department->hero_image)
                    <div class="dpt-overview-img">
                      <img src="{{ asset('storage/' . $department->hero_image) }}" alt="{{ $department->name }}">
                      <div class="dpt-overview-overlay">
                        <span class="dpt-overview-badge"><i class="bi bi-people-fill me-1"></i> {{ $department->doctors_count }} Doctor{{ $department->doctors_count !== 1 ? 's' : '' }}</span>
                      </div>
                    </div>
                  @endif
                  <div class="dpt-overview-body">
                    <h3 class="dpt-overview-title">{{ $department->name }}</h3>
                    <p class="dpt-overview-desc">{{ $department->description }}</p>
                    @if($department->contact_email || $department->contact_phone)
                    <div class="dpt-overview-contact">
                      @if($department->contact_phone)
                        <span><i class="bi bi-telephone me-1"></i> {{ $department->contact_phone }}</span>
                      @endif
                      @if($department->contact_email)
                        <span><i class="bi bi-envelope me-1"></i> {{ $department->contact_email }}</span>
                      @endif
                    </div>
                    @endif
                  </div>
                </div>

                {{-- Services Grid --}}
                @if($department->services && count($department->services) > 0)
                <div class="dpt-block">
                  <h4 class="dpt-block-title"><i class="bi bi-clipboard2-check-fill me-2"></i>Services Offered</h4>
                  <div class="row g-3">
                    @foreach($department->services as $si => $service)
                    <div class="col-sm-6">
                      <div class="dpt-service-item">
                        <span class="dpt-service-icon"><i class="bi bi-check-circle-fill"></i></span>
                        <span class="dpt-service-text">{{ $service }}</span>
                      </div>
                    </div>
                    @endforeach
                  </div>
                </div>
                @endif

                {{-- Doctors Grid --}}
                @if($department->doctors->count() > 0)
                <div class="dpt-block">
                  <h4 class="dpt-block-title"><i class="bi bi-people-fill me-2"></i>Meet Our Doctors</h4>
                  <div class="row g-3">
                    @foreach($department->doctors as $doctor)
                    <div class="col-sm-6 col-xl-3">
                      <div class="dpt-doc-card">
                        <div class="dpt-doc-avatar-wrap">
                          @if($doctor->avatar)
                            <img src="{{ asset('storage/' . $doctor->avatar) }}" alt="{{ $doctor->name }}" class="dpt-doc-avatar">
                          @else
                            <div class="dpt-doc-initials">{{ $doctor->initials() }}</div>
                          @endif
                          @if($doctor->isAvailable())
                            <span class="dpt-doc-status-dot"></span>
                          @endif
                        </div>
                        <h6 class="dpt-doc-name">{{ $doctor->name }}</h6>
                        <p class="dpt-doc-spec">{{ $doctor->specialty }}</p>
                        <div class="dpt-doc-actions">
                          <a href="{{ route('website.doctors.show', $doctor) }}" class="dpt-doc-btn-outline" title="View Profile">
                            <i class="bi bi-person-lines-fill"></i>
                          </a>
                          @if($doctor->isAvailable())
                            @auth
                              @if(auth()->user()->isPatient())
                              <a href="{{ route('website.book', ['doctor_id' => $doctor->id]) }}" class="dpt-doc-btn-primary" title="Book Now">
                                <i class="bi bi-calendar-check"></i>
                              </a>
                              @endif
                            @else
                              <a href="{{ route('login') }}" class="dpt-doc-btn-primary" title="Login to Book">
                                <i class="bi bi-calendar-check"></i>
                              </a>
                            @endauth
                          @endif
                        </div>
                      </div>
                    </div>
                    @endforeach
                  </div>
                </div>
                @endif

                {{-- View full department --}}
                <div class="text-center mt-2">
                  <a href="{{ route('website.departments.show', $department) }}" class="dpt-link-more">
                    View full department details <i class="bi bi-arrow-right ms-1"></i>
                  </a>
                </div>

              </div>
              @endforeach
            </div>
          </div>

        </div>
      </div>

      {{-- Bottom CTA --}}
      <div class="container text-center dpt-cta-wrap" data-aos="fade-up">
        <a href="{{ route('website.departments') }}" class="dpt-btn-primary-lg">
          View All Departments <i class="bi bi-arrow-right ms-2"></i>
        </a>
      </div>
    </section>

    <!-- Doctors Section -->
    <section id="doctors" class="doctors section">
      <div class="container section-title" data-aos="fade-up">
        <h2>Our Doctors</h2>
        <p>Meet trusted specialists focused on precise, compassionate care</p>
      </div>
      <div class="container">
        <div class="doctor-tools" data-aos="fade-up" data-aos-delay="50">
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
          <div class="col-md-6 col-xl-4 doctor-card-col" data-aos="fade-up" data-aos-delay="{{ 80 + ($loop->index * 80) }}">
            @include('website.doctors._card', ['doctor' => $doctor, 'featured' => $loop->first])
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
      <div class="container section-title" data-aos="fade-up">
        <h2>Frequently Asked Questions</h2>
      </div>
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-lg-10" data-aos="fade-up" data-aos-delay="100">
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
      <div class="container section-title" data-aos="fade-up">
        <h2>Contact Us</h2>
      </div>
      <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row gy-4">
          <div class="col-md-4">
            <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="300">
              <i class="bi bi-geo-alt flex-shrink-0"></i>
              <div>
                <h3>Location</h3>
                <p>456 Elm Street, Springfield</p>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="400">
              <i class="bi bi-telephone flex-shrink-0"></i>
              <div>
                <h3>Call Us</h3>
                <p>+20 2 1234 5678</p>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="500">
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
