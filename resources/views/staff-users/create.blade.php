@extends('layouts.app')

@section('content')
  <div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('staff-users.index') }}" class="btn btn-outline-secondary btn-sm rounded-circle p-2 lh-1">
      <i class="fas fa-arrow-left"></i>
    </a>
    <div>
      <p class="mb-0 text-muted small">Create admin, lab, pharmacy, or scan center access</p>
      <h4 class="fw-bold mb-0">Create Staff User</h4>
    </div>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <form action="{{ route('staff-users.store') }}" method="POST" class="row g-3">
        @csrf
        <div class="col-md-6">
          <label class="form-label small fw-semibold">Name</label>
          <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>
          @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label small fw-semibold">Email</label>
          <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required>
          @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label small fw-semibold">Role</label>
          <select name="role" class="form-select @error('role') is-invalid @enderror" required>
            @foreach($roles as $value => $label)
              <option value="{{ $value }}" @selected(old('role') === $value)>{{ $label }}</option>
            @endforeach
          </select>
          @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label small fw-semibold">Password</label>
          <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
          @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label small fw-semibold">Confirm Password</label>
          <input type="password" name="password_confirmation" class="form-control" required>
        </div>
        <div class="col-12 d-flex justify-content-end gap-2">
          <a href="{{ route('staff-users.index') }}" class="btn btn-outline-secondary">Cancel</a>
          <button type="submit" class="btn btn-dark">Create User</button>
        </div>
      </form>
    </div>
  </div>
@endsection
