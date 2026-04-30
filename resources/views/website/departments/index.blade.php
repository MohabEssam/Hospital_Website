@extends('layouts.website')

@section('title', 'Departments - Medicare Hospital')

@section('content')

  {{-- Page Header --}}
  <section class="dept-index-hero">
    <div class="container position-relative" style="z-index:2;">
      <h2 data-aos="fade-right">Our Departments</h2>
      <p data-aos="fade-right" data-aos-delay="100">Explore our specialized medical departments staffed by experienced professionals</p>
    </div>
  </section>

  <section class="dept-index-content">
    <div class="container" data-aos="fade-up" data-aos-delay="100">
      <div class="row gy-4">
        @forelse($departments as $index => $department)
        <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ ($index % 3) * 100 }}">
          <div class="dept-idx-card">
            @if($department->hero_image)
              <div class="dept-idx-img-wrap">
                <img src="{{ asset('storage/' . $department->hero_image) }}" alt="{{ $department->name }}">
              </div>
            @endif
            <div class="dept-idx-body">
              <div class="d-flex align-items-center gap-3 mb-3">
                <div class="dept-idx-icon">
                  @if($department->icon)
                    <img src="{{ asset('storage/' . $department->icon) }}" alt="" style="width:24px;height:24px;">
                  @else
                    <i class="bi bi-hospital"></i>
                  @endif
                </div>
                <h5 class="dept-idx-title">{{ $department->name }}</h5>
              </div>
              <p class="dept-idx-desc">{{ Str::limit($department->description, 120) }}</p>

              @if($department->services)
                <div class="mb-3">
                  @foreach(array_slice($department->services, 0, 3) as $service)
                    <span class="dept-idx-badge">{{ $service }}</span>
                  @endforeach
                  @if(count($department->services) > 3)
                    <span class="dept-idx-badge" style="background:#f1f5f9;color:#94a3b8;">+{{ count($department->services) - 3 }} more</span>
                  @endif
                </div>
              @endif

              <div class="dept-idx-footer">
                <span class="dept-idx-count">
                  <i class="bi bi-people-fill"></i> {{ $department->doctors_count }} Doctor{{ $department->doctors_count !== 1 ? 's' : '' }}
                </span>
                <a href="{{ route('website.departments.show', $department) }}" class="dept-idx-link">
                  View Details <i class="bi bi-arrow-right"></i>
                </a>
              </div>
            </div>
          </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
          <i class="bi bi-building" style="font-size:56px;color:#cbd5e1;"></i>
          <p class="mt-3" style="color:#94a3b8;font-size:16px;">No departments available at the moment.</p>
        </div>
        @endforelse
      </div>
    </div>
  </section>

@endsection

@push('styles')
<style>
/* ── Departments Index ──────────────────────────────────────── */
.dept-index-hero {
  background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 50%, #084298 100%);
  padding: 48px 0 40px;
  position: relative;
  overflow: hidden;
}
.dept-index-hero::before {
  content: '';
  position: absolute;
  top: -50%;
  right: -15%;
  width: 420px;
  height: 420px;
  background: rgba(255,255,255,0.04);
  border-radius: 50%;
}
.dept-index-hero h2 {
  font-family: 'Poppins', sans-serif;
  font-size: 36px;
  font-weight: 700;
  color: #fff;
  margin-bottom: 8px;
}
.dept-index-hero p {
  color: rgba(255,255,255,0.8);
  font-size: 16px;
  margin: 0;
}

.dept-index-content {
  padding: 48px 0 64px;
  background: #f8f9fa;
}

.dept-idx-card {
  background: #fff;
  border-radius: 16px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 2px 16px rgba(0,0,0,0.05);
  overflow: hidden;
  height: 100%;
  display: flex;
  flex-direction: column;
  transition: all 0.35s ease;
}
.dept-idx-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 12px 32px rgba(13,110,253,0.12);
  border-color: #0d6efd;
}
.dept-idx-img-wrap {
  overflow: hidden;
}
.dept-idx-img-wrap img {
  width: 100%;
  height: 190px;
  object-fit: cover;
  display: block;
  transition: transform 0.4s ease;
}
.dept-idx-card:hover .dept-idx-img-wrap img {
  transform: scale(1.06);
}
.dept-idx-body {
  padding: 20px 24px 24px;
  flex: 1;
  display: flex;
  flex-direction: column;
}
.dept-idx-icon {
  width: 44px;
  height: 44px;
  background: #e7f1ff;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  color: #0d6efd;
  flex-shrink: 0;
  transition: all 0.3s;
}
.dept-idx-card:hover .dept-idx-icon {
  background: #0d6efd;
  color: #fff;
}
.dept-idx-title {
  font-family: 'Poppins', sans-serif;
  font-weight: 600;
  font-size: 17px;
  color: #1e293b;
  margin: 0;
}
.dept-idx-desc {
  color: #64748b;
  font-size: 14px;
  line-height: 1.6;
  margin-bottom: 12px;
}
.dept-idx-badge {
  display: inline-block;
  background: #e7f1ff;
  color: #0d6efd;
  padding: 4px 12px;
  border-radius: 99px;
  font-size: 12px;
  font-weight: 500;
  margin-right: 4px;
  margin-bottom: 4px;
}
.dept-idx-footer {
  margin-top: auto;
  padding-top: 16px;
  border-top: 1px solid #f1f5f9;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.dept-idx-count {
  font-size: 13px;
  color: #64748b;
  font-weight: 500;
}
.dept-idx-count i { color: #0d6efd; margin-right: 4px; }
.dept-idx-link {
  font-size: 13px;
  font-weight: 600;
  color: #0d6efd;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 4px;
  transition: all 0.2s;
}
.dept-idx-link:hover {
  color: #0b5ed7;
  gap: 8px;
}
</style>
@endpush
