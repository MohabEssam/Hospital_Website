@extends('layouts.website')

@section('title', $hospital->name . ' - MEDITRACK')

@section('content')

  <!-- Hospital Info Section -->
  <section id="about" class="about section">
    <div class="container">
      <div class="row gy-4 gx-5">
        <div class="col-lg-6 position-relative align-self-start reveal" style="transition-delay: 200ms">
          <img src="{{ asset('images/' . $hospital->image) }}" class="img-fluid" alt="{{ $hospital->name }}" width="500">
        </div>
        <div class="col-lg-6 content reveal" style="transition-delay: 100ms">
          <h3>{{ $hospital->name }}</h3>
          <p>{{ $hospital->description }}</p>
          <ul>
            @if($hospital->email)
            <li>
              <i class="bi bi-envelope-at-fill"></i>
              <div>
                <h5>EMAIL</h5>
                <p>{{ $hospital->email }}</p>
              </div>
            </li>
            @endif
            @if($hospital->phone)
            <li>
              <i class="bi bi-telephone-forward-fill"></i>
              <div>
                <h5>Phone</h5>
                <p>{{ $hospital->phone }}</p>
              </div>
            </li>
            @endif
            @if($hospital->address)
            <li>
              <i class="bi bi-pin-map-fill"></i>
              <div>
                <h5>ADDRESS</h5>
                <p>{{ $hospital->address }}</p>
              </div>
            </li>
            @endif
          </ul>
        </div>
      </div>
    </div>
  </section>

  <!-- Doctors Section -->
  @if($hospital->doctors && count($hospital->doctors) > 0)
  <div class="container py-5" id="lab">
    <h1 class="text-center">DOCTORS <i class="fa-solid fa-user-doctor" style="margin-left: 5px; color: #1977cc;"></i></h1>
    <div class="row row-cols-1 row-cols-md-3 g-4 py-5">
      @foreach($hospital->doctors as $doctor)
      <div class="col">
        <div class="card">
          <div class="card-body">
            <h3 class="card-title">{{ $doctor['name'] }}</h3>
            <h6 class="card-title">Department: {{ $doctor['department'] }}</h6>
            <h6 class="card-title">Days: {{ $doctor['days'] }}</h6>
            <h6 class="card-title">Work hours: {{ $doctor['hours'] }}</h6>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
  @endif

  <!-- Booking Form -->
  @auth
    @if(auth()->user()->isPatient())
    <section class="book_section layout_padding">
      <div class="container">
        <div class="row">
          <div class="col-md-8 mx-auto">
            <form action="{{ route('bookings.hospital.store', $hospital) }}" method="POST">
              @csrf
              <h1 class="text-center">Booking <i class="fa-solid fa-address-card" style="margin-left: 5px; color: #1977cc;"></i></h1>

              @if($errors->any())
                <div class="alert alert-danger">
                  <ul class="mb-0">
                    @foreach($errors->all() as $error)
                      <li>{{ $error }}</li>
                    @endforeach
                  </ul>
                </div>
              @endif

              <div class="form-group mb-3">
                <label>Patient Name</label>
                <input type="text" name="name" class="form-control" value="{{ auth()->user()->name }}" readonly>
              </div>

              @if($hospital->doctors && count($hospital->doctors) > 0)
              <div class="form-group mb-3">
                <label>Doctor's Name</label>
                <select name="doctor_name" class="form-control" required>
                  @foreach($hospital->doctors as $doctor)
                    <option value="{{ $doctor['name'] }}">{{ $doctor['name'] }} - {{ $doctor['department'] }}</option>
                  @endforeach
                </select>
              </div>
              @endif

              <div class="form-group mb-3">
                <label>Phone Number</label>
                <input type="text" name="phone" class="form-control" maxlength="11" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
              </div>

              <div class="form-group mb-3">
                <label>Choose Date</label>
                <input type="date" name="appointment_date" class="form-control" min="{{ date('Y-m-d') }}" required>
              </div>

              <div class="text-center">
                <button type="submit" class="btn btn-primary" style="margin-top: 10px; width: 100px; padding: 8px 0; font-size: 16px; border-radius: 25px;">BOOK</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>
    @endif
  @else
    <div class="container text-center py-5">
      <p>Please <a href="{{ route('login') }}">login</a> to book an appointment.</p>
    </div>
  @endauth

@endsection
