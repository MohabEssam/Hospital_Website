@extends('layouts.app')

@section('content')
  <div class="d-flex align-items-center justify-content-between gap-3 mb-4">
    <div class="d-flex align-items-center gap-3">
      <a href="{{ route('patients.show', $patient) }}" class="btn btn-outline-secondary btn-sm rounded-circle p-2 lh-1">
        <i class="fas fa-arrow-left"></i>
      </a>
      <div>
        <p class="mb-0 text-muted small">Create diagnosis and orders</p>
        <h4 class="fw-bold mb-0">{{ $patient->name }} <span class="text-muted fs-6">{{ $patient->patient_code }}</span></h4>
      </div>
    </div>
  </div>

  <form action="{{ route('patients.clinical-records.store', $patient) }}" method="POST">
    @csrf

    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <h6 class="fw-semibold mb-3">Diagnosis</h6>
        <div class="row g-3">
          <div class="col-md-8">
            <label class="form-label small fw-semibold">Diagnosis Title</label>
            <input type="text" name="title" value="{{ old('title') }}" class="form-control @error('title') is-invalid @enderror" required>
            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label class="form-label small fw-semibold">Related Appointment</label>
            <select name="appointment_id" class="form-select">
              <option value="">None</option>
              @foreach($patient->appointments as $appointment)
                <option value="{{ $appointment->id }}" @selected(old('appointment_id') == $appointment->id)>
                  {{ $appointment->appointment_date?->format('d M Y') }} - {{ $appointment->treatment }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label small fw-semibold">Diagnosed At</label>
            <input type="datetime-local" name="diagnosed_at" value="{{ old('diagnosed_at', now()->format('Y-m-d\TH:i')) }}" class="form-control">
          </div>
          <div class="col-md-4">
            <label class="form-label small fw-semibold">Status</label>
            <select name="status" class="form-select">
              <option value="active" @selected(old('status', 'active') === 'active')>Active</option>
              <option value="resolved" @selected(old('status') === 'resolved')>Resolved</option>
            </select>
          </div>
          <div class="col-12">
            <label class="form-label small fw-semibold">Summary</label>
            <textarea name="summary" rows="3" class="form-control">{{ old('summary') }}</textarea>
          </div>
          <div class="col-md-6">
            <label class="form-label small fw-semibold">Symptoms</label>
            <textarea name="symptoms" rows="4" class="form-control">{{ old('symptoms') }}</textarea>
          </div>
          <div class="col-md-6">
            <label class="form-label small fw-semibold">Doctor Notes</label>
            <textarea name="notes" rows="4" class="form-control">{{ old('notes') }}</textarea>
          </div>
        </div>
      </div>
    </div>

    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <h6 class="fw-semibold mb-3">Lab Requests</h6>
        @for($i = 0; $i < 3; $i++)
          <div class="row g-2 mb-2">
            <div class="col-md-4"><input name="lab_requests[{{ $i }}][test_name]" value="{{ old("lab_requests.$i.test_name") }}" class="form-control" placeholder="Test name"></div>
            <div class="col-md-2"><input name="lab_requests[{{ $i }}][specimen]" value="{{ old("lab_requests.$i.specimen") }}" class="form-control" placeholder="Specimen"></div>
            <div class="col-md-2">
              <select name="lab_requests[{{ $i }}][priority]" class="form-select">
                <option value="routine">Routine</option>
                <option value="urgent" @selected(old("lab_requests.$i.priority") === 'urgent')>Urgent</option>
              </select>
            </div>
            <div class="col-md-4"><input name="lab_requests[{{ $i }}][instructions]" value="{{ old("lab_requests.$i.instructions") }}" class="form-control" placeholder="Instructions"></div>
          </div>
        @endfor
      </div>
    </div>

    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <h6 class="fw-semibold mb-3">Scan Requests</h6>
        @for($i = 0; $i < 3; $i++)
          <div class="row g-2 mb-2">
            <div class="col-md-3"><input name="scan_requests[{{ $i }}][scan_type]" value="{{ old("scan_requests.$i.scan_type") }}" class="form-control" placeholder="X-ray, MRI, CT"></div>
            <div class="col-md-3"><input name="scan_requests[{{ $i }}][body_area]" value="{{ old("scan_requests.$i.body_area") }}" class="form-control" placeholder="Body area"></div>
            <div class="col-md-2">
              <select name="scan_requests[{{ $i }}][contrast_required]" class="form-select">
                <option value="0">No contrast</option>
                <option value="1" @selected(old("scan_requests.$i.contrast_required") == 1)>Contrast</option>
              </select>
            </div>
            <div class="col-md-4"><input name="scan_requests[{{ $i }}][instructions]" value="{{ old("scan_requests.$i.instructions") }}" class="form-control" placeholder="Instructions"></div>
          </div>
        @endfor
      </div>
    </div>

    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <h6 class="fw-semibold mb-3">Prescriptions</h6>
        @for($i = 0; $i < 4; $i++)
          <div class="row g-2 mb-2">
            <div class="col-md-3"><input name="prescriptions[{{ $i }}][medication_name]" value="{{ old("prescriptions.$i.medication_name") }}" class="form-control" placeholder="Medication"></div>
            <div class="col-md-2"><input name="prescriptions[{{ $i }}][dosage]" value="{{ old("prescriptions.$i.dosage") }}" class="form-control" placeholder="Dosage"></div>
            <div class="col-md-2"><input name="prescriptions[{{ $i }}][frequency]" value="{{ old("prescriptions.$i.frequency") }}" class="form-control" placeholder="Frequency"></div>
            <div class="col-md-2"><input name="prescriptions[{{ $i }}][duration]" value="{{ old("prescriptions.$i.duration") }}" class="form-control" placeholder="Duration"></div>
            <div class="col-md-1"><input type="number" min="1" name="prescriptions[{{ $i }}][quantity]" value="{{ old("prescriptions.$i.quantity") }}" class="form-control" placeholder="Qty"></div>
            <div class="col-md-2"><input name="prescriptions[{{ $i }}][instructions]" value="{{ old("prescriptions.$i.instructions") }}" class="form-control" placeholder="Instructions"></div>
          </div>
        @endfor
      </div>
    </div>

    <div class="d-flex justify-content-end gap-2">
      <a href="{{ route('patients.show', $patient) }}" class="btn btn-outline-secondary">Cancel</a>
      <button type="submit" class="btn btn-dark">Save Clinical Record</button>
    </div>
  </form>
@endsection
