@extends('layouts.website')

@section('title', $lab->name . ' - MEDITRACK')

@section('content')

  <!-- Lab Info Section -->
  <section id="about" class="about section">
    <div class="container">
      <div class="row gy-4 gx-5">
        <div class="col-lg-6 position-relative align-self-start reveal" style="transition-delay: 200ms">
          <img src="{{ asset('images/' . $lab->image) }}" class="img-fluid" alt="{{ $lab->name }}" width="500">
        </div>
        <div class="col-lg-6 content reveal" style="transition-delay: 100ms">
          <h3>{{ $lab->name }}</h3>
          <p>{{ $lab->description }}</p>
          <ul>
            @if($lab->email)
            <li>
              <i class="bi bi-envelope-at-fill"></i>
              <div>
                <h5>EMAIL</h5>
                <p>{{ $lab->email }}</p>
              </div>
            </li>
            @endif
            @if($lab->phone)
            <li>
              <i class="bi bi-telephone-forward-fill"></i>
              <div>
                <h5>Phone</h5>
                <p>{{ $lab->phone }}</p>
              </div>
            </li>
            @endif
            @if($lab->address)
            <li>
              <i class="bi bi-pin-map-fill"></i>
              <div>
                <h5>ADDRESS</h5>
                <p>{{ $lab->address }}</p>
              </div>
            </li>
            @endif
            @if($lab->work_hours)
            <li>
              <i class="fa-solid fa-clock"></i>
              <div>
                <h5>WORK HOURS</h5>
                <p>{{ $lab->work_hours }}</p>
              </div>
            </li>
            @endif
          </ul>
        </div>
      </div>
    </div>
  </section>

  <!-- X-Rays Section -->
  @if($lab->xrays && count($lab->xrays) > 0)
  <div class="container py-5">
    <h1 class="text-center">X-Rays <i class="fa-solid fa-x-ray" style="margin-left: 5px; color: #1977cc;"></i></h1>
    <div class="row row-cols-1 row-cols-md-2 g-4 py-5">
      @foreach($lab->xrays as $xray)
      <div class="col">
        <div class="card">
          <div class="card-body">
            <h3 class="card-title" style="text-align: center;">{{ $xray['name'] }}</h3>
            <h6 class="card-title">COST: {{ $xray['cost'] }} EGP</h6>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
  @endif

  <!-- Medical Tests Section -->
  @if($lab->medical_tests && count($lab->medical_tests) > 0)
  <div class="container py-5">
    <h1 class="text-center">MEDICAL TEST <i class="fa-solid fa-flask-vial" style="margin-left: 5px; color: #1977cc;"></i></h1>
    <div class="row row-cols-1 row-cols-md-2 g-4 py-5">
      @foreach($lab->medical_tests as $test)
      <div class="col">
        <div class="card">
          <div class="card-body">
            <h3 class="card-title" style="text-align: center;">{{ $test['name'] }}</h3>
            <h6 class="card-title">COST: {{ $test['cost'] }} EGP</h6>
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
            <form action="{{ route('bookings.lab.store', $lab) }}" method="POST">
              @csrf
              <h1 class="text-center">Book at {{ $lab->name }} <i class="fa-solid fa-flask" style="margin-left: 5px; color: #1977cc;"></i></h1>

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
                <input type="text" class="form-control" value="{{ auth()->user()->name }}" readonly>
              </div>

              <div class="form-group mb-3">
                <label>Phone Number</label>
                <input type="text" name="phone" class="form-control" maxlength="11" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
              </div>

              @if($lab->xrays && count($lab->xrays) > 0)
              <div class="form-group mb-3">
                <label>X-Ray (optional)</label>
                <select name="xray" class="form-control">
                  <option value="">-- Select X-Ray --</option>
                  @foreach($lab->xrays as $xray)
                    <option value="{{ $xray['name'] }}">{{ $xray['name'] }} - {{ $xray['cost'] }} EGP</option>
                  @endforeach
                </select>
              </div>
              @endif

              @if($lab->medical_tests && count($lab->medical_tests) > 0)
              <div class="form-group mb-3">
                <label>Medical Test (optional)</label>
                <select name="medical_test" class="form-control">
                  <option value="">-- Select Test --</option>
                  @foreach($lab->medical_tests as $test)
                    <option value="{{ $test['name'] }}">{{ $test['name'] }} - {{ $test['cost'] }} EGP</option>
                  @endforeach
                </select>
              </div>
              @endif

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
