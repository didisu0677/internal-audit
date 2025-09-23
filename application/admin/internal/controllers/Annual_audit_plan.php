<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Annual_audit_plan extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		$year = date('Y');
		$query = get_data('tbl_annual_audit_plan a', [
			'select' => 'a.id as id_audit_plan, dep.section_name as department, ak.aktivitas, sa.sub_aktivitas, a.*, rc.id_risk',
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
			$query[$i]['risk'] = [];
			$query[$i]['duration'] = 0;
			$query[$i]['expense_est_total'] = 0;
			$query[$i]['expense_real_total'] = 0;

			$id_risk = json_decode($v['id_risk']);
			foreach($id_risk as $id){
				$risk = get_data('tbl_risk_register', 'id', $id)->row_array();
				if($risk){
					$query[$i]['risk'][] = $risk;
				}	
			}

			$durasi = get_data('tbl_audit_plan_duration', 'id_audit_plan', $v['id_audit_plan'])->result_array();
			foreach($durasi as $d){
				if(isset($d['duration_day'])){
					$query[$i]['duration'] += (int)$d['duration_day'];
				}
			}

			$expense_est= get_data('tbl_audit_plan_expense', [
				'where' => [
					'id_audit_plan' => $v['id_audit_plan'],
					'category' => 'est'
				]
			])->result_array();
			foreach($expense_est as $e){
				if(isset($e['days']) && isset($e['amount'])){
					$query[$i]['expense_est_total'] += ((int)$e['days'] * (int)$e['amount']);
				}
			}

			$expense_real = get_data('tbl_audit_plan_expense', [
				'where' => [
					'id_audit_plan' => $v['id_audit_plan'],
					'category' => 'real'
				]
			])->result_array();

			foreach($expense_real as $e){
				if(isset($e['days']) && isset($e['amount'])){
					$query[$i]['expense_real_total'] += ((int)$e['days'] * (int)$e['amount']);
				}
			}

		}
		$expense_item = get_data('tbl_expense_type', 'is_active', 1)->result_array();
		render([
			'data' => $query,
			'expense_item' => $expense_item]);
	}

	function getData(){
		$id = post('id');
		$data = get_data('tbl_annual_audit_plan', 'id', $id)->row_array();
		render($data,'json');
	}

	function save(){
		$id = post('id_plan');
		$objektif = post('objektif');
		$start_date = post('start_date');
		$activity = $this->input->post('activity_name');
		$duration = $this->input->post('duration');
		$expense_type = $this->input->post('expense_type');
		$expense_amount = $this->input->post('expense_amount');
		$expense_day = $this->input->post('expense_day');
		$expense_note = $this->input->post('expense_note');
		
		// $expense_est = 0;
		foreach($expense_type as $i => $type){
			if(empty($type)){
				$response = [
					'status' => 'error',
					'message' => 'Tipe expense tidak boleh kosong'
				];
				render($response, 'json');
				return;
			}
			if(!isset($expense_amount[$i]) || empty($expense_amount[$i]) || (int)$expense_amount[$i] <= 0){
				$response = [
					'status' => 'error',
					'message' => 'Amount pada expense item '.$type.' harus diisi dan lebih dari 0'
				];
				render($response, 'json');
				return;
			}
			if(!isset($expense_day[$i]) || empty($expense_day[$i]) || (int)$expense_day[$i] <= 0){
				$response = [
					'status' => 'error',
					'message' => 'Days pada expense item '.$type.' harus diisi dan lebih dari 0'
				];
				render($response, 'json');
				return;
			}

			$insertExpense = [
				'id_audit_plan' => $id,
				'expense_type' => $type,
				'category' => 'est',
				'days' => (int)$expense_day[$i],
				'amount' => (int)$expense_amount[$i],
				'note' => isset($expense_note[$i]) ? $expense_note[$i] : '',
			];
			insert_data('tbl_audit_plan_expense', $insertExpense);
			// $expense_est += ((int)$expense_day[$i] * (int)$expense_amount[$i]);
		}

		$total_durasi = 0;
		foreach($activity as $i => $act){
			if(empty($act)){
				$response = [
					'status' => 'error',
					'message' => 'Nama aktivitas tidak boleh kosong'
				];
				render($response, 'json');
				return;
			}
			if(!isset($duration[$i]) || empty($duration[$i]) || (int)$duration[$i] <= 0){
				$response = [
					'status' => 'error',
					'message' => 'Durasi pada aktivitas '.$act.' harus diisi dan lebih dari 0'
				];
				render($response, 'json');
				return;
			}

			$insertActivity = [
				'id_audit_plan' => $id,
				'activity_name' => $act,
				'duration_day' => (int)$duration[$i],
			];
			insert_data('tbl_audit_plan_duration', $insertActivity);
			$total_durasi += (int)$duration[$i];
		}

		$end_date = null;
		if(!empty($start_date) && !empty($total_durasi)){
			$end_date = $this->add_working_days($start_date, $total_durasi);
		}
		
		$data = [
			'id' => $id,
			'objektif' => $objektif,
			// 'durasi' => $total_durasi,
			'start_date' => $start_date,
			'end_date' => $end_date,
			// 'expense_est' => $expense_est,
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

	function getDetailDuration(){
		$id = post('id');
		$data = get_data('tbl_audit_plan_duration', 'id_audit_plan', $id)->result_array();
		render($data,'json');
	}

	function getDetailExpense(){
		$category = post('cat');
		$id = post('id');
		$data = get_data('tbl_audit_plan_expense a', [
			'join' => [
				'tbl_expense_type et on a.expense_type = et.id'
			],
			'where' => [
				'a.category' => $category,
				'a.id_audit_plan' => $id
			],
		])->result_array();

		render($data,'json');
	}

	function cancelPlan(){
		$id = post('id');
		$reason = post('reason');

		if(empty($reason)){
			$response = [
				'status' => 'error',
				'message' => 'Reason for cancellation is required'
			];
			render($response, 'json');
			return;
		}

		$data_cancel = [
			'id_audit_plan' => $id,
			'reason' => $reason,
			'canceled_at' => date('Y-m-d H:i:s'),
			'canceled_by' => user('id')
		];
		
		insert_data('tbl_audit_plan_canceled', $data_cancel);
		
		$data = [
			'id' => $id,
			'status' => 'canceled',
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

	function getCancelDetail(){
		$id = post('id');
		$data = get_data('tbl_audit_plan_canceled', [
			'join' => [
				'tbl_user u on tbl_audit_plan_canceled.canceled_by = u.id'
			],
			'where' => [
				'tbl_audit_plan_canceled.id_audit_plan' => $id
			]
		])->row_array() ?? [];
		render($data,'json');
	}

	function completedPlan(){
		$id = post('id_plan');
		$closing_date = post('closing_date');
		$expense = $this->input->post('expense_real_type');
		$amount = $this->input->post('expense_real_amount');
		$day = $this->input->post('expense_real_day');
		$note = $this->input->post('expense_real_note');
		
		// $expense_real = 0;
		foreach($expense as $i => $type){
			if(empty($type)){
				$response = [
					'status' => 'error',
					'message' => 'Tipe expense tidak boleh kosong'
				];
				render($response, 'json');
				return;
			}
			if(!isset($amount[$i]) || empty($amount[$i]) || (int)$amount[$i] <= 0){
				$response = [
					'status' => 'error',
					'message' => 'Amount pada expense item '.$type.' harus diisi dan lebih dari 0'
				];
				render($response, 'json');
				return;
			}
			if(!isset($day[$i]) || empty($day[$i]) || (int)$day[$i] <= 0){
				$response = [
					'status' => 'error',
					'message' => 'Days pada expense item '.$type.' harus diisi dan lebih dari 0'
				];
				render($response, 'json');
				return;
			}

			$insertExpense = [
				'id_audit_plan' => $id,
				'expense_type' => $type,
				'category' => 'real',
				'days' => (int)$day[$i],
				'amount' => (int)$amount[$i],
				'note' => isset($note[$i]) ? $note[$i] : '',
			];
			insert_data('tbl_audit_plan_expense', $insertExpense);
			// $expense_real += ((int)$day[$i] * (int)$amount[$i]);
		}

		$data = [
			'id' => $id,
			'status' => 'completed',
			'closing_date' => $closing_date,
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

	function add_plan(){
		$id_universe = post('id_audit_universe');
		$year_plan = post('year_plan');

		$cek = get_data('tbl_annual_audit_plan', [
			'where' => [
				'id_universe' => $id_universe,
			]
		])->row_array();

		if(empty($id_universe)){
			$response = [
				'status' => 'error',
				'message' => 'Audit Universe is required'
			];
			render($response, 'json');
			return;
		}

		$start_date = $year_plan.'-'.date('-m-d');
		// $end_date = $year_plan.'-12-31';

		$data = [
			'id' => $cek['id'] ?? 0,
			'id_universe' => $id_universe,
			'start_date' => $start_date,
			'status' => 'planned'
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


}