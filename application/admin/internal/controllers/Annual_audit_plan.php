<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Annual_audit_plan extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		$year = date('Y');
		$query = get_data('tbl_annual_audit_plan a', [
			'select' => 'dep.section_name as department, ak.aktivitas, sa.sub_aktivitas, a.*, rc.id_risk',
			'join' => [
				'tbl_audit_universe u on a.id_universe = u.id', 
				'tbl_rcm rcm on u.id_rcm = rcm.id',
				'tbl_risk_control rc on rcm.id_risk_control = rc.id',
				'tbl_m_audit_section s on rcm.id_section = s.id',
				'tbl_m_audit_section dep on s.level4 = dep.id',
				'tbl_sub_aktivitas sa on rcm.id_sub_aktivitas = sa.id',
				'tbl_aktivitas ak on sa.id_aktivitas = ak.id'
			],			
			'where' => [
				'year(a.start_date) >= ' => $year
			],
			'order_by' => 'year(a.start_date),dep.urutan'
		])->result_array();
		foreach($query as $i => $v){
			$id_risk = json_decode($v['id_risk']);
			foreach($id_risk as $id){
				$risk = get_data('tbl_risk_register', 'id', $id)->row_array();
				if($risk){
					$query[$i]['risk'][] = $risk;
				}	
			}
		}
		
		render(['data' => $query]);
	}

	function getData(){
		$id = post('id');
		$data = get_data('tbl_annual_audit_plan', 'id', $id)->row_array();
		render($data,'json');
	}

	function save(){
		$id = post('id_plan');
		$durasi = post('durasi');
		$expense = post('expense');
		$objektif = post('objektif');
		$start_date = post('start_date');
		$end_date = null;
		if(!empty($start_date) && !empty($durasi)){
			$end_date = $this->add_working_days($start_date, $durasi);
		}
		
		$data = [
			'id' => $id,
			'objektif' => $objektif,
			'durasi' => $durasi,
			'start_date' => $start_date,
			'end_date' => $end_date,
			'expense_est' => $expense,
		];
		$save = save_data('tbl_annual_audit_plan', $data);
		if($save){
			$response = [
				'status' => 'success',
				'message' => 'Data berhasil disimpan'
			];
		}else{
			$response = [
				'status' => 'error',
				'message' => 'Data gagal disimpan'
			];
		}
		render($response, 'json');
	}

	function add_working_days($start_date, $days) {
		$date = new DateTime($start_date);
		$added = 0;

		while ($added < $days) {
			$date->modify('+1 day');

			// format('N') menghasilkan angka 1–7 (Senin = 1, Minggu = 7)
			// Jadi kalau hasilnya < 6 (1–5 = Senin–Jumat), berarti hari kerja
			if ($date->format('N') < 6) {
				$added++; // tambahkan counter hari kerja
			}
		}
		return $date->format('Y-m-d');
	}


}