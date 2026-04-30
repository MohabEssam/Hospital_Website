<div class="row g-3">
  <div class="col-md-6">
    <label class="form-label small fw-semibold">Patient</label>
    <select name="patient_id" class="form-select" required>
      <option value="">Select patient</option>
      @foreach ($patients as $patientOption)
        <option value="{{ $patientOption->id }}" @selected(old('patient_id', $appointment->patient_id ?: request('patient_id')) == $patientOption->id)>{{ $patientOption->name }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-6">
    <label class="form-label small fw-semibold">Doctor</label>
    <select name="doctor_id" class="form-select" required>
      <option value="">Select doctor</option>
      @foreach ($doctors as $doctorOption)
        <option value="{{ $doctorOption->id }}" @selected(old('doctor_id', $appointment->doctor_id ?: request('doctor_id')) == $doctorOption->id)>{{ $doctorOption->name }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-6">
    <label class="form-label small fw-semibold">Department</label>
    <select name="department_id" class="form-select">
      <option value="">Auto from doctor</option>
      @foreach ($departments as $departmentOption)
        <option value="{{ $departmentOption->id }}" @selected(old('department_id', $appointment->department_id ?: request('department_id')) == $departmentOption->id)>{{ $departmentOption->name }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-4">
    <label class="form-label small fw-semibold">Date</label>
    <input type="date" name="appointment_date" class="form-control" value="{{ old('appointment_date', $appointment->appointment_date?->format('Y-m-d')) }}" required>
  </div>
  <div class="col-md-4">
    <label class="form-label small fw-semibold">Start Time</label>
    <input type="time" name="start_time" class="form-control" value="{{ old('start_time', $appointment->start_time) }}" required>
  </div>
  <div class="col-md-4">
    <label class="form-label small fw-semibold">End Time</label>
    <input type="time" name="end_time" class="form-control" value="{{ old('end_time', $appointment->end_time) }}">
  </div>
  <div class="col-md-6">
    <label class="form-label small fw-semibold">Treatment</label>
    <input type="text" name="treatment" class="form-control" value="{{ old('treatment', $appointment->treatment) }}" required>
  </div>
  <div class="col-md-3">
    <label class="form-label small fw-semibold">Status</label>
    <select name="status" class="form-select">
      @foreach (\App\Models\Appointment::statusOptions() as $status)
        <option value="{{ $status }}" @selected(old('status', $appointment->status ?: \App\Models\Appointment::STATUS_PENDING) === $status)>{{ ucfirst($status) }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-3">
    <label class="form-label small fw-semibold">Fee</label>
    <input type="number" min="0" step="0.01" name="fee" class="form-control" value="{{ old('fee', $appointment->fee ?: 0) }}">
  </div>
  <div class="col-12">
    <label class="form-label small fw-semibold">Notes</label>
    <textarea name="notes" class="form-control" rows="4">{{ old('notes', $appointment->notes) }}</textarea>
  </div>
</div>
