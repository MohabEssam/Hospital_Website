<div class="row g-3">
  <div class="col-md-6">
    <label class="form-label small fw-semibold">Full Name</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $doctor->name) }}" required>
  </div>
  <div class="col-md-6">
    <label class="form-label small fw-semibold">Doctor ID</label>
    <input type="text" name="doctor_code" class="form-control" value="{{ old('doctor_code', $doctor->doctor_code) }}" placeholder="WNH-GM-001">
  </div>
  <div class="col-md-6">
    <label class="form-label small fw-semibold">Department</label>
    <select name="department_id" class="form-select" required>
      <option value="">Select department</option>
      @foreach ($departments as $department)
        <option value="{{ $department->id }}" @selected(old('department_id', $doctor->department_id) == $department->id)>{{ $department->name }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-6">
    <label class="form-label small fw-semibold">Specialty</label>
    <input type="text" name="specialty" class="form-control" value="{{ old('specialty', $doctor->specialty) }}" required>
  </div>
  <div class="col-md-6">
    <label class="form-label small fw-semibold">Email</label>
    <input type="email" name="email" class="form-control" value="{{ old('email', $doctor->email) }}">
  </div>
  <div class="col-md-6">
    <label class="form-label small fw-semibold">Phone</label>
    <input type="text" name="phone" class="form-control" value="{{ old('phone', $doctor->phone) }}">
  </div>
  <div class="col-md-4">
    <label class="form-label small fw-semibold">Availability</label>
    <select name="availability_status" class="form-select" required>
      @foreach (\App\Models\Doctor::availabilityOptions() as $status)
        <option value="{{ $status }}" @selected(old('availability_status', $doctor->availability_status ?: \App\Models\Doctor::STATUS_AVAILABLE) === $status)>{{ ucfirst($status) }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-4">
    <label class="form-label small fw-semibold">Consultation Fee</label>
    <input type="number" min="0" step="0.01" name="consultation_fee" class="form-control" value="{{ old('consultation_fee', $doctor->consultation_fee ?: 0) }}">
  </div>
  <div class="col-md-4">
    <label class="form-label small fw-semibold">Years of Experience</label>
    <input type="number" min="0" max="70" name="years_of_experience" class="form-control" value="{{ old('years_of_experience', $doctor->years_of_experience) }}">
  </div>
  <div class="col-12">
    <label class="form-label small fw-semibold">Avatar Path</label>
    <input type="text" name="avatar_path" class="form-control" value="{{ old('avatar_path', $doctor->avatar_path) }}" placeholder="assets/images/profile/user-1.jpg">
  </div>
  <div class="col-12">
    <label class="form-label small fw-semibold">Biography</label>
    <textarea name="biography" class="form-control" rows="4">{{ old('biography', $doctor->biography) }}</textarea>
  </div>
  <div class="col-12">
    <label class="form-label small fw-semibold">Address</label>
    <textarea name="address" class="form-control" rows="3">{{ old('address', $doctor->address) }}</textarea>
  </div>
</div>
