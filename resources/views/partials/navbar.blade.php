<header class="app-header">
  <nav class="navbar navbar-expand-lg navbar-light">
    <ul class="navbar-nav flex-grow-1">
      <li class="nav-item d-block d-xl-none">
        <a class="nav-link sidebartoggler nav-icon-hover" id="headerCollapse" href="javascript:void(0)"><i class="ti ti-menu-2"></i></a>
      </li>
    </ul>
    <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
      <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
        <li class="nav-item">
          <a class="nav-link nav-icon-hover" href="javascript:void(0)">
            <i class="ti ti-bell-ringing"></i>
            <div class="notification bg-primary rounded-circle"></div>
          </a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link nav-icon-hover" href="javascript:void(0)" id="drop2" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="{{ asset('assets/images/profile/user-1.jpg') }}" alt="" width="35" height="35" class="rounded-circle">
          </a>
          <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
            <div class="message-body">
              @php
                $profileUrl = route('dashboard');

                if (auth()->user()->isDoctor() && auth()->user()->doctorProfile) {
                    $profileUrl = route('doctors.show', auth()->user()->doctorProfile);
                } elseif (auth()->user()->isPatient() && auth()->user()->patientProfile) {
                    $profileUrl = route('patients.show', auth()->user()->patientProfile);
                } elseif (auth()->user()->isLab()) {
                    $profileUrl = route('lab.dashboard');
                } elseif (auth()->user()->isScanCenter()) {
                    $profileUrl = route('scan-center.dashboard');
                } elseif (auth()->user()->isPharmacy()) {
                    $profileUrl = route('pharmacy.dashboard');
                }
              @endphp

              <a href="{{ $profileUrl }}" class="d-flex align-items-center gap-2 dropdown-item"><i class="ti ti-user fs-6"></i><p class="mb-0 fs-3">My Profile</p></a>
              @if(auth()->user()->isAdmin() || auth()->user()->isDoctor())
                <a href="{{ route('appointments.index') }}" class="d-flex align-items-center gap-2 dropdown-item"><i class="ti ti-mail fs-6"></i><p class="mb-0 fs-3">My Appointments</p></a>
              @endif
              <div class="px-3 py-2">
                <p class="mb-1 fw-semibold small">{{ auth()->user()->name }}</p>
                <p class="mb-0 text-muted small text-capitalize">{{ auth()->user()->role }}</p>
              </div>
              <form action="{{ route('logout') }}" method="POST" class="px-3">
                @csrf
                <button type="submit" class="btn btn-outline-primary mt-2 d-block w-100">Logout</button>
              </form>
            </div>
          </div>
        </li>
      </ul>
    </div>
  </nav>
</header>
