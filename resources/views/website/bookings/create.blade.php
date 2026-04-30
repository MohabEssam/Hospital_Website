@extends('layouts.website')

@section('title', 'Book Appointment - Medicare Hospital')

@section('content')

  <section class="contact section">
    <div class="container section-title" data-aos="fade-up">
      <h2>Book an Appointment</h2>
      <p>Select a doctor, choose your preferred date and time</p>
    </div>

    <div class="container" data-aos="fade-up" data-aos-delay="100">
      <div class="row justify-content-center">
        <div class="col-md-8">

          @if($errors->any())
            <div class="alert alert-danger">
              <ul class="mb-0">
                @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <form action="{{ route('website.book.store') }}" method="POST" class="p-4 bg-white rounded shadow-sm">
            @csrf

            <div class="mb-3">
              <label class="form-label">Patient Name</label>
              <input type="text" class="form-control" value="{{ auth()->user()->name }}" readonly>
            </div>

            <div class="mb-3">
              <label for="department_filter" class="form-label">Filter by Department</label>
              <select id="department_filter" class="form-select" onchange="filterDoctors(this.value)">
                <option value="">All Departments</option>
                @foreach($departments as $department)
                  <option value="{{ $department->id }}">{{ $department->name }}</option>
                @endforeach
              </select>
            </div>

            <div class="mb-3">
              <label for="doctor_id" class="form-label">Select Doctor <span class="text-danger">*</span></label>
              <select name="doctor_id" id="doctor_id" class="form-select @error('doctor_id') is-invalid @enderror" required>
                <option value="">-- Choose a Doctor --</option>
                @foreach($doctors as $doctor)
                  <option value="{{ $doctor->id }}"
                    data-department="{{ $doctor->department_id }}"
                    data-fee="{{ $doctor->consultation_fee }}"
                    {{ old('doctor_id', request('doctor_id')) == $doctor->id ? 'selected' : '' }}>
                    {{ $doctor->name }} — {{ $doctor->specialty }}
                    ({{ $doctor->department?->name }}) — ${{ number_format($doctor->consultation_fee, 2) }}
                  </option>
                @endforeach
              </select>
              @error('doctor_id')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="appointment_date" class="form-label">Appointment Date <span class="text-danger">*</span></label>
                <input type="date" name="appointment_date" id="appointment_date"
                  class="form-control @error('appointment_date') is-invalid @enderror"
                  value="{{ old('appointment_date') }}"
                  min="{{ date('Y-m-d') }}" required>
                @error('appointment_date')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="col-md-6 mb-3">
                <label for="start_time" class="form-label">Preferred Time <span class="text-danger">*</span></label>
                <select name="start_time" id="start_time" class="form-select @error('start_time') is-invalid @enderror" required>
                  <option value="">-- Select Time --</option>
                  @foreach(['09:00','09:30','10:00','10:30','11:00','11:30','12:00','12:30','13:00','13:30','14:00','14:30','15:00','15:30','16:00'] as $time)
                    <option value="{{ $time }}" {{ old('start_time') === $time ? 'selected' : '' }}>{{ $time }}</option>
                  @endforeach
                </select>
                @error('start_time')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <div class="mb-3">
              <label for="treatment" class="form-label">Reason / Treatment <span class="text-danger">*</span></label>
              <input type="text" name="treatment" id="treatment"
                class="form-control @error('treatment') is-invalid @enderror"
                value="{{ old('treatment') }}"
                placeholder="e.g. Routine Check-Up, Cardiac Consultation" required>
              @error('treatment')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-3">
              <label for="notes" class="form-label">Additional Notes</label>
              <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror"
                rows="3" placeholder="Any additional information...">{{ old('notes') }}</textarea>
              @error('notes')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div id="fee-display" class="alert alert-info d-none mb-3">
              <strong>Consultation Fee:</strong> $<span id="fee-amount">0.00</span>
            </div>

            <div class="text-center">
              <button type="submit" class="btn btn-primary px-5 py-2" style="border-radius: 25px;">
                <i class="bi bi-calendar-check"></i> Confirm Booking
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>

@endsection

@push('scripts')
<script>
  function filterDoctors(departmentId) {
    const select = document.getElementById('doctor_id');
    const options = select.querySelectorAll('option[data-department]');
    options.forEach(option => {
      if (!departmentId || option.dataset.department === departmentId) {
        option.style.display = '';
      } else {
        option.style.display = 'none';
        if (option.selected) {
          select.value = '';
          document.getElementById('fee-display').classList.add('d-none');
        }
      }
    });
  }

  document.getElementById('doctor_id').addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    const fee = selected.dataset.fee;
    const feeDisplay = document.getElementById('fee-display');
    if (fee) {
      document.getElementById('fee-amount').textContent = parseFloat(fee).toFixed(2);
      feeDisplay.classList.remove('d-none');
    } else {
      feeDisplay.classList.add('d-none');
    }
  });

  // Trigger on load if doctor pre-selected
  document.addEventListener('DOMContentLoaded', function() {
    const doctorSelect = document.getElementById('doctor_id');
    if (doctorSelect.value) {
      doctorSelect.dispatchEvent(new Event('change'));
    }
  });
</script>
@endpush
