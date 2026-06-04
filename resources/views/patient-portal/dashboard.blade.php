@extends('layouts.website')

@section('title', 'Patient Portal - Medicare Hospital')

@push('styles')
  <link href="{{ asset('website-assets/css/patient-portal.css') }}" rel="stylesheet">
@endpush

@section('content')
<section class="portal-shell">
  <div class="portal-wrap">
    <div class="portal-top">
      <div>
        <div class="portal-kicker">Patient Portal</div>
        <h1 class="portal-title">{{ $patient->name }}</h1>
        <div class="portal-code">Patient ID: <strong>{{ $patient->patient_code }}</strong></div>
      </div>
      <div class="portal-actions">
        <button class="portal-btn" type="button" data-copy-patient-id="{{ $patient->patient_code }}"><i class="bi bi-copy"></i> Copy Patient ID</button>
        <a class="portal-btn" href="{{ route('patient.medical-card.download') }}"><i class="bi bi-download"></i> Download Medical Card</a>
        <a class="portal-btn" href="#patient-qr-code"><i class="bi bi-qr-code"></i> Show QR Code</a>
        <a class="portal-btn" href="{{ route('patient.medical-card.show') }}"><i class="bi bi-printer"></i> Print Card</a>
        <a class="portal-btn is-primary" href="{{ route('patient.results') }}"><i class="bi bi-clipboard2-pulse"></i> Results</a>
        <a class="portal-btn" href="{{ route('my-bookings') }}"><i class="bi bi-calendar2-check"></i> Bookings</a>
      </div>
    </div>

    <div class="portal-tabs">
      <a class="portal-btn is-primary" href="{{ route('patient.dashboard') }}">Dashboard</a>
      <a class="portal-btn" href="{{ route('patient.results') }}">Results</a>
    </div>

    <div class="portal-summary">
      <div class="portal-card"><div class="portal-stat">{{ $diagnoses->count() }}</div><div class="portal-label">Diagnoses</div></div>
      <div class="portal-card"><div class="portal-stat">{{ $labRequests->where('status', 'pending')->count() }}</div><div class="portal-label">Pending lab tests</div></div>
      <div class="portal-card"><div class="portal-stat">{{ $scanRequests->where('status', 'pending')->count() }}</div><div class="portal-label">Pending scans</div></div>
      <div class="portal-card"><div class="portal-stat">{{ $prescriptions->where('status', 'pending')->count() }}</div><div class="portal-label">Medications due</div></div>
    </div>

    <div class="portal-grid">
      <aside>
        <div class="portal-section-block" id="patient-qr-code">
          <div class="portal-section-head"><h2>Personal Information</h2></div>
          <div class="portal-info-list">
            <div><span>Patient ID</span><strong>{{ $patient->patient_code }}</strong></div>
            <div><span>Email</span><strong>{{ $patient->email ?? 'Not recorded' }}</strong></div>
            <div><span>Phone</span><strong>{{ $patient->phone ?? 'Not recorded' }}</strong></div>
            <div><span>Date of birth</span><strong>{{ $patient->date_of_birth?->format('M d, Y') ?? 'Not recorded' }}</strong></div>
            <div><span>Age</span><strong>{{ $patient->age() ?? 'Not recorded' }}</strong></div>
            <div><span>Gender</span><strong>{{ $patient->gender ?? 'Not recorded' }}</strong></div>
            <div><span>Primary doctor</span><strong>{{ $patient->doctor?->name ?? 'Not assigned' }}</strong></div>
          </div>
        </div>
        <div class="portal-section-block">
          <div class="portal-section-head"><h2>QR Code</h2></div>
          <div class="portal-qr-card">
            {!! $qrSvg !!}
            <p class="portal-muted mb-0">QR contains {{ $patient->patient_code }}</p>
          </div>
        </div>
      </aside>

      <div>
        <div class="portal-section-block">
          <div class="portal-section-head"><h2>Diagnoses</h2></div>
          <div class="portal-records">
            @forelse($diagnoses as $diagnosis)
              <article class="portal-record">
                <div class="portal-record-top">
                  <div>
                    <h3>{{ $diagnosis->title }}</h3>
                    <div class="portal-meta">{{ $diagnosis->diagnosed_at->format('M d, Y') }} · {{ $diagnosis->doctor?->name ?? 'Doctor not recorded' }}</div>
                  </div>
                  <span class="portal-status is-{{ $diagnosis->status }}">{{ $diagnosis->status }}</span>
                </div>
                @if($diagnosis->summary)
                  <p class="portal-note">{{ $diagnosis->summary }}</p>
                @endif
              </article>
            @empty
              <div class="portal-empty">No diagnoses recorded.</div>
            @endforelse
          </div>
        </div>

        <div class="portal-section-block">
          <div class="portal-section-head"><h2>Lab Tests</h2></div>
          <div class="portal-records">
            @forelse($labRequests as $labRequest)
              <article class="portal-record">
                <div class="portal-record-top">
                  <div>
                    <h3>{{ $labRequest->test_name }}</h3>
                    <div class="portal-meta">{{ $labRequest->requested_at->format('M d, Y') }} · {{ $labRequest->doctor?->name ?? 'Doctor not recorded' }}</div>
                  </div>
                  <span class="portal-status is-{{ $labRequest->status }}">{{ $labRequest->status }}</span>
                </div>
                @if($labRequest->result?->result_text)
                  <p class="portal-note">{{ $labRequest->result->result_text }}</p>
                @endif
                @if($labRequest->result?->file_paths)
                  <div class="portal-files">
                    @foreach($labRequest->result->file_paths as $index => $path)
                      <a class="portal-file-link" href="{{ route('patient.lab-results.files', [$labRequest->result, $index, 'download' => 1]) }}">
                        <i class="bi bi-download"></i> File {{ $loop->iteration }}
                      </a>
                    @endforeach
                  </div>
                @endif
              </article>
            @empty
              <div class="portal-empty">No lab requests recorded.</div>
            @endforelse
          </div>
        </div>

        <div class="portal-section-block">
          <div class="portal-section-head"><h2>Scans</h2></div>
          <div class="portal-records">
            @forelse($scanRequests as $scanRequest)
              <article class="portal-record">
                <div class="portal-record-top">
                  <div>
                    <h3>{{ $scanRequest->scan_type }}{{ $scanRequest->body_area ? ' · '.$scanRequest->body_area : '' }}</h3>
                    <div class="portal-meta">{{ $scanRequest->requested_at->format('M d, Y') }} · {{ $scanRequest->doctor?->name ?? 'Doctor not recorded' }}</div>
                  </div>
                  <span class="portal-status is-{{ $scanRequest->status }}">{{ $scanRequest->status }}</span>
                </div>
                @if($scanRequest->result?->impression)
                  <p class="portal-note">{{ $scanRequest->result->impression }}</p>
                @endif
                @if($scanRequest->result?->image_paths)
                  <div class="portal-images">
                    @foreach($scanRequest->result->image_paths as $index => $path)
                      <a class="portal-image-link" href="{{ route('patient.scan-results.files', [$scanRequest->result, $index]) }}" target="_blank" rel="noopener">
                        <img src="{{ route('patient.scan-results.files', [$scanRequest->result, $index]) }}" alt="Scan image {{ $loop->iteration }}">
                      </a>
                    @endforeach
                  </div>
                @endif
              </article>
            @empty
              <div class="portal-empty">No scan requests recorded.</div>
            @endforelse
          </div>
        </div>

        <div class="portal-section-block">
          <div class="portal-section-head"><h2>Prescriptions</h2></div>
          <div class="portal-records">
            @forelse($prescriptions as $prescription)
              <article class="portal-record">
                <div class="portal-record-top">
                  <div>
                    <h3>{{ $prescription->medication_name }}</h3>
                    <div class="portal-meta">{{ $prescription->prescribed_at->format('M d, Y') }} · {{ $prescription->doctor?->name ?? 'Doctor not recorded' }}</div>
                  </div>
                  <span class="portal-status is-{{ $prescription->status }}">{{ $prescription->status }}</span>
                </div>
                <p class="portal-note">
                  {{ collect([$prescription->dosage, $prescription->frequency, $prescription->duration])->filter()->implode(' · ') ?: 'Dose details not recorded.' }}
                </p>
              </article>
            @empty
              <div class="portal-empty">No prescriptions recorded.</div>
            @endforelse
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

@push('scripts')
  <script>
    document.querySelectorAll('[data-copy-patient-id]').forEach((button) => {
      button.addEventListener('click', async () => {
        if (navigator.clipboard) {
          await navigator.clipboard.writeText(button.dataset.copyPatientId);
        } else {
          const fallback = document.createElement('input');
          fallback.value = button.dataset.copyPatientId;
          document.body.appendChild(fallback);
          fallback.select();
          document.execCommand('copy');
          fallback.remove();
        }
        button.classList.add('is-primary');
        setTimeout(() => button.classList.remove('is-primary'), 1200);
      });
    });
  </script>
@endpush
