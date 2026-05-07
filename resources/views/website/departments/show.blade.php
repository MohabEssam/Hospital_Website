@extends('layouts.website')

@section('title', $department->name . ' — Medicare Hospital')

@section('content')

  {{-- ═══ Hero Banner ═══ --}}
  <section class="dept-hero">
    <div class="container position-relative" style="z-index:2;">
      <nav aria-label="breadcrumb" class="reveal right" style="transition-delay: 0ms">
        <ol class="dept-breadcrumb">
          <li><a href="{{ route('home') }}">Home</a></li>
          <li><a href="{{ route('website.departments') }}">Departments</a></li>
          <li class="active">{{ $department->name }}</li>
        </ol>
      </nav>
      <h1 class="dept-hero-title reveal right" style="transition-delay: 100ms">{{ $department->name }}</h1>
      @if($department->description)
        <p class="dept-hero-desc reveal right" style="transition-delay: 200ms">{{ Str::limit($department->description, 200) }}</p>
      @endif
      <div class="dept-hero-badges reveal right" style="transition-delay: 300ms">
        <span class="dept-hero-badge"><i class="bi bi-people-fill"></i> {{ $department->doctors->count() }} Doctor{{ $department->doctors->count() !== 1 ? 's' : '' }}</span>
        @if($department->services)
          <span class="dept-hero-badge"><i class="bi bi-clipboard2-check-fill"></i> {{ count($department->services) }} Service{{ count($department->services) !== 1 ? 's' : '' }}</span>
        @endif
        @if($department->contact_phone)
          <span class="dept-hero-badge"><i class="bi bi-telephone-fill"></i> {{ $department->contact_phone }}</span>
        @endif
      </div>
    </div>
  </section>

  {{-- ═══ Main Content ═══ --}}
  <section class="dept-content">
    <div class="container">
      <div class="row g-4">

        {{-- ── Sidebar ── --}}
        <div class="col-lg-3">

          {{-- Mobile toggle --}}
          <button class="btn dept-sidebar-toggle d-lg-none w-100 mb-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#deptSidebar">
            <i class="bi bi-list-ul me-2"></i> Browse Departments
          </button>

          {{-- Offcanvas sidebar for mobile --}}
          <div class="offcanvas offcanvas-start d-lg-none" id="deptSidebar">
            <div class="offcanvas-header">
              <h5 class="offcanvas-title">Departments</h5>
              <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
            </div>
            <div class="offcanvas-body p-0">
              <ul class="dept-sidebar-list">
                @foreach($allDepartments as $dept)
                <li>
                  <a href="{{ route('website.departments.show', $dept) }}"
                     class="dept-sidebar-item {{ $dept->id === $department->id ? 'active' : '' }}">
                    <div class="dept-sidebar-icon">
                      @if($dept->icon)
                        <img src="{{ asset('storage/' . $dept->icon) }}" alt="" style="width:20px;height:20px;">
                      @else
                        <i class="bi bi-hospital"></i>
                      @endif
                    </div>
                    <div class="flex-grow-1">
                      <div class="dept-sidebar-name">{{ $dept->name }}</div>
                      <div class="dept-sidebar-count">{{ $dept->doctors_count }} Doctor{{ $dept->doctors_count !== 1 ? 's' : '' }}</div>
                    </div>
                    <i class="bi bi-chevron-right dept-sidebar-arrow"></i>
                  </a>
                </li>
                @endforeach
              </ul>
            </div>
          </div>

          {{-- Desktop sidebar --}}
          <div class="dept-sidebar-card d-none d-lg-block reveal right">
            <h5 class="dept-sidebar-title">
              <i class="bi bi-grid-fill me-2"></i>Departments
            </h5>
            <ul class="dept-sidebar-list">
              @foreach($allDepartments as $dept)
              <li>
                <a href="{{ route('website.departments.show', $dept) }}"
                   class="dept-sidebar-item {{ $dept->id === $department->id ? 'active' : '' }}">
                  <div class="dept-sidebar-icon">
                    @if($dept->icon)
                      <img src="{{ asset('storage/' . $dept->icon) }}" alt="" style="width:20px;height:20px;">
                    @else
                      <i class="bi bi-hospital"></i>
                    @endif
                  </div>
                  <div class="flex-grow-1">
                    <div class="dept-sidebar-name">{{ $dept->name }}</div>
                    <div class="dept-sidebar-count">{{ $dept->doctors_count }} Doctor{{ $dept->doctors_count !== 1 ? 's' : '' }}</div>
                  </div>
                  <i class="bi bi-chevron-right dept-sidebar-arrow"></i>
                </a>
              </li>
              @endforeach
            </ul>

            {{-- Contact card --}}
            @if($department->contact_email || $department->contact_phone)
            <div class="dept-contact-card">
              <h6><i class="bi bi-headset me-2"></i>Need Help?</h6>
              @if($department->contact_phone)
                <p><i class="bi bi-telephone me-2"></i>{{ $department->contact_phone }}</p>
              @endif
              @if($department->contact_email)
                <p><i class="bi bi-envelope me-2"></i>{{ $department->contact_email }}</p>
              @endif
            </div>
            @endif
          </div>
        </div>

        {{-- ── Main Content Area ── --}}
        <div class="col-lg-9">

          {{-- About Card --}}
          @if($department->description)
          <div class="dept-card reveal">
            <div class="dept-card-header">
              <i class="bi bi-info-circle-fill"></i>
              <h4>About {{ $department->name }}</h4>
            </div>
            <div class="dept-card-body">
              @if($department->hero_image)
                <div class="dept-about-img mb-4">
                  <img src="{{ asset('storage/' . $department->hero_image) }}" alt="{{ $department->name }}">
                </div>
              @endif
              <p class="dept-about-text">{{ $department->description }}</p>
            </div>
          </div>
          @endif

          {{-- Services Grid --}}
          @if($department->services && count($department->services) > 0)
          <div class="dept-card reveal">
            <div class="dept-card-header">
              <i class="bi bi-clipboard2-check-fill"></i>
              <h4>Our Services</h4>
            </div>
            <div class="dept-card-body">
              <div class="row g-3">
                @foreach($department->services as $index => $service)
                <div class="col-md-6 reveal" style="transition-delay: {{ ($index % 4) * 80 }}ms">
                  <div class="dept-service-card">
                    <div class="dept-service-icon">
                      <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <span class="dept-service-name">{{ $service }}</span>
                  </div>
                </div>
                @endforeach
              </div>
            </div>
          </div>
          @endif

          {{-- Doctors Section --}}
          <div class="dept-card reveal">
            <div class="dept-card-header">
              <i class="bi bi-people-fill"></i>
              <h4>Our Doctors</h4>
              <span class="dept-card-badge">{{ $department->doctors->count() }} Doctor{{ $department->doctors->count() !== 1 ? 's' : '' }}</span>
            </div>
            <div class="dept-card-body">
              @if($department->doctors->count() > 0)
              <div class="row g-4">
                @foreach($department->doctors as $index => $doctor)
                <div class="col-md-6 col-xl-4 reveal" style="transition-delay: {{ ($index % 3) * 100 }}ms">
                  <div class="dept-doctor-card">
                    <div class="dept-doctor-img-wrap">
                      @if($doctor->avatar)
                        <img src="{{ asset('storage/' . $doctor->avatar) }}" alt="{{ $doctor->name }}" class="dept-doctor-img">
                      @else
                        <div class="dept-doctor-initials">{{ $doctor->initials() }}</div>
                      @endif
                      @if($doctor->isAvailable())
                        <span class="dept-doctor-status available"><i class="bi bi-circle-fill"></i> Available</span>
                      @else
                        <span class="dept-doctor-status unavailable"><i class="bi bi-circle-fill"></i> Unavailable</span>
                      @endif
                    </div>
                    <div class="dept-doctor-info">
                      <h5 class="dept-doctor-name">{{ $doctor->name }}</h5>
                      <p class="dept-doctor-spec">{{ $doctor->specialty }}</p>

                      <div class="dept-doctor-actions">
                        <a href="{{ route('website.doctors.show', $doctor) }}" class="dept-btn-outline">
                          <i class="bi bi-person-lines-fill"></i> Profile
                        </a>
                        @if($doctor->isAvailable())
                          @auth
                            @if(auth()->user()->isPatient())
                              <a href="{{ route('website.book', ['doctor_id' => $doctor->id]) }}" class="dept-btn-primary">
                                <i class="bi bi-calendar-check"></i> Book Now
                              </a>
                            @endif
                          @else
                            <a href="{{ route('login') }}" class="dept-btn-primary">
                              <i class="bi bi-calendar-check"></i> Book Now
                            </a>
                          @endauth
                        @endif
                      </div>
                    </div>
                  </div>
                </div>
                @endforeach
              </div>
              @else
              <div class="text-center py-5">
                <i class="bi bi-person-x" style="font-size:48px;color:#cbd5e1;"></i>
                <p class="mt-3" style="color:#94a3b8;">No doctors available in this department at the moment.</p>
              </div>
              @endif
            </div>
          </div>

          {{-- CTA Back --}}
          <div class="text-center mt-4 reveal">
            <a href="{{ route('website.departments') }}" class="dept-btn-outline" style="padding:12px 32px;">
              <i class="bi bi-arrow-left"></i> Back to All Departments
            </a>
          </div>

        </div>
      </div>
    </div>
  </section>

@endsection

@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════════
   Department Details — Premium Medical UI
   ═══════════════════════════════════════════════════════════════ */

/* ── Hero ───────────────────────────────────────────────────── */
.dept-hero {
  background: linear-gradient(135deg, #3f4047 0%, #2d2e33 50%, #1a1b1f 100%);
  padding: 56px 0 48px;
  position: relative;
  overflow: hidden;
}
.dept-hero::before {
  content: '';
  position: absolute;
  top: -40%;
  right: -15%;
  width: 500px;
  height: 500px;
  background: rgba(255,255,255,0.04);
  border-radius: 50%;
}
.dept-hero::after {
  content: '';
  position: absolute;
  bottom: -30%;
  left: -8%;
  width: 350px;
  height: 350px;
  background: rgba(255,255,255,0.03);
  border-radius: 50%;
}
.dept-hero-title {
  font-family: 'Poppins', sans-serif;
  font-size: 42px;
  font-weight: 700;
  color: #fff;
  margin-bottom: 12px;
  line-height: 1.2;
}
.dept-hero-desc {
  color: rgba(255,255,255,0.85);
  font-size: 17px;
  max-width: 640px;
  line-height: 1.7;
  margin-bottom: 20px;
}
.dept-breadcrumb {
  list-style: none;
  display: flex;
  gap: 8px;
  padding: 0;
  margin: 0 0 16px;
  font-size: 14px;
}
.dept-breadcrumb li + li::before {
  content: '/';
  margin-right: 8px;
  color: rgba(255,255,255,0.4);
}
.dept-breadcrumb a { color: rgba(255,255,255,0.7); text-decoration: none; transition: color 0.2s; }
.dept-breadcrumb a:hover { color: #fff; }
.dept-breadcrumb .active { color: rgba(255,255,255,0.95); font-weight: 500; }
.dept-hero-badges { display: flex; gap: 12px; flex-wrap: wrap; }
.dept-hero-badge {
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

@media (max-width: 767px) {
  .dept-hero { padding: 36px 0 32px; }
  .dept-hero-title { font-size: 28px; }
  .dept-hero-desc { font-size: 15px; }
}

/* ── Content wrapper ────────────────────────────────────────── */
.dept-content {
  padding: 40px 0 64px;
  background: #f8f9fa;
}

/* ── Sidebar ────────────────────────────────────────────────── */
.dept-sidebar-card {
  background: #fff;
  border-radius: 16px;
  box-shadow: 0 2px 20px rgba(0,0,0,0.06);
  overflow: hidden;
  position: sticky;
  top: 90px;
}
.dept-sidebar-title {
  font-family: 'Poppins', sans-serif;
  font-weight: 600;
  font-size: 16px;
  padding: 20px 20px 12px;
  color: #1e293b;
  margin: 0;
  border-bottom: 1px solid #f1f5f9;
}
.dept-sidebar-list {
  list-style: none;
  padding: 8px;
  margin: 0;
}
.dept-sidebar-item {
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
.dept-sidebar-item:hover {
  background: #e8e8ea;
  color: #3f4047;
  transform: translateX(4px);
}
.dept-sidebar-item.active {
  background: linear-gradient(135deg, #3f4047, #2d2e33);
  color: #fff;
  box-shadow: 0 4px 16px rgba(63,64,71,0.25);
}
.dept-sidebar-item.active .dept-sidebar-count,
.dept-sidebar-item.active .dept-sidebar-arrow {
  color: rgba(255,255,255,0.8);
}
.dept-sidebar-icon {
  width: 40px;
  height: 40px;
  background: #e8e8ea;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
  color: #3f4047;
  flex-shrink: 0;
  transition: all 0.25s;
}
.dept-sidebar-item.active .dept-sidebar-icon {
  background: rgba(255,255,255,0.2);
  color: #fff;
}
.dept-sidebar-item:hover .dept-sidebar-icon {
  background: #3f4047;
  color: #fff;
}
.dept-sidebar-name {
  font-weight: 600;
  font-size: 14px;
  line-height: 1.3;
}
.dept-sidebar-count {
  font-size: 12px;
  color: #94a3b8;
  margin-top: 1px;
}
.dept-sidebar-arrow {
  font-size: 12px;
  color: #cbd5e1;
  transition: transform 0.25s;
}
.dept-sidebar-item:hover .dept-sidebar-arrow {
  transform: translateX(3px);
  color: #3f4047;
}
.dept-sidebar-item.active .dept-sidebar-arrow {
  transform: translateX(3px);
}

/* Sidebar toggle (mobile) */
.dept-sidebar-toggle {
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
.dept-sidebar-toggle:hover {
  background: #e8e8ea;
  border-color: #3f4047;
}

/* Contact card */
.dept-contact-card {
  margin: 8px;
  padding: 20px;
  background: linear-gradient(135deg, #e8e8ea, #f3f4f6);
  border-radius: 12px;
  margin-top: 4px;
}
.dept-contact-card h6 {
  font-weight: 600;
  color: #3f4047;
  margin-bottom: 12px;
}
.dept-contact-card p {
  font-size: 13px;
  color: #475569;
  margin-bottom: 6px;
}
.dept-contact-card p:last-child { margin-bottom: 0; }

/* ── Cards (generic) ────────────────────────────────────────── */
.dept-card {
  background: #fff;
  border-radius: 16px;
  box-shadow: 0 2px 20px rgba(0,0,0,0.06);
  margin-bottom: 24px;
  overflow: hidden;
  transition: box-shadow 0.3s;
}
.dept-card:hover {
  box-shadow: 0 4px 28px rgba(0,0,0,0.09);
}
.dept-card-header {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 20px 24px;
  border-bottom: 1px solid #f1f5f9;
}
.dept-card-header i {
  font-size: 20px;
  color: #3f4047;
}
.dept-card-header h4 {
  font-family: 'Poppins', sans-serif;
  font-weight: 600;
  font-size: 18px;
  margin: 0;
  color: #1e293b;
}
.dept-card-badge {
  margin-left: auto;
  background: #e8e8ea;
  color: #3f4047;
  padding: 5px 14px;
  border-radius: 99px;
  font-size: 12px;
  font-weight: 600;
}
.dept-card-body {
  padding: 24px;
}

/* ── About image ────────────────────────────────────────────── */
.dept-about-img {
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}
.dept-about-img img {
  width: 100%;
  height: 280px;
  object-fit: cover;
  display: block;
}
.dept-about-text {
  color: #475569;
  font-size: 15px;
  line-height: 1.8;
}

/* ── Service Cards ──────────────────────────────────────────── */
.dept-service-card {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 16px 20px;
  background: #f8f9fa;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  transition: all 0.3s ease;
}
.dept-service-card:hover {
  background: #e8e8ea;
  border-color: #d1d1d3;
  transform: translateY(-2px);
  box-shadow: 0 4px 16px rgba(63,64,71,0.08);
}
.dept-service-icon {
  width: 40px;
  height: 40px;
  background: #e8e8ea;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #3f4047;
  font-size: 18px;
  flex-shrink: 0;
  transition: all 0.3s;
}
.dept-service-card:hover .dept-service-icon {
  background: #3f4047;
  color: #fff;
}
.dept-service-name {
  font-weight: 500;
  font-size: 14px;
  color: #334155;
}

/* ── Doctor Cards ───────────────────────────────────────────── */
.dept-doctor-card {
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  overflow: hidden;
  transition: all 0.35s ease;
  height: 100%;
  display: flex;
  flex-direction: column;
}
.dept-doctor-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 12px 32px rgba(63,64,71,0.12);
  border-color: #3f4047;
}
.dept-doctor-img-wrap {
  position: relative;
  overflow: hidden;
}
.dept-doctor-img {
  width: 100%;
  height: 200px;
  object-fit: cover;
  display: block;
  transition: transform 0.4s ease;
}
.dept-doctor-card:hover .dept-doctor-img {
  transform: scale(1.05);
}
.dept-doctor-initials {
  width: 100%;
  height: 200px;
  background: linear-gradient(135deg, #3f4047 0%, #1a1b1f 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: 'Poppins', sans-serif;
  font-size: 44px;
  font-weight: 700;
  color: #fff;
}
.dept-doctor-status {
  position: absolute;
  top: 12px;
  right: 12px;
  padding: 4px 12px;
  border-radius: 99px;
  font-size: 11px;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 4px;
}
.dept-doctor-status i { font-size: 7px; }
.dept-doctor-status.available {
  background: rgba(16,185,129,0.9);
  color: #fff;
}
.dept-doctor-status.available i {
  animation: pulse-dot 1.5s ease-in-out infinite;
}
@keyframes pulse-dot {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.4; }
}
.dept-doctor-status.unavailable {
  background: rgba(100,116,139,0.85);
  color: #fff;
}
.dept-doctor-info {
  padding: 20px;
  flex: 1;
  display: flex;
  flex-direction: column;
}
.dept-doctor-name {
  font-family: 'Poppins', sans-serif;
  font-weight: 600;
  font-size: 16px;
  color: #1e293b;
  margin-bottom: 2px;
}
.dept-doctor-spec {
  color: #3f4047;
  font-size: 13px;
  font-weight: 500;
  margin-bottom: 10px;
}

.dept-doctor-actions {
  margin-top: auto;
  display: flex;
  gap: 8px;
}

/* ── Buttons ────────────────────────────────────────────────── */
.dept-btn-primary {
  background: #3f4047;
  color: #fff;
  border: none;
  border-radius: 10px;
  padding: 9px 18px;
  font-size: 13px;
  font-weight: 600;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  transition: all 0.25s ease;
  flex: 1;
  justify-content: center;
}
.dept-btn-primary:hover {
  background: #2d2e33;
  color: #fff;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(63,64,71,0.3);
}
.dept-btn-outline {
  background: transparent;
  color: #3f4047;
  border: 1.5px solid #3f4047;
  border-radius: 10px;
  padding: 9px 18px;
  font-size: 13px;
  font-weight: 600;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  transition: all 0.25s ease;
  flex: 1;
  justify-content: center;
}
.dept-btn-outline:hover {
  background: #3f4047;
  color: #fff;
  transform: translateY(-2px);
}
</style>
@endpush
