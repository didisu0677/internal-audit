<link rel="stylesheet" href="https://cdn.datatables.net/2.3.3/css/dataTables.dataTables.css">

<div id="page_loading" style="
    display: none;
    position: fixed;
    top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(255,255,255,0.9);
    backdrop-filter: blur(5px);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
">
  <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;"></div>
  <div class="text-muted font-weight-medium">Memuat data...</div>
</div>

<div class="container-fluid py-4">
  <!-- Header Section -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="card border-0 shadow-sm"
        style="border-radius: 15px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="card-body py-4">
          <div class="row align-items-center">
            <div class="col-md-8">
              <h3 class="mb-1 font-weight-bold text-white">Internal Audit Dashboard</h3>
              <p class="mb-0 text-white-50">Comprehensive audit monitoring and reporting system</p>
            </div>
            <div class="col-md-4 text-md-right">
              <div class="bg-white rounded p-3 d-inline-block">
                <label for="year" class="form-label mb-2 text-muted font-weight-medium small">Filter Tahun</label>
                <select id="year" name="year" class="form-control border-0 bg-light" style="border-radius: 8px;">
                  <?php
                  $current = date('Y');
                  for ($y = $current; $y >= $year; $y--) {
                    echo '<option value="' . $y . '">' . $y . '</option>';
                  }
                  ?>
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Main Dashboard Cards -->
  <div class="row mb-4">
    <!-- AUDIT COMPLETED VS PLAN -->
    <div class="col-lg-3 col-md-6 mb-4">
      <div class="card border-0 shadow-sm h-100" style="border-radius: 15px; transition: transform 0.2s;">
        <div class="card-body text-center p-4">
          <div class="mb-3">
            <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center"
              style="width: 40px; height: 40px;">
              <i class="fas fa-chart-pie text-white"></i>
            </div>
          </div>
          <h6 class="font-weight-bold text-dark mb-3">AUDIT COMPLETED VS PLAN</h6>
          <div style="height: 200px;">
            <canvas id="audit_plan_chart"></canvas>
          </div>
          <div id="audit_plan_legend" class="mt-3" style="display: flex; justify-content: center; flex-wrap: wrap; gap: 10px;">
          </div>
        </div>
      </div>
    </div>

    <!-- STATUS FINDING CONTROL -->
    <div class="col-lg-6 col-md-12 mb-4">
      <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
        <div class="card-body p-4">
          <div class="text-center mb-3">
            <div class="bg-info rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
              style="width: 40px; height: 40px;">
              <i class="fas fa-search text-white"></i>
            </div>
            <h6 class="font-weight-bold text-dark mb-0">STATUS FINDING CONTROL</h6>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div id="finding_legend" class="mb-3" style="display: flex; justify-content: center; flex-wrap: wrap; gap: 8px;"></div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-8">
              <div style="overflow-x:auto; width:100%;">
                <div style="width: max-content; min-width:100%;">
                  <canvas id="findingBar" style="height:300px;"></canvas>
                </div>
              </div>
            </div>
            <div class="col-md-4 d-flex align-items-center">
              <div style="height: 200px; width: 100%;">
                <canvas id="findingPie"></canvas>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- QUESTIONER RESULT -->
    <div class="col-lg-3 col-md-6 mb-4">
      <div class="card border-0 shadow-sm h-100 text-center" style="border-radius: 15px;">
        <div class="card-body p-4">
          <div class="mb-3">
            <div class="bg-warning rounded-circle d-inline-flex align-items-center justify-content-center"
              style="width: 40px; height: 40px;">
              <i class="fas fa-question-circle text-white"></i>
            </div>
          </div>
          <h6 class="font-weight-bold text-dark mb-3">QUESTIONER RESULT</h6>
          <div style="width:100%;">
            <div style="width: max-content; min-width:100%;">
              <canvas id="question_gauge" style="height:100px;"></canvas>
              <h2 class="display-4 font-weight-bold" id="question_result"></h2>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Secondary Dashboard Cards -->
  <div class="row mb-4">
    <!-- CAPA PLAN PROGRESS -->
    <div class="col-lg-3 col-md-6 mb-4">
      <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
        <div class="card-body text-center p-4">
          <div class="mb-3">
            <div class="bg-success rounded-circle d-inline-flex align-items-center justify-content-center"
              style="width: 40px; height: 40px;">
              <i class="fas fa-tasks text-white"></i>
            </div>
          </div>
          <h6 class="font-weight-bold text-dark mb-3">CAPA PLAN PROGRESS</h6>
          <h2 id="capa_percentage" class="text-success font-weight-bold mb-3" style="font-size: 2.5rem;">0%</h2>
          <div class="mb-3">
            <span id="capa_completed_badge" class="badge badge-primary px-3 py-2 mr-1" style="border-radius: 20px;">0 Completed</span>
            <span id="capa_progress_badge" class="badge badge-warning px-3 py-2" style="border-radius: 20px;">0 In-progress</span>
          </div>
          <div class="bg-light rounded p-3">
            <table class="table table-borderless table-sm mb-0 text-left">
              <tr>
                <td class="text-muted">Head Office</td>
                <td id="capa_ho" class="font-weight-medium">0</td>
              </tr>
              <tr>
                <td class="text-muted">Factory</td>
                <td id="capa_factory" class="font-weight-medium">0</td>
              </tr>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- RISK MONITORING -->
    <div class="col-lg-6 col-md-12 mb-4">
      <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
        <div class="card-body p-4">
          <div class="text-center mb-3">
            <div class="bg-danger rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
              style="width: 40px; height: 40px;">
              <i class="fas fa-exclamation-triangle text-white"></i>
            </div>
            <h6 class="font-weight-bold text-dark mb-0">RISK MONITORING</h6>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div id="risk_legend" class="mb-3" style="display: flex; justify-content: center; flex-wrap: wrap; gap: 8px;"></div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-8">
              <div style="overflow-x:auto; width:100%;">
                <div style="width: max-content; min-width:100%;">
                  <canvas id="riskBar" style="height:300px;"></canvas>
                </div>
              </div>
            </div>
            <div class="col-md-4 d-flex align-items-center">
              <div style="height: 200px; width: 100%;">
                <canvas id="riskPie"></canvas>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
      <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
        <div class="card-body text-center p-4">
          <!-- <h6 class="font-weight-bold text-dark mb-4">RISK OVERVIEW</h6> -->
          <div style="height: 400px;">
            <canvas id="question_bar"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- FINDING & CAPA MONITORING -->
  <div class="row">
    <div class="col-12">
      <div class="card border-0 shadow-sm" style="border-radius: 15px;">
        <div class="card-header bg-white border-0 py-4"
          style="border-top-left-radius: 15px; border-top-right-radius: 15px;">
          <div class="row align-items-center">
            <div class="col-md-6">
              <div class="d-flex align-items-center">
                <div class="bg-dark rounded-circle d-inline-flex align-items-center justify-content-center mr-3"
                  style="width: 40px; height: 40px;">
                  <i class="fas fa-table text-white"></i>
                </div>
                <div>
                  <h5 class="font-weight-bold mb-1 text-dark">FINDING & CAPA MONITORING</h5>
                  <p class="text-muted mb-0 small">Detailed monitoring and tracking overview</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0" id="finding_capa">
              <thead style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                <tr>
                  <th rowspan="2" class="text-center align-middle border-right font-weight-bold text-dark py-3">Site
                  </th>
                  <th rowspan="2" class="text-center align-middle border-right font-weight-bold text-dark py-3">Dept
                  </th>
                  <th rowspan="2" class="text-center align-middle border-right font-weight-bold text-dark py-3">
                    Aktivitas</th>
                  <th colspan="3" class="text-center font-weight-bold text-primary border-bottom border-right py-2">
                    Finding Status</th>
                  <th colspan="6" class="text-center font-weight-bold text-success py-2">CAPA Status</th>
                </tr>
                <tr style="background-color: #fafbfc;">
                  <th class="text-center small font-weight-medium text-muted py-2">Open</th>
                  <th class="text-center small font-weight-medium text-muted py-2">Delivered</th>
                  <th class="text-center small font-weight-medium text-muted py-2 border-right">Close</th>
                  <th class="text-center small font-weight-medium text-muted py-2">Delivered</th>
                  <th class="text-center small font-weight-medium text-muted py-2">On Progress</th>
                  <th class="text-center small font-weight-medium text-muted py-2">Done</th>
                  <th class="text-center small font-weight-medium text-muted py-2">Pending</th>
                  <th class="text-center small font-weight-medium text-muted py-2">Cancelled</th>
                  <th class="text-center small font-weight-medium text-muted py-2">Deadline Exceeded</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>
<script src="https://cdn.datatables.net/2.3.3/js/dataTables.js"> </script>

<script>

  const COLOR_MAP = [
    '#6EF3F2', // Implementasi tidak sesuai 
    '#40BCD8', // Design kurang efektif 
    '#2A80B9', // Design tidak ditemukan 
    '#6B5C99', // Improvement
    '#7B6EA8', // Critical 
    '#5B8CCB', // Major
    '#6ECFF6', // Moderate
    '#6EF3F2', // Minor
  ];
  let auditPlanChart = null;
  let findingControlPie = null;
  let findingControlBar = null;
  let riskControlPie = null;
  let riskControlBar = null;
  let questionerGauge = null;
  let questionerBar = null;


  $(document).ready(function () {
    $('#page_loading').show();
    $.when(
      // generateFindingAndCapa(),
      // get_monitoring_capa(),
      generateAuditPlanChart(),
      generateCapaPlanProgress(),
      generateFindingControlPie(),
      generateFindingControlBar(),
      generateRiskMonitoringBar(),
      generateRiskMonitoringPie(),
      generateQuestionerGauge()
    ).done(function () {
      $('#page_loading').hide();
      $('#finding_capa').DataTable().ajax.reload();
    });
  });

  $(document).on('change', '#year', function () {
    $('#page_loading').show();
    $.when(
      // generateFindingAndCapa(),
      // get_monitoring_capa(),
      generateAuditPlanChart(),
      generateCapaPlanProgress(),
      generateFindingControlPie(),
      generateFindingControlBar(),
      generateRiskMonitoringBar(),
      generateRiskMonitoringPie(),
      generateQuestionerGauge()
    ).done(function () {
      $('#page_loading').hide();
      $('#finding_capa').DataTable().ajax.reload();
    });
  });

  $('#finding_capa').DataTable({
    autoWidth: false,
    ajax: {
      url: base_url + 'dashboard/get_auditor_monitoring_capa',
      type: 'POST',
      data: function (d) {
        d.year = $('#year').val();
      },
      dataSrc: '' // karena  `render($data, 'json')` udah dalam bentuk json jadi tinggal pakai
    },
    columns: [
      { data: 'site', width: "1%", className: "text-nowrap" },
      { data: 'dept', width: "150px", className: "text-nowrap text-center" },
      { data: 'aktivitas',
        className: "text-center"
      },
      { data: 'finding.0',
        className: "text-cente text-",
        render: renderZero()
      },
      { data: 'finding.1',
        className: "text-center text-warning",
        render: renderZero()
      },
      { data: 'finding.2',
        className: "text-center text-primary",
        render: renderZero()
      },
      { data: 'capa.1',
        className: "text-center text-warning",
        render: renderZero()
      },
      { data: 'capa.2',
        className: "text-center text-info",
        render: renderZero()
      },
      { data: 'capa.3',
        className: "text-center text-primary",
        render: renderZero()
      },
      { data: 'capa.4',
        className: "text-center text-danger",
        render: renderZero()
      },
      { data: 'capa.5',
        className: "text-center text-warning",
        render: renderZero()
      },
      { data: 'capa.6',
        className: "text-center text-danger",
        render: renderZero()
      },
    ]
  });

  function renderZero(val) {
      return function (d) {
        return d == 0 ? '-' : d;
      };
  }

function generateCapaPlanProgress() {
    let year = $('#year').val();
    return $.ajax({
      url: base_url + 'dashboard/get_capa_plan_progress',
      type: 'post',
      data: {
        year: year
      },
      success: function (res) {
        let completed = parseInt(res.completed || 0);
        let total = parseInt(res.total || 0);
        let capa = parseInt(res.capa || 0);
        let factory = parseInt(res.factory || 0);
        let ho = parseInt(res.ho || 0);
        
        let inProgress = capa - completed;
        if (inProgress < 0) inProgress = 0;
        
        let percentage = total > 0 ? Math.round((completed / total) * 100) : 0;
        
        $('#capa_percentage').text(percentage + '%');
        $('#capa_completed_badge').text(completed + ' Completed');
        $('#capa_progress_badge').text(capa + ' In-progress');
        $('#capa_ho').text(ho);
        $('#capa_factory').text(factory);
      }
    });
  }
  function generateAuditPlanChart() {
    let year = $('#year').val();
    return $.ajax({
      url: base_url + 'dashboard/get_audit_plan_data',
      type: 'post',
      data: {
        year: year
      },
      success: function (res) {
        let completed = parseInt(res.completed || 0);
        let planned = parseInt(res.planned || 0) + parseInt(res.canceled || 0);

        let labels = ['Completed', 'Planned'];
        let datas = [completed, planned];
        let colors = ['#5B8CCB', '#6EF3F2'];

        let total = completed + planned;
        let completedPercentage = total > 0 ? Math.round((completed / total) * 100) : 0;
        let plannedPercentage = total > 0 ? Math.round((planned / total) * 100) : 0;

        let ctx = $('#audit_plan_chart');

        if (auditPlanChart !== null) {
          auditPlanChart.destroy();
        }

        auditPlanChart = new Chart(ctx, {
          type: 'doughnut',
          plugins: [ChartDataLabels],
          data: {
            labels: labels,
            datasets: [{
              label: 'Audit Plan',
              data: datas,
              backgroundColor: colors,
              borderColor: '#fff',
              borderWidth: 2
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              datalabels: {
                font: { weight: 'bold', size: 13 },
                color: '#fff',
                display: (ctx) => datas[ctx.dataIndex] > 0,
                formatter: (value, ctx) => {
                  if (value === 0) return '';
                  let percentage = ctx.dataIndex === 0 ? completedPercentage : plannedPercentage;
                  return percentage + '%';
                }
              },
              tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                cornerRadius: 8,
                titleFont: { size: 13, weight: 'bold' },
                bodyFont: { size: 12 },
                callbacks: {
                  label: (ctx) => {
                    const i = ctx.dataIndex;
                    let percentage = i === 0 ? completedPercentage : plannedPercentage;
                    return `${labels[i]}: ${datas[i]} (${percentage}%)`;
                  }
                }
              },
              legend: {
                display: false
              }
            }
          }
        });

        // Generate custom legend
        let legendHTML = '';
        labels.forEach((label, i) => {
          if (datas[i] > 0) {
            legendHTML += `
              <div style="display: flex; align-items: center; padding: 5px 10px; background: #f8f9fa; border-radius: 8px;">
                <div style="width: 12px; height: 12px; background: ${colors[i]}; border-radius: 50%; margin-right: 8px;"></div>
                <span style="font-size: 11px; font-weight: 500; color: #495057;">${label}: ${datas[i]}</span>
              </div>
            `;
          }
        });
        $('#audit_plan_legend').html(legendHTML);
      }
    });
  }

  function generateFindingControlPie() {
    let year = $('#year').val();
    return $.ajax({
      url: base_url + 'dashboard/get_data_finding_pie',
      type: 'post',
      data: {
        year: year
      },
      success: function (res) {
        let labels = res.map(data => data.label);
        let datas = res.map(data => parseInt(data.jumlah));
        let total = datas.reduce((a, b) => a + b, 0);
        let persentase = datas.map(val => total > 0 ? Math.round((val / total) * 100) : 0);

        // Cek apakah ada data yang nilainya bukan 0
        let hasData = datas.some(val => val > 0);
        if (hasData) {
          $('#no_data_finding_pie').empty();
          let ctx = $('#findingPie');

          if (findingControlPie !== null) {
            findingControlPie.destroy();
          }
          findingControlPie = new Chart(ctx, {
            type: 'doughnut',
            plugins: [ChartDataLabels],
            data: {
              labels: labels,
              datasets: [{
                label: 'Finding Control',
                data: datas,
                borderWidth: 3,
                backgroundColor: COLOR_MAP,
                borderWidth: 2
              }]
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: {
                datalabels: {
                  font: { weight: 'bold', size: 12 },
                  color: '#fff',
                  // ambil persen dari array di atas
                  display: (ctx) => persentase[ctx.dataIndex] > 0, // hanya tampil kalau > 0 %
                  formatter: (_, ctx) => persentase[ctx.dataIndex] + '%'
                },
                tooltip: {
                  callbacks: {
                    label: (ctx) => {
                      const i = ctx.dataIndex;
                      return `${labels[i]} : ${datas[i]}  (${persentase[i]}%)`;
                    }
                  }
                },
                legend: false
              }
            }
          });
        } else {
          // kalau semua datanya 0
          $('#no_data_finding_pie').html('<div class="text-muted text-center">No Data Available</div>');
          if (findingControlPie !== null) {
            findingControlPie.destroy();
            findingControlPie = null;
          }
        }
      }
    });
  }

  function generateRiskMonitoringPie() {
    let year = $('#year').val();
    return $.ajax({
      url: base_url + 'dashboard/get_data_risk_control_pie',
      type: 'post',
      data: {
        year: year
      },
      success: function (res) {
        let labels = res.label;
        let datas = res.data;
        let persentase = res.persentase;

        let hasData = datas.some(val => val > 0);

        if (hasData) {
          $('#no_data_risk_pie').empty();

          if (riskControlPie !== null) {
            riskControlPie.destroy();
          }
          let ctx = $('#riskPie');
          riskControlPie = new Chart(ctx, {
            type: 'doughnut',
            plugins: [ChartDataLabels],
            data: {
              labels: labels,
              datasets: [{
                label: 'Risk Control',
                data: datas,
                borderWidth: 3,
                backgroundColor: COLOR_MAP.slice(3, 8),
                borderColor: '#fff',
                borderWidth: 1
              }]
            },
            options: {
              responsive: true,
              plugins: {
                datalabels: {
                  font: { weight: 'bold', size: 14 },
                  color: '#fff',
                  // ambil persen dari array di atas
                  display: (ctx) => persentase[ctx.dataIndex] > 0,   // muncul hanya kalau > 0
                  formatter: (_, ctx) => persentase[ctx.dataIndex] + '%'
                },
                tooltip: {
                  callbacks: {
                    label: (ctx) => {
                      const i = ctx.dataIndex;
                      if (persentase[i] === 0) return ''; // hilangkan baris tooltip 0 %
                      return `${labels[i]} : ${datas[i]} (${persentase[i]}%)`;
                    }
                  }
                },
                legend: false
              }
            }
          });
        } else {
          $('#no_data_risk_pie').html('<div class="text-muted text-center">No Data Available</div>');
          if (riskControlPie !== null) {
            riskControlPie.destroy();
            riskControlPie = null;
          }
        }
      }
    });
  }

  function generateFindingControlBar() {
    let year = $('#year').val();
    return $.ajax({
      url: base_url + 'dashboard/get_auditor_finding_bar',
      type: 'post',
      data: {
        year: year
      },
      success: function (res) {
        let datas = res.data;  // data temuan
        let dept = res.dept;   // data dept/section

        // Inisialisasi nilai sfc_* ke 0 untuk setiap dept
        dept.forEach(d => {
          d.sfc_1 = 0;
          d.sfc_2 = 0;
          d.sfc_3 = 0;
        });

        // Loop untuk mengisi jumlah temuan ke dept yang sesuai
        let total_data = 0;
        datas.forEach(item => {
          total_data += parseInt(item.jumlah);
          let targetDept = dept.find(d => d.id == item.id_department_auditee);
          if (targetDept) {
            if (item.status_finding_control == '1') {
              targetDept.sfc_1 += parseInt(item.jumlah);
            } else if (item.status_finding_control == '2') {
              targetDept.sfc_2 += parseInt(item.jumlah);
            } else if (item.status_finding_control == '3') {
              targetDept.sfc_3 += parseInt(item.jumlah);
            }
          }
        });

        // Urutkan dept berdasarkan total temuan (descending)
        dept.sort((a, b) => {
          let totalA = a.sfc_1 + a.sfc_2 + a.sfc_3;
          let totalB = b.sfc_1 + b.sfc_2 + b.sfc_3;
          return totalB - totalA;
        });

        // Ambil labels dan data dari hasil sort
        let labels = dept.map(d => d.section_name); // sesuaikan field label
        let data1 = dept.map(d => d.sfc_1);
        let data2 = dept.map(d => d.sfc_2);
        let data3 = dept.map(d => d.sfc_3);

        const totalPerDept = data1.map((_, i) => data1[i] + data2[i] + data3[i]);

        // Atur lebar canvas dinamis
        let canvas = document.getElementById('findingBar');
        console.log(canvas);
        canvas.width = labels.length * 100;

        let ctx = $('#findingBar');

        if (findingControlBar !== null) {
          findingControlBar.destroy();
        }

        findingControlBar = new Chart(ctx, {
          type: 'bar',
          plugins: [ChartDataLabels],
          data: {
            labels: labels,
            datasets: [
              {
                label: 'Implementasi tidak sesuai',
                data: data1,
                barThickness: 50,
                borderColor: '#fff',
                backgroundColor: COLOR_MAP[0],
                borderWidth: 1
              },
              {
                label: 'Design kurang efektif',
                data: data2,
                barThickness: 50,
                borderColor: '#fff',
                backgroundColor: COLOR_MAP[1],
                borderWidth: 1
              },
              {
                label: 'Design tidak ditemukan',
                data: data3,
                barThickness: 50,
                borderColor: '#fff',
                backgroundColor: COLOR_MAP[2],
                borderWidth: 1
              },
            ]
          },
          options: {
            responsive: false,
            plugins: {
              // formatter global berlaku utk semua dataset
              datalabels: {
                anchor: 'center',
                align: 'center',
                color: '#fff',
                font: { weight: 'bold', size: 12 },
                display: (ctx) => {                     // hanya tampil kalau > 0 %
                  const v = ctx.dataset.data[ctx.dataIndex];
                  const tot = totalPerDept[ctx.dataIndex];
                  return tot && v > 0;
                },
                formatter: (value, ctx) => {
                  const tot = totalPerDept[ctx.dataIndex];
                  const pct = tot ? value / tot * 100 : 0;
                  return pct ? pct.toFixed(1) + '%' : '';
                }
              },
              legend: { display: false },
              tooltip: {
                callbacks: {
                  label: (ctx) => {
                    const i = ctx.dataIndex;
                    const tot = totalPerDept[i];
                    const pct = tot ? (ctx.raw / tot * 100).toFixed(1) : 0;
                    return `${ctx.dataset.label} : ${ctx.raw} (${pct}%)`;
                  }
                }
              }
            },
            scales: {
              x: {
                stacked: true,
                ticks: {
                  autoSkip: false,
                  maxRotation: 0,
                  minRotation: 0,
                  callback(value) {
                    const lbl = this.getLabelForValue(value);
                    return lbl.split(/[\s-]+/);
                  }
                }
              },
              y: {
                stacked: true,
                beginAtZero: true
              }
            }
          }
        });

        // Generate custom legend for Finding Control Bar
        let findingLegendHTML = '';
        const findingLabels = ['Implementasi tidak sesuai', 'Design kurang efektif', 'Design tidak ditemukan'];
        const findingColors = [COLOR_MAP[0], COLOR_MAP[1], COLOR_MAP[2]];
        findingLabels.forEach((label, i) => {
          findingLegendHTML += `
            <div style="display: flex; align-items: center; padding: 6px 12px; background: #f8f9fa; border-radius: 8px; font-size: 11px;">
              <div style="width: 16px; height: 16px; background: ${findingColors[i]}; border-radius: 4px; margin-right: 8px;"></div>
              <span style="font-weight: 500; color: #495057; white-space: nowrap;">${label}</span>
            </div>
          `;
        });
        $('#finding_legend').html(findingLegendHTML);
      }
    })
  }

  function generateRiskMonitoringBar() {
    let year = $('#year').val();
    return $.ajax({
      url: base_url + 'dashboard/get_auditor_monitoring_bar',
      type: 'post',
      data: {
        year: year
      },
      success: function (res) {
        console.log(res);
        let datas = res.data;  // data temuan
        let dept = res.dept;   // data dept/section

        // Inisialisasi nilai jumlah untuk setiap dept
        dept.forEach(d => {
          d.status_critical = 0;
          d.status_improve = 0;
          d.status_major = 0;
          d.status_moderate = 0;
          d.status_minor = 0;
        });

        // Loop untuk mengisi jumlah temuan ke dept yang sesuai
        datas.forEach(item => {
          let targetDept = dept.find(d => d.id == item.id_department_auditee);
          if (targetDept) {
            if (item.bobot_finding == 'Improvement') {
              targetDept.status_improve++;
            } else if (item.bobot_finding == 'Critical') {
              targetDept.status_critical++;
            } else if (item.bobot_finding == 'Major') {
              targetDept.status_major++;
            } else if (item.bobot_finding == 'Moderate') {
              targetDept.status_moderate++;
            } else if (item.bobot_finding == 'Minor') {
              targetDept.status_minor++;
            }
          }
        });

         // Urutkan dept berdasarkan total temuan (descending)
        dept.sort((a, b) => {
          let totalA = a.status_critical + a.status_major + a.status_moderate + a.status_minor + a.status_improve;
          let totalB = b.status_critical + b.status_major + b.status_moderate + b.status_minor + b.status_improve;
          return totalB - totalA;
        });

        // Ambil labels dan data dari hasil sort
        let labels = dept.map(d => d.section_name);
        let data_improve  = dept.map(d => d.status_improve);
        let data_crit     = dept.map(d => d.status_critical);
        let data_major    = dept.map(d => d.status_major);
        let data_moderate = dept.map(d => d.status_moderate);
        let data_minor    = dept.map(d => d.status_minor);

        const totalPerDept = labels.map((_, i) =>
          data_improve[i] + data_crit[i] + data_major[i] + data_moderate[i] + data_minor[i]);
        // Atur lebar canvas dinamis
        let canvas = document.getElementById('riskBar');
        canvas.width = labels.length * 100;

        let ctx = $('#riskBar');

        if (riskControlBar !== null) {
          riskControlBar.destroy();
        }

        riskControlBar = new Chart(ctx, {
          type: 'bar',
          plugins: [ChartDataLabels],
          data: {
            labels: labels,
            datasets: [
              {
                label: 'Improvement',
                data: data_improve,
                barThickness: 50,
                backgroundColor: COLOR_MAP[3],
                borderColor: '#fff',
                borderWidth: 1
              },
              {
                label: 'Critical',
                data: data_crit,
                barThickness: 50,
                backgroundColor: COLOR_MAP[4],
                borderColor: '#fff',
                borderWidth: 1
              },
              {
                label: 'Major',
                data: data_major,
                barThickness: 50,
                backgroundColor: COLOR_MAP[5],
                borderColor: '#fff',
                borderWidth: 1
              },
              {
                label: 'Moderate',
                data: data_moderate,
                barThickness: 50,
                backgroundColor: COLOR_MAP[6],
                borderColor: '#fff',
                borderWidth: 1
              },
              {
                label: 'Minor',
                data: data_minor,
                barThickness: 50,
                backgroundColor: COLOR_MAP[7],
                borderColor: '#fff',
                borderWidth: 1
              },

            ]
          },
          options: {
            responsive: false,
            plugins: {
              legend: { display: false },
              datalabels: {
                anchor: 'center',
                align: 'center',
                color: '#fff',
                font: { weight: 'bold', size: 12 },
                display: ctx => {          // sembunyikan kalau 0 %
                  const v = ctx.dataset.data[ctx.dataIndex];
                  return v > 0;
                },
                formatter: (value, ctx) => {
                  const tot = totalPerDept[ctx.dataIndex];
                  return tot ? (value / tot * 100).toFixed(1) + '%' : '';
                }
              },
              tooltip: {
                callbacks: {
                  label: (ctx) => {
                    const i = ctx.dataIndex;
                    const tot = totalPerDept[i];
                    const pct = tot ? (ctx.raw / tot * 100).toFixed(1) : 0;
                    return `${ctx.dataset.label}: ${ctx.raw} (${pct}%)`;
                  }
                }
              }
            },
            scales: {
              x: {
                stacked: true,
                ticks: {
                  autoSkip: false,
                  maxRotation: 0,
                  minRotation: 0,
                  callback(value) {
                    const lbl = this.getLabelForValue(value);
                    return lbl.split(/[\s-]+/);
                  }
                }
              },
              y: {
                stacked: true,
                beginAtZero: true
              }
            }
          }
        });

        // Generate custom legend for Risk Monitoring Bar
        let riskLegendHTML = '';
        const riskLabels = ['Improvement', 'Critical', 'Major', 'Moderate', 'Minor'];
        const riskColors = [COLOR_MAP[3], COLOR_MAP[4], COLOR_MAP[5], COLOR_MAP[6], COLOR_MAP[7]];
        riskLabels.forEach((label, i) => {
          riskLegendHTML += `
            <div style="display: flex; align-items: center; padding: 6px 12px; background: #f8f9fa; border-radius: 8px; font-size: 11px;">
              <div style="width: 16px; height: 16px; background: ${riskColors[i]}; border-radius: 4px; margin-right: 8px;"></div>
              <span style="font-weight: 500; color: #495057; white-space: nowrap;">${label}</span>
            </div>
          `;
        });
        $('#risk_legend').html(riskLegendHTML);
      }
    })
  }

  function generateQuestionerGauge() {
    let tahun =  $('#year').val();
    $.ajax({
      url: base_url + 'dashboard/get_data_questioner_gauge',
      type: 'post',
      data: {
        year: tahun
      },
      success: function (res) {
        // Destroy existing chart instances before creating new ones
        if (questionerGauge) {
          questionerGauge.destroy();
        }
        if (questionerBar) {
          questionerBar.destroy();
        }

        const ctx = document.getElementById('question_gauge').getContext('2d');

        let value = parseFloat(res.total_average) ;
        let max = 4.00;

        let gaugeColor;
        let txtClass;

        if (value < 2) {
          gaugeColor = '#f44336';
          txtClass = 'text-danger';
        } else if (value < 3) {
          gaugeColor = '#ff9800';
          txtClass = 'text-warning';
        } else {
          gaugeColor = '#4caf50';
          txtClass = 'text-success';
        }

        questionerGauge = new Chart(ctx, {
          type: 'doughnut',
          data: {
            datasets: [{
              data: [value, max - value],
              backgroundColor: [gaugeColor, '#E0E0E0'],
              borderWidth: 0
            }]
          },
          options: {
            circumference: 180,
            rotation: -90,
            cutout: '70%',
            plugins: {
              legend: { display: false },
              tooltip: { enabled: false }
            }
          },
          plugins: [{
            id: 'custom-labels',
            afterDraw: (chart) => {
              const { ctx, chartArea: { left, right, bottom } } = chart;

              // posisi tengah chart
              let cx = (left + right) / 2;
              let cy = bottom;

              // tulis nilai di tengah
              ctx.save();
              ctx.font = "bold 48px Arial";
              ctx.fillStyle = gaugeColor;
              ctx.textAlign = "center";
              ctx.fillText(value.toFixed(2), cx, cy - 10);

              // tulis min (0) di kiri
              ctx.font = "bold 24px Arial";
              ctx.fillStyle = "#f44336";
              ctx.textAlign = "left";
              ctx.fillText("0", left + 10, cy - 5);

              // tulis max (4) di kanan
              ctx.font = "bold 24px Arial";
              ctx.fillStyle = "#4caf50";
              ctx.textAlign = "right";
              ctx.fillText(max.toFixed(0), right - 10, cy - 5);

              ctx.restore();
            }
          }]
        });

        const ctxBar = document.getElementById('question_bar').getContext('2d');
        let labels = ["Hasil Audit", "Proses Audit", "Auditor"];
        let values = [
          parseFloat(res.hasil_audit),
          parseFloat(res.proses_audit),
          parseFloat(res.auditor)
        ];

        let colors = values.map(v => {
          if (v < 2) return '#f44336';
          else if (v < 3) return '#ff9800';
          else return '#4caf50';
        });

        questionerBar = new Chart(ctxBar, {
          type: 'bar',
          data: {
            labels: labels, 
            datasets: [{
              label: 'Rata-rata Skor',
              data: values,
              backgroundColor: '#6EF3F2',
              borderRadius: 10,
              barPercentage: 0.9,      // lebar bar lebih proporsional
              categoryPercentage: 1.0  // jarak antar bar lebih lebar
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false, // isi penuh container
            scales: {
              y: {
                beginAtZero: true,
                ticks: { stepSize: 1, font: { size: 11 } },
              },
              x: {
                ticks: { font: { size: 11 } }
              }
            },
            plugins: {
              legend: { display: false }
            },
            layout: {
              padding: {
                top: 5,
                bottom: 0
              }
            }
          }
        });
      }
    })
  }

  // function get_monitoring_capa() {
  //   let year = $('#year').val();

  //   return $.ajax({
  //     url: base_url + 'dashboard/get_auditor_monitoring_capa',
  //     type: 'post',
  //     data: {
  //       year: year
  //     },
  //     success: function (res) {
  //       let html = '';
  //       $.each(res, function (i, v) {
  //         html += `
  //                       <tr class="text-center">
  //                           <td>${v.site}</td>
  //                           <td>${v.dept}</td>
  //                           <td>${v.aktivitas}</td>
  //                           <td>${v.finding[0]}</td>
  //                           <td>${v.finding[1]}</td>
  //                           <td>${v.finding[2]}</td>
  //                           <td>${v.capa[1]}</td>
  //                           <td>${v.capa[2]}</td>
  //                           <td>${v.capa[3]}</td>
  //                           <td>${v.capa[4]}</td>
  //                           <td>${v.capa[5]}</td>
  //                           <td>${v.capa[6]}</td>
  //                       </tr>
  //                   `
  //       })

  //       $('#finding_capa tbody').html(html);
  //     }
  //   });
  // }



</script>