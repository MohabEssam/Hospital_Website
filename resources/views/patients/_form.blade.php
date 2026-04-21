<div class="row g-3">
  <div class="col-md-6">
    <label class="form-label small fw-semibold">Full Name</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $patient->name) }}" required>
  </div>
  <div class="col-md-6">
    <label class="form-label small fw-semibold">Email</label>
    <input type="email" name="email" class="form-control" value="{{ old('email', $patient->email) }}">
  </div>
  <div class="col-md-6">
    <label class="form-label small fw-semibold">Phone</label>
    <input type="text" name="phone" class="form-control" value="{{ old('phone', $patient->phone) }}">
  </div>
  <div class="col-md-6">
    <label class="form-label small fw-semibold">Date of Birth</label>
    <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth', $patient->date_of_birth?->format('Y-m-d')) }}">
  </div>
  <div class="col-md-6">
    <label class="form-label small fw-semibold">Gender</label>
    <input type="text" name="gender" class="form-control" value="{{ old('gender', $patient->gender) }}" placeholder="e.g. Female">
  </div>
  <div class="col-md-6">
    <label class="form-label small fw-semibold">Check In Date</label>
    <input type="date" name="check_in_date" class="form-control" value="{{ old('check_in_date', $patient->check_in_date?->format('Y-m-d')) }}">
  </div>
  <div class="col-md-6">
    <label class="form-label small fw-semibold">Treatment</label>
    <input type="text" name="treatment" class="form-control" value="{{ old('treatment', $patient->treatment) }}">
  </div>
  <div class="col-md-6">
    <label class="form-label small fw-semibold">Doctor</label>
    <select name="doctor_id" class="form-select">
      <option value="">Select doctor</option>
      @foreach ($doctors as $doctor)
        <option value="{{ $doctor->id }}" @selected(old('doctor_id', $patient->doctor_id) == $doctor->id)>{{ $doctor->name }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-6">
    <label class="form-label small fw-semibold">Room</label>
    <input type="text" name="room_number" class="form-control" value="{{ old('room_number', $patient->room_number) }}">
  </div>
  <div class="col-md-6">
    <label class="form-label small fw-semibold">Status</label>
    <select name="status" class="form-select" required>
      @foreach (\App\Models\Patient::statusOptions() as $status)
        <option value="{{ $status }}" @selected(old('status', $patient->status ?: \App\Models\Patient::STATUS_ACTIVE) === $status)>{{ str($status)->replace('_', ' ')->title() }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-6">
    <label class="form-label small fw-semibold">Avatar Path</label>
    <input type="text" name="avatar_path" class="form-control" value="{{ old('avatar_path', $patient->avatar_path) }}" placeholder="assets/images/profile/user-1.jpg">
  </div>
  <div class="col-12">
    <label class="form-label small fw-semibold">Address</label>
    <textarea name="address" class="form-control" rows="3">{{ old('address', $patient->address) }}</textarea>
  </div>
  <div class="col-12">
    <label class="form-label small fw-semibold">Notes</label>
    <textarea name="notes" class="form-control" rows="4">{{ old('notes', $patient->notes) }}</textarea>
  </div>
</div>
