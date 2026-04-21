@extends('layouts.app')

@section('content')
  <div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('appointments.index') }}" class="btn btn-outline-secondary btn-sm rounded-circle p-2 lh-1">
      <i class="fas fa-arrow-left"></i>
    </a>
    <div>
      <p class="mb-0 text-muted small">Back to Appointment List</p>
      <h4 class="fw-bold mb-0">Add Appointment</h4>
    </div>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <form action="{{ route('appointments.store') }}" method="POST">
        @csrf
        @include('appointments._form')

        <div class="d-flex justify-content-end gap-2 mt-4">
          <a href="{{ route('appointments.index') }}" class="btn btn-outline-secondary">Cancel</a>
          <button type="submit" class="btn btn-dark">Save Appointment</button>
        </div>
      </form>
    </div>
  </div>
@endsection
