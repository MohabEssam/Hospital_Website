@extends('layouts.website')

@section('title', 'Pharmacies - MEDITRACK')

@push('styles')
<style>
  nav#navmenu ul li a {
    text-decoration: none !important;
    color: inherit;
  }
  .card {
    border-radius: 15px;
    overflow: hidden;
  }
  .btn-primary a {
    color: white;
    text-decoration: none;
    display: block;
    width: 100%;
  }
</style>
@endpush

@section('content')

  <div class="container py-5" id="lab">
    <h1 class="text-center">Pharmacies</h1>
    <div class="row row-cols-1 row-cols-md-3 g-4 py-5">

      @foreach($pharmacies as $pharmacy)
      <div class="col">
        <div class="card" style="border-radius: 30px; overflow: hidden;">
          <img src="{{ asset('images/' . $pharmacy->image) }}" class="card-img-top" alt="{{ $pharmacy->name }}">
          <div class="card-body">
            <h3 class="card-title">{{ $pharmacy->name }}</h3>
            <h6 class="card-text">{{ Str::limit($pharmacy->description, 200) }}</h6>
          </div>
          <div class="mb-5 d-flex justify-content-around">
            <a href="{{ route('pharmacies.show', $pharmacy) }}" class="btn btn-primary" style="border-radius: 30px; text-decoration: none; color: white;">READ MORE</a>
          </div>
        </div>
      </div>
      @endforeach

    </div>
  </div>

@endsection
