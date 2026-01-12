<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use FontLib\Table\Type\post;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

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
			'select' => 'aa.id, aa.kriteria, aa.pengujian, aa.hasil_review, aa.unconfirmity, aa.dampak, aa.root_cause, aa.recomendation, aa.finding, aa.bobot_finding, aa.status_finding, aa.status, ag.year, a.id as id_audit_plan, a.id_audit_plan_group, dep.section_name as department,ak.id as id_aktivitas, ak.aktivitas, sa.id as id_sub_aktivitas, sa.sub_aktivitas, rc.id_risk, s.section_name as section',
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
				// 'tbl_individual_audit_assignment_files af on af.id_audit_assignment = aa.id type left'
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
			$internal_control = get_data('tbl_internal_control','id_sub_aktivitas',$row['id_sub_aktivitas'])->result_array();
			$row['filename'] = get_data('tbl_individual_audit_assignment_files','id_audit_assignment',$row['id'])->row_array(); // cek aja minimal kalo ada 1 file
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
			
			if($plan['status'] != 'completed'){
				render([
					'status' => 'info',
					'message' => 'Pastikan status Annual Audit Plan sudah "Completed" sebelum menandai Assignment sebagai Completed!'
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
				'tbl_individual_audit_assignment aa on aa.id_audit_plan = a.id'
			],		
			'where' => [
				'a.id_audit_plan_group' => $id_audit_plan_group
			],
			'order_by' => 'dep.urutan'
		])->result_array();
		
		$clean_data = [];
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
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setTitle('Detil Temuan Audit');

		// Lebar kolom (sekali saja)
		foreach (['A'=>20,'B'=>20,'C'=>25,'D'=>25,'E'=>25,'F'=>25] as $col=>$width) {
			$sheet->getColumnDimension($col)->setWidth($width);
		}

		// Print setting (1 halaman per blok saat dicetak skala lebar)
		$sheet->getPageSetup()
			->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4)
			->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT)
			->setFitToWidth(1)
			->setFitToHeight(0);

		$startRow = 2; // baris awal blok pertama (meniru A2 sebelumnya)
		$blockHeight = 21; // baris 2..22 inklusif
		$gap = 1; // satu baris kosong antar blok

		foreach ($clean_data as $row) {
			// Hitung baris-baris relatif
			$rTitle = $startRow;               // A2
			$rInfo1 = $startRow + 2;           // A4/D4
			$rInfo2 = $startRow + 3;           // A5/D5
			$rHeader = $startRow + 5;          // A7/D7
			$rTopHead = $startRow + 6;         // A8..F8
			$rTopStart = $startRow + 7;        // A9.. (isi atas)
			$rTopEnd = $startRow + 12;         // ..B14/C14/E14
			$rBottomHead = $startRow + 13;     // A15..F15
			$rBottomStart = $startRow + 14;    // A16..F16
			$rBottomEnd = $startRow + 20;      // ..A22..F22

			// ================= JUDUL =================
			$sheet->setCellValue("A{$rTitle}", 'DETIL TEMUAN AUDIT');
			$sheet->mergeCells("A{$rTitle}:F{$rTitle}");
			$sheet->getStyle("A{$rTitle}")->getFont()->setBold(true)->setSize(14);
			$sheet->getStyle("A{$rTitle}")->getAlignment()->setHorizontal('center');

			// ================= INFO =================
			$sheet->setCellValue("A{$rInfo1}", 'Tanggal audit');
			$sheet->setCellValue("B{$rInfo1}", ': ' . date('d F Y', strtotime($row['start_date'])) . ' - ' . date('d F Y', strtotime($row['end_date'])));

			$sheet->setCellValue("A{$rInfo2}", 'Entitas');
			$sheet->setCellValue("B{$rInfo2}", ': ' . $row['aktivitas']);

			$sheet->setCellValue("D{$rInfo1}", 'Auditee');
			$sheet->setCellValue("E{$rInfo1}", ': ' . $row['auditee']);

			$sheet->setCellValue("D{$rInfo2}", 'Auditor');
			$sheet->setCellValue("E{$rInfo2}", ': ' . $row['auditor']);

			// ================= HEADER =================
			$sheet->setCellValue("A{$rHeader}", $row['department']);
			$sheet->mergeCells("A{$rHeader}:C{$rHeader}");

			$sheet->setCellValue("D{$rHeader}", 'Bobot : ' . ($row['bobot_finding'] ?? ''));
			$sheet->mergeCells("D{$rHeader}:F{$rHeader}");

			// ================= HEADER TABEL ATAS =================
			$sheet->setCellValue("A{$rTopHead}", 'Finding');
			$sheet->mergeCells("A{$rTopHead}:B{$rTopHead}");

			$sheet->setCellValue("C{$rTopHead}", 'Internal Control Existing');
			$sheet->mergeCells("C{$rTopHead}:D{$rTopHead}");

			$sheet->setCellValue("E{$rTopHead}", 'Non-Conformance');
			$sheet->mergeCells("E{$rTopHead}:F{$rTopHead}");

			// ================= ISI TABEL ATAS =================
			$sheet->mergeCells("A{$rTopStart}:B{$rTopEnd}");
			$sheet->mergeCells("C{$rTopStart}:D{$rTopEnd}");
			$sheet->mergeCells("E{$rTopStart}:F{$rTopEnd}");

			$sheet->setCellValue("A{$rTopStart}", $this->html_to_excel_text($row['finding']));
			$sheet->setCellValue("C{$rTopStart}", $this->html_to_excel_text($row['internal_control']));
			$sheet->setCellValue("E{$rTopStart}", $this->html_to_excel_text($row['unconfirmity']));

			// ================= HEADER TABEL BAWAH =================
			$sheet->setCellValue("A{$rBottomHead}", 'Risiko');
			$sheet->mergeCells("A{$rBottomHead}:B{$rBottomHead}");

			$sheet->setCellValue("C{$rBottomHead}", 'Root Cause');
			$sheet->mergeCells("C{$rBottomHead}:D{$rBottomHead}");

			$sheet->setCellValue("E{$rBottomHead}", 'Recommendation');
			$sheet->mergeCells("E{$rBottomHead}:F{$rBottomHead}");

			// ================= ISI TABEL BAWAH =================
			$sheet->mergeCells("A{$rBottomStart}:B{$rBottomEnd}");
			$sheet->mergeCells("C{$rBottomStart}:D{$rBottomEnd}");
			$sheet->mergeCells("E{$rBottomStart}:F{$rBottomEnd}");

			$sheet->setCellValue("A{$rBottomStart}", $this->html_to_excel_text($row['risk']));
			$sheet->setCellValue("C{$rBottomStart}", $this->html_to_excel_text($row['root_cause']));
			$sheet->setCellValue("E{$rBottomStart}", $this->html_to_excel_text($row['recomendation']));

			// ================= STYLE =================
			$sheet->getStyle("A{$rTopHead}:F{$rTopHead}")->getFont()->setBold(true);
			$sheet->getStyle("A{$rBottomHead}:F{$rBottomHead}")->getFont()->setBold(true);

			$sheet->getStyle("A{$rTopHead}:F{$rBottomEnd}")->getAlignment()
				->setWrapText(true)
				->setVertical('top');

			// Border untuk keseluruhan blok tabel (dari header tabel hingga akhir)
			$sheet->getStyle("A{$rHeader}:F{$rBottomEnd}")->getBorders()->getAllBorders()->setBorderStyle('thin');

			// Page break opsional per blok saat print
			$sheet->setBreak("A" . ($rBottomEnd + 2), \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);

			// Geser ke blok berikutnya
			$startRow += ($blockHeight + $gap);
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

	public function download_report_docx(){
		ini_set('memory_limit', '-1');
		$id_audit_plan_group = get('id_audit_plan_group');

		// Safety check if PhpWord not installed
		if (!class_exists('PhpOffice\\PhpWord\\PhpWord')) {
			header('HTTP/1.1 500 Internal Server Error');
			echo 'PhpWord library is not installed. Run: composer require phpoffice/phpword';
			exit;
		}

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
				'tbl_individual_audit_assignment aa on aa.id_audit_plan = a.id'
			],		
			'where' => [
				'a.id_audit_plan_group' => $id_audit_plan_group
			],
			'order_by' => 'dep.urutan'
		])->result_array();

		$clean_data = [];
		foreach($data as $item) {
			$auditor = get_detail_auditor($item['auditor']);
			$item['auditor'] = $auditor ? $auditor['nama'] : '';

			$auditee = get_detail_auditee($item['auditee']);
			$item['auditee'] = $auditee ? $auditee['nama'] : '';

			$item['bobot_finding'] = get_data('tbl_bobot_status_audit','id',$item['bobot_finding'])->row_array()['bobot'] ?? '';
			$id_risk = json_decode($item['id_risk'],true);
			$data_risk = get_data('tbl_risk_register','id',$id_risk)->result_array();
			$internal_control = get_data('tbl_internal_control','id_aktivitas',$item['id_aktivitas'])->result_array();
			$item['internal_control'] = '> ' . implode("\n> ", array_column($internal_control, 'internal_control'));
			$item['risk'] = '> '. implode("\n> ", array_column($data_risk, 'risk'));
			$clean_data[] = $item;
		}

		$phpWord = new PhpWord();
		// Set a clean, consistent default font for better readability
		$phpWord->setDefaultFontName('Calibri');
		$phpWord->setDefaultFontSize(11);
		$section = $phpWord->addSection([
			'pageSizeW' => 11906, 'pageSizeH' => 16838, // A4 portrait
			'marginLeft' => 720, 'marginRight' => 720, 'marginTop' => 720, 'marginBottom' => 720
		]);

		// Compute usable content width and standardized column widths
		$contentWidth = 11906 - (720 + 720); // page width - margins
		$colEqual = intdiv($contentWidth, 3);
		$colWidths = [$colEqual, $colEqual, $contentWidth - 2 * $colEqual];

		$tableStyle = ['borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 120, 'width' => $contentWidth];
		$headerCellStyle = ['bgColor' => 'F2F2F2', 'valign' => 'center'];
		$cellStyle = ['valign' => 'top'];
		$phpWord->addTableStyle('BlockTable', $tableStyle);
		$phpWord->addTableStyle('InfoTable', ['borderSize' => 0, 'cellMargin' => 80, 'width' => $contentWidth]);

		foreach ($clean_data as $i => $row) {
			// Title
			$section->addText('DETIL TEMUAN AUDIT', ['bold' => true, 'size' => 14], ['alignment' => 'center', 'spaceAfter' => 200]);

			// Info (aligned label/value pairs)
			$info = $section->addTable('InfoTable');
			$infoLabelWidth = 2200;
			$infoValueWidth = $contentWidth - $infoLabelWidth;
			$bold = ['bold' => true];

			$info->addRow();
			$info->addCell($infoLabelWidth)->addText('Tanggal audit', $bold);
			$info->addCell($infoValueWidth)->addText(': ' . date('d F Y', strtotime($row['start_date'])) . ' - ' . date('d F Y', strtotime($row['end_date'])));

			$info->addRow();
			$info->addCell($infoLabelWidth)->addText('Entitas', $bold);
			$info->addCell($infoValueWidth)->addText(': ' . ($row['aktivitas'] ?? ''));

			$info->addRow();
			$info->addCell($infoLabelWidth)->addText('Auditee', $bold);
			$info->addCell($infoValueWidth)->addText(': ' . ($row['auditee'] ?? ''));

			$info->addRow();
			$info->addCell($infoLabelWidth)->addText('Auditor', $bold);
			$info->addCell($infoValueWidth)->addText(': ' . ($row['auditor'] ?? ''));

			// Department + Bobot line
			$meta = $section->addTable('InfoTable');
			$meta->addRow();
			$meta->addCell(intdiv($contentWidth, 2))->addText($row['department'] ?? '');
			$meta->addCell($contentWidth - intdiv($contentWidth, 2))->addText('Bobot: ' . ($row['bobot_finding'] ?? ''), $bold, ['alignment' => 'right']);

			$section->addTextBreak(1);

			// Tabel atas (Finding/Control/Non-Conformance)
			$t1 = $section->addTable('BlockTable');
			$t1->addRow();
			$t1->addCell($colWidths[0], $headerCellStyle)->addText('Finding', $bold);
			$t1->addCell($colWidths[1], $headerCellStyle)->addText('Internal Control Existing', $bold);
			$t1->addCell($colWidths[2], $headerCellStyle)->addText('Non-Conformance', $bold);
			$t1->addRow();
			$t1->addCell($colWidths[0], $cellStyle)->addText($this->html_to_excel_text($row['finding'] ?? ''), [], ['spaceAfter' => 0]);
			$t1->addCell($colWidths[1], $cellStyle)->addText($this->html_to_excel_text($row['internal_control'] ?? ''), [], ['spaceAfter' => 0]);
			$t1->addCell($colWidths[2], $cellStyle)->addText($this->html_to_excel_text($row['unconfirmity'] ?? ''), [], ['spaceAfter' => 0]);

			$section->addTextBreak(1);

			// Tabel bawah (Risk/Root Cause/Recommendation)
			$t2 = $section->addTable('BlockTable');
			$t2->addRow();
			$t2->addCell($colWidths[0], $headerCellStyle)->addText('Risiko', $bold);
			$t2->addCell($colWidths[1], $headerCellStyle)->addText('Root Cause', $bold);
			$t2->addCell($colWidths[2], $headerCellStyle)->addText('Recommendation', $bold);
			$t2->addRow();
			$t2->addCell($colWidths[0], $cellStyle)->addText($this->html_to_excel_text($row['risk'] ?? ''), [], ['spaceAfter' => 0]);
			$t2->addCell($colWidths[1], $cellStyle)->addText($this->html_to_excel_text($row['root_cause'] ?? ''), [], ['spaceAfter' => 0]);
			$t2->addCell($colWidths[2], $cellStyle)->addText($this->html_to_excel_text($row['recomendation'] ?? ''), [], ['spaceAfter' => 0]);

			if ($i < count($clean_data) - 1) {
				$section->addPageBreak();
			}
		}

		$filename = 'Detil_Temuan_Audit.docx';
		header('Content-Description: File Transfer');
		header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
		header('Content-Disposition: attachment; filename="'.$filename.'"');
		header('Cache-Control: max-age=0');

		$writer = IOFactory::createWriter($phpWord, 'Word2007');
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