<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		
		$mytask = get_data('tbl_mytask', [
			'where' =>[
				'id_user' => user('id'),
				'status' => 'pending' 
			]
		])->result_array();
		
		$year = get_data('tbl_finding_records', [
			'select' => 'min(year(tgl_mulai_audit)) as min_year',
		])->row_array();

		$data = [
			'year' => $year['min_year'] ?: date('Y'),
			'mytask' => $mytask
		];
		render($data);
	}

	function get_finding_record_capa(){
		$year = post('year') ?: date('Y');
		$finding = [];
		$capa = [];
		
		$auditee = get_data('tbl_auditee', 'id_user', user('id'))->row_array();
		if($auditee){
			$finding = get_data('tbl_finding_records fr', [
				'select' => 'fr.*',
				'join' => [
					'tbl_m_audit_section s on fr.id_section_department = s.id',
				],
				'where' => [
					's.id' => json_decode($auditee['id_section']) ?? '',
					'year(fr.tgl_mulai_audit)' => $year
				]
			])->result_array();

			foreach($finding as $v){
				$data_capa = get_data('tbl_capa', 'id_finding', $v['id'])->result_array();
				if($data_capa){
					$capa[] = $data_capa; 
				}
			}
		}
		$data = [
			'finding' => count($finding),
			'capa' => count($capa),
		];

		render($data, 'json');
	}

	function get_data_finding_pie(){
		$nip = user('kode');
		$auditee = get_data('tbl_detail_auditee', 'nip', $nip)->result_array();
		$section = array_column($auditee, 'id_section');
		
		$year = post('year') ?? date('Y');
		$where = ['year(tgl_mulai_audit)' => $year];
		
		if(user('id_group') == USER_GROUP_USER){
			$where['id_section_department'] = $section ?: '';
		}
		
		$data_finding = get_data('tbl_finding_records', [
			'select' => 'status_finding_control, count(*) as jumlah',
			'where' => $where,
			'group_by' => 'status_finding_control'
		])->result_array();

		$status_label = [
			'1' => 'Implementasi tidak sesuai',
			'2' => 'Design kurang efektif',
			'3' => 'Design tidak ditemukan'
		];


		$data = [];
		
		foreach ($status_label as $key => $label) {
			$jumlah = 0;
			foreach ($data_finding as $row) {
				if ($row['status_finding_control'] == $key) {
					$jumlah = $row['jumlah'];
					break;
				}
			}
			$data[] = [
				'label' => $label,
				'jumlah' => $jumlah,
			];
		}

		
		render($data, 'json');
	}

	function get_finding_control_bar(){
		$year = post('year') ?? date('Y');
		$auditee = get_data('tbl_auditee', 'id_user', user('id'))->row_array();
		$section = $auditee ? json_decode($auditee['id_section']) : [];
	
		$dept = get_data('tbl_m_audit_section', 'id', $section)->result_array();	
		$data = get_data('tbl_finding_records fr', [
			'select' => 's.*, fr.id_section_department,fr.status_finding_control, count(*) as jumlah',
			'join' => [
				'tbl_m_audit_section s on fr.id_section_department = s.id'
			],
			'where' => [
				'year(tgl_mulai_audit)' => $year,
				'id_section_department' => $section ?: ''
			],
			'group_by' => 'id_section_department, status_finding_control'
		])->result_array();

		$dept_labels = [];
		$labels = array_column($dept, 'section_name');
		
		foreach($labels as $label){
			$part = explode('-', $label);
			$fix = array_slice($part, 1);
			$dept_labels[] = implode(' ', $fix);
		}

		$clean_data = [
			'labels' => $dept_labels,
			'dept' => $dept,
			'data' => $data,
		];
		
		render($clean_data, 'json');
	}
	// function get_finding_control_bar(){
	// 	$nip = user('kode');
		
	// 	$auditee = get_data('tbl_detail_auditee', 'nip', $nip)->result_array();
	// 	$section = array_column($auditee, 'id_section') ?: '';
	
	// 	$year = post('year');
	// 	$where = [
	// 		'year(tgl_mulai_audit)' => $year
	// 	];
	
	// 	$data_section = get_data('tbl_m_audit_section', 'id', $section)->result_array();
	// 	$where['id_section_department'] = $section;
	// 	$dept = [];		
	// 	foreach($data_section as $row){
	// 		if($row['level3'] == '4') {  // CIBG
	// 			$row['section_name'] = 'CIBG '.$row['section_name'];
	// 		}elseif($row['level3'] == '5'){ //TMBG
	// 			$row['section_name'] = 'TMBG '.$row['section_name'];
	// 		}
			
	// 		if($row['level2'] == '3'){
	// 			$row['section_name'] = 'Factory '.$row['section_name'];
	// 		}
	// 		$dept[] = $row;
	// 	}		

	// 	$data = get_data('tbl_finding_records fr',[
	// 		'select' => 's.section_name, s.parent_id, fr.id_section_department,fr.status_finding_control, count(*) as jumlah',
	// 		'join' => [
	// 			'tbl_m_audit_section s on fr.id_section_department = s.id'
	// 		],
	// 		'where' => $where,
	// 		'group_by' => 'id_section_department, status_finding_control'
	// 	])->result_array();
		
	// 	$dept_labels = [];
	// 	$labels = array_column($dept, 'section_name');
	// 	foreach($labels as $label){
	// 		$part = explode('-', $label);
	// 		$fix = array_slice($part, 1);
	// 		$dept_labels[] = implode(' ', $fix);
	// 	}
		
	// 	$clean_data = [
	// 		'labels' => $dept_labels,
	// 		'dept' => $dept,
	// 		'data' => $data,
	// 	];
		
	// 	render($clean_data, 'json');
	// }
	function get_data_risk_control_pie(){
		$nip = user('kode');
		$auditee = get_data('tbl_detail_auditee', 'nip', $nip)->result_array();
		$section = array_column($auditee, 'id_section');
		
		$year = post('year');
		$where = [
			'year(tgl_mulai_audit)' => $year
		];
		$user_group = user('id_group');
		if($user_group == AUDITEE){
			$where['id_section_department'] = $section ?: '';
		}
		
		$data_bobot = get_data('tbl_finding_records', [
			'select' => 'bobot_finding',
			'where' => $where,
		])->result_array();

		$counter = [
			'Improvement' 	=> 0,
			'Critical' 		=> 0,
			'Major' 		=> 0,
			'Moderate' 		=> 0,
			'Minor' 		=> 0,
		];

		// Hitung total tiap bobot
		foreach ($data_bobot as $row) {
			$bobot = $row['bobot_finding'];
			if (isset($counter[$bobot])) {
				$counter[$bobot]++;
			}
		}

		$total = array_sum(array_values($counter));
		$values = array_values($counter);
		foreach($values as $v){
			$persentase = $total ? round($v / $total * 100, 1) : 0;
			$result['persentase'][] = $persentase; 
		}
		
		$result['label'] = array_keys($counter);
		$result['data'] = array_values($counter);
		render($result, 'json');
	}


	function get_data_risk_control_bar(){
		$nip = user('kode');
		$auditee = get_data('tbl_detail_auditee', 'nip', $nip)->result_array();
		$section = array_column($auditee, 'id_section') ?: '';
		
		$user_group = user('id_group');

		$year = post('year');
		$where = [
			'year(tgl_mulai_audit)' => $year
		];
		$data_section = get_data('tbl_m_audit_section', 'id', $section)->result_array();
		$where['id_section_department'] = $section;
		
		$dept = [];		
		foreach($data_section as $row){
			if($row['level3'] == '4') {  // CIBG
				$row['section_name'] = 'CIBG '.$row['section_name'];
			}elseif($row['level3'] == '5'){ //TMBG
				$row['section_name'] = 'TMBG '.$row['section_name'];
			}
			
			if($row['level2'] == '3'){
				$row['section_name'] = 'Factory '.$row['section_name'];
			}
			$dept[] = $row;
		}		

		$data = get_data('tbl_finding_records fr',[
			'select' => 's.section_name, s.parent_id, fr.id_section_department,fr.status_finding_control, fr.bobot_finding',
			'join' => [
				'tbl_m_audit_section s on fr.id_section_department = s.id'
			],
			'where' => $where,
			// 'group_by' => 'id_section_department, status_finding_control'
		])->result_array();

		$dept_labels = [];
		$labels = array_column($dept, 'section_name');
		foreach($labels as $label){
			$part = explode('-', $label);
			$fix = array_slice($part, 1);
			$dept_labels[] = implode(' ', $fix);
		}

		$clean_data = [
			'labels' => $dept_labels,
			'dept' => $dept,
			'data' => $data
		];

		render($clean_data, 'json');
	}

	function get_data_monitoring_capa(){
		$year = post('year') ?? date('Y');
		$auditee_dept = get_data('tbl_auditee a', [
			'join' => [
				'tbl_user u on a.id_user = u.id type left'
			],
			'where' => [
				'u.id' => user('id')
			]
		])->row_array()['id_department'] ?? [];
		// $auditee_section = $auditee_section ? json_decode($auditee_section['id_section']) : [];

		$data = get_data('tbl_finding_records fr', [
			'select' => 'DISTINCT dept.section_name AS dept_name, fr.site_auditee, s.section_name as dept, a.aktivitas, fr.finding, fr.status_finding, sc.status as status_capa, c.dateline_capa as deadline_capa',
			'join' => [
				'tbl_capa c on fr.id = c.id_finding type left',
				'tbl_m_audit_section s on fr.id_department_auditee = s.id',
				'tbl_status_capa sc on c.id_status_capa = sc.id',
				'tbl_sub_aktivitas sa on fr.id_sub_aktivitas = sa.id type left',
				'tbl_aktivitas a on sa.id_aktivitas = a.id type left',
				'tbl_m_audit_section dept on s.level3 = dept.id'
			],
			'where' => [
				'YEAR(fr.tgl_mulai_audit)' => $year,
				'dept.id' => $auditee_dept 
			],
			'order_by' => 'dept.section_name, a.aktivitas',
			'sort' => 'ASC'
		])->result_array();	
	
		render($data, 'json');
	}

	
	function get_auditor_finding_bar(){
		$year = post('year') ?: date('Y');
	
		$dept = get_data('tbl_m_audit_section', 'group_section', 'DEPARTMENT')->result_array();	
		$id_dept = array_column($dept, 'id');
		
		$data = get_data('tbl_finding_records fr', [
			'select' => 'fr.id_department_auditee,fr.status_finding_control, count(*) as jumlah',
			'join' => [
				'tbl_m_audit_section s on fr.id_section_department = s.id'
			],
			'where' => [
				'year(tgl_mulai_audit)' => $year,
				'id_department_auditee' => $id_dept ?: ''
			],
			'group_by' => 'id_department_auditee, status_finding_control'
		])->result_array();

		$department = [];		
		foreach($dept as $row){
			if($row['level3'] == '4') {  // CIBG
				$row['section_name'] = $row['section_name'].' CIBG';
			}elseif($row['level3'] == '5'){ //TMBG
				$row['section_name'] = $row['section_name'].' TMBG';
			}
			
			if($row['level2'] == '3'){
				$row['section_name'] = $row['section_name'].' Factory';
			}
			$department[] = $row;
		}	
		
		$clean_data = [
			// 'labels' => array_column($department, 'section_name'),
			'dept' => $department,
			'data' => $data,
		];

		render($clean_data, 'json');
	}

	function get_auditor_monitoring_bar(){
		$year = post('year') ?: date('Y');
		$dept = get_data('tbl_m_audit_section', 'group_section', 'DEPARTMENT')->result_array();	
		$id_dept = array_column($dept, 'id');
		
		$data = get_data('tbl_finding_records fr',[
			'select' => 's.section_name, s.parent_id, fr.id_department_auditee,fr.status_finding_control, fr.bobot_finding',
			'join' => [
				'tbl_m_audit_section s on fr.id_section_department = s.id'
			],
			'where' => [
				'year(tgl_mulai_audit)' => $year,
				'id_department_auditee' => $id_dept ?: ''
			],
		])->result_array();

		$department = [];		
		foreach($dept as $row){
			if($row['level3'] == '4') {  // CIBG
				$row['section_name'] = $row['section_name'].' CIBG';
			}elseif($row['level3'] == '5'){ //TMBG
				$row['section_name'] = $row['section_name'].' TMBG';
			}
			
			if($row['level2'] == '3'){
				$row['section_name'] = $row['section_name'].' Factory';
			}
			$department[] = $row;
		}	

		$clean_data = [
			// 'labels' => array_column($department, 'section_name'),
			'dept' => $department,
			'data' => $data,
		];

		render($clean_data, 'json');
	}

	function get_data_questioner_gauge(){
		$year = post('year') ?: date('Y');
		$question = get_data('tbl_kuisioner_respon', [
			'where' => [
				'respon !=' => null,
				'periode_audit' => $year
			]
		])->result_array();
		
		$hasil_audit = 0;
		$proses_audit = 0;
		$auditor = 0;

		$count_hasil = 0;
		$count_proses = 0;
		$count_auditor = 0;

		foreach($question as $v){
			$respon = json_decode($v['respon'], true); // hasilnya array

			foreach ($respon as $idx => $val) {
				if ($idx >= 0 && $idx <= 3) { // hasil audit
					$hasil_audit += $val;
					$count_hasil++;
				} elseif ($idx >= 4 && $idx <= 5) { // proses audit
					$proses_audit += $val;
					$count_proses++;
				} elseif ($idx >= 6 && $idx <= 9) { // auditor
					$auditor += $val;
					$count_auditor++;
				}
			}
		}

		// hitung rata-rata
		$avg_hasil  = $count_hasil  ? $hasil_audit / $count_hasil : 0;
		$avg_proses = $count_proses ? $proses_audit / $count_proses : 0;
		$avg_auditor= $count_auditor? $auditor / $count_auditor : 0;

		$data = [
			'hasil_audit'  => round($avg_hasil,2),
			'proses_audit' => round($avg_proses,2),
			'auditor'      => round($avg_auditor,2)
		];
		$data['total_average'] = round(($avg_hasil + $avg_proses + $avg_auditor) / 3, 2);
		
		render($data, 'json');
	}


	function get_auditor_monitoring_capa(){
		$year = post('year') ?: date('Y');
		$data = get_data('tbl_finding_records fr', [
			'select' => 'fr.site_auditee, s.section_name, fr.status_finding, sc.id as id_status_capa, a.id as id_aktivitas, a.aktivitas',
			'join' => [
				'tbl_capa c on fr.id = c.id_finding type left',
				'tbl_m_audit_section s on fr.id_department_auditee = s.id',
				'tbl_status_capa sc on c.id_status_capa = sc.id',
				'tbl_sub_aktivitas sa on fr.id_sub_aktivitas = sa.id type left',
				'tbl_aktivitas a on sa.id_aktivitas = a.id type left',
			],
			'where' => [
				'YEAR(fr.tgl_mulai_audit)' => $year
			],
			'order_by' => 's.section_name, a.aktivitas',
			'sort' => 'ASC'
		])->result_array();
		
		$clean_data = [];
		$last_dept = null;
		$last_site = null;
		$last_aktivitas = null;

		$counter_finding = [
			'0' => 0, //Open
			'1' => 0, //Delivered
			'2' => 0  //Closed
		];

		$counter_capa = [
			'1' => 0, //Delivered
			'2' => 0, //On-Progress
			'3' => 0, //Done
			'4' => 0, //Pending
			'5' => 0, //Cancel
			'6' => 0, //DeadlineExceeded
		];

		foreach ($data as $v) {
			$dept = $v['section_name'];
			$site = $v['site_auditee'];
			$status = $v['status_finding'];
			$capa = $v['id_status_capa'];
			$aktivitas = $v['aktivitas'];

			// Jika ganti department
			if ($last_aktivitas !== null && $last_aktivitas !== $aktivitas) {
				$clean_data[] = [
					'site' => $last_site,
					'dept' => $last_dept,
					'finding' => $counter_finding,
					'capa' => $counter_capa,
					'aktivitas' => $last_aktivitas
				];
				// reset counter
				$counter_finding = ['0' => 0, '1' => 0, '2' => 0];
				$counter_capa = ['1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0,];
			}

			if (isset($counter_finding[$status])) {
				$counter_finding[$status]++;
			}

			if (isset($counter_capa[$capa])) {
				$counter_capa[$capa]++;
			}

			$last_dept = $dept;
			$last_site = $site;
			$last_aktivitas = $aktivitas;
		}
		
		// Tambahkan data terakhir
		if ($last_aktivitas !== null) {
			$clean_data[] = [
				'site' => $last_site,
				'dept' => $last_dept,
				'finding' => $counter_finding,
				'capa' => $counter_capa,
				'aktivitas' => $last_aktivitas
			];
		}
		render($clean_data, 'json');
	}

	function get_audit_plan_data (){
		$year = post('year') ?: date('Y');
		$data = get_data('tbl_annual_audit_plan_group', [
			'select' => 'status, count(*) as jumlah',
			'where' => [
				'year' => $year
			],
			'group_by' => 'status'
		])->result_array();
		$result = [];
		foreach($data as $v){
			$result[$v['status']] = $v['jumlah'];
		}
		render($result, 'json');
	}

	function get_capa_plan_progress(){
		$year = post('year') ?: date('Y');
		$result = get_data('tbl_finding_records fr', [
			'select' => 'fr.site_auditee, s.section_name, fr.status_finding, sc.id as id_status_capa, a.id as id_aktivitas, a.aktivitas',
			'where' => [
				'YEAR(fr.tgl_mulai_audit)' => $year
			],
			'join' => [
				'tbl_capa c on fr.id = c.id_finding',
				'tbl_status_capa sc on c.id_status_capa = sc.id', 
				'tbl_m_audit_section s on fr.id_department_auditee = s.id ',
				'tbl_sub_aktivitas sa on fr.id_sub_aktivitas = sa.id ',
				'tbl_aktivitas a on sa.id_aktivitas = a.id'
			]
		])->result_array();
		
		$factory = 0;
		$ho = 0;
		$capa = 0;
		$completed = 0;
		$total = 0;
		foreach($result as $v){
			if($v['site_auditee'] == 'Factory'){
				$factory++;
			}else{
				$ho++;
			}

			if(in_array($v['id_status_capa'], ['1','2'])){ // delivered on progress
				$capa++;
				$total++;
			}elseif($v['id_status_capa'] == '3'){ // done
				$completed++;
				$total++;
			}
		}

		$data['factory'] = $factory;;
		$data['ho'] = $ho;
		$data['capa'] = $capa;
		$data['completed'] = $completed;
		$data['total'] = $total;
		render($data, 'json');
	}
}