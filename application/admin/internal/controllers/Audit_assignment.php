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
					'status' => $row['status'],
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
			'select' => 'aa.id, aa.kriteria, aa.pengujian, aa.hasil_review, aa.unconformity, aa.dampak, aa.root_cause, aa.recomendation, aa.finding, aa.bobot_finding, aa.status_finding, ag.year, a.id as id_audit_plan, a.id_audit_plan_group, dep.section_name as department,ak.id as id_aktivitas, ak.aktivitas, sa.id as id_sub_aktivitas, sa.sub_aktivitas, rc.id_risk, s.section_name as section, af.filename',
			'join' => [
				'tbl_audit_universe u on a.id_universe = u.id', 
				'tbl_rcm rcm on u.id_rcm = rcm.id',
				'tbl_risk_control rc on rcm.id_risk_control = rc.id',
				'tbl_m_audit_section s on rcm.id_section = s.id',
				'tbl_m_audit_section dep on s.level4 = dep.id',
				'tbl_sub_aktivitas sa on rcm.id_sub_aktivitas = sa.id',
				'tbl_aktivitas ak on sa.id_aktivitas = ak.id',
				'tbl_annual_audit_plan_group ag on ag.id = a.id_audit_plan_group',
				'tbl_individual_audit_assignment aa on aa.id_audit_plan = a.id',
				'tbl_individual_audit_assignment_files af on af.id_audit_assignment = aa.id type left'
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
			$internal_control = get_data('tbl_internal_control','id_aktivitas',$row['id_aktivitas'])->result_array();
			$row['internal_control'] = $internal_control;
			$row['status_finding'] = get_data('tbl_status_finding_control','id',$row['status_finding'])->row_array()['description'] ?? '';
			$row['bobot_finding'] = get_data('tbl_bobot_status_audit','id',$row['bobot_finding'])->row_array()['bobot'] ?? '';
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
			$field => $value
		];

		$resp = update_data('tbl_individual_audit_assignment',$data, 'id', $id_assignment);
		
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
		$id = post('id');
		$original_name = $this->input->post('original_name');
		$files = $this->input->post('file');
		$response = [
			'status' => 'success',
			'message' => 'Successfully uploaded file.'
		];
		if($files){
			foreach($files as $i => $v){
				$parts = explode('.', $v);
				$extension = end($parts);
				$fileName = $original_name[$i];
				$file = uniqid() . '.' . $extension;
				$destination = FCPATH . 'assets/uploads/assignment_file/'. $file;
				if(copy($v, $destination)) {
					save_data('tbl_individual_audit_assignment_files', [
					'id_audit_assignment' => $id,
					'filename'	=> $fileName,
					'file' => $file,
					'created_at' => date('Y-m-d H:i:s'),
					'created_by' => user('id')
					]);
				}else{
					$response = [
						'status' => 'error',
						'message' => 'Failed to upload file.'
					];
				}
			}
		}
		render($response,'json');
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

	function mark_completed(){
		$id_plangroup = post('id_audit_plan_group');
		$audit_group = get_data('tbl_annual_audit_plan','id_audit_plan_group',$id_plangroup)->result_array();
		$id_plan = array_column($audit_group, 'id');
		$data = get_data('tbl_individual_audit_assignment','id_audit_plan',$id_plan)->result_array();
		
		$data_plangroup = get_data('tbl_annual_audit_plan_group','id',$id_plangroup)->row_array();
		$data_finding = [];
		foreach($data as $row){
			$id_audit_plan = $row['id_audit_plan'];
			$plan = get_data('tbl_annual_audit_plan ap',[
				'select' => 'ag.*,rcm.id_section as id_section, ms.section_name, ms.description as site_audit, sa.id as id_audit_area, sa.sub_aktivitas as audit_area',
				'join' => [
					'tbl_annual_audit_plan_group ag on ap.id_audit_plan_group = ag.id type left',
					'tbl_audit_universe au on ap.id_universe = au.id type left',
					'tbl_rcm rcm on au.id_rcm = rcm.id type left',
					'tbl_m_audit_section ms on rcm.id_section = ms.id type left',
					'tbl_sub_aktivitas sa on rcm.id_sub_aktivitas = sa.id type left',
				],
				'where' => [
					'ap.id' => $id_audit_plan
				]
			])->row_array();
			
			$detail_schedule = get_detail_schedule_audit($plan['schedule_audit'] ?? '');
			$schedule = !empty($detail_schedule) ? $detail_schedule['nomor'] : '';
			$data_finding[] = [
				'id_assignment' => $row['id'],
				'id_schedule' => $detail_schedule['id'],
				'periode_audit' => $schedule,
				'id_institusi_audit' => '1',
				'auditor' => $plan['auditor'],
				'nama_auditor' => get_detail_auditor($plan['auditor'])['nama'],
				'tgl_mulai_audit' => $plan['start_date'],
				'tgl_akhir_audit' => $plan['end_date'],
				// 'auditee' => get_detail_auditee($plan['auditee'])['nama'],	
				'auditee' => $plan['auditee'],
				'site_auditee' => $plan['site_audit'],
				'id_department_auditee' => $plan['id_department'],
				'id_divisi' => get_detail_department($plan['id_department'])['level3'],
				'id_section_department' => $plan['id_section'],
				'audit_area' => $plan['audit_area'],
				'id_sub_aktivitas' => $plan['id_audit_area'],
				'finding' => $row['finding'],
				'bobot_finding' => get_detail_bobot($row['bobot_finding'])['bobot'],
				'status_finding_control' => $row['status_finding'],
				'status_finding' => '0' 
			];
		}
		foreach($data_finding as $finding){
			if(empty($finding['finding'])) continue;
			if(empty($finding['auditor']) || empty($finding['auditee'])){
				render([
					'status' => 'error',
					'message' => 'Pastikan Auditee dan Auditor tidak kosong pada Annual Audit Plan!'
				],'json');
				return;
			}
			$cek = get_data('tbl_finding_records', 'id_assignment', $finding['id_assignment'])->row_array();
			$finding['id'] = $cek['id'] ?? 0;
			save_data('tbl_finding_records',$finding);
		}
		
		$resp = update_data('tbl_individual_audit_assignment',['status' => 'history'],'id_audit_plan',$id_plan);
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

	function get_bobot_name(){
		$id = post('id');
		$data = get_data('tbl_bobot_status_audit', 'id', $id)->row_array();
		
		render($data,'json');
	}

	function get_status_finding(){
		$id = post('id');

		$data = get_data('tbl_status_finding_control','id',$id)->row_array();
		render($data,'json');
	}

	function get_attachments(){
		$id = post('id');

		$data = get_data('tbl_individual_audit_assignment_files af',[
			'select' => 'aa.id as id_assignment, af.id as id_file, af.*',
			'join' => [
				'tbl_individual_audit_assignment aa on aa.id = af.id_audit_assignment type left'
			],
			'where' => [
				'af.id_audit_assignment' => $id
			]
		])->result_array();
		render($data,'json');
	}

	function delete_file(){
		$id = post('id');

		$resp = delete_data('tbl_individual_audit_assignment_files','id', $id);
		if($resp){
			$response = [
				'status' => 'success',
				'message' => lang('data_berhasil_dihapus')
			];
		}else{
			$response = [
				'status' => 'error',
				'message' => lang('data_gagal_dihapus')
			];
		}
		render($response,'json');
	}

	function download_file(){
		$id = get('id');
		$row = get_data('tbl_individual_audit_assignment_files','id',$id)->row_array();
		if(!$row) exit;

		$path = FCPATH.'assets/uploads/assignment_file/'.$row['file'];
		if(!is_file($path)) exit;

		$downloadName = $row['filename'];
		$extStored = pathinfo($row['file'], PATHINFO_EXTENSION);
		$extGiven = pathinfo($downloadName, PATHINFO_EXTENSION);
		if(!$extGiven){
			$downloadName .= '.'.$extStored;
		}

		$mime = function_exists('mime_content_type') ? mime_content_type($path) : 'application/octet-stream';
		header('Content-Description: File Transfer');
		header('Content-Type: '.$mime);
		header('Content-Disposition: attachment; filename="'.addslashes($downloadName).'"');
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: '.filesize($path));
		readfile($path);
		exit;
	}
}