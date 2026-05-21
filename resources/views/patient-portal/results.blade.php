@extends('layouts.website')

@section('title', 'My Results - Medicare Hospital')

@push('styles')
  <link href="{{ asset('website-assets/css/patient-portal.css') }}" rel="stylesheet">
@endpush

@section('content')
<section class="portal-shell">
  <div class="portal-wrap">
    <div class="portal-top">
      <div>
        <div class="portal-kicker">Patient Results</div>
        <h1 class="portal-title">{{ $patient->name }}</h1>
        <div class="portal-code">Patient ID: <strong>{{ $patient->patient_code }}</strong></div>
      </div>
      <div class="portal-actions">
        <a class="portal-btn" href="{{ route('patient.dashboard') }}"><i class="bi bi-grid"></i> Dashboard</a>
        <a class="portal-btn" href="{{ route('website.book') }}"><i class="bi bi-calendar-plus"></i> Book</a>
      </div>
    </div>

    <div class="portal-tabs">
      <a class="portal-btn" href="{{ route('patient.dashboard') }}">Dashboard</a>
      <a class="portal-btn is-primary" href="{{ route('patient.results') }}">Results</a>
    </div>

    <div class="portal-section-block">
      <div class="portal-section-head"><h2>Lab Results</h2></div>
      <div class="portal-records">
        @forelse($labResults as $result)
          <article class="portal-record">
            <div class="portal-record-top">
              <div>
                <h3>{{ $result->labRequest?->test_name ?? 'Lab result' }}</h3>
                <div class="portal-meta">{{ $result->resulted_at->format('M d, Y') }} · {{ $result->doctor?->name ?? 'Doctor not recorded' }} · Lab</div>
              </div>
              <span class="portal-status is-{{ $result->status }}">{{ $result->status }}</span>
            </div>
            @if($result->result_text)
              <p class="portal-note">{{ $result->result_text }}</p>
            @endif
            @if($result->file_paths)
              <div class="portal-files">
                @foreach($result->file_paths as $index => $path)
                  <a class="portal-file-link" href="{{ route('patient.lab-results.files', [$result, $index, 'download' => 1]) }}">
                    <i class="bi bi-download"></i> File {{ $loop->iteration }}
                  </a>
                @endforeach
              </div>
            @endif
          </article>
        @empty
          <div class="portal-empty">No lab results recorded.</div>
        @endforelse
      </div>
    </div>

    <div class="portal-section-block">
      <div class="portal-section-head"><h2>Scan Results</h2></div>
      <div class="portal-records">
        @forelse($scanResults as $result)
          <article class="portal-record">
            <div class="portal-record-top">
              <div>
                <h3>{{ $result->scanRequest?->scan_type ?? 'Scan result' }}{{ $result->scanRequest?->body_area ? ' · '.$result->scanRequest->body_area : '' }}</h3>
                <div class="portal-meta">{{ $result->resulted_at->format('M d, Y') }} · {{ $result->doctor?->name ?? 'Doctor not recorded' }} · Scan</div>
              </div>
              <span class="portal-status is-{{ $result->status }}">{{ $result->status }}</span>
            </div>
            @if($result->impression)
              <p class="portal-note">{{ $result->impression }}</p>
            @elseif($result->findings)
              <p class="portal-note">{{ $result->findings }}</p>
            @endif
            @if($result->image_paths)
              <div class="portal-images">
                @foreach($result->image_paths as $index => $path)
                  <a class="portal-image-link" href="{{ route('patient.scan-results.files', [$result, $index]) }}" target="_blank" rel="noopener">
                    <img src="{{ route('patient.scan-results.files', [$result, $index]) }}" alt="Scan image {{ $loop->iteration }}">
                  </a>
                @endforeach
              </div>
            @endif
          </article>
        @empty
          <div class="portal-empty">No scan results recorded.</div>
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
                <div class="portal-meta">{{ $prescription->prescribed_at->format('M d, Y') }} · {{ $prescription->doctor?->name ?? 'Doctor not recorded' }} · Prescription</div>
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
</section>
@endsection
