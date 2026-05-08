<div class="card border-0 shadow-sm" data-doctors-results>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th class="text-muted fw-semibold small">Name</th>
            <th class="text-muted fw-semibold small">ID</th>
            <th class="text-muted fw-semibold small">Department</th>
            <th class="text-muted fw-semibold small">Total Patients</th>
            <th class="text-muted fw-semibold small">Today's Appointment</th>
            <th class="text-muted fw-semibold small">Availability</th>
            <th class="text-muted fw-semibold small">Action</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($doctors as $doctor)
            <tr style="cursor:pointer;" onclick="window.location='{{ route('doctors.show', $doctor) }}'">
              <td>
                <div class="d-flex align-items-center gap-2">
                  <span class="rounded-circle bg-primary bg-opacity-25 text-primary fw-bold d-inline-flex align-items-center justify-content-center" style="width:34px;height:34px;font-size:.72rem;">{{ $doctor->initials() }}</span>
                  <span class="fw-medium small">{{ $doctor->name }}</span>
                </div>
              </td>
              <td class="text-muted small">{{ $doctor->doctor_code }}</td>
              <td class="small">{{ $doctor->department?->name ?? '--' }}</td>
              <td class="small">{{ $doctor->patients_count }}</td>
              <td class="small">{{ $doctor->today_appointments_count }}</td>
              <td>
                <span class="badge border {{ $doctor->isAvailable() ? 'border-info text-info' : 'border-danger text-danger' }} bg-transparent px-3 py-2">
                  {{ ucfirst($doctor->availability_status) }}
                </span>
              </td>
              <td>
                <div class="d-flex gap-1" onclick="event.stopPropagation()">
                  <a href="{{ route('doctors.show', $doctor) }}" class="btn btn-sm btn-outline-secondary border-0 p-1" title="View">
                    <i class="fas fa-eye text-muted" style="font-size:.75rem;"></i>
                  </a>
                  @if (auth()->user()->isAdmin())
                    <a href="{{ route('doctors.edit', $doctor) }}" class="btn btn-sm btn-outline-secondary border-0 p-1" title="Edit">
                      <i class="fas fa-edit text-muted" style="font-size:.75rem;"></i>
                    </a>
                    <form action="{{ route('doctors.destroy', $doctor) }}" method="POST" onsubmit="return confirm('Delete this doctor?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-outline-secondary border-0 p-1" title="Delete">
                        <i class="fas fa-trash text-danger" style="font-size:.75rem;"></i>
                      </button>
                    </form>
                  @endif
                  <a href="{{ route('doctors.schedule', $doctor) }}" class="btn btn-sm btn-outline-secondary border-0 p-1" title="Schedule">
                    <i class="fas fa-calendar-day text-muted" style="font-size:.75rem;"></i>
                  </a>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center text-muted py-4 small">No doctors found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @include('partials.pagination', ['paginator' => $doctors])
  </div>
</div>
