@extends('layouts.app')

@section('content')
  <div class="d-flex align-items-center justify-content-between mb-4">
    <div>
      <p class="mb-0 text-muted small">Search by Patient ID</p>
      <h4 class="fw-bold mb-0">Scan Center</h4>
      <p class="mb-0 text-muted small">Staff ID: {{ auth()->user()->public_id }}</p>
    </div>
  </div>

  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
      <form method="GET" action="{{ route('scan-center.dashboard') }}" class="row g-2 align-items-end">
        <div class="col-md-8">
          <label class="form-label small fw-semibold">Patient ID</label>
          <input type="text" name="patient_code" value="{{ $patientCode }}" class="form-control" placeholder="PAT-1" required>
        </div>
        <div class="col-md-4">
          <button type="submit" class="btn btn-dark w-100">Find Scan Requests</button>
        </div>
      </form>
    </div>
  </div>

  @if($patientCode && ! $patient)
    <div class="alert alert-warning">No patient found with ID {{ $patientCode }}.</div>
  @endif

  @if($patient)
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body d-flex flex-wrap justify-content-between gap-2">
        <div>
          <p class="text-muted small mb-1">Patient</p>
          <h5 class="fw-bold mb-0">{{ $patient->name }}</h5>
        </div>
        <span class="badge bg-dark align-self-center">{{ $patient->patient_code }}</span>
      </div>
    </div>

    <div class="row g-3">
      @forelse($scanRequests as $scanRequest)
        <div class="col-lg-6">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between gap-3 mb-2">
                <div>
                  <h6 class="fw-bold mb-1">{{ $scanRequest->scan_type }}{{ $scanRequest->body_area ? ' - '.$scanRequest->body_area : '' }}</h6>
                  <p class="text-muted small mb-0">
                    {{ $scanRequest->requested_at->format('d M Y, h:i A') }}
                    · {{ $scanRequest->doctor?->name ?? 'Doctor not recorded' }}
                  </p>
                </div>
                <span class="badge {{ $scanRequest->status === 'completed' ? 'bg-success' : 'bg-warning text-dark' }} align-self-start">
                  {{ ucfirst($scanRequest->status) }}
                </span>
              </div>

              @if($scanRequest->instructions)
                <p class="small text-secondary">{{ $scanRequest->instructions }}</p>
              @endif

              @if($scanRequest->result)
                <div class="alert alert-success small py-2">
                  Result saved {{ $scanRequest->result->resulted_at?->format('d M Y, h:i A') }}.
                </div>
              @endif

              <form action="{{ route('scan-center.results.store', $scanRequest) }}" method="POST" enctype="multipart/form-data" class="d-grid gap-2">
                @csrf
                <textarea name="findings" rows="3" class="form-control" placeholder="Findings">{{ old('findings', $scanRequest->result?->findings) }}</textarea>
                <textarea name="impression" rows="2" class="form-control" placeholder="Impression">{{ old('impression', $scanRequest->result?->impression) }}</textarea>
                <input type="file" name="images[]" class="form-control" multiple accept=".jpg,.jpeg,.png,.webp,.pdf">
                <select name="status" class="form-select">
                  <option value="final" @selected(old('status', $scanRequest->result?->status ?? 'final') === 'final')>Final</option>
                  <option value="preliminary" @selected(old('status', $scanRequest->result?->status) === 'preliminary')>Preliminary</option>
                </select>
                <button class="btn btn-dark" type="submit">Save Result</button>
              </form>
            </div>
          </div>
        </div>
      @empty
        <div class="col-12">
          <div class="card border-0 shadow-sm"><div class="card-body text-center text-muted">No scan requests found for this patient.</div></div>
        </div>
      @endforelse
    </div>
  @endif
@endsection
