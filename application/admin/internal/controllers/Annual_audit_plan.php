<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Annual_audit_plan extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		$activity = [
			'SPA, STA',
			'Pemahaman Bisnis Proses (Incl. Interview)',
			'Kick off Meeting',
			'Permintaan & Pengumpulan Data',
			'On-desk & on-site Audit (Incl. Interview)',
			'Analisa Data & Perumusan Temuan',
			'Pre-exit (Konfirmasi Temuan)',
			'Exit Meeting, Questioner',
			'CAPA', 
			'LHA'
		];

		// Filter (plan | history) - default plan
		$filter = get('filter');
		if(!in_array($filter, ['plan','history'])) $filter = 'plan';
		$plan_status    = ['unplanned','planned'];
		$history_status = ['completed','canceled'];

		$year = date('Y');
		$data_clean = [];
		$query = get_data('tbl_annual_audit_plan a', [
			'select' => 'a.id as id_audit_plan, a.id_audit_plan_group, dep.id as id_department, concat(dep.section_name, " - ",dep.description) as label_department, dep.section_name as department, ak.aktivitas, sa.sub_aktivitas, ag.*, rc.id_risk',
			'join' => [
				'tbl_audit_universe u on a.id_universe = u.id', 
				'tbl_rcm rcm on u.id_rcm = rcm.id',
				'tbl_risk_control rc on rcm.id_risk_control = rc.id',
				'tbl_m_audit_section s on rcm.id_section = s.id',
				'tbl_m_audit_section dep on s.level4 = dep.id',
				'tbl_sub_aktivitas sa on rcm.id_sub_aktivitas = sa.id',
				'tbl_aktivitas ak on sa.id_aktivitas = ak.id',
				'tbl_annual_audit_plan_group ag on ag.id = a.id_audit_plan_group'
			],			
			'where' => [
				'ag.year >= ' => $year
			],
			'order_by' => 'ag.year, dep.urutan'
		])->result_array();
			
		foreach($query as $v){
			// Apply status filter early to reduce processing
			$group_status = $v['status'];
			if($filter == 'plan' && !in_array($group_status, $plan_status)) continue;
			if($filter == 'history' && !in_array($group_status, $history_status)) continue;

			if(!isset($data_clean[$v['year']][$v['label_department']]['data'])){
				$data_clean[$v['year']][$v['label_department']]['data'] = [
					'id_audit_plan_group' => $v['id_audit_plan_group'],
					'id_department' => $v['id_department'],
					'objective'    	=> $v['objective'],
					'start_date'   	=> $v['start_date'],
					'end_date'     	=> $v['end_date'],
					'closing_date' 	=> $v['closing_date'],
					'status'       	=> $v['status'],
					'auditee'     	=> $v['auditee'],
					'auditor'     	=> $v['auditor'],
					'duration'     	=> 0,
					'expense_est_total' => 0,
					'aktivitas' => []
				];
			}
			// tambahkan aktivitas baru
			$data_clean[$v['year']][$v['label_department']]['data']['aktivitas'][] = [
				'id_audit_plan' => $v['id_audit_plan'],
				'aktivitas'     => $v['aktivitas'],
				'sub_aktivitas' => $v['sub_aktivitas'],
				'id_risk'       => $v['id_risk'],
				'risk'			=> []
			];

			// ambil index aktivitas terakhir
			$lastIndex = count($data_clean[$v['year']][$v['label_department']]['data']['aktivitas']) - 1;

			// decode risk
			$id_risk = json_decode($v['id_risk'], true);
			if(is_array($id_risk)){
				foreach($id_risk as $id){
					$risk = get_data('tbl_risk_register', 'id', $id)->row_array();
					if($risk){
						$data_clean[$v['year']][$v['label_department']]['data']['aktivitas'][$lastIndex]['risk'][] = $risk;
					}
				}
			}
			
			$durasi = get_data('tbl_audit_plan_duration', 'id_audit_plan_group', $v['id_audit_plan_group'])->result_array();
			$data_clean[$v['year']][$v['label_department']]['data']['duration'] = 0;
			foreach($durasi as $d){
				if(isset($d['duration_day'])){
					$data_clean[$v['year']][$v['label_department']]['data']['duration'] += (int)$d['duration_day'];
				}
			}

			$data_clean[$v['year']][$v['label_department']]['data']['expense_est_total'] = 0;
			$expense_est= get_data('tbl_audit_plan_expense', [
				'where' => [
					'id_audit_plan_group' => $v['id_audit_plan_group'],
					'category' => 'est'
				]
			])->result_array();
			foreach($expense_est as $e){
				if(isset($e['days']) && isset($e['amount'])){
					$data_clean[$v['year']][$v['label_department']]['data']['expense_est_total'] += ((int)$e['days'] * (int)$e['amount']);
				}
			}

			$data_clean[$v['year']][$v['label_department']]['data']['expense_real_total'] = 0;
			$expense_real= get_data('tbl_audit_plan_expense', [
				'where' => [
					'id_audit_plan_group' => $v['id_audit_plan_group'],
					'category' => 'real'
				]
			])->result_array();
			foreach($expense_real as $r){
				if(isset($r['days']) && isset($r['amount'])){
					$data_clean[$v['year']][$v['label_department']]['data']['expense_real_total'] += ((int)$r['days'] * (int)$r['amount']);
				}
			}
		}
		$auditee = get_active_auditee();
		$auditor = get_active_auditor();
		$expense_item = get_data('tbl_expense_type', 'is_active', 1)->result_array();
		render([
			'data' => $data_clean,
			'expense_item' => $expense_item,
			'activity' => $activity,
			'filter' => $filter,
			'plan_status' => $plan_status,
			'history_status' => $history_status,
			'auditee' => $auditee,
			'auditor' => $auditor,
		]);
	}

	function setAuditeeAuditor(){
		$value = post('value');
		$type = post('type');
		$id_audit_plan_group = post('id_audit_plan_group');
		$id_department = post('id_department');


		$where = [
			'id' => $id_audit_plan_group,
			'id_department' => $id_department
		];

		if($type === 'auditee') {
			$data['auditee'] = $value;
		} else {
			$data['auditor'] = $value;
		}
		
		$save = update_data('tbl_annual_audit_plan_group', $data, $where);
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
	function getData(){
		$id_audit_plan_group = post('id');
		$data = get_data('tbl_annual_audit_plan a',[
			'join' => [
				'tbl_annual_audit_plan_group ag on a.id_audit_plan_group = ag.id'
			],
			'where' => [
				'a.id_audit_plan_group' => $id_audit_plan_group
			]
		])->row_array();
		render($data,'json');
	}

	function save(){
		$id= post('id_plan');
		$id_audit_plan_group= post('id_plan_group');
		$objektif = post('objektif');
		$start_date = post('start_date');
		$activity_type = post('activity_type');
		$activity = $this->input->post('activity_name');
		$start_duration = $this->input->post('start_duration');
		$end_duration = $this->input->post('end_duration');
		$duration = $this->input->post('duration');
		$expense_type = $this->input->post('expense_type');
		$expense_amount = toInt($this->input->post('expense_amount'));
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
				'id_audit_plan_group' => $id_audit_plan_group,
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

			$insertActivity = [
				'id_audit_plan_group' => $id_audit_plan_group,
				'activity_name' => $act,
				'start_date' => isset($start_duration[$i]) ? $start_duration[$i] : null,
				'duration_day' => (int)$duration[$i],
				'end_date' => isset($end_duration[$i]) ? $end_duration[$i] : null,
			];
			insert_data('tbl_audit_plan_duration', $insertActivity);
			$total_durasi += (int)$duration[$i];
		}

		$end_date = null;
		if(!empty($start_date) && !empty($total_durasi)){
			$end_date = $this->add_working_days($start_date, $total_durasi);
		}
		
		$data = [
			'id' => $id_audit_plan_group,
			'objective' => $objektif,
			'start_date' => $start_date,
			'end_date' => $end_date,
			'type' => $activity_type,
			'status' => 'planned'
		];
		$save = $id_audit_plan_group = save_data('tbl_annual_audit_plan_group', $data);
		$data_audit_plan = get_data('tbl_annual_audit_plan', 'id_audit_plan_group', $id_audit_plan_group['id'])->result_array();
		foreach($data_audit_plan as $d){
			$data_assignment = get_data('tbl_individual_audit_assignment', 'id_audit_plan', $d['id'])->row_array();
			$insert_assignment = [
				'id' => $data_assignment['id'] ?? 0,
				'id_audit_plan' => $d['id']
			];
			save_data('tbl_individual_audit_assignment', $insert_assignment);
		}
		
		// $audit_assignment = get_data('tbl_individual_audit_assignment', 'id_audit_plan_group', $id_audit_plan_group['id'])->row_array();
		// $data_audit_assignment = [
		// 	'id' => $audit_assignment['id'] ?? 0,
		// 	'id_audit_plan_group' => $id_audit_plan_group['id']
		// ];
		// $save = save_data('tbl_individual_audit_assignment', $data_audit_assignment);

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
		$id_audit_plan_group = post('id');
		$data = get_data('tbl_audit_plan_duration', 'id_audit_plan_group', $id_audit_plan_group)->result_array();
		$formated = [];
		foreach($data as $v){
			$v['start_date'] = date('Y-m-d', strtotime($v['start_date']));
			$v['end_date'] = date('Y-m-d', strtotime($v['end_date']));
			$formated[] = $v;
		}
		render($formated,'json');
	}

	function getDetailExpense(){
		$category = post('cat');
		$id_audit_plan_group = post('id');
		$data = get_data('tbl_audit_plan_expense a', [
			'join' => [
				'tbl_expense_type et on a.expense_type = et.id'
			],
			'where' => [
				'a.category' => $category,
				'a.id_audit_plan_group' => $id_audit_plan_group
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
			'id_audit_plan_group' => $id,
			'reason' => $reason,
			'canceled_at' => date('Y-m-d H:i:s'),
			'canceled_by' => user('id')
		];

		insert_data('tbl_audit_plan_canceled', $data_cancel);
		
		$data = [
			'id' => $id,
			'status' => 'canceled',
		];

		$save = save_data('tbl_annual_audit_plan_group', $data);
		
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
				'tbl_audit_plan_canceled.id_audit_plan_group' => $id
			]
		])->row_array() ?? [];
		render($data,'json');
	}

	function completedPlan(){
		$id_plan_group = post('id_plan_group');
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
				'id_audit_plan_group' => $id_plan_group,
				'expense_type' => $type,
				'category' => 'real',
				'days' => (int)$day[$i],
				'amount' => (int)$amount[$i],
				'note' => isset($note[$i]) ? $note[$i] : '',
			];
			insert_data('tbl_audit_plan_expense', $insertExpense);
			// $expense_real += ((int)$day[$i] * (int)$amount[$i]);
		}
		$response = [
			'status' => 'success',
			'message' => 'Data berhasil disimpan'
		];

		$data = [
			'id' => $id_plan_group,
			'status' => 'completed',
			'closing_date' => $closing_date,
		];
		$save = save_data('tbl_annual_audit_plan_group', $data);
		if($save){
			$data_audit_plan = get_data('tbl_annual_audit_plan', 'id_audit_plan_group', $save['id'])->result_array();
			
			$id_universe = array_column($data_audit_plan, 'id_universe'); 
			$data_universe = get_data('tbl_audit_universe', 'id', $id_universe)->result_array();
			foreach($data_universe as $universe){
				update_data('tbl_audit_universe', ['last_audit' => date('Y-m-d H:i:s')], 'id', $universe['id']);
				$this->set_new_plan($universe['id'], date('Y-m-d H:i:s'));
			}
		}else{
			$response = [
				'status' => 'error',
				'message' => 'Data gagal disimpan'
			];

		}

		render($response, 'json');
	}

	function add_plan(){
		$id_universe = $this->input->post('id_audit_universe');
		$year_plan = post('year_plan');

		$data = get_data('tbl_audit_universe u', [
			'select' => 'dep.id as id_department, u.id as id_universe',
			'join' => [
				'tbl_rcm r on u.id_rcm = r.id',
				'tbl_m_audit_section s on r.id_section = s.id',
				'tbl_m_audit_section dep on s.level4 = dep.id',
			],
			'where' =>[
				'u.id' => $id_universe,
			]
		])->result_array();
		
		$start_date = $year_plan.date('-m-d');
		
		foreach($data as $i => $d){

			$cek = get_data('tbl_annual_audit_plan a', [
				'select' => 'a.id as id_plan, ag.id_department, ag.id as id_audit_plan_group, ag.year',
				'join' => [
					'tbl_annual_audit_plan_group ag on a.id_audit_plan_group = ag.id'
				],
				'where' => [
					'ag.id_department' => $d['id_department'],
					'ag.year' => $year_plan
				]
			])->row_array();

			
			// $end_date = $year_plan.'-12-31';
			if(!$cek){
				$data_plan_group = [
					'id_department' => $d['id_department'],
					'year' => $year_plan,
					'start_date' => $start_date,
					'status' => 'unplanned'				
				];
				$id_group = save_data('tbl_annual_audit_plan_group', $data_plan_group);
			}
			
			$data_plan = [
				'id' => $cek['id'] ?? 0,
				'id_universe' => $id_universe[$i],
				'id_audit_plan_group' => $cek['id_audit_plan_group'] ?? $id_group['id'],
			];
			$save = save_data('tbl_annual_audit_plan', $data_plan);
		}
		
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

	function set_new_plan($id_universe, $last_audit){
		$audit_universe = get_data('tbl_audit_universe u',[
			'select' => 'u.*, r.*, s4.id as id_department, s4.section_name as department, s.section_name, rc.id_risk',
			'join' => [
				'tbl_rcm r on u.id_rcm = r.id',
				'tbl_m_audit_section s on r.id_section = s.id',
				'tbl_m_audit_section s4 on s.parent_id = s4.id',
				'tbl_risk_control rc on r.id_risk_control = rc.id',
			],
			'where' => [
				'u.id' => $id_universe
			]
		])->row_array();
		
		if($audit_universe){
			$id_risk = json_decode($audit_universe['id_risk'], true);
			
			$resp = [
				'status' => 'success',
				'message' => 'Success'
			];
			foreach($id_risk as $id){
				$risk = get_data('tbl_risk_register a',[
					'select' => 'a.*, b.id as id_bobot, b.status_audit, b.description',
					'join' => [
						'tbl_bobot_status_audit b on a.bobot = b.id'
					],
					'where' => [
						'a.id' => $id
					]
				])->row_array();
				$start_date = null;
				if($risk['status_audit'] == 'Tahunan'){ // moderate & major
					$start_date = date('Y-m-d', strtotime('+1 years', strtotime($last_audit)));
				}elseif($risk['status_audit'] == '2 Tahun'){
					$start_date = date('Y-m-d', strtotime('+2 years', strtotime($last_audit)));	
				}else{
					continue;
				}
				
				$cek = get_data('tbl_annual_audit_plan_group', [
					'where' => [
						'id_department' => $audit_universe['id_department'],
						'year' => date('Y', strtotime($start_date))
					]
				])->row_array();

				$data_audit_plan_group = [
					'id' =>	$cek['id'] ?? 0,				
					'id_department' => $audit_universe['id_department'],
					'year' => date('Y', strtotime($start_date)),
					'start_date' => $start_date
				];
				$id_group = save_data('tbl_annual_audit_plan_group', $data_audit_plan_group);
				if(!$id_group['status']){
					$resp = [
						'status' => 'error',
						'message' => 'Failed to create new audit plan group'
					];
					return $resp;
				}

				$data_audit_plan = [
					'id_universe' => $id_universe,
					'id_audit_plan_group' => $id_group['id'],
				];
				$audit_plan = save_data('tbl_annual_audit_plan', $data_audit_plan);
				if(!$audit_plan['status']){
					$resp = [
						'status' => 'error',
						'message' => 'Failed to create new audit plan'
					];
					return $resp;
				}
			}
		}
		return $resp;
	}

}