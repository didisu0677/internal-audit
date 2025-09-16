<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Kuisioner extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		$data = [];
		$year = date('Y');
		$years = [$year-1, $year];
		
		$kuisioner = get_data('tbl_kuisioner_respon',[
			'select' => 'distinct periode_audit',
		])->result_array();
		
		// $schedule = get_data('tbl_schedule_audit', [
		// 	'select' => 'id, nomor, deskripsi, concat(nomor, '.' " | " '.', deskripsi) as nomor_deskripsi',
		// ])->result_array();
		
		// $tahun = [];
		// foreach($schedule as $val){
		// 	$periode = explode('/', $val['nomor']);
		// 	$tahun[] = end($periode);
		// }
		
		// rsort($tahun, SORT_NUMERIC); // sort descending 
		$data['tahun'] = array_column($kuisioner, 'periode_audit');
		$data['data'] = $years;
		render($data);
	}

	function entry($id = null){
		
		$id = decode_id($id)[0];

		// $kuisioner = get_data('tbl_kuisioner_respon', 'id', $id)->row_array();
		$kuisioner = get_data('tbl_auditee a', [
			'select' => 'a.id as id_auditee, u.id as id_user, r.*',
			'join' => [
				'tbl_user u on a.nip = u.kode',
				'tbl_kuisioner_respon r on a.id = r.id_auditee'
			],
			'where' => [
				'r.id' => $id,
				'a.id_user' => user('id')
			]
		])->row_array();
		
		$data = [
			'nomor' => $kuisioner['periode_audit'] ?? '',
			'question' => get_data('tbl_m_kuisioner', 'is_active', '1')->result_array(),
			'status' => 'success',
			'message' => ''
		];

		if(!empty($kuisioner) && $kuisioner['status'] == '1'){
			$data['status'] = 'info';
			$data['message'] = 'Anda sudah mengisi kuisioner ini.'; 
			render($data);
			return;
		}

		if(empty($kuisioner)){
			$data['status'] = 'info';
			$data['message'] = 'Anda tidak memiliki akses untuk mengisi kuisioner ini.'; 
			render($data);
			return;
		}
		
		render($data);
	}

	function check_kuisioner(){
		$data = get_data('tbl_kuisioner_respon r', [
			'select' => 'r.*, a.nama',
			'join' => [
				'tbl_auditee a on r.id_auditee = a.id',
				'tbl_user u on a.id_user = u.id'
			],
			'where' => [
				'u.id' => user('id'),
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
			$data = get_data('tbl_auditee a', [
				'select' => 'a.id, u.id as id_user, u.*',
				'join' => [
					'tbl_user u on a.nip = u.kode'
				],
				'where' => [
					'a.id' => $val
				]
			])->row_array();
			
			$dataInsert = [
				'id_auditee'     => $val,
				'periode_audit'	 => $periode,
				'status'	     => '0'
			];

			$id_kuisioner = insert_data('tbl_kuisioner_respon', $dataInsert);

			$data_mytask = [
				'id_user' => $data['id_user'] ?? 0,
				'type' => 'questioner',
				'id_transaction' => $id_kuisioner,
				'title' => 'Pengisian Kuisioner Setelah Audit',
				'description' => 'Pengisian kuisioner ini bertujuan untuk memperoleh umpan balik setelah pelaksanaan audit.',
				'status' => 'pending'
			];
			insert_data('tbl_mytask', $data_mytask);

			$cc = get_data('tbl_user',[
				'where' => [
					'id_group' => [USER_STAFF_IA, USER_DEP_HEAD_IA]
				]
			])->result_array();

			$audit_cc = array_column($cc, 'email');
			$id = encode_id($id_kuisioner);
		
			$status = send_mail([
				'subject'		=> 'Permintaan Pengisian Kuisioner Setelah Audit',
				'to'			=> $data['email'],
				'cc'			=> $audit_cc,
				'nama_user'		=> $data['nama'],
				'url'			=> base_url('internal/kuisioner/entry/'.$id),
				'view'			=> 'internal/kuisioner/mailer_send_kuisioner'
			]);

			if($status['status'] !== 'success'){
				$status['nip'] = $data['kode'];
				$status['status'] = 'failed';
			}
			$resp[] = $status;
		}
		render($resp, 'json');
	}

	function get_list_periode(){
		$periode = post('tahun');
		$data = get_data('tbl_kuisioner_respon r', [
			'select' => 'u.kode, u.nama, r.*',
			'join' => [
				'tbl_auditee a on r.id_auditee = a.id',
				'tbl_user u on a.id_user = u.id'
			],
			'where' => [
				'r.periode_audit' => $periode
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
			$respon[] = (int) $data['question'.$i];
		}
		$ratarata = floatval(array_sum($respon) / count($respon));
		$dataUpdate = [
			'respon' => json_encode($respon),
			'nilai_akhir' => $ratarata,
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

	function export() {
		ini_set('memory_limit', '-1');
		$arr = [
			'nip' => 'NIP',
			'nama' => 'Nama',
			'section_name' => 'Section',
			'q1' => 'Q1',
			'q2' => 'Q2',
			'q3' => 'Q3',
			'q4' => 'Q4',
			'q5' => 'Q5',
			'q6' => 'Q6',
			'q7' => 'Q7',
			'q8' => 'Q8',
			'q9' => 'Q9',
			'q10' => 'Q10',
			'komentar' => 'Komentar',
			'submitted_at' => 'Submitted At'
		];

		$data = get_data('tbl_kuisioner_respon',[
			'join' => [
				'tbl_auditee a on tbl_kuisioner_respon.id_auditee = a.id',
				'tbl_m_audit_section s on a.id_department = s.id'
			],
			'where' => [
				'tbl_kuisioner_respon.status' => '1'
			]
		])->result_array();

		foreach ($data as &$row) {
			// hapus bracket dulu, lalu pecah
			$respon = trim($row['respon'], '[]');
			$arrRespon = explode(',', $respon);

			// isi ke q1..q10
			for ($i=0; $i < 10; $i++) {
				$row['q'.($i+1)] = isset($arrRespon[$i]) ? $arrRespon[$i] : null;
			}

			// kalau tidak mau simpan kolom respon asli
			unset($row['respon']);
		}

		$config = [
			'title' => 'export_kuisioner',
			'data' => $data,
			'header' => $arr,
		];
		$this->load->library('simpleexcel', $config);
		$this->simpleexcel->export();
	}

}