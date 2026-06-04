@php
  $buttonLabel = $buttonLabel ?? 'Search Patient';
  $value = $value ?? '';
@endphp

<form method="GET" action="{{ $action }}" class="row g-2 align-items-end js-patient-lookup" data-context="{{ $context }}">
  <input type="hidden" name="patient_code" value="{{ request('patient_code') }}" data-patient-code>
  <div class="col-md-8 position-relative">
    <label class="form-label small fw-semibold">Search Patient</label>
    <input
      type="search"
      name="patient_search"
      value="{{ $value }}"
      class="form-control"
      placeholder="Patient ID, name, phone, or email"
      autocomplete="off"
      data-patient-search>
    <div class="patient-lookup-menu d-none" data-patient-results></div>
  </div>
  <div class="col-md-4">
    <button type="submit" class="btn btn-dark w-100">{{ $buttonLabel }}</button>
  </div>
</form>

@once
  @push('styles')
    <style>
      .patient-lookup-menu { background: #fff; border: 1px solid #dbe4ef; border-radius: 8px; box-shadow: 0 18px 40px rgba(15, 23, 42, .14); left: 0; max-height: 280px; overflow-y: auto; position: absolute; right: 0; top: calc(100% + 4px); z-index: 30; }
      .patient-lookup-item { align-items: center; border: 0; border-bottom: 1px solid #eef2f7; background: transparent; display: flex; gap: 12px; padding: 10px 12px; text-align: left; width: 100%; }
      .patient-lookup-item:hover, .patient-lookup-item:focus { background: #f8fafc; outline: none; }
      .patient-lookup-code { color: #111827; font-weight: 800; white-space: nowrap; }
      .patient-lookup-name { color: #1f2937; font-weight: 700; }
      .patient-lookup-meta { color: #64748b; font-size: 12px; }
      .patient-lookup-mark { background: #fef3c7; border-radius: 3px; padding: 0 1px; }
      @media (max-width: 575px) { .patient-lookup-item { align-items: flex-start; flex-direction: column; gap: 2px; } }
    </style>
  @endpush

  @push('scripts')
    <script>
      (() => {
        const endpoint = @json(route('patients.lookup.search'));
        const escapeHtml = (value) => String(value || '').replace(/[&<>"']/g, (char) => ({
          '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
        })[char]);
        const highlight = (value, term) => {
          const safe = escapeHtml(value);
          if (!term) return safe;
          const escapedTerm = term.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
          return safe.replace(new RegExp(`(${escapedTerm})`, 'ig'), '<span class="patient-lookup-mark">$1</span>');
        };
        const debounce = (callback, wait = 180) => {
          let timeout;
          return (...args) => {
            clearTimeout(timeout);
            timeout = setTimeout(() => callback(...args), wait);
          };
        };

        document.querySelectorAll('.js-patient-lookup').forEach((form) => {
          const input = form.querySelector('[data-patient-search]');
          const codeInput = form.querySelector('[data-patient-code]');
          const menu = form.querySelector('[data-patient-results]');
          const context = form.dataset.context;

          const close = () => menu.classList.add('d-none');
          const render = (patients, term) => {
            if (!patients.length) {
              menu.innerHTML = '<div class="text-muted small p-3">No matching patients found.</div>';
              menu.classList.remove('d-none');
              return;
            }

            menu.innerHTML = patients.map((patient) => `
              <button type="button" class="patient-lookup-item" data-code="${escapeHtml(patient.patient_code)}" data-label="${escapeHtml(patient.patient_code + ' | ' + patient.name)}">
                <span class="patient-lookup-code">${highlight(patient.patient_code, term)}</span>
                <span>
                  <span class="patient-lookup-name">${highlight(patient.name, term)}</span>
                  <span class="patient-lookup-meta d-block">${highlight(patient.phone || 'No phone', term)} | ${highlight(patient.email || 'No email', term)}</span>
                </span>
              </button>
            `).join('');
            menu.classList.remove('d-none');
          };

          const search = debounce(async () => {
            const term = input.value.trim();
            codeInput.value = '';

            if (term.length < 2) {
              close();
              return;
            }

            const params = new URLSearchParams({ context, q: term });
            const response = await fetch(`${endpoint}?${params.toString()}`, {
              headers: { Accept: 'application/json' }
            });

            if (!response.ok) {
              close();
              return;
            }

            const payload = await response.json();
            render(payload.results || [], term);
          });

          input.addEventListener('input', search);
          input.addEventListener('focus', search);
          menu.addEventListener('click', (event) => {
            const item = event.target.closest('[data-code]');
            if (!item) return;
            codeInput.value = item.dataset.code;
            input.value = item.dataset.label;
            close();
            form.submit();
          });
          document.addEventListener('click', (event) => {
            if (!form.contains(event.target)) close();
          });
        });
      })();
    </script>
  @endpush
@endonce
