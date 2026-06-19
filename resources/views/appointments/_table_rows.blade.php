@forelse ($appointments as $appointment)
  @php
    $statusClasses = [
        'confirmed' => 'background:#d1faf3;color:#0a8c6a;',
        'cancelled' => 'background:#fdecea;color:#c0392b;',
        'completed' => 'background:#d1faf3;color:#0a8c6a;',
    ];
  @endphp
  <tr>
    <td>
      <input type="checkbox" class="form-check-input appointment-row-check" value="{{ $appointment->id }}">
    </td>
    <td class="small fw-medium">{{ $appointment->patient?->name }}</td>
    <td class="text-muted small">{{ $appointment->appointment_date?->format('Y-m-d') }}</td>
    <td class="text-muted small">{{ $appointment->start_time }} - {{ $appointment->end_time }}</td>
    <td class="small">{{ $appointment->doctor?->name }}</td>
    <td class="small">{{ $appointment->department?->name ?? '-' }}</td>
    <td class="small">{{ $appointment->treatment }}</td>
    <td><span class="badge px-3 py-2" style="{{ $statusClasses[$appointment->status] ?? 'background:#e9ecef;color:#495057;' }}">{{ ucfirst($appointment->status) }}</span></td>
    <td>
      <div class="dropdown">
        <button class="btn btn-sm btn-outline-secondary border-0 p-1" data-bs-toggle="dropdown">
          <i class="fas fa-ellipsis-h text-muted" style="font-size:.7rem;"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="font-size:.8rem;">
          <li>
            <a class="dropdown-item" href="{{ route('appointments.show', $appointment) }}">
              <i class="fas fa-eye me-2 text-muted"></i>Open
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="{{ route('appointments.edit', $appointment) }}">
              <i class="fas fa-calendar-alt me-2 text-muted"></i>Reschedule
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="{{ route('doctors.show', $appointment->doctor) }}">
              <i class="fas fa-user-md me-2 text-muted"></i>View Doctor
            </a>
          </li>
          @if(auth()->user()->isAdmin())
          <li><hr class="dropdown-divider"></li>
          <li>
            <form action="{{ route('appointments.destroy', $appointment) }}" method="POST" onsubmit="return confirm('Delete this appointment?')">
              @csrf
              @method('DELETE')
              <button type="submit" class="dropdown-item text-danger">
                <i class="fas fa-trash me-2"></i>Delete
              </button>
            </form>
          </li>
          @endif
        </ul>
      </div>
    </td>
  </tr>
@empty
  <tr>
    <td colspan="9" class="text-center text-muted py-4 small">No appointments found.</td>
  </tr>
@endforelse
