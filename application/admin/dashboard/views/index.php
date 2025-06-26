<div id="page_loading" style="
    display: none;
    position: fixed;
    top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(255,255,255,0.7);
    z-index: 9999;
    text-align: center;
    padding-top: 200px;
    font-size: 1.2em;
">
    <div class="spinner-border text-primary" role="status"></div>
    <div>Memuat data...</div>
</div>

<div class="continer-fluid mx-5 my-3">
    <div class="row align-items-start mb-3">
        <div class="col-md-9">
            <h1 class="display-4 font-weight-bolder mb-0">Dashboard</h1>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-4">
            <h5>Status Finding Control</h5>
            <hr>
        </div>
        <div class="col-md-4 offset-4 text-right">
            <select class="select2 form-control w-25" name="year" id="year">
                <?php 
                $year = $year['min_year'];
                $current = date('Y');
                for($y = $current; $y >= $year; $y--){
                    echo '<option value="'.$y.'">'.$y.'</option>';
                }
                ?>    
            </select>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div id="no_data"></div>
                    <canvas id="finding_control" style="margin:auto;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="card shadow-sm">
                <div class="card-header bg-white text-center">
                    <h5 class="font-weight-bolder mb-0">Status Finding Control All Department</h5>
                </div>
                <div class="card-body" style="overflow-x: auto; width: 100%;">
                    <div style="width: max-content;">
                        <canvas id="finding_control_dept" style="height:300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-md-4">
            <h5>Monitoring Risk</h5>
            <hr>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <canvas id="risk_control" style="margin:auto;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="card shadow-sm">
                <div class="card-header bg-white text-center">
                    <h5 class="font-weight-bolder mb-0">Monitoring Risk All Department</h5>
                </div>
                <div class="card-body" style="overflow-x: auto; width: 100%;">
                    <div style="width: max-content;">
                        <canvas id="risk_control_bar" style="height:300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-md-4">
            <h5>Monitoring Finding dan CAPA Auditee</h5>
            <hr>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive my-3">
                <table class="table table-bordered table-sm text-center" id="finding_capa">
                    <thead class="thead-dark">
                        <tr>
                            <th rowspan="2">Site</th>
                            <th rowspan="2">Dept</th>
                            <th rowspan="2">Aktivitas</th>
                            <th colspan="3">Finding Progress</th>
                            <th colspan="6">CAPA Progress</th>
                        </tr>
                        <tr>
                            <th>Open</th>
                            <th>Delivered</th>
                            <th>Close</th>
                            <th>Delivered</th>
                            <th>On Progress</th>
                            <th>Done</th>
                            <th>Pending</th>
                            <th>Cancelled</th>
                            <th>Deadline Exceeded</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let findingControlPie = null;
    let findingControlBar = null;
    let riskControlPie = null;
    let riskControlBar = null;

    $(document).ready(function() {
        $('#page_loading').show();
        $.when(
            get_finding_control_chart(),
            get_finding_control_dept_chart(),
            get_risk_control_pie(),
            get_risk_control_bar(),
            get_monitoring_capa(),
        ).always(function(){
            $('#page_loading').hide();
        })
    });
    
    $(document).on('change', '#year', function(){
        // get_finding_control_chart();
        // get_finding_control_dept_chart();

        $('#page_loading').show();
        $.when(
            get_finding_control_chart(),
            get_finding_control_dept_chart(),
            get_risk_control_pie(),
            get_risk_control_bar(),
            // get_monitoring_capa()
        ).always(function() {
            $('#page_loading').hide(); // Sembunyikan loading setelah semua selesai
        });
    });

    function get_finding_control_chart() {
        let year = $('#year').val();
        return $.ajax({
            url: base_url + 'dashboard/get_data_finding_control',
            type: 'post',
            data: {
                year: year
            },
            success: function(res) {
                let labels = res.map(data => data.label);
                let datas = res.map(data => parseInt(data.jumlah)); // pastikan angka

                // Cek apakah ada data yang nilainya bukan 0
                let hasData = datas.some(val => val > 0);
                
                if (hasData) {
                    $('#no_data').empty(); 
                    let ctx = $('#finding_control');

                    if (findingControlPie !== null) {
                        findingControlPie.destroy();
                    }
                    findingControlPie = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Finding Control',
                                data: datas,
                                borderWidth: 3
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                },
                                title: {
                                    display: true,
                                    text: 'Status Finding Control'
                                }
                            }
                        }
                    });
                } else {
                    $('#finding_control').hide(); 
                    $('#no_data').html('<p class="text-center text-muted">No Data Status Finding Control</p>');
                }
            }
        });
    }


    function get_finding_control_dept_chart(){
        let year = $('#year').val();
        return $.ajax({
            url: base_url + 'dashboard/get_finding_control_dept',
            type: 'post',
            data: {
                year: year
            },
            success:function(res){
                let datas = res.data;  // data temuan
                let dept = res.dept;   // data dept/section
                let is_admin = res.is_admin;
                // Inisialisasi nilai sfc_* ke 0 untuk setiap dept
                dept.forEach(d => {
                    d.sfc_1 = 0;
                    d.sfc_2 = 0;
                    d.sfc_3 = 0;
                });

                // Loop untuk mengisi jumlah temuan ke dept yang sesuai
                datas.forEach(item => {
                    let targetDept = dept.find(d => d.parent_id == item.parent_id);
                    
                    if(is_admin){
                        targetDept = dept.find(d => d.id == item.parent_id);
                    }
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
                let labels = dept.map(d => d.section_name || `Dept ${d.id}`);
                let data1 = dept.map(d => d.sfc_1);
                let data2 = dept.map(d => d.sfc_2);
                let data3 = dept.map(d => d.sfc_3);

                // Atur lebar canvas dinamis
                let canvas = document.getElementById('finding_control_dept');
                canvas.width = labels.length * 350;

                let ctx = $('#finding_control_dept');
                
                if (findingControlBar !== null) {
                    findingControlBar.destroy();
                }

                findingControlBar = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                        {
                            label: 'Tidak Ada',
                            data: data1,
                            barThickness: 50
                        },
                        {
                            label: 'Tidak Sesuai',
                            data: data2,
                            barThickness: 50
                        },
                        {
                            label: 'Tidak Efektif',
                            data: data3,
                            barThickness: 50
                        },
                    ]
                    },
                    options: {
                        responsive: false,
                        plugins: {
                            legend: { position: 'top' }
                        },
                        scales: {
                            x: {
                                stacked: true,
                                ticks: {
                                    autoSkip: false,
                                    maxRotation: 0,
                                    minRotation: 0
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

    function get_risk_control_pie() {
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
                if (riskControlPie !== null) {
                    riskControlPie.destroy();
                }

                let ctx = $('#risk_control');
                riskControlPie = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Risk Control',
                            data: datas,
                            borderWidth: 3
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            },
                            title: {
                                display: true,
                                text: 'Monitoring Risk'
                            }
                        }
                    }
                });
            }
        });
    }

    function get_risk_control_bar(){
        let year = $('#year').val();
        return $.ajax({
            url: base_url + 'dashboard/get_data_risk_control_bar',
            type: 'post',
            data: {
                year: year
            },
            success:function(res){
                let datas = res.data;  // data temuan
                let dept = res.dept;   // data dept/section
                let is_admin = res.is_admin;

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
                    
                    if(is_admin){
                        targetDept = dept.find(d => d.id == item.parent_id);
                    }
                    if (targetDept) {
                        if(item.bobot_finding == 'Improvement') {
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
                let labels = dept.map(d => d.section_name || `Dept ${d.id}`);
                let data_improve = dept.map(d => d.status_improve);
                let data_crit = dept.map(d => d.status_critical);
                let data_major = dept.map(d => d.status_major);
                let data_moderate = dept.map(d => d.status_moderate);
                let data_minor = dept.map(d => d.status_minor);

                // Atur lebar canvas dinamis
                let canvas = document.getElementById('risk_control_bar');
                canvas.width = labels.length * 350;

                let ctx = $('#risk_control_bar');
                
                if (riskControlBar !== null) {
                    riskControlBar.destroy();
                }

                riskControlBar = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                        {
                            label: 'Improvement',
                            data: data_improve,
                            barThickness: 50
                        },
                        {
                            label: 'Critical',
                            data: data_crit,
                            barThickness: 50
                        },
                        {
                            label: 'Major',
                            data: data_major,
                            barThickness: 50
                        },
                                                {
                            label: 'Moderate',
                            data: data_moderate,
                            barThickness: 50
                        },
                                                {
                            label: 'Minor',
                            data: data_minor,
                            barThickness: 50
                        },
                    ]
                    },
                    options: {
                        responsive: false,
                        plugins: {
                            legend: { position: 'top' }
                        },
                        scales: {
                            x: {
                                stacked: true,
                                ticks: {
                                    autoSkip: false,
                                    maxRotation: 0,
                                    minRotation: 0
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
    
    function get_monitoring_capa(){
        let year = $('#year').val();

        return $.ajax({
            url: base_url + 'dashboard/get_data_monitoring_capa',
            type: 'post',
            data: {
                year:year
            },
            success: function(res){
                console.log(res);
                let html = '';

                $.each(res, function(i,v){
                    html += `
                        <tr>
                            <td>${v.site}</td>
                            <td>${v.dept}</td>
                            <td>${v.aktivitas}</td>
                            <td>${v.finding[0]}</td>
                            <td>${v.finding[1]}</td>
                            <td>${v.finding[2]}</td>
                            <td>${v.capa[1]}</td>
                            <td>${v.capa[2]}</td>
                            <td>${v.capa[3]}</td>
                            <td>${v.capa[4]}</td>
                            <td>${v.capa[5]}</td>
                            <td>${v.capa[6]}</td>
                            
                        </tr>
                    `
                })

                $('#finding_capa tbody').html(html);
            }
        });
    }
    
</script>