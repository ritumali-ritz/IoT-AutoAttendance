
const apiBase = 'api';
let classes = [];

async function apiPost(path, data){
  const res = await fetch(`${apiBase}/${path}`, {method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(data)});
  return res.json();
}
async function apiGet(path){ const res=await fetch(`${apiBase}/${path}`); return res.json(); }

// navigation
document.querySelectorAll('#sidebar .nav-link').forEach(a=>a.addEventListener('click', (e)=>{
  e.preventDefault(); document.querySelectorAll('.view').forEach(v=>v.classList.add('d-none'));
  const view = e.target.dataset.view || 'dashboard'; document.getElementById('view-'+view).classList.remove('d-none');
  document.querySelectorAll('#sidebar .nav-link').forEach(x=>x.classList.remove('active')); e.target.classList.add('active');
}));

document.getElementById('btnLogout')?.addEventListener('click', async ()=>{ await apiGet('logout.php'); window.location='admin_login.php.'; });

// load classes and initial data
async function loadClasses(){
  const res = await apiGet('classes.php');
  classes = res;
  const sel = document.getElementById('classSelect');
  const rsel = document.getElementById('reportClass');
  const ssel = document.getElementById('studentClass');
  const classList = document.getElementById('classList');
  sel.innerHTML = '<option value="">All Classes</option>';
  rsel.innerHTML = '<option value="">All Classes</option>';
  ssel.innerHTML = '<option value="">Select class</option>';
  classList.innerHTML='';
  res.forEach(c=>{
    sel.insertAdjacentHTML('beforeend', `<option value="${c.id}">${c.class_name}</option>`);
    rsel.insertAdjacentHTML('beforeend', `<option value="${c.id}">${c.class_name}</option>`);
    ssel.insertAdjacentHTML('beforeend', `<option value="${c.id}">${c.class_name}</option>`);
    classList.insertAdjacentHTML('beforeend', `<li class="list-group-item d-flex justify-content-between">${c.class_name}<button class="btn btn-sm btn-danger" onclick="deleteClass(${c.id})">Delete</button></li>`);
  });
}

// dashboard stats + chart
async function loadDashboard(){
  const cid = document.getElementById('classSelect').value || '';
  const res = await apiGet(`dashboard.php?class_id=${cid}`);
  document.getElementById('totalStudents').textContent = res.total_students;
  document.getElementById('presentToday').textContent = res.present_today;
  document.getElementById('attPercent').textContent = res.att_percent + '%';
  // chart
  const ctx = document.getElementById('attendanceChart').getContext('2d');
  if(window.attChart) window.attChart.destroy();
  window.attChart = new Chart(ctx, {type:'line', data:{labels:res.labels, datasets:[{label:'Present', data:res.data, fill:true}]}, options:{responsive:true}});
}

// attendance list
async function loadAttendance(){
  const res = await apiGet('fetch_attendance.php');
  const tbody = document.querySelector('#attendanceTable tbody');
  tbody.innerHTML='';
  res.forEach((r,i)=>{
    tbody.insertAdjacentHTML('beforeend', `<tr><td>${i+1}</td><td>${r.roll_no}</td><td>${r.name}</td><td>${r.class_name}</td><td>${r.date}</td><td>${r.time}</td><td><button class="btn btn-sm btn-warning" onclick="editAttendance(${r.id})">Edit</button></td></tr>`);
  });
}

// Manage - add class/student
document.getElementById('addClassBtn').addEventListener('click', async ()=>{
  const name = document.getElementById('newClass').value.trim(); if(!name){alert('Enter class name'); return;}
  await apiPost('add_class.php',{class_name:name}); document.getElementById('newClass').value=''; loadClasses();
});
document.getElementById('addStudentBtn').addEventListener('click', async ()=>{
  const roll = document.getElementById('rollInput').value.trim();
  const name = document.getElementById('nameInput').value.trim();
  const cid = document.getElementById('studentClass').value;
  if(!roll||!name||!cid){alert('Fill all fields'); return;}
  await apiPost('add_student.php',{roll_no:roll,name:name,class_id:cid});
  document.getElementById('rollInput').value=''; document.getElementById('nameInput').value=''; loadStudents();
});

async function loadStudents(){
  const res = await apiGet('students.php');
  const div = document.getElementById('studentList'); div.innerHTML='';
  res.forEach(s=> div.insertAdjacentHTML('beforeend', `<div class="p-2 border mb-1">${s.roll_no} - ${s.name} <small class="text-muted">(${s.class_name})</small></div>`));
}

async function deleteClass(id){ if(!confirm('Delete class?')) return; await apiPost('delete_class.php',{id}); loadClasses(); }

// edit attendance
function editAttendance(id){
  const newStatus = prompt('Mark Present or Absent? (Present/Absent)');
  if(!newStatus) return;
  apiPost('edit_attendance.php',{id, status:newStatus}).then(()=> loadAttendance());
}

// reports
document.getElementById('genReport').addEventListener('click', async ()=>{
  const cid = document.getElementById('reportClass').value;
  const type = document.getElementById('reportType').value;
  const from = document.getElementById('reportFrom').value;
  const to = document.getElementById('reportTo').value;
  const res = await apiGet(`report.php?class_id=${cid}&type=${type}&from=${from}&to=${to}`);
  const rdiv = document.getElementById('reportResult');
  rdiv.innerHTML = `<button class="btn btn-sm btn-success" onclick="downloadCSV()">Download CSV</button><pre>${JSON.stringify(res.data, null, 2)}</pre>`;
  window.reportData = res.data;
});

function downloadCSV(){
  const data = window.reportData || []; if(!data.length){ alert('No data'); return; }
  const rows = [['RollNo','Name','Class','Date','Status']].concat(data.map(r=>[r.roll_no,r.name,r.class_name,r.date,r.status]));
  const csv = rows.map(r=> r.map(c=>`"${String(c).replace(/"/g,'""')}"`).join(',')).join('\n');
  const blob = new Blob([csv], {type:'text/csv'}); const url=URL.createObjectURL(blob);
  const a=document.createElement('a'); a.href=url; a.download='report.csv'; a.click(); URL.revokeObjectURL(url);
}

// initial load
(async function(){
  await loadClasses(); await loadStudents(); await loadDashboard(); await loadAttendance();
  document.getElementById('classSelect').addEventListener('change', ()=>{ loadDashboard(); });
  document.getElementById('refreshBtn').addEventListener('click', loadAttendance);
  setInterval(()=>{ loadDashboard(); loadAttendance(); }, 5000);
})();
