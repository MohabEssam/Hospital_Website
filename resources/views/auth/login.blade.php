@extends('layouts.auth')

@section('content')
  <form action="{{ route('login.store') }}" method="POST">
    @csrf

    <div class="mb-3">
      <label for="email" class="form-label">Username</label>
      <input
        type="email"
        class="form-control @error('email') is-invalid @enderror"
        id="email"
        name="email"
        value="{{ old('email') }}"
        required
        autocomplete="username"
        placeholder="name@example.com">
      @error('email')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <div class="mb-4">
      <label for="password" class="form-label">Password</label>
      <input
        type="password"
        class="form-control @error('password') is-invalid @enderror"
        id="password"
        name="password"
        required
        autocomplete="current-password">
      @error('password')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <div class="d-flex align-items-center justify-content-between mb-4">
      <div class="form-check">
        <input class="form-check-input primary" type="checkbox" value="1" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
        <label class="form-check-label text-dark" for="remember">
          Remember this Device
        </label>
      </div>
      <a class="text-primary fw-bold" href="{{ route('register') }}">Create Account</a>
    </div>

    <button type="submit" class="btn btn-primary w-100 py-8 fs-4 mb-4 rounded-2">
      Sign In
    </button>

    <div class="d-flex align-items-center justify-content-center">
      <p class="fs-4 mb-0 fw-bold">New to MediCare?</p>
      <a class="text-primary fw-bold ms-2" href="{{ route('register') }}">Create an account</a>
    </div>
  </form>
@endsection
