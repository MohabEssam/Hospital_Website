@extends('layouts.app')

@section('content')
  <div class="d-flex align-items-center justify-content-between gap-3 mb-4">
    <div>
      <p class="mb-0 text-muted small">Reception desk patient lookup</p>
      <h4 class="fw-bold mb-0">Patient Lookup</h4>
    </div>
  </div>

  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
      <label class="form-label small fw-semibold">Search Patient</label>
      <input
        type="search"
        class="form-control"
        id="receptionPatientSearch"
        placeholder="Patient ID, name, phone, or email"
        autocomplete="off">
    </div>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th class="small text-muted fw-semibold">Patient ID</th>
            <th class="small text-muted fw-semibold">Full Name</th>
            <th class="small text-muted fw-semibold">Phone</th>
            <th class="small text-muted fw-semibold">Age</th>
            <th class="small text-muted fw-semibold">Gender</th>
            <th class="small text-muted fw-semibold text-end">Action</th>
          </tr>
        </thead>
        <tbody id="receptionPatientResults">
          <tr><td colspan="6" class="text-center text-muted py-4 small">Start typing to search patients.</td></tr>
        </tbody>
      </table>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    (() => {
      const endpoint = @json(route('patients.lookup.search'));
      const input = document.getElementById('receptionPatientSearch');
      const results = document.getElementById('receptionPatientResults');
      const escapeHtml = (value) => String(value || '').replace(/[&<>"']/g, (char) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
      })[char]);
      const debounce = (callback, wait = 180) => {
        let timeout;
        return (...args) => {
          clearTimeout(timeout);
          timeout = setTimeout(() => callback(...args), wait);
        };
      };

      const render = (patients) => {
        if (!patients.length) {
          results.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4 small">No matching patients found.</td></tr>';
          return;
        }

        results.innerHTML = patients.map((patient) => `
          <tr>
            <td class="fw-semibold">${escapeHtml(patient.patient_code)}</td>
            <td>${escapeHtml(patient.name)}</td>
            <td class="small text-muted">${escapeHtml(patient.phone || 'Not recorded')}</td>
            <td class="small">${escapeHtml(patient.age || '--')}</td>
            <td class="small">${escapeHtml(patient.gender || 'Not recorded')}</td>
            <td class="text-end"><a class="btn btn-sm btn-dark" href="${escapeHtml(patient.profile_url)}">Open Profile</a></td>
          </tr>
        `).join('');
      };

      const search = debounce(async () => {
        const term = input.value.trim();

        if (term.length < 2) {
          results.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4 small">Start typing to search patients.</td></tr>';
          return;
        }

        const params = new URLSearchParams({ context: 'reception', q: term });
        const response = await fetch(`${endpoint}?${params.toString()}`, {
          headers: { Accept: 'application/json' }
        });

        if (!response.ok) {
          results.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4 small">Search is unavailable.</td></tr>';
          return;
        }

        const payload = await response.json();
        render(payload.results || []);
      });

      input.addEventListener('input', search);
    })();
  </script>
@endpush
