@extends('layouts.website')

@section('title', $pharmacy->name . ' - MEDITRACK')

@section('content')

  <!-- Pharmacy Info Section -->
  <section id="about" class="about section">
    <div class="container">
      <div class="row gy-4 gx-5">
        <div class="col-lg-6 position-relative align-self-start reveal" style="transition-delay: 200ms">
          <img src="{{ asset('images/' . $pharmacy->image) }}" class="img-fluid" alt="{{ $pharmacy->name }}" width="450">
        </div>
        <div class="col-lg-6 content reveal" style="transition-delay: 100ms">
          <h3>{{ $pharmacy->name }}</h3>
          <h6>{{ $pharmacy->description }}</h6>
          <ul>
            @if($pharmacy->email)
            <li>
              <i class="bi bi-envelope-at-fill"></i>
              <div>
                <h5>EMAIL</h5>
                <p>{{ $pharmacy->email }}</p>
              </div>
            </li>
            @endif
            @if($pharmacy->phone)
            <li>
              <i class="bi bi-telephone-forward-fill"></i>
              <div>
                <h5>Phone</h5>
                <p>{{ $pharmacy->phone }}</p>
              </div>
            </li>
            @endif
            @if($pharmacy->address)
            <li>
              <i class="bi bi-pin-map-fill"></i>
              <div>
                <h5>ADDRESS</h5>
                <p>{{ $pharmacy->address }}</p>
              </div>
            </li>
            @endif
          </ul>
        </div>
      </div>
    </div>

    <!-- Products Coming Soon -->
    <section id="gallery" class="gallery section">
      <div class="container section-title reveal">
        <h2>Medicine <i class="fa-solid fa-capsules" style="margin-left: 5px; color: #1977cc;"></i></h2>
        <p style="text-align: center; font-size: 48px; color: #1977cc;">COMING SOON <i class="fa-solid fa-face-smile-beam" style="margin-left: 5px; color: #1977cc;"></i></p>
      </div>
    </section>
  </section>

@endsection
