@extends('layouts.website')

@section('title', 'Book Appointment - Medicare Hospital')

@section('content')
<style>
  .bk-section{padding-top:120px;padding-bottom:60px;background:#f4f6f9;min-height:100vh}
  .bk-title{text-align:center;margin-bottom:32px}.bk-title h2{font-weight:700;font-size:30px;color:#1e293b;margin-bottom:4px}.bk-title p{color:#64748b;font-size:14px;margin:0}
  .bk-card{background:#fff;border-radius:14px;box-shadow:0 2px 12px rgba(0,0,0,.05);margin-bottom:20px;overflow:hidden}
  .bk-card-head{display:flex;align-items:center;gap:14px;padding:20px 24px;border-bottom:1px solid #f1f5f9}
  .bk-card-head i{font-size:22px;color:#fff;width:44px;height:44px;border-radius:11px;background:#3f4047;display:flex;align-items:center;justify-content:center;flex-shrink:0}
  .bk-card-head h3{font-weight:600;font-size:16px;color:#1e293b;margin:0}.bk-card-head p{font-size:12px;color:#94a3b8;margin:0}
  .bk-card-body{padding:20px 24px}
  .bk-label{display:block;font-weight:600;font-size:12px;color:#475569;margin-bottom:6px;text-transform:uppercase;letter-spacing:.4px}
  .bk-input,.bk-select{width:100%;padding:10px 14px;border:2px solid #e2e8f0;border-radius:10px;font-size:14px;color:#1e293b;outline:none;transition:border-color .2s,box-shadow .2s;background:#fff;font-family:inherit}
  .bk-input:focus,.bk-select:focus{border-color:#3f4047;box-shadow:0 0 0 3px rgba(63,64,71,.1)}
  .bk-input[readonly]{background:#f8fafc;color:#64748b}
  .bk-input.is-invalid{border-color:#ef4444}.bk-field-error{display:block;font-size:12px;color:#ef4444;margin-top:4px;min-height:16px}
  .bk-field{margin-bottom:16px}.bk-field:last-child{margin-bottom:0}
  .bk-row{display:flex;gap:16px}.bk-half{flex:1}
  .bk-doc-preview{display:flex;align-items:center;gap:14px;padding:14px 18px;background:#f8fafc;border:2px solid #e2e8f0;border-radius:12px;margin-top:12px;animation:bkSlide .3s ease}
  .bk-doc-preview.hidden{display:none}
  @keyframes bkSlide{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:translateY(0)}}
  .bk-doc-preview img{width:56px;height:56px;border-radius:50%;object-fit:cover;border:2px solid #e2e8f0}
  .bk-doc-preview .initials{width:56px;height:56px;border-radius:50%;background:#3f4047;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:18px}
  .bk-doc-preview h4{font-weight:600;font-size:14px;color:#1e293b;margin:0 0 2px}.bk-doc-preview p{font-size:12px;color:#64748b;margin:0 0 4px}
  .bk-doc-badges{display:flex;flex-wrap:wrap;gap:8px}.bk-doc-badges span{font-size:11px;color:#64748b;display:inline-flex;align-items:center;gap:3px;background:#fff;padding:2px 8px;border-radius:16px;border:1px solid #e2e8f0}
  .bk-doc-badges .bi-star-fill{color:#f59e0b}
  .bk-slots-area{margin-top:8px}.bk-day-row{margin-bottom:16px}
  .bk-day-label{font-weight:600;font-size:13px;color:#1e293b;margin-bottom:8px;display:flex;align-items:center;gap:6px}
  .bk-day-label small{font-weight:400;color:#94a3b8}
  .bk-slots-grid{display:flex;flex-wrap:wrap;gap:8px}
  .bk-slot{padding:8px 14px;border:2px solid #e2e8f0;border-radius:8px;background:#fff;font-size:13px;font-weight:600;color:#475569;cursor:pointer;transition:all .2s;text-align:center}
  .bk-slot:hover:not(:disabled){border-color:#3f4047;background:#fafafa;transform:translateY(-1px)}
  .bk-slot.selected{border-color:#3f4047;background:#3f4047;color:#fff;box-shadow:0 3px 10px rgba(63,64,71,.2)}
  .bk-slot:disabled{background:#f8fafc;color:#cbd5e1;cursor:not-allowed;border-color:#f1f5f9;text-decoration:line-through}
  .bk-no-slots{color:#94a3b8;font-size:13px;font-style:italic}
  .bk-slots-loading{text-align:center;padding:24px;color:#94a3b8;font-size:13px}
  .bk-actions{display:flex;justify-content:space-between;align-items:center;margin-top:8px;margin-bottom:20px}
  .bk-btn{display:inline-flex;align-items:center;gap:8px;padding:12px 24px;border-radius:10px;font-weight:600;font-size:14px;border:none;cursor:pointer;text-decoration:none;transition:all .2s;font-family:inherit}
  .bk-btn-primary{background:#3f4047;color:#fff}.bk-btn-primary:hover{background:#2d2e33;transform:translateY(-1px);box-shadow:0 4px 14px rgba(63,64,71,.25);color:#fff}
  .bk-btn-back{background:#f1f5f9;color:#475569}.bk-btn-back:hover{background:#e2e8f0;color:#1e293b}
  .bk-btn-confirm{background:linear-gradient(135deg,#10b981,#059669);color:#fff;padding:13px 28px;font-size:15px}.bk-btn-confirm:hover{transform:translateY(-1px);box-shadow:0 6px 18px rgba(16,185,129,.3);color:#fff}
  .bk-sidebar{background:#fff;border-radius:14px;box-shadow:0 2px 12px rgba(0,0,0,.05);padding:22px;position:sticky;top:100px}
  .bk-sidebar h5{font-weight:600;font-size:15px;color:#1e293b;margin-bottom:16px;padding-bottom:12px;border-bottom:2px solid #f1f5f9}
  .bk-side-row{display:flex;justify-content:space-between;align-items:center;padding:8px 0;font-size:13px}
  .bk-side-row span{color:#94a3b8}.bk-side-row strong{color:#1e293b;text-align:right;max-width:58%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
  .bk-side-fee{margin-top:6px;padding-top:10px;border-top:2px solid #f1f5f9}.bk-side-fee strong{font-size:15px;color:#3f4047}
  /* Modal */
  .bk-overlay{display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(15,23,42,.55);backdrop-filter:blur(3px);z-index:9999;align-items:center;justify-content:center;padding:20px}
  .bk-overlay.open{display:flex}
  .bk-modal{background:#fff;border-radius:18px;box-shadow:0 20px 50px rgba(0,0,0,.18);max-width:500px;width:100%;overflow:hidden;animation:bkModalIn .3s ease}
  @keyframes bkModalIn{from{opacity:0;transform:translateY(18px) scale(.97)}to{opacity:1;transform:translateY(0) scale(1)}}
  .bk-modal-head{display:flex;justify-content:space-between;align-items:center;padding:20px 24px;border-bottom:1px solid #f1f5f9}
  .bk-modal-head h3{font-weight:600;font-size:17px;color:#1e293b;margin:0;display:flex;align-items:center;gap:8px}
  .bk-modal-close{background:#f1f5f9;border:none;width:34px;height:34px;border-radius:9px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:#64748b;transition:all .2s}.bk-modal-close:hover{background:#e2e8f0;color:#1e293b}
  .bk-modal-body{padding:20px 24px}
  .bk-rev-grid{display:grid;grid-template-columns:1fr 1fr;gap:0}
  .bk-rev-item{padding:10px 0;border-bottom:1px solid #f1f5f9}.bk-rev-item:nth-last-child(-n+2){border-bottom:none}
  .bk-rev-item span{display:block;font-size:11px;color:#94a3b8;text-transform:uppercase;letter-spacing:.4px;margin-bottom:3px}.bk-rev-item strong{font-size:13px;color:#1e293b}
  .bk-modal-foot{display:flex;justify-content:space-between;align-items:center;padding:16px 24px;border-top:1px solid #f1f5f9;background:#fafbfc}
  .bk-alert{display:flex;align-items:flex-start;gap:10px;border-radius:10px;padding:14px 18px;margin-bottom:20px;font-size:13px}
  .bk-alert-error{background:#fef2f2;border:1px solid #fecaca;color:#991b1b}.bk-alert-error i{font-size:18px;flex-shrink:0;margin-top:1px}
  .bk-alert p{margin:0 0 2px}
  @media(max-width:991px){.bk-sidebar{position:static}}
  @media(max-width:575px){.bk-section{padding-top:100px}.bk-row{flex-direction:column;gap:0}.bk-actions{flex-direction:column;gap:10px}.bk-btn{width:100%;justify-content:center}.bk-rev-grid{grid-template-columns:1fr}.bk-modal-foot{flex-direction:column;gap:8px}.bk-modal-foot .bk-btn{width:100%;justify-content:center}}
</style>

<section class="bk-section section">
  <div class="container">
    <div class="bk-title reveal">
      <h2>Book an Appointment</h2>
      <p>Fill in the details below to schedule your visit</p>
    </div>

    @if($errors->any())
      <div class="bk-alert bk-alert-error reveal">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <div>@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>
      </div>
    @endif

    <div class="row g-4 justify-content-center">
      <div class="col-lg-8">
        <form action="{{ route('website.book.store') }}" method="POST" id="bookingForm" novalidate>
          @csrf

          {{-- Section 1: Select Doctor --}}
          <div class="bk-card reveal">
            <div class="bk-card-head">
              <i class="bi bi-person-badge"></i>
              <div><h3>Select Doctor</h3><p>Choose department and doctor</p></div>
            </div>
            <div class="bk-card-body">
              <div class="bk-field">
                <label class="bk-label" for="department_filter">Department</label>
                <select id="department_filter" class="bk-select">
                  <option value="">All Departments</option>
                  @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ $preselectedDoctor && $preselectedDoctor->department_id == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="bk-field">
                <label class="bk-label" for="doctor_id">Doctor <span class="text-danger">*</span></label>
                <select name="doctor_id" id="doctor_id" class="bk-select" required>
                  <option value="">-- Choose a Doctor --</option>
                  @foreach($doctors as $doc)
                    <option value="{{ $doc->id }}"
                      data-department="{{ $doc->department_id }}"
                      data-fee="{{ $doc->consultation_fee }}"
                      data-name="{{ $doc->name }}"
                      data-specialty="{{ $doc->specialty }}"
                      data-avatar="{{ $doc->avatar ? asset('storage/' . $doc->avatar) : '' }}"
                      data-dept-name="{{ $doc->department?->name }}"
                      data-rating="{{ $doc->rating }}"
                      {{ old('doctor_id', request('doctor_id')) == $doc->id ? 'selected' : '' }}>
                      {{ $doc->name }} — {{ $doc->specialty }} ({{ $doc->department?->name }})
                    </option>
                  @endforeach
                </select>
                <span class="bk-field-error" id="errDoctor"></span>
              </div>

              <div class="bk-doc-preview {{ $preselectedDoctor ? '' : 'hidden' }}" id="docPreview">
                <div id="previewAvatarWrap">
                  @if($preselectedDoctor?->avatar)
                    <img src="{{ asset('storage/' . $preselectedDoctor->avatar) }}" alt="">
                  @elseif($preselectedDoctor)
                    <div class="initials">{{ $preselectedDoctor->initials() }}</div>
                  @endif
                </div>
                <div>
                  <h4 id="prevName">{{ $preselectedDoctor?->name ?? '' }}</h4>
                  <p id="prevSpec">{{ $preselectedDoctor?->specialty ?? '' }}</p>
                  <div class="bk-doc-badges">
                    <span id="prevDept"><i class="bi bi-building"></i> {{ $preselectedDoctor?->department?->name ?? '' }}</span>
                    <span id="prevFee"><i class="bi bi-cash"></i> ${{ $preselectedDoctor ? number_format($preselectedDoctor->consultation_fee, 2) : '0.00' }}</span>
                    <span id="prevRating" style="{{ ($preselectedDoctor?->rating ?? 0) > 0 ? '' : 'display:none' }}"><i class="bi bi-star-fill"></i> {{ $preselectedDoctor ? number_format($preselectedDoctor->rating ?? 0, 1) : '' }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          {{-- Section 2: Choose Appointment Slot --}}
          <div class="bk-card reveal" style="transition-delay: 50ms">
            <div class="bk-card-head">
              <i class="bi bi-calendar3"></i>
              <div><h3>Choose an Appointment Slot</h3><p>Available slots from doctor's schedule</p></div>
            </div>
            <div class="bk-card-body">
              <input type="hidden" name="appointment_date" id="hiddenDate" value="{{ old('appointment_date') }}">
              <input type="hidden" name="start_time" id="hiddenTime" value="{{ old('start_time') }}">
              <span class="bk-field-error" id="errSlot"></span>

              <div id="slotsContainer">
                @if($preselectedDoctor && $weeklySlots->isNotEmpty())
                  <div class="bk-slots-area">
                    @foreach($weeklySlots as $day)
                      @php $d = $day['date']; $slots = $day['slots']; @endphp
                      @if($slots->isNotEmpty())
                        <div class="bk-day-row">
                          <div class="bk-day-label">{{ $d->format('l') }} <small>{{ $d->format('M d, Y') }}</small></div>
                          <div class="bk-slots-grid">
                            @foreach($slots as $slot)
                              <button type="button" class="bk-slot {{ !$slot['available'] ? '' : '' }}"
                                data-date="{{ $d->toDateString() }}" data-time="{{ $slot['time'] }}"
                                {{ $slot['available'] ? '' : 'disabled' }}
                                title="{{ $slot['reason'] ?? '' }}">
                                {{ $slot['label'] }}
                              </button>
                            @endforeach
                          </div>
                        </div>
                      @endif
                    @endforeach
                  </div>
                @else
                  <p class="bk-no-slots" id="noSlotsMsg">Select a doctor above to see available slots.</p>
                @endif
              </div>
            </div>
          </div>

          {{-- Section 3: Patient Information --}}
          <div class="bk-card reveal" style="transition-delay: 100ms">
            <div class="bk-card-head">
              <i class="bi bi-person-vcard"></i>
              <div><h3>Patient Information</h3><p>Your details and reason for visiting</p></div>
            </div>
            <div class="bk-card-body">
              <div class="bk-row">
                <div class="bk-field bk-half">
                  <label class="bk-label">Full Name</label>
                  <input type="text" class="bk-input" value="{{ auth()->user()->name }}" readonly>
                </div>
                <div class="bk-field bk-half">
                  <label class="bk-label">Email</label>
                  <input type="email" class="bk-input" value="{{ auth()->user()->email }}" readonly>
                </div>
              </div>
              <div class="bk-field">
                <label class="bk-label" for="phone_number">Phone Number <span class="text-danger">*</span></label>
                <input type="tel" name="phone_number" id="phone_number" class="bk-input @error('phone_number') is-invalid @enderror"
                  value="{{ old('phone_number', auth()->user()->patientProfile?->phone ?? '') }}"
                  placeholder="e.g. +1 555 123 4567" required>
                <span class="bk-field-error" id="errPhone"></span>
              </div>
              <div class="bk-field">
                <label class="bk-label" for="treatment">Reason / Treatment <span class="text-danger">*</span></label>
                <input type="text" name="treatment" id="treatment" class="bk-input @error('treatment') is-invalid @enderror"
                  value="{{ old('treatment') }}" placeholder="e.g. Routine Check-Up, Cardiac Consultation" required>
                <span class="bk-field-error" id="errTreatment"></span>
              </div>
              <div class="bk-field">
                <label class="bk-label" for="notes">Additional Notes</label>
                <textarea name="notes" id="notes" class="bk-input" rows="3" placeholder="Any additional information...">{{ old('notes') }}</textarea>
              </div>
            </div>
          </div>

          {{-- Actions --}}
          <div class="bk-actions reveal" style="transition-delay: 150ms">
            <a href="{{ url()->previous() }}" class="bk-btn bk-btn-back"><i class="bi bi-arrow-left"></i> Back</a>
            <button type="button" class="bk-btn bk-btn-primary" id="btnReview">Review Booking <i class="bi bi-arrow-right"></i></button>
          </div>

          {{-- Review Modal --}}
          <div class="bk-overlay" id="reviewModal">
            <div class="bk-modal">
              <div class="bk-modal-head">
                <h3><i class="bi bi-clipboard-check"></i> Review Your Appointment</h3>
                <button type="button" class="bk-modal-close" id="modalClose"><i class="bi bi-x-lg"></i></button>
              </div>
              <div class="bk-modal-body">
                <div class="bk-rev-grid">
                  <div class="bk-rev-item"><span>Doctor</span><strong id="revDoctor">—</strong></div>
                  <div class="bk-rev-item"><span>Department</span><strong id="revDept">—</strong></div>
                  <div class="bk-rev-item"><span>Date</span><strong id="revDate">—</strong></div>
                  <div class="bk-rev-item"><span>Time</span><strong id="revTime">—</strong></div>
                  <div class="bk-rev-item"><span>Patient</span><strong>{{ auth()->user()->name }}</strong></div>
                  <div class="bk-rev-item"><span>Phone</span><strong id="revPhone">—</strong></div>
                  <div class="bk-rev-item"><span>Treatment</span><strong id="revTreatment">—</strong></div>
                  <div class="bk-rev-item"><span>Fee</span><strong id="revFee">—</strong></div>
                </div>
              </div>
              <div class="bk-modal-foot">
                <button type="button" class="bk-btn bk-btn-back" id="modalEdit"><i class="bi bi-pencil"></i> Edit</button>
                <button type="submit" class="bk-btn bk-btn-confirm"><i class="bi bi-check-circle"></i> Confirm Booking</button>
              </div>
            </div>
          </div>
        </form>
      </div>

      {{-- Sidebar --}}
      <div class="col-lg-4">
        <div class="bk-sidebar reveal left">
          <h5><i class="bi bi-receipt me-2"></i>Summary</h5>
          <div class="bk-side-row" id="sideDoc"><span>Doctor</span><strong>—</strong></div>
          <div class="bk-side-row" id="sideDept"><span>Department</span><strong>—</strong></div>
          <div class="bk-side-row" id="sideDate"><span>Date</span><strong>—</strong></div>
          <div class="bk-side-row" id="sideTime"><span>Time</span><strong>—</strong></div>
          <div class="bk-side-row bk-side-fee" id="sideFee"><span>Fee</span><strong>—</strong></div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
  const deptFilter=document.getElementById('department_filter'),
    docSelect=document.getElementById('doctor_id'),
    hiddenDate=document.getElementById('hiddenDate'),
    hiddenTime=document.getElementById('hiddenTime'),
    phoneInput=document.getElementById('phone_number'),
    treatInput=document.getElementById('treatment'),
    preview=document.getElementById('docPreview'),
    slotsContainer=document.getElementById('slotsContainer'),
    modal=document.getElementById('reviewModal');

  let st={doctor:'',dept:'',specialty:'',fee:'',avatar:'',initials:'',date:'',time:'',timeLabel:''};

  // Department filter
  deptFilter.addEventListener('change',function(){
    const v=this.value;
    docSelect.querySelectorAll('option[data-department]').forEach(o=>{
      o.style.display=(!v||o.dataset.department===v)?'':'none';
      if(o.style.display==='none'&&o.selected){docSelect.value='';hidePreview();}
    });
  });

  // Doctor change
  docSelect.addEventListener('change',function(){
    const o=this.options[this.selectedIndex];
    if(!o||!o.value){hidePreview();return;}
    st.doctor=o.dataset.name||'';st.specialty=o.dataset.specialty||'';
    st.dept=o.dataset.deptName||'';st.fee=o.dataset.fee||'';
    st.avatar=o.dataset.avatar||'';
    st.initials=st.doctor.split(' ').map(w=>w[0]).join('').substring(0,2).toUpperCase();
    showPreview();loadSlots(o.value);updateSidebar();clearErr('errDoctor');
  });

  function showPreview(){
    const wrap=document.getElementById('previewAvatarWrap');
    wrap.innerHTML=st.avatar?'<img src="'+st.avatar+'" alt="">':'<div class="initials">'+st.initials+'</div>';
    document.getElementById('prevName').textContent=st.doctor;
    document.getElementById('prevSpec').textContent=st.specialty;
    document.getElementById('prevDept').innerHTML='<i class="bi bi-building"></i> '+st.dept;
    document.getElementById('prevFee').innerHTML='<i class="bi bi-cash"></i> $'+parseFloat(st.fee||0).toFixed(2);
    const r=docSelect.options[docSelect.selectedIndex].dataset.rating;
    const re=document.getElementById('prevRating');
    if(r&&parseFloat(r)>0){re.innerHTML='<i class="bi bi-star-fill"></i> '+parseFloat(r).toFixed(1);re.style.display='';}
    else re.style.display='none';
    preview.classList.remove('hidden');
  }
  function hidePreview(){preview.classList.add('hidden');st.doctor='';st.dept='';st.fee='';updateSidebar();}

  // Load slots via AJAX
  function loadSlots(doctorId){
    st.date='';st.time='';st.timeLabel='';hiddenDate.value='';hiddenTime.value='';
    slotsContainer.innerHTML='<p class="bk-slots-loading"><i class="bi bi-arrow-repeat"></i> Loading available slots...</p>';
    fetch('/api/doctors/'+doctorId+'/slots',{headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}})
      .then(r=>r.json())
      .then(data=>{
        if(!data.days||data.days.length===0){slotsContainer.innerHTML='<p class="bk-no-slots">No available slots for this doctor.</p>';return;}
        let html='<div class="bk-slots-area">';
        data.days.forEach(day=>{
          if(!day.slots||day.slots.length===0)return;
          html+='<div class="bk-day-row"><div class="bk-day-label">'+day.date_label+'</div><div class="bk-slots-grid">';
          day.slots.forEach(s=>{
            const dis=!s.available;
            html+='<button type="button" class="bk-slot" data-date="'+day.date+'" data-time="'+s.time+'"'+(dis?' disabled title="'+(s.reason||'Unavailable')+'"':'')+'>'+s.label+'</button>';
          });
          html+='</div></div>';
        });
        html+='</div>';
        slotsContainer.innerHTML=html;
        bindSlots();
      })
      .catch(()=>{slotsContainer.innerHTML='<p class="bk-no-slots">Failed to load slots. Please try again.</p>';});
  }

  // Bind slot clicks
  function bindSlots(){
    slotsContainer.querySelectorAll('.bk-slot').forEach(btn=>{
      btn.addEventListener('click',function(){
        if(this.disabled)return;
        slotsContainer.querySelectorAll('.bk-slot').forEach(b=>b.classList.remove('selected'));
        this.classList.add('selected');
        st.date=this.dataset.date;st.time=this.dataset.time;st.timeLabel=this.textContent.trim();
        hiddenDate.value=st.date;hiddenTime.value=st.time;
        updateSidebar();clearErr('errSlot');
      });
    });
  }
  bindSlots(); // bind initial server-rendered slots

  // Sidebar
  function updateSidebar(){
    setSide('sideDoc',st.doctor);setSide('sideDept',st.dept);
    const dl=st.date?new Date(st.date+'T00:00:00').toLocaleDateString('en-US',{weekday:'short',month:'short',day:'numeric'}):'';
    setSide('sideDate',dl);setSide('sideTime',st.timeLabel);
    setSide('sideFee',st.fee?'$'+parseFloat(st.fee).toFixed(2):'');
  }
  function setSide(id,v){const el=document.getElementById(id);if(!el)return;el.querySelector('strong').textContent=v||'—';}

  // Validation
  function validate(){
    let ok=true;
    if(!docSelect.value){showErr('errDoctor','Please select a doctor.');ok=false;}else clearErr('errDoctor');
    if(!hiddenDate.value||!hiddenTime.value){showErr('errSlot','Please select an appointment slot.');ok=false;}else clearErr('errSlot');
    if(!phoneInput.value.trim()||phoneInput.value.trim().length<7){showErr('errPhone','Please enter a valid phone number (min 7 digits).');ok=false;}else clearErr('errPhone');
    if(!treatInput.value.trim()){showErr('errTreatment','Please enter the reason for your visit.');ok=false;}else clearErr('errTreatment');
    return ok;
  }
  function showErr(id,m){const e=document.getElementById(id);if(e)e.textContent=m;}
  function clearErr(id){const e=document.getElementById(id);if(e)e.textContent='';}

  // Inline validation on blur
  phoneInput.addEventListener('blur',function(){
    if(!this.value.trim()||this.value.trim().length<7)showErr('errPhone','Please enter a valid phone number.');else clearErr('errPhone');
  });
  treatInput.addEventListener('blur',function(){
    if(!this.value.trim())showErr('errTreatment','Please enter the reason for your visit.');else clearErr('errTreatment');
  });

  // Review modal
  document.getElementById('btnReview').addEventListener('click',function(){
    if(!validate()){const f=document.querySelector('.bk-field-error:not(:empty)');if(f)f.scrollIntoView({behavior:'smooth',block:'center'});return;}
    document.getElementById('revDoctor').textContent=st.doctor;
    document.getElementById('revDept').textContent=st.dept;
    document.getElementById('revDate').textContent=st.date?new Date(st.date+'T00:00:00').toLocaleDateString('en-US',{weekday:'long',year:'numeric',month:'long',day:'numeric'}):'—';
    document.getElementById('revTime').textContent=st.timeLabel||'—';
    document.getElementById('revPhone').textContent=phoneInput.value;
    document.getElementById('revTreatment').textContent=treatInput.value||'—';
    document.getElementById('revFee').textContent=st.fee?'$'+parseFloat(st.fee).toFixed(2):'—';
    modal.classList.add('open');document.body.style.overflow='hidden';
  });
  document.getElementById('modalClose').addEventListener('click',closeModal);
  document.getElementById('modalEdit').addEventListener('click',closeModal);
  modal.addEventListener('click',function(e){if(e.target===modal)closeModal();});
  document.addEventListener('keydown',function(e){if(e.key==='Escape'&&modal.classList.contains('open'))closeModal();});
  function closeModal(){modal.classList.remove('open');document.body.style.overflow='';}

  // Init: fire doctor change if pre-selected
  if(docSelect.value)docSelect.dispatchEvent(new Event('change'));
  updateSidebar();
});
</script>
@endpush
