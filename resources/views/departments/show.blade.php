@extends('layouts.app')

@section('content')
  <div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('departments.index') }}" class="btn btn-outline-secondary btn-sm rounded-circle p-2 lh-1">
      <i class="fas fa-arrow-left"></i>
    </a>
    <div>
      <p class="mb-0 text-muted small">Back to Department List</p>
      <h4 class="fw-bold mb-0">{{ $department->name }}</h4>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-lg-8">
      <h2 class="fw-bold mb-2">{{ $department->name }}</h2>
      <p class="text-muted mb-4">{{ $department->description }}</p>

      <div class="mb-4">
        <img src="{{ $department->hero_image ? asset($department->hero_image) : asset('assets/images/Department/cardiology-edit.jpg') }}" alt="{{ $department->name }}" class="dept-hero">
      </div>

      <h6 class="fw-bold text-uppercase text-muted mb-3" style="letter-spacing:.05em;font-size:.75rem;">Services &amp; Treatments</h6>
      <ul class="list-unstyled mb-5">
        @forelse ($department->services ?? [] as $service)
          <li class="d-flex align-items-center gap-3 py-2 border-bottom">
            <span class="treatment-dot"></span>
            <span class="small">{{ $service }}</span>
          </li>
        @empty
          <li class="small text-muted">No services defined for this department yet.</li>
        @endforelse
      </ul>

      <h6 class="fw-bold text-uppercase text-muted mb-3" style="letter-spacing:.05em;font-size:.75rem;">Our Doctors</h6>
      <div class="row g-3">
        @forelse ($department->doctors as $doctor)
          <div class="col-6 col-sm-4 col-xl-3">
            <div class="card border-0 shadow-sm text-center doctor-card h-100">
              <div class="card-body p-3">
                <span class="rounded-circle bg-primary bg-opacity-25 text-primary fw-bold d-inline-flex align-items-center justify-content-center mb-2 mx-auto"
                  style="width:58px;height:58px;font-size:.85rem;">{{ $doctor->initials() }}</span>
                <p class="fw-bold small mb-0">{{ $doctor->name }}</p>
                <p class="text-muted mb-2" style="font-size:.72rem;">{{ $doctor->specialty }}</p>
                <div class="d-flex justify-content-center gap-2">
                  <a href="{{ route('appointments.create', ['doctor_id' => $doctor->id]) }}" class="text-muted" title="Book"><i class="fas fa-calendar-plus" style="font-size:.75rem;"></i></a>
                  <a href="{{ route('doctors.show', $doctor) }}" class="text-muted" title="Profile"><i class="fas fa-user" style="font-size:.75rem;"></i></a>
                </div>
              </div>
            </div>
          </div>
        @empty
          <p class="text-muted small mb-0">No doctors assigned to this department yet.</p>
        @endforelse
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card border-0 shadow-sm p-3 sticky-top" style="top:80px;">
        <p class="fw-semibold small text-muted text-uppercase mb-2" style="letter-spacing:.05em;">Other Departments</p>
        <ul class="list-unstyled mb-3">
          @foreach ($otherDepartments as $otherDepartment)
            <li class="border-bottom">
              <a href="{{ route('departments.show', $otherDepartment) }}" class="d-flex align-items-center gap-2 py-2 text-decoration-none dept-nav-link">
                <i class="fas fa-arrow-right" style="font-size:.65rem;"></i>
                <span class="small">{{ $otherDepartment->name }}</span>
              </a>
            </li>
          @endforeach
        </ul>

        <div class="mb-3">
          <img src="{{ $department->sidebar_image ? asset($department->sidebar_image) : asset('assets/images/Department/specialist-side-image.jpg') }}" alt="{{ $department->name }}" class="sidebar-banner">
        </div>

        <div class="d-flex align-items-center gap-2 mb-2">
          <i class="fas fa-mobile-alt" style="color:#0dcaf0;font-size:.85rem;"></i>
          <span class="small fw-semibold">{{ $department->contact_phone ?? '+20 2 1234 5678' }}</span>
        </div>
        <div class="d-flex align-items-center gap-2 mb-3">
          <i class="fas fa-envelope" style="color:#0dcaf0;font-size:.85rem;"></i>
          <span class="small">{{ $department->contact_email ?? 'info@medicare-hospital.com' }}</span>
        </div>

        <a href="{{ route('appointments.create') }}" class="btn btn-teal btn-sm fw-semibold w-100 mb-2">
          <i class="fas fa-calendar-check me-1"></i> Book an Appointment
        </a>
        <a href="{{ route('doctors.index') }}" class="btn btn-outline-secondary btn-sm fw-semibold w-100">
          <i class="fas fa-user-md me-1"></i> See Our Doctors
        </a>
      </div>
    </div>
  </div>
@endsection
