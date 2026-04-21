@if (session('status'))
  <div class="alert alert-success border-0 shadow-sm mb-4" role="alert">
    {{ session('status') }}
  </div>
@endif

@if ($errors->any())
  <div class="alert alert-danger border-0 shadow-sm mb-4" role="alert">
    <p class="fw-semibold mb-2">Please review the highlighted information.</p>
    <ul class="mb-0 ps-3">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif
