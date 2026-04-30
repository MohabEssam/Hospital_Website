@extends('layouts.website')

@section('title', 'Labs - MEDITRACK')

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
  .btn-primary a:hover {
    color: white;
    text-decoration: none;
  }
</style>
@endpush

@section('content')

  <div class="container py-5" id="lab">
    <h1 class="text-center">LABS</h1>
    <div class="row row-cols-1 row-cols-md-3 g-4 py-5">

      @foreach($labs as $lab)
      <div class="col">
        <div class="card" style="border-radius: 30px; overflow: hidden;">
          <img src="{{ asset('images/' . $lab->image) }}" class="card-img-top" alt="{{ $lab->name }}">
          <div class="card-body">
            <h5 class="card-title">{{ $lab->name }}</h5>
            <p class="card-text">{{ Str::limit($lab->description, 200) }}</p>
          </div>
          <div class="mb-5 d-flex justify-content-around">
            <a href="{{ route('labs.show', $lab) }}" class="btn btn-primary" style="border-radius: 25px; text-decoration: none; color: white;">READ MORE</a>
          </div>
        </div>
      </div>
      @endforeach

    </div>
  </div>

@endsection
