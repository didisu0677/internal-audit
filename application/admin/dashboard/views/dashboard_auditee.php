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
      <div class="card border-0 shadow-sm" style="border-radius: 15px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="card-body py-4">
          <div class="row align-items-center">
            <div class="col-md-8">
              <h3 class="mb-1 font-weight-bold text-white">Internal Audit Dashboard</h3>
              <p class="mb-0 text-white-50">Auditee comprehensive monitoring and reporting system</p>
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

  <div class="row">
    <div class="col-lg-8">
      <!-- STAT CARDS -->
      <div class="row mb-4">
        <div class="col-md-4 mb-3">
          <div class="card h-100 border-0 shadow-sm" style="border-radius: 15px; transition: transform 0.2s;">
            <div class="card-body text-center p-4">
              <div class="mb-3">
                <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                  <i class="fas fa-search text-white"></i>
                </div>
              </div>
              <h6 class="font-weight-bold text-dark mb-1">FINDING RECORDS</h6>
              <div class="display-4 text-primary font-weight-bold mb-2" id="findingCount"></div>
              <p class="text-muted mb-0 small">New findings discovered</p>
            </div>
          </div>
        </div>
        <div class="col-md-4 mb-3">
          <div class="card h-100 border-0 shadow-sm" style="border-radius: 15px; transition: transform 0.2s;">
            <div class="card-body text-center p-4">
              <div class="mb-3">
                <div class="bg-success rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                  <i class="fas fa-tasks text-white"></i>
                </div>
              </div>
              <h6 class="font-weight-bold text-dark mb-1">CAPA MONITORING</h6>
              <div class="display-4 text-success font-weight-bold mb-2" id="capaCount"></div>
              <p class="text-muted mb-0 small">Active CAPAs in progress</p>
            </div>
          </div>
        </div>
        <div class="col-md-4 mb-3">
          <div class="card h-100 border-0 shadow-sm" style="border-radius: 15px; transition: transform 0.2s;">
            <div class="card-body text-center p-4">
              <div class="mb-3">
                <div class="bg-warning rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                  <i class="fas fa-clipboard-list text-white"></i>
                </div>
              </div>
              <h6 class="font-weight-bold text-dark mb-1">MY TASKS</h6>
              <div class="display-4 text-warning font-weight-bold mb-2"><?= count($mytask) ?></div>
              <p class="text-muted mb-0 small">Pending tasks to complete</p>
            </div>
          </div>
        </div>
      </div>

      <!-- STATUS FINDING CONTROL -->
      <div class="row mb-4">
        <div class="col-md-8 mb-3">
          <div class="card h-100 border-0 shadow-sm" style="border-radius: 15px;">
            <div class="card-header bg-white border-0 py-3" style="border-top-left-radius: 15px; border-top-right-radius: 15px;">
              <div class="d-flex align-items-center">
                <div class="bg-info rounded-circle d-inline-flex align-items-center justify-content-center mr-3" style="width: 30px; height: 30px;">
                  <i class="fas fa-chart-bar text-white" ></i>
                </div>
                <h6 class="mb-0 font-weight-bold text-dark">Status Finding Control</h6>
              </div>
            </div>
            <div class="card-body p-4">
              <div style="overflow-x:auto; width:100%;">
                <div style="width: max-content; min-width:100%;">
                  <canvas id="findingChart" style="height:300px;"></canvas>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4 mb-3">
          <div class="card h-100 border-0 shadow-sm" style="border-radius: 15px;">
            <div class="card-header bg-white border-0 py-3" style="border-top-left-radius: 15px; border-top-right-radius: 15px;">
              <div class="d-flex align-items-center">
                <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mr-3" style="width: 30px; height: 30px;">
                  <i class="fas fa-chart-pie text-white"></i>
                </div>
                <h6 class="mb-0 font-weight-bold text-dark">Finding Status</h6>
              </div>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center p-4">
              <canvas id="findingPie"></canvas>
              <div id="no_data_finding_pie" class="text-muted text-center"
                style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%);">
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- RISK MONITORING -->
      <div class="row mb-4">
        <div class="col-md-8 mb-3">
          <div class="card h-100 border-0 shadow-sm" style="border-radius: 15px;">
            <div class="card-header bg-white border-0 py-3" style="border-top-left-radius: 15px; border-top-right-radius: 15px;">
              <div class="d-flex align-items-center">
                <div class="bg-danger rounded-circle d-inline-flex align-items-center justify-content-center mr-3" style="width: 30px; height: 30px;">
                  <i class="fas fa-exclamation-triangle text-white"></i>
                </div>
                <h6 class="mb-0 font-weight-bold text-dark">Risk Monitoring</h6>
              </div>
            </div>
            <div class="card-body p-4">
              <div style="overflow-x:auto; width:100%;">
                <div style="width: max-content; min-width:100%;">
                  <canvas id="riskChart" style="height:300px;"></canvas>
                  <div id="no_data_risk" class="text-muted text-center"
                    style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%);">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4 mb-3">
          <div class="card h-100 border-0 shadow-sm" style="border-radius: 15px;">
            <div class="card-header bg-white border-0 py-3" style="border-top-left-radius: 15px; border-top-right-radius: 15px;">
              <div class="d-flex align-items-center">
                <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center mr-3" style="width: 30px; height: 30px;">
                  <i class="fas fa-chart-pie text-white"></i>
                </div>
                <h6 class="mb-0 font-weight-bold text-dark">Risk Distribution</h6>
              </div>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center p-4">
              <canvas id="riskDonut"></canvas>
              <div id="no_data_risk_pie" class="text-muted text-center"
                style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%);">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="col-md-12 mb-3">
        <div class="card border-0 shadow-sm" style="border-radius: 15px;">
          <div class="card-header bg-white border-0 py-3" style="border-top-left-radius: 15px; border-top-right-radius: 15px;">
            <div class="d-flex align-items-center">
              <div class="bg-warning rounded-circle d-inline-flex align-items-center justify-content-center mr-3" style="width: 45px; height: 45px;">
                <i class="fas fa-bell text-white"></i>
              </div>
              <h6 class="mb-0 font-weight-bold text-dark">NOTIFICATION</h6>
            </div>
          </div>
          <div class="card-body p-0" style="max-height:500px; overflow-y:auto;">
            <ul class="list-group list-group-flush">
              <?php foreach ($mytask as $val):
                switch ($val['type']) {
                  case 'questioner':
                    $link = base_url('internal/kuisioner/entry/' . encode_id($val['id_transaction']));
                    break;
                  case 'finding':
                    $link = base_url('internal/finding_records/?id=' . encode_id($val['id_transaction']));
                    break;
                  case 'capa':
                    $link = base_url('internal/capa_monitoring/');
                    break;
                  default:
                    $link = '#';
                    break;
                }
                ?>
                <a href="<?= $link ?? '' ?>" class="text-decoration-none">
                  <li class="list-group-item border-0 py-3 px-4 hover-bg-light" style="transition: background-color 0.2s;">
                    <div class="d-flex align-items-start">
                      <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 40px; height: 40px; min-width: 40px;">
                        <i class="fas fa-file-alt text-muted" style="font-size: 14px;"></i>
                      </div>
                      <div class="flex-grow-1">
                        <small class="text-muted d-block mb-1"><?= date_indo(date('Y-m-d', strtotime($val['created_at']))) ?></small>
                        <h6 class="mb-1 font-weight-bold text-dark"><?= $val['title'] ?></h6>
                        <p class="mb-0 text-muted small"><?= $val['description'] ?></p>
                      </div>
                    </div>
                  </li>
                </a>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>


  <!-- TABLE + NOTIFICATION -->
  <div class="row">
    <div class="col-md-12 mb-3">
      <div class="card border-0 shadow-sm" style="border-radius: 15px;">
        <div class="card-header bg-white border-0 py-4" style="border-top-left-radius: 15px; border-top-right-radius: 15px;">
          <div class="d-flex align-items-center">
            <div class="bg-dark rounded-circle d-inline-flex align-items-center justify-content-center mr-3" style="width: 40px; height: 40px;">
              <i class="fas fa-table text-white"></i>
            </div>
            <div>
              <h5 class="font-weight-bold mb-1 text-dark">FINDING & CAPA MONITORING</h5>
              <p class="text-muted mb-0 small">Comprehensive tracking and monitoring overview</p>
            </div>
          </div>
        </div>
        <div class="card-body p-4">
          <div class="table-responsive">
            <table class="table table-hover mb-0" id="example">
              <thead style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                <tr>
                  <th class="font-weight-bold text-dark py-3">Site</th>
                  <th class="font-weight-bold text-dark py-3">Dept</th>
                  <th class="font-weight-bold text-dark py-3">Aktivitas</th>
                  <th class="font-weight-bold text-dark py-3">Finding</th>
                  <th class="font-weight-bold text-dark py-3">Finding Status</th>
                  <th class="font-weight-bold text-dark py-3">CAPA Status</th>
                  <th class="font-weight-bold text-dark py-3">Due Date</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>
<script src="https://cdn.datatables.net/2.3.3/js/dataTables.js"> </script>

<script>
  
  const CAPA_COLOR = {
    "Delivered" : "#1976D2",        // Biru (selesai dikirim)
    "On-Progress" : "#FBC02D",      // Kuning (sedang berjalan)
    "Done" : "#4CAF50",             // Hijau (selesai)
    "Pending" : "#9E9E9E",          // Abu-abu (tertunda)
    "Cancel" : "#E53935",           // Merah (dibatalkan)
    "Deadline Exceeded" : "#F57C00" // Oranye (lewat batas waktu)
  };

  $('#example').DataTable({
    autoWidth: false,
    ajax: {
      url: base_url + 'dashboard/get_data_monitoring_capa',
      type: 'POST',
      data: function (d) {
        d.year = $('#year').val();
      },
      dataSrc: '' // karena  `render($data, 'json')` udah dalam bentuk json jadi tinggal pakai
    },
    columns: [
      { data: 'site_auditee', width: "1%", className: "text-nowrap" },
      { data: 'dept', width: "150px", className: "text-nowrap text-center" },
      { data: 'aktivitas',
        className: "text-center"
      },
      { data: 'finding',
        render: function (data, type, row) {
          return truncateText(data, 150); 
        }
      },
      { 
        data: 'status_finding',
        render: function (data) {
          switch (data) {
            case "0": return '<span class="badge badge-danger">Open</span>';
            case "1": return '<span class="badge badge-warning">Delivered</span>';
            case "2": return '<span class="badge badge-success">Closed</span>';
            default:  return '<span class="badge badge-secondary">N/A</span>';
          }
        },
        className: "text-center"
      },
      { data: 'status_capa',
        render: function(data){
          let color = CAPA_COLOR[data] || '#9E9E9E';
          return '<span class="badge text-light" style="background-color:' + color + ';">' + data + '</span>';
        },
        className: "text-center",
       },
      { data: 'deadline_capa',
        className: "font-weight-bold text-center",
       }
    ]
  });

  let findingControlPie = null;
  let findingControlBar = null;
  let riskControlPie = null;
  let riskControlBar = null;

  const COLOR_MAP = [
    '#4CAF50', // Implementasi tidak sesuai 
    '#F9A825', // Design kurang efektif 
    '#E53935', // Design tidak ditemukan 
    '#1976D2', // Improvement
    '#D32F2F', // Critical 
    '#F57C00', // Major
    '#FBC02D', // Moderate
    '#388E3C', // Minor
  ];

  $(document).ready(function () {
    $('#page_loading').show();
    $.when(
      generateFindingAndCapa(),
      generateFindingControlPie(),
      generateFindingControlBar(),
      generateRiskMonitoringBar(),
      generateRiskMonitoringPie()
    ).done(function() {
      $('#page_loading').hide();
      $('#example').DataTable().ajax.reload();
    });
  });

  $(document).on('change', '#year', function() {
    $('#page_loading').show();
    $.when(
      generateFindingAndCapa(),
      generateFindingControlPie(),
      generateFindingControlBar(),
      generateRiskMonitoringBar(),
      generateRiskMonitoringPie()
    ).done(function() {
      $('#page_loading').hide();
      $('#example').DataTable().ajax.reload();
    });
  });

  function generateFindingAndCapa(){
    $.ajax({
      url: base_url + 'dashboard/get_finding_record_capa',
      type: 'post',
      data: {
        year: $('#year').val()
      },
      success: function(res) {
        $('#findingCount').text(res.finding);
        $('#capaCount').text(res.capa);
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

  function generateFindingControlBar() {
    let year = $('#year').val();
    return $.ajax({
      url: base_url + 'dashboard/get_finding_control_bar',
      type: 'post',
      data: {
        year: year
      },
      success: function (res) {
        
        let datas = res.data;  // data temuan
        let dept = res.dept;   // data dept/section
        let labels = res.labels;
        console.log(res);
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
          let targetDept = dept.find(d => d.parent_id == item.parent_id);
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

        // Ambil label dan data untuk chart
       
        let data1 = dept.map(d => d.sfc_1);
        let data2 = dept.map(d => d.sfc_2);
        let data3 = dept.map(d => d.sfc_3);

        const totalPerDept = data1.map((_, i) => data1[i] + data2[i] + data3[i]);

        // Atur lebar canvas dinamis
        let canvas = document.getElementById('findingChart');
        canvas.width = labels.length * 100;

        let ctx = $('#findingChart');

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
              legend: { position: 'top' },
              tooltip: {
                callbacks: {
                  label: (ctx) => {
                    const i = ctx.dataIndex;
                    const tot = totalPerDept[i];
                    const pct = tot ? (ctx.raw / tot * 100).toFixed(1) : 0;
                    return `${ctx.dataset.label} : ${ctx.raw} (${pct}%)`;
                  }
                }
              },
              legend: { position: 'top' }
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
      }
    })
  }

  function generateRiskMonitoringBar() {
    let year = $('#year').val();
    return $.ajax({
      url: base_url + 'dashboard/get_data_risk_control_bar',
      type: 'post',
      data: {
        year: year
      },
      success: function (res) {
        console.log(res);
        let datas = res.data;  // data temuan
        let dept = res.dept;   // data dept/section
        let labels = res.labels;
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
          let targetDept = dept.find(d => d.parent_id == item.parent_id);

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

        // Ambil label dan data untuk chart
        // let labels = dept.map(d => d.section_name || `Dept ${d.id}`);
        let data_improve = dept.map(d => d.status_improve);
        let data_crit = dept.map(d => d.status_critical);
        let data_major = dept.map(d => d.status_major);
        let data_moderate = dept.map(d => d.status_moderate);
        let data_minor = dept.map(d => d.status_minor);
        const totalPerDept = labels.map((_, i) =>
          data_improve[i] + data_crit[i] + data_major[i] + data_moderate[i] + data_minor[i]);
        // Atur lebar canvas dinamis
        let canvas = document.getElementById('riskChart');
        canvas.width = labels.length * 100;

        let ctx = $('#riskChart');

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
              legend: { position: 'top' },
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
      }
    })
  }

  function generateRiskMonitoringPie() {
        let year = $('#year').val();
        return $.ajax({
            url: base_url + 'dashboard/get_data_risk_control_pie',
            type: 'post',
            data: {
                year: year
            },
            success: function(res) {
                let labels = res.label;
                let datas = res.data;
                let persentase = res.persentase;

                let hasData = datas.some(val => val > 0);
                
                if(hasData){
                $('#no_data_risk_pie').empty();

                if (riskControlPie !== null) {
                    riskControlPie.destroy();
                }
                let ctx = $('#riskDonut');
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
                            borderColor    : '#fff',
                            borderWidth    : 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            datalabels: {
                                font: { weight:'bold', size:14 },
                                color: '#fff',
                                // ambil persen dari array di atas
                                display: (ctx) => persentase[ctx.dataIndex] > 0,   // muncul hanya kalau > 0
                                formatter: (_, ctx) => persentase[ctx.dataIndex] + '%'
                            },
                            tooltip: {
                                callbacks: {
                                    label: (ctx) => {
                                        const i   = ctx.dataIndex;
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

    function truncateText(text, length = 150) {
      return text.length > length ? text.substring(0, length) + "..." : text;
    }


</script>