@extends('layouts.app')

@section('content')
  <div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('doctors.show', $doctor) }}" class="btn btn-outline-secondary btn-sm rounded-circle p-2 lh-1">
      <i class="fas fa-arrow-left"></i>
    </a>
    <div>
      <p class="mb-0 text-muted small">Update doctor profile</p>
      <h4 class="fw-bold mb-0">Edit Doctor</h4>
    </div>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <form action="{{ route('doctors.update', $doctor) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('doctors._form')

        <div class="d-flex justify-content-between gap-2 mt-4">
          <a href="{{ route('doctors.show', $doctor) }}" class="btn btn-outline-secondary">Cancel</a>
          <button type="submit" class="btn btn-dark">Update Doctor</button>
        </div>
      </form>

      <form action="{{ route('doctors.destroy', $doctor) }}" method="POST" class="mt-3">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Delete this doctor?')">Delete Doctor</button>
      </form>
    </div>
  </div>
@endsection
