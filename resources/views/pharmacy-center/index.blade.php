@extends('layouts.app')

@section('content')
  <div class="d-flex align-items-center justify-content-between mb-4">
    <div>
      <p class="mb-0 text-muted small">Search by Patient ID</p>
      <h4 class="fw-bold mb-0">Pharmacy</h4>
      <p class="mb-0 text-muted small">Staff ID: {{ auth()->user()->public_id }}</p>
    </div>
  </div>

  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
      <form method="GET" action="{{ route('pharmacy.dashboard') }}" class="row g-2 align-items-end">
        <div class="col-md-8">
          <label class="form-label small fw-semibold">Patient ID</label>
          <input type="text" name="patient_code" value="{{ $patientCode }}" class="form-control" placeholder="PAT-1" required>
        </div>
        <div class="col-md-4">
          <button type="submit" class="btn btn-dark w-100">Find Prescriptions</button>
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

    <div class="card border-0 shadow-sm">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th class="small text-muted fw-semibold">Medication</th>
              <th class="small text-muted fw-semibold">Dose</th>
              <th class="small text-muted fw-semibold">Doctor</th>
              <th class="small text-muted fw-semibold">Date</th>
              <th class="small text-muted fw-semibold">Status</th>
              <th class="small text-muted fw-semibold text-end">Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse($prescriptions as $prescription)
              <tr>
                <td>
                  <div class="fw-semibold">{{ $prescription->medication_name }}</div>
                  <div class="small text-muted">{{ $prescription->instructions ?: 'No instructions' }}</div>
                </td>
                <td class="small">
                  {{ collect([$prescription->dosage, $prescription->frequency, $prescription->duration])->filter()->implode(' / ') ?: '--' }}
                </td>
                <td class="small">{{ $prescription->doctor?->name ?? '--' }}</td>
                <td class="small">{{ $prescription->prescribed_at->format('d M Y') }}</td>
                <td><span class="badge {{ $prescription->status === 'dispensed' ? 'bg-success' : 'bg-warning text-dark' }}">{{ ucfirst($prescription->status) }}</span></td>
                <td class="text-end">
                  <form action="{{ route('pharmacy-center.prescriptions.update', $prescription) }}" method="POST" class="d-inline-flex gap-2">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="dispensed">
                    <button type="submit" class="btn btn-sm btn-dark" @disabled($prescription->status === 'dispensed')>Dispense</button>
                  </form>
                </td>
              </tr>
            @empty
              <tr><td colspan="6" class="text-center text-muted py-4 small">No prescriptions found for this patient.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  @endif
@endsection
