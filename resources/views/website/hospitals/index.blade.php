@extends('layouts.website')

@section('title', 'Hospitals - MEDITRACK')

@section('content')

  <div class="container py-5" id="lab">
    <h1 class="text-center">Hospitals</h1>
    <div class="row row-cols-1 row-cols-md-3 g-4 py-5">

      @foreach($hospitals as $hospital)
      <div class="col">
        <div class="card" style="border-radius: 30px; overflow: hidden;">
          <img src="{{ asset('images/' . $hospital->image) }}" class="card-img-top" alt="{{ $hospital->name }}" style="border-top-left-radius: 20px; border-top-right-radius: 20px;">
          <div class="card-body">
            <h5 class="card-title">{{ $hospital->name }}</h5>
            <p class="card-text">{{ Str::limit($hospital->description, 200) }}</p>
          </div>
          <div class="mb-5 d-flex justify-content-around">
            <a href="{{ route('hospitals.show', $hospital) }}" class="btn btn-primary" style="border-radius: 25px; text-decoration: none; color: white;">READ MORE</a>
          </div>
        </div>
      </div>
      @endforeach

    </div>
  </div>

@endsection
