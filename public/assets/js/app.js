// =========================================
// /* ----------appointment-------- */
// =========================================
/* ── Data ── */

let appointments = [
  { id:1, name:'Caren G. Simpson',  date:'2026-07-20', time:'09:00 AM', doctor:'Dr. Petra Winsburry',   treatment:'Routine Check-Up',      status:'Confirmed' },
  { id:2, name:'Edgar Warrow',       date:'2026-07-20', time:'10:00 AM', doctor:'Dr. Olivia Martinez',   treatment:'Cardiac Consultation',  status:'Pending'   },
  { id:3, name:'Ocean Jane Lupre',   date:'2026-07-20', time:'11:00 AM', doctor:'Dr. Damian Sanchez',    treatment:'Pediatric Check-Up',    status:'Confirmed' },
  { id:4, name:'Shane Riddick',      date:'2026-07-20', time:'01:00 PM', doctor:'Dr. Chloe Harrington',  treatment:'Skin Allergy',          status:'Cancelled' },
  { id:5, name:'Queen Lawnston',     date:'2026-07-20', time:'02:30 PM', doctor:'Dr. Petra Winsburry',   treatment:'Follow-Up Visit',       status:'Confirmed' },
  { id:6, name:'Alice Mitchell',     date:'2026-07-21', time:'09:30 AM', doctor:'Dr. Emily Smith',       treatment:'Neurology Consult',     status:'Pending'   },
  { id:7, name:'Omar Ali',           date:'2026-07-21', time:'11:00 AM', doctor:'Dr. Petra Winsburry',   treatment:'Routine Check-Up',      status:'Confirmed' },
  { id:8, name:'Camila Alvarez',     date:'2026-07-22', time:'03:00 PM', doctor:'Dr. Luke Harrison',     treatment:'Allergy Test',          status:'Cancelled' },
];

let currentFilter = 'all';
let currentSearch = '';
let currentDoctor = 'all';
let currentSort   = { col: null, dir: 1 };
let currentPage   = 1;
const perPage     = 6;
let reschedTarget = null;

const badgeCls = {
  Confirmed: 'bg-success bg-opacity-25 text-success',
  Pending:   'bg-warning bg-opacity-25 text-warning',
  Cancelled: 'bg-danger bg-opacity-25 text-danger'
};

function getFiltered() {
  let rows = [...appointments];
  if (currentFilter !== 'all') rows = rows.filter(r => r.status === currentFilter);
  if (currentSearch) rows = rows.filter(r =>
    r.name.toLowerCase().includes(currentSearch) ||
    r.doctor.toLowerCase().includes(currentSearch) ||
    r.treatment.toLowerCase().includes(currentSearch)
  );
  if (currentDoctor !== 'all') rows = rows.filter(r => r.doctor.includes(currentDoctor));
  if (currentSort.col === 'name') rows.sort((a,b) => a.name.localeCompare(b.name) * currentSort.dir);
  if (currentSort.col === 'date') rows.sort((a,b) => (a.date > b.date ? 1 : -1) * currentSort.dir);
  return rows;
}

function renderTable() {
  const rows = getFiltered();
  const total = rows.length;
  const pages = Math.max(1, Math.ceil(total / perPage));
  if (currentPage > pages) currentPage = pages;
  const slice = rows.slice((currentPage-1)*perPage, currentPage*perPage);

  const tbody = document.getElementById('apptBody');
  tbody.innerHTML = slice.length ? slice.map(r => `
    <tr id="row-${r.id}">
      <td><input type="checkbox" class="form-check-input row-check" data-id="${r.id}" onchange="updateBulk()"></td>
      <td class="small fw-medium">${r.name}</td>
      <td class="text-muted small">${r.date.split('-').reverse().join('-').slice(0,8)}</td>
      <td class="text-muted small">${r.time}</td>
      <td class="small">${r.doctor}</td>
      <td class="small">${r.treatment}</td>
      <td><span class="badge px-3 py-2 ${badgeCls[r.status]}">${r.status}</span></td>
      <td>
        <button class="btn btn-sm btn-outline-secondary border-0 me-1" title="Reschedule"
          onclick="openReschedule(${r.id})"><i class="fas fa-calendar-alt text-muted" style="font-size:.75rem;"></i></button>
        <button class="btn btn-sm btn-outline-secondary border-0 me-1" title="Mark Confirmed"
          onclick="setStatus(${r.id},'Confirmed')"><i class="fas fa-check text-success" style="font-size:.75rem;"></i></button>
        <button class="btn btn-sm btn-outline-secondary border-0" title="Cancel"
          onclick="setStatus(${r.id},'Cancelled')"><i class="fas fa-times text-danger" style="font-size:.75rem;"></i></button>
      </td>
    </tr>`).join('') :
    '<tr><td colspan="8" class="text-center text-muted py-4 small">No appointments found.</td></tr>';

  // Page info
  document.getElementById('pageInfo').textContent = total
    ? `Showing ${(currentPage-1)*perPage+1}–${Math.min(currentPage*perPage,total)} of ${total}`
    : '';

  // Pagination
  const pag = document.getElementById('pagination');
  pag.innerHTML = '';
  if (pages > 1) {
    pag.innerHTML += `<li class="page-item ${currentPage===1?'disabled':''}"><a class="page-link" href="#" onclick="goPage(${currentPage-1})">‹</a></li>`;
    for (let i=1; i<=pages; i++) {
      pag.innerHTML += `<li class="page-item ${i===currentPage?'active':''}"><a class="page-link" href="#" onclick="goPage(${i})">${i}</a></li>`;
    }
    pag.innerHTML += `<li class="page-item ${currentPage===pages?'disabled':''}"><a class="page-link" href="#" onclick="goPage(${currentPage+1})">›</a></li>`;
  }

  // Update counts
  updateCounts();
  document.getElementById('checkAll').checked = false;
  updateBulk();
}

function updateCounts() {
  document.getElementById('countAll').textContent       = appointments.length;
  document.getElementById('countConfirmed').textContent = appointments.filter(r=>r.status==='Confirmed').length;
  document.getElementById('countPending').textContent   = appointments.filter(r=>r.status==='Pending').length;
  document.getElementById('countCancelled').textContent = appointments.filter(r=>r.status==='Cancelled').length;
}

function filterTab(f) { currentFilter = f; currentPage = 1; renderTable(); }
function applySearch() { currentSearch = document.getElementById('searchInput').value.toLowerCase(); currentPage = 1; renderTable(); }
function filterDoctor(d) { currentDoctor = d; currentPage = 1; renderTable(); showToast('Filter applied', 'bg-secondary'); }
function sortByDate(dir) { currentSort = { col:'date', dir: dir==='asc'?1:-1 }; renderTable(); }
function sortCol(col) {
  currentSort = { col, dir: currentSort.col===col ? -currentSort.dir : 1 };
  renderTable();
}
function goPage(p) { currentPage = p; renderTable(); }

function toggleAll(cb) {
  document.querySelectorAll('.row-check').forEach(c => c.checked = cb.checked);
  updateBulk();
}
function updateBulk() {
  const checked = document.querySelectorAll('.row-check:checked').length;
  const btn = document.getElementById('bulkCancelBtn');
  const info = document.getElementById('selectionCount');
  if (checked > 0) {
    btn.style.display = '';
    info.textContent = `${checked} selected`;
  } else {
    btn.style.display = 'none!important';
    info.textContent = '';
  }
}
document.getElementById('bulkCancelBtn').addEventListener('click', () => {
  document.querySelectorAll('.row-check:checked').forEach(c => {
    const id = parseInt(c.dataset.id);
    const appt = appointments.find(a => a.id === id);
    if (appt) appt.status = 'Cancelled';
  });
  renderTable();
  showToast('Selected appointments cancelled', 'bg-danger');
});

function setStatus(id, status) {
  const a = appointments.find(r => r.id === id);
  if (a) { a.status = status; renderTable(); }
  const msgs = { Confirmed:'Appointment confirmed ✓', Cancelled:'Appointment cancelled', Pending:'Marked as pending' };
  const cls  = { Confirmed:'bg-success', Cancelled:'bg-danger', Pending:'bg-warning' };
  showToast(msgs[status], cls[status]);
}

function openReschedule(id) {
  reschedTarget = id;
  const a = appointments.find(r => r.id === id);
  document.getElementById('reschedName').textContent = a.name;
  document.getElementById('reschedDate').value = a.date;
  new bootstrap.Modal(document.getElementById('rescheduleModal')).show();
}
function confirmReschedule() {
  if (!reschedTarget) return;
  const a = appointments.find(r => r.id === reschedTarget);
  a.date = document.getElementById('reschedDate').value;
  const t = document.getElementById('reschedTime').value;
  if (t) {
    const [h,m] = t.split(':');
    const hr = parseInt(h);
    a.time = `${hr%12||12}:${m} ${hr<12?'AM':'PM'}`;
  }
  a.status = 'Confirmed';
  bootstrap.Modal.getInstance(document.getElementById('rescheduleModal')).hide();
  renderTable();
  showToast('Appointment rescheduled ✓', 'bg-success');
}

function addAppointment() {
  const name = document.getElementById('newName').value.trim();
  const doctor = document.getElementById('newDoctor').value;
  const date = document.getElementById('newDate').value;
  const treatment = document.getElementById('newTreatment').value.trim();
  const rawTime = document.getElementById('newTime').value;
  if (!name || !date) { showToast('Please fill in all required fields', 'bg-warning'); return; }
  const [h,m] = rawTime.split(':');
  const hr = parseInt(h);
  const time = `${hr%12||12}:${m} ${hr<12?'AM':'PM'}`;
  const newId = Math.max(...appointments.map(a=>a.id)) + 1;
  appointments.push({ id:newId, name, date, time, doctor, treatment:treatment||'General', status:'Pending' });
  bootstrap.Modal.getInstance(document.getElementById('addApptModal')).hide();
  document.getElementById('newName').value = '';
  document.getElementById('newTreatment').value = '';
  currentFilter = 'all';
  currentPage = 1;
  renderTable();
  showToast('Appointment added ✓', 'bg-success');
}

function exportCSV() {
  const rows = getFiltered();
  const header = 'Name,Date,Time,Doctor,Treatment,Status';
  const csv = [header, ...rows.map(r => `${r.name},${r.date},${r.time},${r.doctor},${r.treatment},${r.status}`)].join('\n');
  const a = document.createElement('a');
  a.href = 'data:text/csv,' + encodeURIComponent(csv);
  a.download = 'appointments.csv';
  a.click();
  showToast('Exported as CSV', 'bg-secondary');
}

function showToast(msg, bg) {
  const el = document.getElementById('toastEl');
  el.className = `toast align-items-center text-white border-0 ${bg}`;
  document.getElementById('toastMsg').textContent = msg;
  new bootstrap.Toast(el, { delay: 2500 }).show();
}

// Init
renderTable();


