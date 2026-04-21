@extends('layouts.app')

@section('content')
  <div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('patients.show', $patient) }}" class="btn btn-outline-secondary btn-sm rounded-circle p-2 lh-1">
      <i class="fas fa-arrow-left"></i>
    </a>
    <div>
      <p class="mb-0 text-muted small">Update patient record</p>
      <h4 class="fw-bold mb-0">Edit Patient</h4>
    </div>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <form action="{{ route('patients.update', $patient) }}" method="POST">
        @csrf
        @method('PUT')
        @include('patients._form')

        <div class="d-flex justify-content-end gap-2 mt-4">
          <a href="{{ route('patients.show', $patient) }}" class="btn btn-outline-secondary">Cancel</a>
          <button type="submit" class="btn btn-dark">Update Patient</button>
        </div>
      </form>

      <form action="{{ route('patients.destroy', $patient) }}" method="POST" class="mt-3">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Delete this patient?')">Delete Patient</button>
      </form>
    </div>
  </div>
@endsection
