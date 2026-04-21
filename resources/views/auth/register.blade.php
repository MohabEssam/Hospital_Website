@extends('layouts.auth')

@section('content')
  <form action="{{ route('register.store') }}" method="POST">
    @csrf

    <div class="mb-3">
      <label for="name" class="form-label">Name</label>
      <input
        type="text"
        class="form-control @error('name') is-invalid @enderror"
        id="name"
        name="name"
        value="{{ old('name') }}"
        required
        autocomplete="name"
        placeholder="Full name">
      @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <div class="mb-3">
      <label for="email" class="form-label">Email Address</label>
      <input
        type="email"
        class="form-control @error('email') is-invalid @enderror"
        id="email"
        name="email"
        value="{{ old('email') }}"
        required
        autocomplete="email"
        placeholder="name@example.com">
      @error('email')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <div class="mb-3">
      <label for="password" class="form-label">Password</label>
      <input
        type="password"
        class="form-control @error('password') is-invalid @enderror"
        id="password"
        name="password"
        required
        autocomplete="new-password">
      @error('password')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <div class="mb-4">
      <label for="password_confirmation" class="form-label">Confirm Password</label>
      <input
        type="password"
        class="form-control @error('password') is-invalid @enderror"
        id="password_confirmation"
        name="password_confirmation"
        required
        autocomplete="new-password">
    </div>

    <button type="submit" class="btn btn-primary w-100 py-8 fs-4 mb-4 rounded-2">
      Sign Up
    </button>

    <div class="d-flex align-items-center justify-content-center">
      <p class="fs-4 mb-0 fw-bold">Already have an Account?</p>
      <a class="text-primary fw-bold ms-2" href="{{ route('login') }}">Sign In</a>
    </div>
  </form>
@endsection
