@extends('layouts.website')

@section('title', 'Doctors - Medicare Hospital')

@section('content')

  <section class="doctors section">
    <div class="container section-title reveal">
      <h2>Our Doctors</h2>
      <p>Find the right specialist for your healthcare needs</p>
    </div>

    <div class="container reveal" style="transition-delay: 100ms">
      {{-- Filter by department --}}
      <div class="doctor-tools doctor-tools-wide">
        <label class="doctor-search">
          <i class="bi bi-search" aria-hidden="true"></i>
          <span class="visually-hidden">Search doctors</span>
          <input type="search" data-doctor-search placeholder="Search by doctor or specialty">
        </label>

        <form method="GET" action="{{ route('website.doctors') }}" class="doctor-filter-form">
          <select name="department_id" data-doctor-department aria-label="Filter doctors by department" onchange="this.form.submit()">
            <option value="">All departments</option>
            @foreach($departments as $department)
              <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                {{ $department->name }}
              </option>
            @endforeach
          </select>
        </form>

        <select data-doctor-sort aria-label="Sort doctors">
          <option value="">Featured first</option>
          <option value="rating">Highest rating</option>
          <option value="experience">Most experience</option>
        </select>
      </div>

      <div class="doctor-card-skeletons" data-doctor-skeletons aria-hidden="true">
        <div></div><div></div><div></div>
      </div>

      <div class="row g-4 doctor-card-grid" data-doctor-grid>
        @forelse($doctors as $doctor)
        <div class="col-md-6 col-xl-4 doctor-card-col reveal" style="transition-delay: {{ 80 + ($loop->index % 3 * 80) }}ms">
          @include('website.doctors._card', ['doctor' => $doctor, 'featured' => $loop->first && !request('department_id')])
        </div>
        @empty
        <div class="col-12 text-center py-5">
          <p class="text-muted">No doctors found for the selected filter.</p>
        </div>
        @endforelse
      </div>

      <div class="doctor-empty-state" data-doctor-empty hidden>
        <i class="bi bi-search-heart" aria-hidden="true"></i>
        <p>No doctors match your search right now.</p>
      </div>

      @if($doctors instanceof \Illuminate\Pagination\LengthAwarePaginator && $doctors->hasPages())
      <div class="mt-4">
        {{ $doctors->withQueryString()->links('vendor.pagination.medicare') }}
      </div>
      @endif
    </div>
  </section>

@endsection
