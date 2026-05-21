<aside class="left-sidebar">
<div>
<div class="brand-logo d-flex align-items-center justify-content-between">
@php
  $homeRoute = match (true) {
    auth()->user()?->isLab() => route('lab.dashboard'),
    auth()->user()?->isScanCenter() => route('scan-center.dashboard'),
    auth()->user()?->isPharmacy() => route('pharmacy.dashboard'),
    default => route('dashboard'),
  };
@endphp
<a href="{{ $homeRoute }}" class="text-nowrap logo-img">
  <img class="w-75 m-1 " src="{{ asset('assets/images/logos/Medicare.png') }}" alt="MediCare">
</a>
<div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
  <i class="ti ti-x fs-8"></i>
</div>
</div>
<nav class="sidebar-nav scroll-sidebar">
<ul id="sidebarnav">
<li class="nav-small-cap"><i class="ti ti-dots nav-small-cap-icon fs-4"></i><span class="hide-menu">Home</span></li>
@if(auth()->user()?->isAdmin() || auth()->user()?->isDoctor())
<li class="sidebar-item"><a class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><span><i class="ti ti-layout-dashboard"></i></span><span class="hide-menu">Dashboard</span></a></li>
<li class="sidebar-item"><a class="sidebar-link {{ request()->routeIs('appointments.*') ? 'active' : '' }}" href="{{ route('appointments.index') }}"><span><i class="fas fa-calendar-check"></i></span><span class="hide-menu">Appointments</span></a></li>
<li class="sidebar-item"><a class="sidebar-link {{ request()->routeIs('patients.*') ? 'active' : '' }}" href="{{ route('patients.index') }}"><span><i class="fas fa-user-injured"></i></span><span class="hide-menu">Patients</span></a></li>
<li class="sidebar-item"><a class="sidebar-link {{ request()->routeIs('doctors.index', 'doctors.show', 'doctors.create', 'doctors.edit') ? 'active' : '' }}" href="{{ route('doctors.index') }}"><span><i class="fas fa-user-md"></i></span><span class="hide-menu">Doctors</span></a></li>
@endif
@if(auth()->user()?->isAdmin())
<li class="sidebar-item"><a class="sidebar-link {{ request()->routeIs('departments.*') ? 'active' : '' }}" href="{{ route('departments.index') }}"><span><i class="fas fa-building"></i></span><span class="hide-menu">Departments</span></a></li>
<li class="sidebar-item"><a class="sidebar-link {{ request()->routeIs('staff-users.*') ? 'active' : '' }}" href="{{ route('staff-users.index') }}"><span><i class="fas fa-users-gear"></i></span><span class="hide-menu">Staff Users</span></a></li>
<li class="sidebar-item"><a class="sidebar-link {{ request()->routeIs('service-bookings.*') ? 'active' : '' }}" href="{{ route('service-bookings.index') }}"><span><i class="fas fa-concierge-bell"></i></span><span class="hide-menu">Service Bookings</span></a></li>
@endif
@if(auth()->user()?->isDoctor() && auth()->user()->doctorProfile)
<li class="sidebar-item"><a class="sidebar-link {{ request()->routeIs('doctors.schedule') ? 'active' : '' }}" href="{{ route('doctors.schedule', auth()->user()->doctorProfile) }}"><span><i class="fas fa-calendar-day"></i></span><span class="hide-menu">My Schedule</span></a></li>
@elseif(auth()->user()?->isAdmin())
<li class="sidebar-item"><a class="sidebar-link {{ request()->routeIs('doctors.schedule') ? 'active' : '' }}" href="{{ route('doctors.index') }}"><span><i class="fas fa-calendar-day"></i></span><span class="hide-menu">Doctors' Schedule</span></a></li>
@endif

@if(auth()->user()?->isAdmin() || auth()->user()?->isLab())
<li class="sidebar-item"><a class="sidebar-link {{ request()->routeIs('lab.*') ? 'active' : '' }}" href="{{ route('lab.dashboard') }}"><span><i class="fas fa-vials"></i></span><span class="hide-menu">Lab</span></a></li>
@endif
@if(auth()->user()?->isAdmin() || auth()->user()?->isScanCenter())
<li class="sidebar-item"><a class="sidebar-link {{ request()->routeIs('scan-center.*') ? 'active' : '' }}" href="{{ route('scan-center.dashboard') }}"><span><i class="fas fa-x-ray"></i></span><span class="hide-menu">Scan Center</span></a></li>
@endif
@if(auth()->user()?->isAdmin() || auth()->user()?->isPharmacy())
<li class="sidebar-item"><a class="sidebar-link {{ request()->routeIs('pharmacy.*', 'pharmacy-center.*') ? 'active' : '' }}" href="{{ route('pharmacy.dashboard') }}"><span><i class="fas fa-prescription-bottle-medical"></i></span><span class="hide-menu">Pharmacy</span></a></li>
@endif

@if(auth()->user()?->isAdmin())
<li class="nav-small-cap"><i class="ti ti-dots nav-small-cap-icon fs-4"></i><span class="hide-menu">AUTH</span></li>
<li class="sidebar-item"><a class="sidebar-link" href="{{ route('login') }}"><span><i class="ti ti-login"></i></span><span class="hide-menu">Login</span></a></li>
<li class="sidebar-item"><a class="sidebar-link" href="{{ route('register') }}"><span><i class="ti ti-user-plus"></i></span><span class="hide-menu">Register</span></a></li>
@endif
</ul>
</nav>
</div>
</aside>
