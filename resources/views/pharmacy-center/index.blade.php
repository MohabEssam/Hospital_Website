@extends('layouts.app')

@section('content')
  <div class="d-flex align-items-center justify-content-between mb-4">
    <div>
      <p class="mb-0 text-muted small">Search by patient ID, name, phone, or email</p>
      <h4 class="fw-bold mb-0">Pharmacy</h4>
      <p class="mb-0 text-muted small">Staff ID: {{ auth()->user()->public_id }}</p>
    </div>
  </div>

  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
      @include('patients._lookup', [
        'context' => 'pharmacy',
        'action' => route('pharmacy.dashboard'),
        'buttonLabel' => 'Find Prescriptions',
        'value' => $patientSearch ?: $patientCode,
      ])
    </div>
  </div>

  @if(($patientCode || $patientSearch) && ! $patient)
    <div class="alert alert-warning">No matching patient with prescriptions was found.</div>
  @endif

  @if($patient)
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <div class="d-flex flex-wrap justify-content-between gap-2 mb-2">
          <div>
            <p class="text-muted small mb-1">Patient</p>
            <h5 class="fw-bold mb-0">{{ $patient->name }}</h5>
          </div>
          <span class="badge bg-dark align-self-center">{{ $patient->patient_code }}</span>
        </div>
        <div class="row g-2 small text-muted">
          <div class="col-md-4"><strong class="text-dark">Phone:</strong> {{ $patient->phone ?? 'Not recorded' }}</div>
          <div class="col-md-4"><strong class="text-dark">Email:</strong> {{ $patient->email ?? 'Not recorded' }}</div>
          <div class="col-md-4"><strong class="text-dark">Gender:</strong> {{ $patient->gender ?? 'Not recorded' }}</div>
        </div>
      </div>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th class="small text-muted fw-semibold">Medication</th>
              <th class="small text-muted fw-semibold">Dose</th>
              <th class="small text-muted fw-semibold">Quantity</th>
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
                <td class="small">{{ $prescription->quantity ?? '--' }}</td>
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
              <tr><td colspan="7" class="text-center text-muted py-4 small">No prescriptions found for this patient.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  @endif
@endsection
