<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>{{ $title ?? 'Hospital Management Dashboard' }}</title>
<link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/logos/hospital (1).png') }}"/>
<link rel="stylesheet" href="{{ asset('assets/css/styles.min.css') }}"/>
<link rel="stylesheet" href="{{ asset('assets/fontawesome-free-7.2.0-web/css/all.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
@stack('styles')
</head>
<body>
<div class="page-wrapper" id="main-wrapper" data-layout="vertical"
  data-navbarbg="skin6" data-sidebartype="full"
  data-sidebar-position="fixed" data-header-position="fixed">
  @include('partials.sidebar')

  <div class="body-wrapper">
    @include('partials.navbar')

    <div class="container-fluid">
      @include('partials.alerts')
      @yield('content')
    </div>

    @include('partials.footer')
  </div>
</div>

<script src="{{ asset('assets/libs/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/js/sidebarmenu.js') }}"></script>
<script src="{{ asset('assets/js/app.min.js') }}"></script>
<script src="{{ asset('assets/libs/apexcharts/dist/apexcharts.min.js') }}"></script>
@stack('scripts')
</body>
</html>
