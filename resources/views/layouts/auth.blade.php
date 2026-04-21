<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $title ?? 'Hospital Management Dashboard' }}</title>
<link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/logos/hospital (1).png') }}" />
<link rel="stylesheet" href="{{ asset('assets/css/styles.min.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
@stack('styles')
</head>
<body>
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    <div class="position-relative overflow-hidden radial-gradient min-vh-100 d-flex align-items-center justify-content-center">
      <div class="d-flex align-items-center justify-content-center w-100">
        <div class="row justify-content-center w-100">
          <div class="col-md-8 col-lg-6 col-xxl-3">
            <div class="card mb-0">
              <div class="card-body">
                <a href="{{ route('home') }}" class="text-nowrap logo-img d-block">
                  <img class="auth-logo my-3" src="{{ asset('assets/images/logos/Medicare.png') }}" alt="MediCare Logo">
                </a>

                @include('partials.alerts')
                @yield('content')
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="{{ asset('assets/libs/jquery/dist/jquery.min.js') }}"></script>
  <script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
  @stack('scripts')
</body>
</html>
