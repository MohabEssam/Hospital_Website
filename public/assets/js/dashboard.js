$(function () {
  // =============================
  // Patient Overview Chart
  // =============================
  const patientChart = new ApexCharts(document.querySelector('#patientOverviewChart'), {
    chart:{ type:'bar', height:200, toolbar:{ show:false } },
    series:[
      { name:'Child',   data:[18,22,15,28,20,16,24,19] },
      { name:'Adult',   data:[30,25,32,27,35,28,22,30] },
      { name:'Elderly', data:[8,6,10,7,9,5,12,8] }
    ],
    colors:['#1a2e4a','#3eb8b0','#b2e0f5'],
    plotOptions:{ bar:{ columnWidth:'60%', borderRadius:3 } },
    dataLabels:{ enabled:false },
    xaxis:{ 
      categories:['4 Jul','5 Jul','6 Jul','7 Jul','8 Jul','9 Jul','10 Jul','11 Jul'],
      labels:{ style:{ fontSize:'10px' } }
    },
    yaxis:{ labels:{ style:{ fontSize:'10px' } } },
    legend:{ show:false },
    grid:{ borderColor:'#f0f0f0' }
  });
  patientChart.render();


  // =============================
  // Revenue Chart
  // =============================
  const revenueData = {
    week:  { cats:['Sun','Mon','Tue','Wed','Thu','Fri','Sat'], income:[800,950,1100,900,1200,1495,1050], expense:[600,700,650,800,700,750,680] },
    month: { cats:['W1','W2','W3','W4'], income:[5200,6100,5800,7200], expense:[3800,4200,4100,5000] },
    year:  { cats:['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'], income:[14000,12500,16000,15000,17500,16200,18000,17000,15500,16800,19000,21000], expense:[10000,9500,11000,10500,12000,11200,12500,11800,10800,11500,13000,14500] }
  };

  const revenueChart = new ApexCharts(document.querySelector('#revenueChart'), {
    chart:{ type:'line', height:200, toolbar:{ show:false } },
    series:[
      { name:'Income', data:revenueData.week.income },
      { name:'Expense', data:revenueData.week.expense }
    ],
    colors:['#1a2e4a','#3eb8b0'],
    stroke:{ curve:'smooth', width:2 },
    dataLabels:{ enabled:false },
    xaxis:{ 
      categories:revenueData.week.cats,
      labels:{ style:{ fontSize:'10px' } }
    },
    yaxis:{ 
      labels:{ 
        formatter: v => '$'+v,
        style:{ fontSize:'10px' }
      }
    },
    legend:{ show:false },
    grid:{ borderColor:'#f0f0f0' },
    markers:{ size:0 }
  });
  revenueChart.render();


  // =============================
  // Switch Revenue Function
  // =============================
  window.switchRevenue = function(btn, period) {

    document.querySelectorAll('#revenueToggle button').forEach(b => {
      b.className = 'btn btn-outline-secondary';
      b.style.cssText = 'font-size:.72rem;padding:3px 10px;';
    });

    btn.className = 'btn btn-dark';
    btn.style.cssText = 'font-size:.72rem;padding:3px 10px;';

    const d = revenueData[period];

    revenueChart.updateOptions({
      xaxis:{ categories: d.cats }
    });

    revenueChart.updateSeries([
      { name:'Income', data: d.income },
      { name:'Expense', data: d.expense }
    ]);
  };


  // =============================
  // Department Donut Chart
  // =============================
  const deptChart = new ApexCharts(document.querySelector('#deptDonutChart'), {
    chart:{ type:'donut', height:180 },
    series:[35,28,20,17],
    labels:['Emergency','General','Internal','Other'],
    colors:['#1a2e4a','#3eb8b0','#b2e0f5','#dee2e6'],
    dataLabels:{ enabled:false },
    legend:{ show:false },
    plotOptions:{
      pie:{
        donut:{
          size:'72%',
          labels:{
            show:true,
            total:{
              show:true,
              label:'Overall',
              fontSize:'11px',
              color:'#adb5bd',
              formatter:()=>'1,890'
            },
            value:{
              fontSize:'18px',
              fontWeight:700,
              color:'#1a2e4a'
            }
          }
        }
      }
    }
  });
  deptChart.render();


  // =============================
  // Highlight Department
  // =============================
  window.highlightDept = function(idx) {
    deptChart.toggleDataPointSelection(idx);
  };


  // =============================
  // Update Dropdown Label
  // =============================
  window.updateOverviewLabel = function(el, label) {
    document.getElementById('overviewDropdown').innerHTML =
      label + ' <i class="fas fa-chevron-down ms-1" style="font-size:.6rem;"></i>';
  };


  

});


const days = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
let dateOffset = 0;
const baseDate = 12; 

function renderDateStrip() {
  const strip = document.getElementById('dateStrip');
  strip.innerHTML = '';
  for (let i = 0; i < 14; i++) {
    const num = baseDate + dateOffset + i;
    const dayIdx = (3 + dateOffset + i) % 7; 
    const isActive = (num === 20 && dateOffset === 0) || (i === 8 && dateOffset !== 0);
    const el = document.createElement('div');
    el.className = 'text-center px-2 py-1 rounded-2' + (isActive ? ' bg-dark text-white' : '');
    el.style.cssText = 'cursor:pointer;min-width:46px;flex-shrink:0;';
    el.innerHTML = `<span class="d-block${isActive ? '' : ' text-muted'}" style="font-size:.6rem;">${days[dayIdx]}</span><span class="fw-bold" style="font-size:.82rem;">${num}</span>`;
    el.addEventListener('click', function() {
      strip.querySelectorAll('div').forEach(d => { d.className = 'text-center px-2 py-1 rounded-2'; d.style.cssText = 'cursor:pointer;min-width:46px;flex-shrink:0;'; d.querySelector('span').className = 'd-block text-muted'; });
      this.className = 'text-center px-2 py-1 rounded-2 bg-dark text-white';
      this.style.cssText = 'cursor:pointer;min-width:46px;flex-shrink:0;';
    });
    strip.appendChild(el);
  }
}
function shiftDates(dir) { dateOffset += dir * 7; renderDateStrip(); }
renderDateStrip();

/* ── Mini Calendar ── */
const monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
let miniMonth = 6; 
let miniYear = 2026;
let selectedDay = 12;

function renderMiniCal() {
  document.getElementById('miniCalTitle').textContent = monthNames[miniMonth] + ' ' + miniYear;
  const firstDay = new Date(miniYear, miniMonth, 1).getDay();
  const daysInMonth = new Date(miniYear, miniMonth + 1, 0).getDate();
  const prevDays = new Date(miniYear, miniMonth, 0).getDate();

  let html = '<table class="w-100" style="border-collapse:collapse;"><thead><tr>';
  ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'].forEach(d => {
    html += `<th class="text-center text-muted fw-semibold" style="font-size:.7rem;padding:4px 2px;">${d}</th>`;
  });
  html += '</tr></thead><tbody>';

  let day = 1, nextDay = 1, cell = 0;
  for (let r = 0; r < 6; r++) {
    html += '<tr>';
    for (let c = 0; c < 7; c++) {
      if (r === 0 && c < firstDay) {
        const pd = prevDays - firstDay + c + 1;
        html += `<td class="text-center text-muted" style="font-size:.72rem;padding:4px 2px;">${pd}</td>`;
      } else if (day > daysInMonth) {
        html += `<td class="text-center text-muted" style="font-size:.72rem;padding:4px 2px;">${nextDay++}</td>`;
      } else {
        const isSel = day === selectedDay;
        const isToday = day === 13 && miniMonth === 6 && miniYear === 2026;
        const d = day;
        let inner;
        if (isSel) inner = `<span class="d-inline-flex align-items-center justify-content-center rounded-circle fw-bold" style="width:22px;height:22px;background:#3eb8b0;color:#fff;font-size:.65rem;">${d}</span>`;
        else if (isToday) inner = `<span class="d-inline-flex align-items-center justify-content-center rounded-circle fw-bold" style="width:22px;height:22px;background:#1a2e4a;color:#fff;font-size:.65rem;">${d}</span>`;
        else inner = d;
        html += `<td class="text-center" style="font-size:.72rem;padding:4px 2px;cursor:pointer;" onclick="selectCalDay(${d})">${inner}</td>`;
        day++;
      }
    }
    html += '</tr>';
    if (day > daysInMonth && r < 5) break;
  }
  html += '</tbody></table>';
  document.getElementById('miniCalBody').innerHTML = html;
}

function selectCalDay(d) {
  selectedDay = d;
  const dayNames = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
  const dow = new Date(miniYear, miniMonth, d).getDay();
  document.getElementById('scheduleTitle').textContent = `${dayNames[dow]}, ${d} ${monthNames[miniMonth].slice(0,3)}`;
  renderMiniCal();
}

function shiftMiniCal(dir) {
  miniMonth += dir;
  if (miniMonth > 11) { miniMonth = 0; miniYear++; }
  if (miniMonth < 0)  { miniMonth = 11; miniYear--; }
  renderMiniCal();
}

renderMiniCal();

