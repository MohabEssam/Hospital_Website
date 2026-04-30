@extends('layouts.website')

@section('title', 'Departments - Medicare Hospital')

@section('content')

  {{-- Section Header --}}
  <section class="dept-section-header">
    <div class="container position-relative" style="z-index:2;">
      <div class="text-center" data-aos="fade-up">
        <h2 class="dept-section-title">Our Departments</h2>
        <p class="dept-section-subtitle">Comprehensive medical specialties for your care</p>
        <div class="dept-section-divider"></div>
      </div>
    </div>
  </section>

  <section class="dept-section-content">
    <div class="container">
      <div class="row g-4">
        @forelse($departments as $index => $department)
        <div class="col-lg-4 col-md-6 col-12" data-aos="fade-up" data-aos-delay="{{ ($index % 3) * 100 }}">
          <div class="dept-card">
            <a href="{{ route('website.departments.show', $department) }}" class="dept-card-link">
              <div class="dept-card-image">
                @if($department->hero_image)
                  <img src="{{ asset('storage/' . $department->hero_image) }}" alt="{{ $department->name }}">
                @else
                  <img src="{{ asset('website-assets/img/departments/default.jpg') }}" alt="{{ $department->name }}">
                @endif
                <div class="dept-card-overlay"></div>
                <div class="dept-card-icon">
                  @if($department->icon)
                    <img src="{{ asset('storage/' . $department->icon) }}" alt="">
                  @else
                    <i class="bi bi-hospital"></i>
                  @endif
                </div>
              </div>
              <div class="dept-card-body">
                <h5 class="dept-card-title">{{ $department->name }}</h5>
                <p class="dept-card-desc">{{ Str::limit($department->description, 110) }}</p>
                <span class="dept-card-cta">
                  Explore Department <i class="bi bi-arrow-right"></i>
                </span>
              </div>
            </a>
          </div>
        </div>
        @empty
        <div class="col-12 text-center py-5" data-aos="fade-up">
          <div class="dept-empty-state">
            <i class="bi bi-building"></i>
            <p>No departments available at the moment.</p>
          </div>
        </div>
        @endforelse
      </div>
    </div>
  </section>

@endsection

@push('styles')
<style>
/* ── Departments Section ───────────────────────────────────── */
.dept-section-header {
  background: #fff;
  padding: 64px 0 32px;
  position: relative;
}
.dept-section-title {
  font-family: 'Poppins', sans-serif;
  font-size: 36px;
  font-weight: 700;
  color: #3f4047;
  margin-bottom: 8px;
  letter-spacing: -0.5px;
}
.dept-section-subtitle {
  font-family: 'Poppins', sans-serif;
  font-size: 16px;
  color: #6b7280;
  margin-bottom: 20px;
}
.dept-section-divider {
  width: 60px;
  height: 3px;
  background: #3f4047;
  border-radius: 2px;
  margin: 0 auto;
}

.dept-section-content {
  padding: 24px 0 80px;
  background: #f8f9fa;
}

/* ── Department Card ─────────────────────────────────────── */
.dept-card {
  background: #fff;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 2px 12px rgba(0,0,0,0.04);
  transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
  height: 100%;
}
.dept-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 40px rgba(63, 64, 71, 0.12);
}
.dept-card-link {
  text-decoration: none;
  display: block;
  height: 100%;
  color: inherit;
}
.dept-card-link:hover {
  color: inherit;
}

/* Card Image */
.dept-card-image {
  position: relative;
  overflow: hidden;
  height: 200px;
}
.dept-card-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  transition: transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}
.dept-card:hover .dept-card-image img {
  transform: scale(1.08);
}

/* Gradient Overlay */
.dept-card-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to top, rgba(63, 64, 71, 0.75) 0%, rgba(63, 64, 71, 0.2) 40%, transparent 100%);
  opacity: 0.7;
  transition: opacity 0.4s ease;
}
.dept-card:hover .dept-card-overlay {
  opacity: 0.9;
}

/* Icon Badge on Image */
.dept-card-icon {
  position: absolute;
  bottom: 16px;
  left: 20px;
  width: 48px;
  height: 48px;
  background: rgba(255,255,255,0.95);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  transition: all 0.35s ease;
}
.dept-card-icon img {
  width: 24px;
  height: 24px;
  object-fit: contain;
  transition: none;
}
.dept-card-icon i {
  font-size: 22px;
  color: #3f4047;
}
.dept-card:hover .dept-card-icon {
  background: #3f4047;
  transform: translateY(-4px);
}
.dept-card:hover .dept-card-icon i {
  color: #fff;
}

/* Card Body */
.dept-card-body {
  padding: 24px;
  display: flex;
  flex-direction: column;
}

.dept-card-title {
  font-family: 'Poppins', sans-serif;
  font-weight: 700;
  font-size: 18px;
  color: #1f2937;
  margin-bottom: 10px;
  line-height: 1.3;
}

.dept-card-desc {
  color: #6b7280;
  font-size: 14px;
  line-height: 1.65;
  margin-bottom: 20px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  flex: 1;
}

/* CTA Button */
.dept-card-cta {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-family: 'Poppins', sans-serif;
  font-size: 13px;
  font-weight: 600;
  color: #3f4047;
  background: #f3f4f6;
  padding: 10px 20px;
  border-radius: 10px;
  transition: all 0.35s ease;
  align-self: flex-start;
}
.dept-card-cta i {
  transition: transform 0.35s ease;
  font-size: 14px;
}
.dept-card:hover .dept-card-cta {
  background: #3f4047;
  color: #fff;
  gap: 12px;
}
.dept-card:hover .dept-card-cta i {
  transform: translateX(4px);
}

/* Empty State */
.dept-empty-state {
  padding: 40px 20px;
}
.dept-empty-state i {
  font-size: 64px;
  color: #d1d5db;
  margin-bottom: 16px;
  display: block;
}
.dept-empty-state p {
  color: #9ca3af;
  font-size: 16px;
  margin: 0;
}

/* Responsive */
@media (max-width: 767px) {
  .dept-section-title {
    font-size: 28px;
  }
  .dept-card-image {
    height: 180px;
  }
  .dept-card-cta {
    width: 100%;
    justify-content: center;
  }
}

@media (min-width: 992px) and (max-width: 1199px) {
  .dept-card-title {
    font-size: 16px;
  }
}
</style>
@endpush
