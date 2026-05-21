<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Medicare Hospital')</title>

  <link href="{{ asset('website-assets/img/favicon.png') }}" rel="icon">
  <link href="{{ asset('website-assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">
<link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/logos/hospital (1).png') }}"/>
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto&family=Poppins&family=Raleway&display=swap" rel="stylesheet">

  <link href="{{ asset('website-assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('website-assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('website-assets/css/style.css') }}" rel="stylesheet">
  <link href="{{ asset('website-assets/css/reveal.css') }}" rel="stylesheet">
  <link href="{{ asset('website-assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
  <link href="{{ asset('website-assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
  <link href="{{ asset('website-assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">
  <link href="{{ asset('website-assets/css/main.css') }}" rel="stylesheet">

  @stack('styles')
</head>
<body class="index-page {{ request()->routeIs('home') ? 'home-page' : 'inner-page' }}">

  <header id="header" class="header fixed-top" role="banner">
    <div class="branding d-flex align-items-center">
      <div class="container position-relative d-flex align-items-center justify-content-between">
        <a href="{{ route('home') }}" class="logo d-flex align-items-center" aria-label="Medicare home">
          <span class="logo-mark" aria-hidden="true">M</span>
          <span class="sitename">Medicare</span>
        </a>

        <nav id="navmenu" class="navmenu" aria-label="Primary navigation">
          <ul>
            <li><a href="{{ route('home') }}#hero" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a></li>
            <li><a href="{{ route('home') }}#about">About</a></li>
            <li class="dropdown specialties-dropdown">
              <a href="{{ route('website.departments') }}" class="{{ request()->routeIs('website.departments*') ? 'active' : '' }}">
                <span>Specialties</span> <i class="bi bi-chevron-down toggle-dropdown"></i>
              </a>
              <ul>
                @foreach($navDepartments as $dept)
                  <li>
                    <a href="{{ route('website.departments.show', $dept) }}" class="{{ request()->is('departments/'.$dept->slug) ? 'active' : '' }}">
                      {{ $dept->name }}
                    </a>
                  </li>
                @endforeach
                <li class="spec-dd-all">
                  <a href="{{ route('website.departments') }}">
                    View All Departments <i class="bi bi-arrow-right"></i>
                  </a>
                </li>
              </ul>
            </li>
            <li class="dropdown specialties-dropdown">
              <a href="{{ route('website.patient-care') }}" class="{{ request()->routeIs('website.patient-care*') ? 'active' : '' }}">
                <span>Patient Care</span> <i class="bi bi-chevron-down toggle-dropdown"></i>
              </a>
              <ul>
                @foreach($navPatientCareServices as $pcService)
                  <li>
                    <a href="{{ route('website.patient-care.show', $pcService) }}" class="{{ request()->is('patient-care/'.$pcService->slug) ? 'active' : '' }}">
                      {{ $pcService->name }}
                    </a>
                  </li>
                @endforeach
                <li class="spec-dd-all">
                  <a href="{{ route('website.patient-care') }}">
                    View All Services <i class="bi bi-arrow-right"></i>
                  </a>
                </li>
              </ul>
            </li>
            <li><a href="{{ route('home') }}#doctors" class="{{ request()->routeIs('website.doctors*') ? 'active' : '' }}">Doctors</a></li>
            <li><a href="{{ route('home') }}#contact">Contact</a></li>
  @auth
    @if(auth()->user()->isPatient())
        <li>
            <a href="{{ route('patient.dashboard') }}" class="{{ request()->routeIs('patient.*') ? 'active' : '' }}">
                Patient Portal
            </a>
        </li>
        <li>
            <a href="{{ route('my-bookings') }}" class="{{ request()->routeIs('my-bookings') ? 'active' : '' }}">
                My Bookings
            </a>
        </li>
    @endif
@endauth
            <li class="mobile-nav-cta">
              <a href="{{ auth()->check() && auth()->user()->isPatient() ? route('website.book') : route('login') }}">Book Appointment</a>
            </li>
          </ul>
        </nav>

        <div class="header-actions d-flex align-items-center">
          @auth
            <div class="header-user">
              Hello <strong>{{ auth()->user()->name }}</strong>
            </div>
            @if(auth()->user()->isAdmin() || auth()->user()->isDoctor())
              <a class="cta-btn d-none d-sm-block me-2" href="{{ route('dashboard') }}">Dashboard</a>
            @endif
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
              @csrf
              <button type="submit" class="cta-btn d-none d-sm-block" style="border:none; cursor:pointer;">Logout</button>
            </form>
          @endauth

          <a class="cta-btn primary-appointment-btn d-none d-sm-inline-flex" href="{{ auth()->check() && auth()->user()->isPatient() ? route('website.book') : route('login') }}">Book Appointment</a>
        </div>

        <button class="mobile-nav-toggle d-xl-none bi bi-list" type="button" aria-label="Open navigation menu" aria-controls="navmenu" aria-expanded="false"></button>
      </div>
    </div>
  </header>
  <div class="mobile-nav-backdrop" aria-hidden="true"></div>

  <main class="main">
    @yield('content')
  </main>

  <footer id="footer" class="footer light-background">
    <div class="container footer-top">
      <div class="row gy-4">
        <div class="col-lg-4 col-md-6 footer-about">
          <a href="{{ route('home') }}" class="logo d-flex align-items-center">
            <span class="sitename">Medicare</span>
          </a>
          <div class="footer-contact pt-3">
            <p>456 Elm Street, Springfield</p>
            <p class="mt-3"><strong>Phone:</strong> <span>+20 2 1234 5678</span></p>
            <p><strong>Email:</strong> <span>info@medicare.test</span></p>
          </div>
          <div class="social-links d-flex mt-4">
            <a href=""><i class="bi bi-twitter-x"></i></a>
            <a href=""><i class="bi bi-facebook"></i></a>
            <a href=""><i class="bi bi-instagram"></i></a>
            <a href=""><i class="bi bi-linkedin"></i></a>
          </div>
        </div>

        <div class="col-lg-2 col-md-3 footer-links"></div>

        <div class="col-lg-3 col-md-3 footer-links">
          <h4>Quick Links</h4>
          <ul>
            <li><a href="{{ route('home') }}">Home</a></li>
            <li><a href="{{ route('home') }}#about">About Us</a></li>
            <li><a href="{{ route('website.departments') }}">Departments</a></li>
            <li><a href="{{ route('website.doctors') }}">Doctors</a></li>
            <li><a href="{{ route('home') }}#contact">Contact</a></li>
          </ul>
        </div>

        <div class="col-lg-3 col-md-3 footer-links">
          <h4>Our Services</h4>
          <ul>
            <li><a href="{{ route('website.departments') }}">Medical Departments</a></li>
            <li><a href="{{ route('website.doctors') }}">Find a Doctor</a></li>
            <li><a href="{{ route('my-bookings') }}">My Bookings</a></li>
            @auth
              @if(auth()->user()->isPatient())
                <li><a href="{{ route('patient.dashboard') }}">Patient Portal</a></li>
                <li><a href="{{ route('website.book') }}">Book Appointment</a></li>
              @endif
            @endauth
          </ul>
        </div>
      </div>
    </div>

    <div class="container copyright text-center mt-4">
      <p>&copy; <span>Copyright</span> <strong class="px-1 sitename">Medicare Hospital</strong> <span>All Rights Reserved</span></p>
    </div>
  </footer>

  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
  <div id="preloader"></div>

  <script src="{{ asset('website-assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('website-assets/vendor/php-email-form/validate.js') }}"></script>
  <script src="{{ asset('website-assets/js/reveal.js') }}"></script>
  <script src="{{ asset('website-assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
  <script src="{{ asset('website-assets/vendor/purecounter/purecounter_vanilla.js') }}"></script>
  <script src="{{ asset('website-assets/vendor/swiper/swiper-bundle.min.js') }}"></script>
  <script src="{{ asset('website-assets/js/main.js') }}"></script>

  @stack('scripts')
</body>
</html>
