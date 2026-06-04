@extends($layout)

@section('title', 'Medical Card - '.$patient->patient_code)

@push('styles')
  <style>
    .medical-card-shell { max-width: 760px; margin: 0 auto; padding: 24px 0; }
    .medical-card-panel { background: #fff; border: 1px solid #dbe4ef; border-radius: 8px; overflow: hidden; }
    .medical-card-head { align-items: center; background: #0f766e; color: #fff; display: flex; gap: 12px; padding: 18px 22px; }
    .medical-card-logo { align-items: center; background: #fff; border-radius: 8px; color: #0f766e; display: inline-flex; font-weight: 800; height: 42px; justify-content: center; width: 42px; }
    .medical-card-body { display: grid; gap: 20px; grid-template-columns: minmax(0, 1fr) 200px; padding: 24px; }
    .medical-card-row { border-bottom: 1px solid #eef2f7; padding: 10px 0; }
    .medical-card-row span { color: #64748b; display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; }
    .medical-card-row strong { color: #172033; display: block; font-size: 18px; margin-top: 2px; }
    .medical-card-qr { align-items: center; display: flex; flex-direction: column; gap: 10px; justify-content: center; }
    .medical-card-actions { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 16px; }
    @media (max-width: 575px) { .medical-card-body { grid-template-columns: 1fr; } }
    @media print {
      body { background: #fff !important; }
      .left-sidebar, .app-header, #header, #footer, .medical-card-actions { display: none !important; }
      .body-wrapper, .container-fluid, .main { margin: 0 !important; padding: 0 !important; }
      .medical-card-shell { max-width: 100%; padding: 0; }
      .medical-card-panel { box-shadow: none; }
    }
  </style>
@endpush

@section('content')
  <div class="medical-card-shell">
    <div class="medical-card-panel">
      <div class="medical-card-head">
        <span class="medical-card-logo">M</span>
        <div>
          <div class="fw-bold">Medicare Hospital</div>
          <div class="small opacity-75">Digital Medical Card</div>
        </div>
      </div>
      <div class="medical-card-body">
        <div>
          <div class="medical-card-row"><span>Patient Name</span><strong>{{ $patient->name }}</strong></div>
          <div class="medical-card-row"><span>Patient ID</span><strong>{{ $patient->patient_code }}</strong></div>
          <div class="medical-card-row"><span>Phone Number</span><strong>{{ $patient->phone ?? 'Not recorded' }}</strong></div>
          <div class="medical-card-row"><span>Email</span><strong>{{ $patient->email ?? 'Not recorded' }}</strong></div>
        </div>
        <div class="medical-card-qr">
          {!! $qrSvg !!}
          <span class="small text-muted">QR contains Patient ID only</span>
        </div>
      </div>
    </div>

    <div class="medical-card-actions">
      <a class="btn btn-dark" href="{{ $downloadRoute }}"><i class="fas fa-download"></i> Download PDF</a>
      <button class="btn btn-outline-dark" type="button" onclick="window.print()"><i class="fas fa-print"></i> Print Card</button>
    </div>
  </div>
@endsection
