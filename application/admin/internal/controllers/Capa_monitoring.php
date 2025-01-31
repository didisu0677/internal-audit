<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Capa_monitoring extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		if(user('id_group') != AUDITEE){
			$data['department'] = get_data('tbl_m_department',[
				'where' => [
					'is_active' => 1,
					'__m' => 'id in (select id_department_auditee from tbl_finding_records)'
				],
				])->result_array();

				$dept =[];
				foreach($data['department'] as $d => $v) {
					$dept[] = $v['id'];
				}

				$data['tahun'] = get_data('tbl_finding_records',[
					'select' => 'distinct year(tgl_mulai_audit) as tahun',
					'where' => [
						'id_department_auditee' => $dept
					]
				])->result();

		}else{
			$dept = get_data('tbl_auditee a',[
				'select' => 'a.nip,a.id_department',
				'join' => 'tbl_user b on a.nip = b.username',
				'where' => [
					'a.nip' => user('username') 
				],
			])->row(); 

			$data['department'] = get_data('tbl_m_department',[
				'where' => [
					'is_active' => 1,
					'id' => $dept->id_department,
					'__m' => 'id in (select id_department_auditee from tbl_finding_records)'
				],
				])->result_array();

				$dept =[];
				foreach($data['department'] as $d => $v) {
					$dept[] = $v['id'];
				}

				$data['tahun'] = get_data('tbl_finding_records',[
					'select' => 'distinct year(tgl_mulai_audit) as tahun',
					'where' => [
						'id_department_auditee' => $dept
					]
				])->result();
		}
		render($data);
	}

	// function data() {
	// 	$data = data_serverside();
	// 	render($data,'json');
	// }

	function data() {


		$arr            = [
			'select'    => 'a.*,b.periode_audit, b.nama_auditor, b.finding,b.bobot_finding, c.department, d.nama as pic, e.status as status_capa', 
			'join'      =>  ['tbl_finding_records b on a.id_finding = b.id type LEFT',
							 'tbl_m_department c on b.id_department_auditee = c.id type LEFT',
							 'tbl_user d on a.pic_capa = d.username type LEFT',
							 'tbl_status_capa e on a.id_status_capa = e.id type LEFT'
							],
				'where' => [
					'a.is_active'   => 0,  
					],
		];


		$data['capa'] = get_data('tbl_capa a',$arr)->result();

		$arr1 = [
			'select' => 'a.*,b.department',
				'join' => 'tbl_m_department b on a.id_department_auditee = b.id type LEFT',
				'where' => [
					'a.is_active' => 0
				],
				'sort_by' => 'a.periode_audit'
		];

		if(post('tahun')) {
			$arr1['where']['year(a.tgl_mulai_audit)'] = post('tahun');
		}

		if(post('dept') && post('dept') != 'ALL') {
			$arr1['where']['a.id_department_auditee'] = post('dept');
		}

		$data['finding'] = get_data('tbl_finding_records a',$arr1)->result();


		render($data,'layout:false');

	}
	

	function get_data() {
		debug(post('id'));die;
		$data = get_data('tbl_capa','id',post('id'))->row_array();
		render($data,'json');
	}
	function detail($id='') {
		$data = get_data('tbl_finding_records a',[
			'select' => 'a.*, b.nama as nama_auditee, c.department',
			'join'	=> [
					'tbl_auditee b on a.auditee = b.id type LEFT',
					'tbl_m_department c on a.id_department_auditee = c.id type LEFT'
			],
			'where' => [
				'a.id'=>$id,
			],
			])->row_array();
		if(isset($data['id'])) {
			render($data,'layout:false');
		} else {
			echo lang('tidak_ada_data');
		}

	}

	function save() {
		$response = save_data('tbl_capa',post(),post(':validation'));
		render($response,'json');
	}

	function delete() {
		$response = destroy_data('tbl_capa','id',post('id'));
		render($response,'json');
	}

	function template() {
		ini_set('memory_limit', '-1');
		$arr = ['id_finding' => 'id_finding','nomor' => 'nomor','description' => 'description','deadline_capa' => 'deadline_capa','pic_capa' => 'pic_capa','status_capa' => 'status_capa','followup' => 'followup','followup_date' => 'followup_date','followup_email' => 'followup_email','followup_meeting' => 'followup_meeting','keterangan_progress' => 'keterangan_progress','capa_score' => 'capa_score','achievement' => 'achievement','evidence' => 'evidence','is_active' => 'is_active'];
		$config[] = [
			'title' => 'template_import_capa_monitoring',
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function import() {
		ini_set('memory_limit', '-1');
		$file = post('fileimport');
		$col = ['id_finding','nomor','description','deadline_capa','pic_capa','status_capa','followup','followup_date','followup_email','followup_meeting','keterangan_progress','capa_score','achievement','evidence','is_active'];
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
					$save = insert_data('tbl_capa',$data);
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
		$arr = ['id_finding' => 'Id Finding','nomor' => 'Nomor','description' => 'Description','deadline_capa' => '-dDeadline Capa','pic_capa' => 'Pic Capa','status_capa' => 'Status Capa','followup' => 'Followup','followup_date' => '-dFollowup Date','followup_email' => 'Followup Email','followup_meeting' => 'Followup Meeting','keterangan_progress' => 'Keterangan Progress','capa_score' => 'Capa Score','achievement' => 'Achievement','evidence' => 'Evidence','is_active' => 'Aktif'];
		$data = get_data('tbl_capa')->result_array();
		$config = [
			'title' => 'data_capa_monitoring',
			'data' => $data,
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

}