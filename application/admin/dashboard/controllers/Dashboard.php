<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		$year = get_data('tbl_finding_records', [
			'select' => 'min(year(tgl_mulai_audit)) as min_year',
		])->row_array();
		
		render(['year' => $year]);
	}

	function get_data_finding_control(){
		$nip = user('kode');
		$auditee = get_data('tbl_detail_auditee', 'nip', $nip)->result_array();
		$section = array_column($auditee, 'id_section');
		
		$year = post('year');
		$where = [
			'year(tgl_mulai_audit)' => $year
		];
		$user_group = user('id_group');
		if($user_group == USER_GROUP_USER){
			$where['id_section_department'] = $section ?: '';
		}
		
		$data_finding = get_data('tbl_finding_records', [
			'select' => 'status_finding_control, count(*) as jumlah',
			'where' => $where,
			'group_by' => 'status_finding_control'
		])->result_array();
	
		$status_label = [
			'0' => 'Undefined',
			'1' => 'Tidak ada',
			'2' => 'Tidak efektif',
			'3' => 'Tidak sesuai'
		];

		$total = array_sum(array_column($data_finding, 'jumlah'));
		$data = [];
		if($data_finding){
			foreach($data_finding as $row){
				$data[] = [
					'label' => $status_label[$row['status_finding_control']],
					'jumlah' => $row['jumlah'],
					'persentase' => $total ? round($row['jumlah'] / $total * 100, 1) : 0
				];
			}
		}
		render($data, 'json');
	}

	
	function get_finding_control_dept(){
		$nip = user('kode');
		$auditee = get_data('tbl_detail_auditee', 'nip', $nip)->result_array();
		$section = array_column($auditee, 'id_section') ?: '';
		
		$user_group = user('id_group');

		$year = post('year');
		$where = [
			'year(tgl_mulai_audit)' => $year
		];
		$is_admin = 0; // 1 True, 0 False
		if($user_group != USER_GROUP_USER){ // 1 = dev, 41 = Internal Audit
			$data_section = get_data('tbl_m_audit_section',[
				'where' => [
					'level4 !=' => '0',
					'level5' => '0'
				]
			])->result_array();
			$is_admin = 1;
		}else{
			$data_section = get_data('tbl_m_audit_section', 'id', $section)->result_array();
			$where['id_section_department'] = $section;
		}

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
			'select' => 's.section_name, s.parent_id, fr.id_section_department,fr.status_finding_control, count(*) as jumlah',
			'join' => [
				'tbl_m_audit_section s on fr.id_section_department = s.id'
			],
			'where' => $where,
			'group_by' => 'id_section_department, status_finding_control'
		])->result_array();
		
		$clean_data = [
			'dept' => $dept,
			'data' => $data,
			'is_admin' => $is_admin
		];
		render($clean_data, 'json');
	}
	function get_data_risk_control_pie(){
		$nip = user('kode');
		$auditee = get_data('tbl_detail_auditee', 'nip', $nip)->result_array();
		$section = array_column($auditee, 'id_section');
		
		$year = post('year');
		$where = [
			'year(tgl_mulai_audit)' => $year
		];
		$user_group = user('id_group');
		if($user_group == USER_GROUP_USER){
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
		$is_admin = 0; // 1 True, 0 False
		if($user_group != USER_GROUP_USER){ // 1 = dev, 41 = Internal Audit
			$data_section = get_data('tbl_m_audit_section',[
				'where' => [
					'level4 !=' => '0',
					'level5' => '0'
				]
			])->result_array();
			$is_admin = 1;
		}else{
			$data_section = get_data('tbl_m_audit_section', 'id', $section)->result_array();
			$where['id_section_department'] = $section;
		}

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
		
		$clean_data = [
			'dept' => $dept,
			'data' => $data,
			'is_admin' => $is_admin
		];
		render($clean_data, 'json');
	}

	function get_data_monitoring_capa(){
		$year = post('year');
		$data = get_data('tbl_finding_records fr', [
			'select' => 'fr.site_auditee, s.section_name, fr.status_finding, sc.id as id_status_capa, a.aktivitas',
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
			'order_by' => 's.section_name',
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

}