<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auditee extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		// $data['department'] = get_data('tbl_m_department a',[
		// 	'select' => 'a.id, CONCAT(b.divisi,"-",a.department) as department',
		// 	'join'   => 'tbl_m_divisi b on a.id_divisi = b.id type LEFT',
		// 	'where' => [
		// 			'a.is_active' => 1
		// 		],
		// 	])->result_array();

		$data['department'] = get_data('tbl_m_audit_section a',[
			'select' => 'a.section_code, CONCAT(a.description,"-",a.section_name) as department',
			'where' => [
					'a.is_active' => 1,
					'a.id_group_section' => 3
				],
			])->result_array();

		

		$data['section'] = get_data('tbl_section_department','is_active',1)->result_array();
	
		render($data);
	}

	function data() {
		$data = data_serverside();
		render($data,'json');
	}

	function get_data() {
		$data = get_data('tbl_auditee a',[
			'select' => 'a.*,b.section_code',
			'join'   => 'tbl_m_audit_section b on a.id_department = b.id type LEFT',
			'where' => [
				'a.id' => post('id'),
			],
		])->row_array();

		$data['id_section']		= json_decode($data['id_section'],true);

		render($data,'json');
	}

	function save() {
		$data = post();
		if($data['id'] == 0){
			$old = get_data('tbl_auditee', 'nip', $data['nip'])->row_array();
			if(!empty($old)){
				$response = [
					'status' => 'info',
					'message' => 'NIP sudah terdaftar.'
				];
				render($response,'json');
				return;
			}
		} 
		$data['id_section'] = json_encode(post('id_section'));
		$id_section = post('id_section');

		$dept = get_data('tbl_m_audit_section','section_code',$data['id_department1'])->row();
		if(isset($dept->id)) $data['id_department'] = $dept->id;

		$response = save_data('tbl_auditee',$data,post(':validation'));

		if($response['status'] == 'success') {
			delete_data('tbl_detail_auditee','nip',$data['nip']);

			$section = '';
			foreach($id_section as $v => $k){
				insert_data('tbl_detail_auditee',[
					'nip' => $data['nip'],
					'id_department' => $data['id_department1'],
					'id_section' => $k,
					'is_active' => 1
				]);


				$sect = get_data('tbl_m_audit_section','id',$k)->row();
				if($section ==''){
					$section = $sect->section_name;
				}else{
					$section = $section . ', ' . $sect->section_name;
				}
			}
			update_data('tbl_auditee',['section' => $section],['id' => $response['id']]);

			$user = get_data('tbl_user',[
				'where' => [
						'username' => $data['nip'],
					],
				])->row();
			if(!isset($user->id)){
				$data_user = [
					'id' => 0,
					'kode' => $data['nip'],
					'username' => $data['nip'],
					'nama' => $data['nama'],
					'id_group' => 38,
					'email' => $data['email'],
					'password' => 'P455w0rd!',
					'is_active' => 1
				];


				$data_user['password']   = $data_user['password'] ? 
				password_hash(md5($data_user['password']),PASSWORD_DEFAULT,array('cost'=>COST)) : 
				password_hash(md5($data_user['kode']),PASSWORD_DEFAULT,array('cost'=>COST));

				$response_user = save_data('tbl_user',$data_user);
				if($response_user=='succes') {
					update_data('tbl_user',[
						'change_password_by'    => user('nama'),
						'change_password_at'    => date('Y-m-d H:i:s')
					],'id',$response_user['id']);
					$check  = get_data('tbl_history_password',[
						'where' => [
							'id_user'   => $response['id'],
							'password'  => md5(post('password'))
						]
					])->row();
					if(isset($check->id)) {
						update_data('tbl_history_password',['tanggal'=>date('Y-m-d H:i:s')],'id',$check->id);
					} else {
						insert_data('tbl_history_password',[
							'id_user'   => $response['id'],
							'password'  => md5(post('password')),
							'tanggal'   => date('Y-m-d H:i:s')
						]);
					}
				}
			}
		}
		render($response,'json');
	}

	function delete() {
		$response = destroy_data('tbl_auditee','id',post('id'));
		render($response,'json');
	}

	function get_section(){
		$dept = post('dept');
		$res['section'] = get_data('tbl_m_audit_section', [
			'select' => '*',
			'where' => [
				'SUBSTRING(section_code,3,2)' => $dept,
				'is_active' => 1
			]
		])->result_array();

		render($res['section'], 'json');
	
		}

	function template() {
		ini_set('memory_limit', '-1');
		$arr = ['nip' => 'nip','email' => 'email','nama' => 'nama','id_department' => 'id_department','is_active' => 'is_active'];
		$config[] = [
			'title' => 'template_import_auditee',
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function import() {
		ini_set('memory_limit', '-1');
		$file = post('fileimport');
		$col = ['nip','email','nama','id_department','is_active'];
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
					$save = insert_data('tbl_auditee',$data);
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
		$arr = ['nip' => 'Nip','email' => 'Email','nama' => 'Nama','id_department' => 'Id Department','is_active' => 'Aktif'];
		$data = get_data('tbl_auditee')->result_array();
		$config = [
			'title' => 'data_auditee',
			'data' => $data,
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}


	function get_user_details(){
		$id_user = post('id');
		$user = get_data('tbl_user','id',$id_user)->row_array();
		
		render($user,'json');
	}
}