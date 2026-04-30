@extends('layouts.app')

@section('content')
  <div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('departments.show', $department) }}" class="btn btn-outline-secondary btn-sm rounded-circle p-2 lh-1">
      <i class="fas fa-arrow-left"></i>
    </a>
    <div>
      <p class="mb-0 text-muted small">Update department details</p>
      <h4 class="fw-bold mb-0">Edit Department</h4>
    </div>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <form action="{{ route('departments.update', $department) }}" method="POST">
        @csrf
        @method('PUT')
        @include('departments._form')

        <div class="d-flex justify-content-between gap-2 mt-4">
          <a href="{{ route('departments.show', $department) }}" class="btn btn-outline-secondary">Cancel</a>
          <button type="submit" class="btn btn-dark">Update Department</button>
        </div>
      </form>

      <form action="{{ route('departments.destroy', $department) }}" method="POST" class="mt-3">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Delete this department?')">Delete Department</button>
      </form>
    </div>
  </div>
@endsection
