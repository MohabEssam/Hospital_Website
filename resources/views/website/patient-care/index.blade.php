@extends('layouts.website')

@section('title', 'Patient Care Services - Medicare Hospital')

@section('content')

  {{-- Section Header --}}
  <section class="pc-section-header">
    <div class="container position-relative" style="z-index:2;">
      <div class="text-center">
        <h2 class="pc-section-title">Patient Care</h2>
        <p class="pc-section-subtitle">Comprehensive facilities and services designed for your comfort, safety, and recovery</p>
        <div class="pc-section-divider"></div>
      </div>
    </div>
  </section>

  <section class="pc-section-content">
    <div class="container">
      <div class="row g-4">
        @forelse($services as $index => $service)
        <div class="col-lg-4 col-md-6 col-12">
          <div class="pc-card">
            <a href="{{ route('website.patient-care.show', $service) }}" class="pc-card-link">
              <div class="pc-card-image">
                @if($service->image)
                  <img src="{{ asset($service->image) }}" alt="{{ $service->name }}">
                @else
                  <div class="pc-card-placeholder">
                    <i class="{{ $service->icon_class ?? 'bi bi-heart-pulse' }}"></i>
                  </div>
                @endif
                <div class="pc-card-overlay"></div>
                @if($service->is_bookable)
                  <span class="pc-card-badge-bookable">
                    <i class="bi bi-calendar-check"></i> Bookable
                  </span>
                @endif
              </div>
              <div class="pc-card-body">
                <div class="pc-card-icon-wrap">
                  <i class="{{ $service->icon_class ?? 'bi bi-heart-pulse' }}"></i>
                </div>
                <h5 class="pc-card-title">{{ $service->name }}</h5>
                <p class="pc-card-desc">{{ Str::limit($service->description, 110) }}</p>
                <span class="pc-card-cta">
                  Read More <i class="bi bi-arrow-right"></i>
                </span>
              </div>
            </a>
          </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
          <div class="pc-empty-state">
            <i class="bi bi-hospital"></i>
            <p>No patient care services available at the moment.</p>
          </div>
        </div>
        @endforelse
      </div>
    </div>
  </section>

@endsection

@push('styles')
<style>
/* ── Patient Care Index ────────────────────────────────────── */
.pc-section-header {
  background: #fff;
  padding: 64px 0 32px;
  position: relative;
}
.pc-section-title {
  font-family: 'Poppins', sans-serif;
  font-size: 36px;
  font-weight: 700;
  color: #3f4047;
  margin-bottom: 8px;
  letter-spacing: -0.5px;
}
.pc-section-subtitle {
  font-family: 'Poppins', sans-serif;
  font-size: 16px;
  color: #6b7280;
  margin-bottom: 20px;
  max-width: 560px;
  margin-left: auto;
  margin-right: auto;
}
.pc-section-divider {
  width: 60px;
  height: 3px;
  background: #3f4047;
  border-radius: 2px;
  margin: 0 auto;
}
.pc-section-content {
  padding: 24px 0 80px;
  background: #f8f9fa;
}

/* ── Card ──────────────────────────────────────────────────── */
.pc-card {
  background: #fff;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 2px 12px rgba(0,0,0,0.04);
  transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
  height: 100%;
}
.pc-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 40px rgba(63, 64, 71, 0.12);
}
.pc-card-link {
  text-decoration: none;
  display: block;
  height: 100%;
  color: inherit;
}
.pc-card-link:hover { color: inherit; }

/* Card Image */
.pc-card-image {
  position: relative;
  overflow: hidden;
  height: 200px;
}
.pc-card-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  transition: transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}
.pc-card:hover .pc-card-image img {
  transform: scale(1.08);
}
.pc-card-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #3f4047 0%, #2d2e33 50%, #1a1b1f 100%);
}
.pc-card-placeholder i {
  font-size: 52px;
  color: rgba(255,255,255,0.25);
  transition: transform 0.4s ease, color 0.4s ease;
}
.pc-card:hover .pc-card-placeholder i {
  transform: scale(1.15);
  color: rgba(255,255,255,0.45);
}

/* Overlay */
.pc-card-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to top, rgba(63, 64, 71, 0.6) 0%, transparent 60%);
  opacity: 0.5;
  transition: opacity 0.4s ease;
}
.pc-card:hover .pc-card-overlay {
  opacity: 0.8;
}

/* Bookable badge */
.pc-card-badge-bookable {
  position: absolute;
  top: 14px;
  right: 14px;
  background: rgba(16, 185, 129, 0.92);
  color: #fff;
  padding: 5px 12px;
  border-radius: 99px;
  font-size: 11px;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 5px;
  backdrop-filter: blur(4px);
}

/* Card Body */
.pc-card-body {
  padding: 24px;
  display: flex;
  flex-direction: column;
}
.pc-card-icon-wrap {
  width: 44px;
  height: 44px;
  background: #f3f4f6;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 14px;
  transition: all 0.35s ease;
}
.pc-card-icon-wrap i {
  font-size: 20px;
  color: #3f4047;
  transition: color 0.35s ease;
}
.pc-card:hover .pc-card-icon-wrap {
  background: #3f4047;
}
.pc-card:hover .pc-card-icon-wrap i {
  color: #fff;
}
.pc-card-title {
  font-family: 'Poppins', sans-serif;
  font-weight: 700;
  font-size: 18px;
  color: #1f2937;
  margin-bottom: 10px;
  line-height: 1.3;
}
.pc-card-desc {
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
.pc-card-cta {
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
.pc-card-cta i { transition: transform 0.35s ease; font-size: 14px; }
.pc-card:hover .pc-card-cta {
  background: #3f4047;
  color: #fff;
  gap: 12px;
}
.pc-card:hover .pc-card-cta i {
  transform: translateX(4px);
}

/* Empty state */
.pc-empty-state { padding: 40px 20px; }
.pc-empty-state i { font-size: 64px; color: #d1d5db; margin-bottom: 16px; display: block; }
.pc-empty-state p { color: #9ca3af; font-size: 16px; margin: 0; }

/* Responsive */
@media (max-width: 767px) {
  .pc-section-title { font-size: 28px; }
  .pc-card-image { height: 180px; }
  .pc-card-cta { width: 100%; justify-content: center; }
}
</style>
@endpush
