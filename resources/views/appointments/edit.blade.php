@extends('layouts.app')

@section('content')
  <div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('appointments.show', $appointment) }}" class="btn btn-outline-secondary btn-sm rounded-circle p-2 lh-1">
      <i class="fas fa-arrow-left"></i>
    </a>
    <div>
      <p class="mb-0 text-muted small">Reschedule or update booking</p>
      <h4 class="fw-bold mb-0">Edit Appointment</h4>
    </div>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <form action="{{ route('appointments.update', $appointment) }}" method="POST">
        @csrf
        @method('PUT')
        @include('appointments._form')

        <div class="d-flex justify-content-end gap-2 mt-4">
          <a href="{{ route('appointments.show', $appointment) }}" class="btn btn-outline-secondary">Cancel</a>
          <button type="submit" class="btn btn-dark">Update Appointment</button>
        </div>
      </form>

      <form action="{{ route('appointments.destroy', $appointment) }}" method="POST" class="mt-3">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Delete this appointment?')">Delete Appointment</button>
      </form>
    </div>
  </div>
@endsection
