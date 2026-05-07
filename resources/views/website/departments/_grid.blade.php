@forelse($departments as $index => $department)
<div class="col-lg-4 col-md-6 col-12 dept-card-col" style="transition-delay: {{ ($index % 3) * 100 }}ms">
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
        <div class="dept-card-meta">
          <span class="dept-card-count"><i class="bi bi-people-fill"></i> {{ $department->doctors_count }} Doctor{{ $department->doctors_count !== 1 ? 's' : '' }}</span>
        </div>
        <p class="dept-card-desc">{{ Str::limit($department->description, 110) }}</p>
        <span class="dept-card-cta">
          Explore Department <i class="bi bi-arrow-right"></i>
        </span>
      </div>
    </a>
  </div>
</div>
@empty
<div class="col-12 text-center py-5">
  <div class="dept-empty-state">
    <i class="bi bi-search"></i>
    <p>No departments match your search.</p>
  </div>
</div>
@endforelse
