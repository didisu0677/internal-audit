<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use FontLib\Table\Type\post;

class Audit_assignment extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		$filter = get('filter') ?? 'active';
		$where = ['aa.status' => 'active'];
		
		if($filter != 'active'){
			$where = ['aa.status' => 'history'];
		}
		$query = get_data('tbl_annual_audit_plan a', [
			'select' => 'aa.id as id_audit_assignment, concat(dep.section_name, " - ",dep.description) as label_department, a.id as id_audit_plan, a.id_audit_plan_group, dep.section_name as department, ak.aktivitas, sa.sub_aktivitas, rc.id_risk, s.section_name as section, ag.year, aa.status',
			'join' => [
				'tbl_audit_universe u on a.id_universe = u.id', 
				'tbl_rcm rcm on u.id_rcm = rcm.id',
				'tbl_risk_control rc on rcm.id_risk_control = rc.id',
				'tbl_m_audit_section s on rcm.id_section = s.id',
				'tbl_m_audit_section dep on s.level4 = dep.id',
				'tbl_sub_aktivitas sa on rcm.id_sub_aktivitas = sa.id',
				'tbl_aktivitas ak on sa.id_aktivitas = ak.id',
				'tbl_annual_audit_plan_group ag on ag.id = a.id_audit_plan_group',
				'tbl_individual_audit_assignment aa on aa.id_audit_plan = a.id'
			],		
			'where' => $where,
			'order_by' => 'ag.year, dep.urutan'
		])->result_array();

		foreach($query as $row) {
			$y = $row['year'];
			$dept_key = $row['label_department'];
			if(!isset($data_grouped[$y][$dept_key])) {
				$data_grouped[$y][$dept_key] = [
					'id_audit_plan_group' => $row['id_audit_plan_group'],
					'section' => $row['section'],
					'count' => 0
				];
			}
			$data_grouped[$y][$dept_key]['count']++;
		}
		
		render([
			'data' => $data_grouped ?? [],
			'filter' => $filter,
		]);
	}

	function data() {
		$id = post('id');
		$query = get_data('tbl_annual_audit_plan a', [
			'select' => 'aa.*, a.id as id_audit_plan, a.id_audit_plan_group, dep.section_name as department,ak.id as id_aktivitas, ak.aktivitas, sa.sub_aktivitas, rc.id_risk, s.section_name as section',
			'join' => [
				'tbl_audit_universe u on a.id_universe = u.id', 
				'tbl_rcm rcm on u.id_rcm = rcm.id',
				'tbl_risk_control rc on rcm.id_risk_control = rc.id',
				'tbl_m_audit_section s on rcm.id_section = s.id',
				'tbl_m_audit_section dep on s.level4 = dep.id',
				'tbl_sub_aktivitas sa on rcm.id_sub_aktivitas = sa.id',
				'tbl_aktivitas ak on sa.id_aktivitas = ak.id',
				'tbl_annual_audit_plan_group ag on ag.id = a.id_audit_plan_group',
				'tbl_individual_audit_assignment aa on aa.id_audit_plan = a.id'
			],		
			'where' => [
				'a.id_audit_plan_group' => $id
			],
			'order_by' => 'dep.urutan'
		])->result_array();

		$clean_data = [];
		foreach($query as $row) {
			$id_risk = json_decode($row['id_risk'],true);
			$data_risk = get_data('tbl_risk_register','id',$id_risk)->result_array();
			// $risk = implode(', ',array_column($data_risk,'risk'));
			$internal_control = get_data('tbl_internal_control','id_aktivitas',$row['id_aktivitas'])->result_array();
			// $str_internal_control = implode(', ',array_column($internal_control,'internal_control'));
			$row['internal_control'] = $internal_control;
			$row_data = $row;
			$row_data['risk'] = $data_risk;
			$clean_data[] = $row_data;
		}
		render($clean_data,'json');
	}

	function get_data() {
		$data_grouped = [];
		$query = get_data('tbl_annual_audit_plan a', [
			'select' => 'aa.id as id_audit_assignment,a.id as id_audit_plan, a.id_audit_plan_group, dep.section_name as department, ak.aktivitas, sa.sub_aktivitas, rc.id_risk, s.section_name as section',
			'join' => [
				'tbl_audit_universe u on a.id_universe = u.id', 
				'tbl_rcm rcm on u.id_rcm = rcm.id',
				'tbl_risk_control rc on rcm.id_risk_control = rc.id',
				'tbl_m_audit_section s on rcm.id_section = s.id',
				'tbl_m_audit_section dep on s.level4 = dep.id',
				'tbl_sub_aktivitas sa on rcm.id_sub_aktivitas = sa.id',
				'tbl_aktivitas ak on sa.id_aktivitas = ak.id',
				'tbl_annual_audit_plan_group ag on ag.id = a.id_audit_plan_group',
				'tbl_individual_audit_assignment aa on aa.id_audit_plan_group = ag.id'
			],		
			'order_by' => 'dep.urutan'
		])->result_array();

		foreach($query as $row) {
			$data_grouped[$row['department']]['id_assignment'] = $row['id_audit_assignment'];
			$data_grouped[$row['department']][$row['section']][] = $row;
		}
		return $data_grouped;
	}

	function save() {
		// $response = save_data('tbl_individual_audit_assignment',post(),post(':validation'));
		// render($response,'json');
		$id_assignment = post('id');
		$field = post('field');
		$value = post('value');
		
		$data = [
			'id' => $id_assignment,
			$field => $value
		];
		$resp = save_data('tbl_individual_audit_assignment',$data);
		
		if($resp){
			$response = [
				'status' => 'success',
				'message' => lang('data_berhasil_disimpan')
			];
		} else {
			$response = [
				'status' => 'error',
				'message' => lang('data_gagal_disimpan')
			];
		}
		render($response,'json');
	}

	function delete() {
		$response = destroy_data('tbl_individual_audit_assignment','id',post('id'));
		render($response,'json');
	}

	function template() {
		ini_set('memory_limit', '-1');
		$arr = ['id_plan' => 'id_plan','id_risk_control' => 'id_risk_control','audit_start_date' => 'audit_start_date','audit_end_date' => 'audit_end_date','audit_closing_date' => 'audit_closing_date','auditor' => 'auditor','auditee' => 'auditee','review_result' => 'review_result','finding' => 'finding','bobot_finding' => 'bobot_finding','unconformity' => 'unconformity','risk_finding' => 'risk_finding','root_cause' => 'root_cause','recomendation' => 'recomendation','status_finding' => 'status_finding','capa' => 'capa','deadline_capa' => 'deadline_capa','pic_capa' => 'pic_capa'];
		$config[] = [
			'title' => 'template_import_audit_assignment',
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function attach_file(){
		debug(post());die;
	}

	function import() {
		ini_set('memory_limit', '-1');
		$file = post('fileimport');
		$col = ['id_plan','id_risk_control','audit_start_date','audit_end_date','audit_closing_date','auditor','auditee','review_result','finding','bobot_finding','unconformity','risk_finding','root_cause','recomendation','status_finding','capa','deadline_capa','pic_capa'];
		$this->load->library('simpleexcel');
		$this->simpleexcel->define_column($col);
		$jml = $this->simpleexcel->read($file);
		$c = 0;
		foreach($jml as $i => $k) {
			if($i==0) {
				for($j = 2; $j <= $k; $j++) {
					$data = $this->simpleexcel->parsing($i,$j);
					$data['create_at'] = date('Y-m-d H:i:s');
					$data['create_by'] = user('nama');
					$save = insert_data('tbl_individual_audit_assignment',$data);
					if($save) $c++;
				}
			}
		}
		$response = [
			'status' => 'success',
			'message' => $c.' '.lang('data_berhasil_disimpan').'.'
		];
		@unlink($file);
		render($response,'json');
	}

	function export() {
		ini_set('memory_limit', '-1');
		$arr = ['id_plan' => 'Id Plan','id_risk_control' => 'Id Risk Control','audit_start_date' => '-dAudit Start Date','audit_end_date' => '-dAudit End Date','audit_closing_date' => '-dAudit Closing Date','auditor' => 'Auditor','auditee' => 'Auditee','review_result' => 'Review Result','finding' => 'Finding','bobot_finding' => 'Bobot Finding','unconformity' => 'Unconformity','risk_finding' => 'Risk Finding','root_cause' => 'Root Cause','recomendation' => 'Recomendation','status_finding' => 'Status Finding','capa' => 'Capa','deadline_capa' => '-dDeadline Capa','pic_capa' => 'Pic Capa'];
		$data = get_data('tbl_individual_audit_assignment')->result_array();
		$config = [
			'title' => 'data_audit_assignment',
			'data' => $data,
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	// function update_field() {
	// 	$id_assignment = post('id');
	// 	$field = post('field');
	// 	$value = post('value');
		
	// 	$resp = update_data('tbl_individual_audit_assignment',[$field => $value],'id',$id_assignment);
	// 	if($resp){
	// 		$response = [
	// 			'status' => 'success',
	// 			'message' => lang('data_berhasil_disimpan')
	// 		];
	// 	} else {
	// 		$response = [
	// 			'status' => 'error',
	// 			'message' => lang('data_gagal_disimpan')
	// 		];
	// 	}
	// 	render($response,'json');
	// }

}