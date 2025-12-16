<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use FontLib\Table\Type\post;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

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
			'select' => 'aa.id, aa.kriteria, aa.pengujian, aa.hasil_review, aa.unconfirmity, aa.dampak, aa.root_cause, aa.recomendation, aa.finding, aa.bobot_finding, aa.status_finding, aa.status, ag.year, a.id as id_audit_plan, a.id_audit_plan_group, dep.section_name as department,ak.id as id_aktivitas, ak.aktivitas, sa.id as id_sub_aktivitas, sa.sub_aktivitas, rc.id_risk, s.section_name as section, af.filename',
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
			$kriteria = json_decode($row['kriteria'], true);
			$data_kriteria = get_data('tbl_kriteria', 'id', $kriteria)->result_array();
			$internal_control = get_data('tbl_internal_control','id_aktivitas',$row['id_aktivitas'])->result_array();
			$row['internal_control'] = $internal_control;
			$row['status_finding'] = get_data('tbl_status_finding_control','id',$row['status_finding'])->row_array()['description'] ?? '';
			$row['bobot_finding'] = get_data('tbl_bobot_status_audit','id',$row['bobot_finding'])->row_array()['bobot'] ?? '';
			$row['kriteria'] = $this->get_detail_kriteria($data_kriteria);
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
		$value = $this->input->post('value');
		if($field == 'kriteria'){
			$value = json_encode($value);
		}
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
		$arr = ['id_plan' => 'id_plan','id_risk_control' => 'id_risk_control','audit_start_date' => 'audit_start_date','audit_end_date' => 'audit_end_date','audit_closing_date' => 'audit_closing_date','auditor' => 'auditor','auditee' => 'auditee','review_result' => 'review_result','finding' => 'finding','bobot_finding' => 'bobot_finding','unconfirmity' => 'unconfirmity','risk_finding' => 'risk_finding','root_cause' => 'root_cause','recomendation' => 'recomendation','status_finding' => 'status_finding','capa' => 'capa','deadline_capa' => 'deadline_capa','pic_capa' => 'pic_capa'];
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
		$col = ['id_plan','id_risk_control','audit_start_date','audit_end_date','audit_closing_date','auditor','auditee','review_result','finding','bobot_finding','unconfirmity','risk_finding','root_cause','recomendation','status_finding','capa','deadline_capa','pic_capa'];
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
		$arr = ['id_plan' => 'Id Plan','id_risk_control' => 'Id Risk Control','audit_start_date' => '-dAudit Start Date','audit_end_date' => '-dAudit End Date','audit_closing_date' => '-dAudit Closing Date','auditor' => 'Auditor','auditee' => 'Auditee','review_result' => 'Review Result','finding' => 'Finding','bobot_finding' => 'Bobot Finding','unconfirmity' => 'unconfirmity','risk_finding' => 'Risk Finding','root_cause' => 'Root Cause','recomendation' => 'Recomendation','status_finding' => 'Status Finding','capa' => 'Capa','deadline_capa' => '-dDeadline Capa','pic_capa' => 'Pic Capa'];
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
			$auditor = get_detail_auditor($plan['auditor']);
			$divisi = get_detail_department($plan['id_department']);
			$bobot = get_detail_bobot($row['bobot_finding']);
			
			if($plan['status'] != 'completed'){
				render([
					'status' => 'info',
					'message' => 'Pastikan status Annual Audit Plan sudah "Completed" sebelum menandai Assignment sebagai Completed!'
				],'json');
				return;
			}
			if(!empty($row['finding']) && empty($row['status_finding'])){
				render([
					'status' => 'info',
					'message' => 'Pastikan status finding diisi jika ada finding!'
				],'json');
				return;
			}

			if(!empty($row['finding']) && empty($row['bobot_finding'])){
				render([
					'status' => 'info',
					'message' => 'Pastikan bobot finding diisi jika ada finding!'
				],'json');
				return;
			}
			if(empty($detail_schedule)){
				render([
					'status' => 'info',
					'message' => 'Pastikan Schedule Audit/Surat Tugas sudah terisi pada Annual Audit Plan!'
				],'json');
				return;
			}
			$data_finding[] = [
				'id_assignment' => $row['id'],
				'id_schedule' => $detail_schedule['id'],
				'periode_audit' => $schedule,
				'id_institusi_audit' => '1',
				'auditor' => $plan['auditor'],
				'nama_auditor' => $auditor ? $auditor['nama'] : '',
				'tgl_mulai_audit' => $plan['start_date'],
				'tgl_akhir_audit' => $plan['end_date'],
				// 'auditee' => get_detail_auditee($plan['auditee'])['nama'],	
				'auditee' => $plan['auditee'],
				'site_auditee' => $plan['site_audit'],
				'id_department_auditee' => $plan['id_department'],
				'id_divisi' => $divisi ? $divisi['level3'] : '',
				'id_section_department' => $plan['id_section'],
				'audit_area' => $plan['audit_area'],
				'id_sub_aktivitas' => $plan['id_audit_area'],
				'finding' => $row['finding'],
				'bobot_finding' => $bobot ? $bobot['bobot'] : '',
				'status_finding_control' => $row['status_finding'],
				'status_finding' => '0' 
			];
		}
		foreach($data_finding as $finding){
			if(empty($finding['finding'])) continue;
			if(empty($finding['auditor']) || empty($finding['auditee'])){
				render([
					'status' => 'info',
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

	function get_detail_kriteria($input){
		$kriteria = [];
		foreach($input as $id){
			$detail = get_data('tbl_kriteria', 'id', $id)->row_array()['detail'];
	        $kriteria[] = '<p class="bg-light p-2 rounded">' . $detail . '</p>';
			// $kriteria[] = get_data('tbl_kriteria', 'id', $id)->row_array()['detail'];
		}
		$string = implode(' ', $kriteria);
		return $string;
	}

	function get_kriteria_string(){
		$input = $this->input->post('data');
		foreach($input as $id){
			$detail = get_data('tbl_kriteria', 'id', $id)->row_array()['detail'];
        	$kriteria[] = '<p class="bg-light p-2 rounded">' . $detail . '</p>';
			// $kriteria[] = get_data('tbl_kriteria', 'id', $id)->row_array()['detail'];
		}
		$string = implode(' ', $kriteria);
		render ($string,'json');
	}

	function get_detail_assignment(){
		$input = post('id');
		$data = get_data('tbl_individual_audit_assignment','id',$input)->row_array();
		render($data, 'json');
	}

	function download_report(){
		ini_set('memory_limit', '-1');
		$id_audit_plan_group = get('id_audit_plan_group');
		
		$data = get_data('tbl_annual_audit_plan a', [
			'select' => 'ag.start_date, ag.end_date, ag.auditee, ag.auditor, aa.root_cause, aa.recomendation, aa.unconfirmity, aa.finding, aa.bobot_finding, a.id as id_audit_plan, a.id_audit_plan_group, ak.id as id_aktivitas, ak.aktivitas, sa.id as id_sub_aktivitas, rc.id_risk, s.section_name as section, dep.section_name as department',
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
				'a.id_audit_plan_group' => $id_audit_plan_group
			],
			'order_by' => 'dep.urutan'
		])->result_array();
		foreach($data as $item) {
			$auditor = get_detail_auditor($item['auditor']);
			$item['auditor'] = $auditor ? $auditor['nama'] : '';

			$auditee = get_detail_auditee($item['auditee']);
			$item['auditee'] = $auditee ? $auditee['nama'] : '';

			$item['bobot_finding'] = get_data('tbl_bobot_status_audit','id',$item['bobot_finding'])->row_array()['bobot'] ?? '';
			$id_risk = json_decode($item['id_risk'],true);
			$data_risk = get_data('tbl_risk_register','id',$id_risk)->result_array();
			$internal_control = get_data('tbl_internal_control','id_aktivitas',$item['id_aktivitas'])->result_array();
			$item['internal_control'] = '> ' . implode('<br>> ', array_column($internal_control, 'internal_control'));
			$item['risk'] = '> '. implode('<br>> ', array_column($data_risk, 'risk'));
			$row_data = $item;
			$clean_data[] = $row_data;
		}
		$spreadsheet = new Spreadsheet();
		$sheetIndex = 0;

		foreach ($clean_data as $row) {

			// Buat sheet baru kecuali pertama
			if ($sheetIndex == 0) {
				$sheet = $spreadsheet->getActiveSheet();
			} else {
				$sheet = $spreadsheet->createSheet();
			}

			$sheet->setTitle('Temuan ' . ($sheetIndex + 1));

			// ================= JUDUL =================
			$sheet->setCellValue('A2', 'DETIL TEMUAN AUDIT');
			$sheet->mergeCells('A2:F2');
			$sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14);
			$sheet->getStyle('A2')->getAlignment()->setHorizontal('center');

			// ================= INFO =================
			$sheet->setCellValue('A4', 'Tanggal audit');
			$sheet->setCellValue('B4', ': ' . date('d F Y', strtotime($row['start_date'])) . ' - ' . date('d F Y', strtotime($row['end_date'])));

			$sheet->setCellValue('A5', 'Entitas');
			$sheet->setCellValue('B5', ': ' . $row['aktivitas']);

			$sheet->setCellValue('D4', 'Auditee');
			$sheet->setCellValue('E4', ': ' . $row['auditee']);

			$sheet->setCellValue('D5', 'Auditor');
			$sheet->setCellValue('E5', ': ' . $row['auditor']);

			// ================= HEADER =================
			$sheet->setCellValue('A7', $row['department']);
			$sheet->mergeCells('A7:C7');

			$sheet->setCellValue('D7', 'Bobot : ' . ($row['bobot_finding'] ?? ''));
			$sheet->mergeCells('D7:F7');

			// ================= HEADER TABEL ATAS =================
			$sheet->setCellValue('A8', 'Finding');
			$sheet->mergeCells('A8:B8');

			$sheet->setCellValue('C8', 'Internal Control Existing');
			$sheet->mergeCells('C8:D8');

			$sheet->setCellValue('E8', 'Non-Conformance');
			$sheet->mergeCells('E8:F8');

			// ================= ISI TABEL ATAS =================
			$sheet->mergeCells('A9:B14');
			$sheet->mergeCells('C9:D14');
			$sheet->mergeCells('E9:F14');

			$sheet->setCellValue('A9', $this->html_to_excel_text($row['finding']));
			$sheet->setCellValue('C9', $this->html_to_excel_text($row['internal_control']));
			$sheet->setCellValue('E9', $this->html_to_excel_text($row['unconfirmity']));

			// ================= HEADER TABEL BAWAH =================
			$sheet->setCellValue('A15', 'Risiko');
			$sheet->mergeCells('A15:B15');

			$sheet->setCellValue('C15', 'Root Cause');
			$sheet->mergeCells('C15:D15');

			$sheet->setCellValue('E15', 'Recommendation');
			$sheet->mergeCells('E15:F15');

			// ================= ISI TABEL BAWAH =================
			$sheet->mergeCells('A16:B22');
			$sheet->mergeCells('C16:D22');
			$sheet->mergeCells('E16:F22');

			$sheet->setCellValue('A16', $this->html_to_excel_text($row['risk']));
			$sheet->setCellValue('C16', $this->html_to_excel_text($row['root_cause']));
			$sheet->setCellValue('E16', $this->html_to_excel_text($row['recomendation']));

			// ================= STYLE =================
			$sheet->getStyle('A8:F8')->getFont()->setBold(true);
			$sheet->getStyle('A15:F15')->getFont()->setBold(true);

			$sheet->getStyle('A8:F22')->getAlignment()
				->setWrapText(true)
				->setVertical('top');

			// Border
			$sheet->getStyle('A7:F22')->getBorders()->getAllBorders()->setBorderStyle('thin');

			// Lebar kolom
			foreach (['A'=>20,'B'=>20,'C'=>25,'D'=>25,'E'=>25,'F'=>25] as $col=>$width) {
				$sheet->getColumnDimension($col)->setWidth($width);
			}

			// Print setting (1 halaman)
			$sheet->getPageSetup()
				->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4)
				->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT)
				->setFitToWidth(1)
				->setFitToHeight(1);

			$sheetIndex++;
		}

		// ================= DOWNLOAD =================
		$filename = 'Detil_Temuan_Audit.xlsx';
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header('Cache-Control: max-age=0');

		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');
		exit;
	}

	function html_to_excel_text($html){
		if (empty($html)) {
			return '';
		}

		// Decode HTML entity
		$html = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');

		// Normalisasi newline
		$html = str_replace(["\r\n", "\r"], "\n", $html);

		// <br> → newline
		$html = preg_replace('/<br\s*\/?>/i', "\n", $html);

		// </p> → newline
		$html = preg_replace('/<\/p>/i', "\n", $html);

		// <li> → bullet
		$html = preg_replace('/<li>/i', "• ", $html);
		$html = preg_replace('/<\/li>/i', "\n", $html);

		// Hapus <ul>, <ol>, <p>
		$html = preg_replace('/<\/?(ul|ol|p)>/i', '', $html);

		// Bullet pakai ">"
		$html = preg_replace('/^\s*>\s*/m', '• ', $html);

		// Hapus semua tag HTML tersisa
		$html = strip_tags($html);

		// Rapikan spasi berlebih
		$html = preg_replace("/[ \t]+/", ' ', $html);

		// Rapikan newline (maks 1 baris kosong)
		$html = preg_replace("/\n{3,}/", "\n\n", $html);

		return trim($html);
	}


}