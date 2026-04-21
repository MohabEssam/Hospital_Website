@extends('layouts.app')

@section('content')
  @php
    $departmentCount = $departments->count();
  @endphp

  <div class="d-flex align-items-center justify-content-between mb-4">
    <h4 class="fw-bold mb-0">Departments</h4>
    @if (auth()->user()->isAdmin())
      <a href="{{ route('departments.create') }}" class="btn btn-dark btn-sm d-flex align-items-center gap-2">
        <i class="fas fa-plus"></i> Add Department
      </a>
    @endif
  </div>

  <div class="bg-white rounded shadow-sm overflow-hidden">
    <div class="row g-0 text-center">
      @forelse ($departments as $department)
        @php
          $isLastInRow = ($loop->iteration % 3) === 0;
          $lastRowStart = $departmentCount - (($departmentCount % 3) ?: 3) + 1;
          $isLastRow = $loop->iteration >= $lastRowStart;
        @endphp
        <div class="col-4 col-md-4 dept-cell {{ $isLastInRow ? '' : 'border-end' }} {{ $isLastRow ? '' : 'border-bottom' }}">
          <a href="{{ route('departments.show', $department) }}">
            <img src="{{ $department->icon_path ? asset($department->icon_path) : asset('assets/images/Department/card.png') }}" alt="{{ $department->name }}">
            <p>{{ $department->name }}</p>
            <span class="text-muted small">{{ $department->doctors_count }} doctors</span>
          </a>
        </div>
      @empty
        <div class="col-12 p-5 text-muted">No departments available.</div>
      @endforelse
    </div>
  </div>
@endsection
