<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Kuisioner extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		$data = [];
		$schedule = get_data('tbl_schedule_audit', [
			'select' => 'id, nomor, deskripsi, concat(nomor, '.' " | " '.', deskripsi) as nomor_deskripsi',
		])->result_array();
		
		$tahun = [];
		foreach($schedule as $val){
			$periode = explode('/', $val['nomor']);
			$tahun[] = end($periode);
		}
		
		rsort($tahun, SORT_NUMERIC); // sort descending 
		$data['tahun'] = array_values(array_unique($tahun));
		$data['data'] = $schedule;
		render($data);
	}

	function entry($token = null){
		
		$nomor = base64_decode($token);
		
		// $data['data'] = get_data('tbl_finding_records fr',[
		// 	'select' => 'a.nama, ad.section_name as department, fr.nama_auditor, fr.periode_audit',
		// 	'join' => [
		// 		'tbl_m_audit_section ad on fr.id_department_auditee = ad.id',
		// 		'tbl_auditee a on fr.auditee = a.id',
		// 		'tbl_user u on a.nip = u.kode'
		// 	],
		// 	'where' =>[
		// 		'u.kode' => user('kode')
		// 	],
		// 	// 'group_by' => 'fr.periode_audit'
		// ])->result_array();
		$data['nomor'] = $nomor;
		$data['question'] = get_data('tbl_m_kuisioner', 'is_active', '1')->result_array();
		// debug($data);die;
		render($data);
	}

	function check_kuisioner(){
		$user_nip = user('kode');
		$data = get_data('tbl_kuisioner_respon r', [
			'select' => 'r.*, a.nama',
			'join' => [
				'tbl_auditee a on r.id_auditee = a.id'
			],
			'where' => [
				'a.nip' => $user_nip,
				'r.status' => '0`'
			]
		])->result_array();
		render($data,'json');
	}
	function send_kuisioner(){
		$periode = post('periode');
		$auditee = $this->input->post('auditee');
		$resp = [];
		
		foreach($auditee as $val){
			$data = get_data('tbl_auditee', 'id', $val)->row_array();
			
			$dataInsert = [
				'id_auditee'     => $val,
				'periode_audit'	 => $periode,
				'status'	     => '0'
			];

			insert_data('tbl_kuisioner_respon', $dataInsert);
			$nomor = base64_encode($periode);

			$status = send_mail([
				'subject'		=> 'Permintaan Pengisian Kuisioner Setelah Audit',
				'to'			=> $data['email'],
				'nama_user'		=> $data['nama'],
				'url'			=> base_url('internal/kuisioner/entry/'.$nomor),
				'view'			=> 'internal/kuisioner/mailer_send_kuisioner'
			]);

			if($status['status'] !== 'success'){
				$status['nip'] = $data['nip'];
				$status['status'] = 'failed';
			}
			$resp[] = $status;
		}
		render($resp, 'json');
	}

	function get_list_periode(){
		$input = post();
		$data = get_data('tbl_schedule_audit', [
			'where' => [
				'__m' => 'nomor like "%'.$input['tahun'].'%"'
			]
		])->result_array();
		render($data, 'json');
	}

	function get_detail_periode_audit(){
		$id = post('id');
		$schedule = get_data('tbl_schedule_audit a', [
			'select' => 'a.nomor, a.deskripsi',
			'where' => [
				'a.id' => $id
			]
		])->row_array();
		$responden = get_data('tbl_kuisioner_respon a', [
			'select' => 'a.status, b.nama',
			'join' => [
				'tbl_auditee b on a.id_auditee = b.id'
			],
			'where' => [
				'periode_audit' => $schedule['nomor']
			]
		])->result_array();
		
		
		$data['data'] = $schedule;
		$data['responden'] = $responden;

		render($data, 'json');
	}

	function save(){
		$data = post();
		$auditee = get_data('tbl_auditee', 'nip', user('kode'))->row_array();
		$respon = [];
		for($i=1;$i<=10;$i++){
			$respon[] = $data['question'.$i];
		}

		$dataUpdate = [
			'respon' => json_encode($respon),
			'komentar' => $data['komentar'],
			'status' => '1',
			'submitted_at' => date('Y-m-d H:i:s')
		];
		
		$resp = update_data('tbl_kuisioner_respon',$dataUpdate, [
			'id_auditee' => $auditee['id'],
			'periode_audit' => $data['nomor']
		]);
		if($resp){
			$response = [
				'status' => 'success',
				'message' => 'Success!'
			];
		}else{
			$response = [
				'status' => 'error',
				'message' => 'Error!'
			];
		}
		render($response, 'json');
	}
}