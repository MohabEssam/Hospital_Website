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
      <label for="phone" class="form-label">Phone Number</label>
      <input
        type="tel"
        class="form-control @error('phone') is-invalid @enderror"
        id="phone"
        name="phone"
        value="{{ old('phone') }}"
        required
        minlength="7"
        maxlength="20"
        autocomplete="tel"
        placeholder="Phone number">
      @error('phone')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <div class="mb-3">
      <label for="gender" class="form-label">Gender</label>
      <select
        class="form-select @error('gender') is-invalid @enderror"
        id="gender"
        name="gender"
        required>
        <option value="" disabled @selected(! old('gender'))>Select gender</option>
        <option value="male" @selected(old('gender') === 'male')>Male</option>
        <option value="female" @selected(old('gender') === 'female')>Female</option>
      </select>
      @error('gender')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <div class="mb-3">
      <label for="age" class="form-label">Age</label>
      <input
        type="number"
        class="form-control @error('age') is-invalid @enderror"
        id="age"
        name="age"
        value="{{ old('age') }}"
        required
        min="1"
        max="120"
        placeholder="Your age">
      @error('age')
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
