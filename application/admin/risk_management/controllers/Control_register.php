<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Control_register extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		render();
	}

	function data() {
		$data = data_serverside();
		render($data,'json');
	}

	function get_data() {
		$data = get_data('tbl_internal_control a',[
			'select' => 'a.id, a.id_aktivitas, a.id_sub_aktivitas, b.aktivitas as aktivitas_id_data, c.id as id_sub_aktivitas, c.sub_aktivitas as audit_area',
			'join' => ['tbl_aktivitas b on a.id_aktivitas = b.id type LEFT',
					   'tbl_sub_aktivitas c on a.id_sub_aktivitas = c.id type LEFT',
					  ],
			'where' => [
				'a.id' => post('id')
			],
		])->row_array();

		$data['ctrl_item'] = get_data('tbl_internal_control',[
			'where' => [
				'id_aktivitas' => $data['id_aktivitas'],
				'id_sub_aktivitas' => $data['id_sub_aktivitas']
			],
			'sort_by' => 'id',
			])->result_array();


		render($data,'json');
	}

	function save() {
		$data = post();
		$data_a['id'] = $data['id_aktivitas'];
		$data_a['aktivitas'] = $data['aktivitas_id_data'];
		$data_a['is_active'] = $data['is_active'];

		$id_control = post('id_control');
		$id_m_control = post('id_m_control');
		$ctrl_existing = post('ctrl_existing');
		$ctrl_location = post('ctrl_location');
		$no_pnp = post('no_pnp');
		$jenis_pnp = post('jenis_pnp');
		$penerbit = post('penerbit');
		$tgl_pnp = post('tgl_pnp');


		if(!empty($ctrl_existing)) {
			$response = save_data('tbl_aktivitas',$data_a,post(':validation'));
			if($response['status'] == 'success') {
				$sub = save_data('tbl_sub_aktivitas',[
					'id' => $data['id_sub_aktivitas'],
					'id_aktivitas' => $response['id'],
					'sub_aktivitas' => $data['audit_area']
				]);
			}
			
			
			$res_vc = [];
			if(is_array($ctrl_existing)){
				foreach($ctrl_existing as $c => $vc) {
					$data_i = [
						'id'	=> $id_m_control[$c],
						'internal_control'=>$ctrl_existing[$c],
						'location_control' => $ctrl_location[$c],
						'no_pnp' => $no_pnp[$c],
						'jenis_pnp' => $jenis_pnp[$c],
						'penerbit_pnp' => $penerbit[$c],
						'tanggal_pnp' => $tgl_pnp[$c],
						'is_active'=>1
					];

					$int = save_data('tbl_m_internal_control', $data_i);

					$data_c = [
						'id'	=> $id_control[$c],
						'id_aktivitas'=>$response['id'],
						'id_sub_aktivitas'=>$sub['id'],
						'id_internal_control' => $int['id'],
						'internal_control'=>$ctrl_existing[$c],
						'location_control' => $ctrl_location[$c],
						'no_pnp' => $no_pnp[$c],
						'jenis_pnp' => $jenis_pnp[$c],
						'penerbit_pnp' => $penerbit[$c],
						'tanggal_pnp' => $tgl_pnp[$c],
						'is_active'=>1
					];

					$res_vc1 = save_data('tbl_internal_control',$data_c);
					$res_vc[] = $res_vc1['id'];
				}

				delete_data('tbl_internal_control',['id not' => $res_vc, 'id_aktivitas' => $response['id'], 'id_internal_control' => $int['id']]);
			}


		}else{
			$response = [
				'status' => 'info',
				'message' => 'Isi Control Risk terlebih dahulu'
			];
		}

		render($response,'json');
	}

	function delete() {

		$cek = get_data('tbl_internal_control','id',post('id'))->row();

		$response = destroy_data('tbl_internal_control',['id_aktivitas'=>$cek->id_aktivitas,'id_sub_aktivitas'=>$cek->id_sub_aktivitas]);


		render($response,'json');
	}

	function template() {
		ini_set('memory_limit', '-1');
		$arr = ['internal_control' => 'internal_control','is_active' => 'is_active'];
		$config[] = [
			'title' => 'template_import_control_register',
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function import() {
		ini_set('memory_limit', '-1');
		$file = post('fileimport');
		$col = ['internal_control','is_active'];
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
					$save = insert_data('tbl_internal_control',$data);
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
		$arr = ['internal_control' => 'Internal Control','is_active' => 'Aktif'];
		$data = get_data('tbl_internal_control')->result_array();
		$config = [
			'title' => 'data_control_register',
			'data' => $data,
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function browse_req() {
		$data['layout']	= 'browse';
		// $data['id'] = $id;
		render($data);
	}

	function browse_ctrl() {
		$data['layout']	= 'browse';
		// $data['id'] = $id;
		render($data);
	}

	function data_aktivitas() {
		$config 					= [
			'access_edit'			=> false,
			'access_view'			=> false,
			'access_delete'			=> false,
		];
		$config['button'][] 	= button_serverside('btn-success','btn-act-choose',['fa-check',lang('pilih'),true],'btn-act-choose');
		$data = data_serverside($config);
		render($data,'json');
	}

	function add_aktivitas(){
		// $id_cs = post('cs');
		$id = post('id');
		$cs = get_data('tbl_aktivitas', 'id', $id)->row_array();
		render([
			'status'	=> 'success',
			'message'	=> lang('data_berhasil_diperbaharui'),
			'id_aktivitas'		=> $cs['id'],
			'aktivitas'		=> $cs['aktivitas'],
		],'json');
	}

	function data_control() {
		$config 					= [
			'access_edit'			=> false,
			'access_view'			=> false,
			'access_delete'			=> false,
		];
		$config['button'][] 	= button_serverside('btn-success','btn-act-choose1',['fa-check',lang('pilih'),true],'btn-act-choose1');
		$data = data_serverside($config);
		render($data,'json');
	}

	function add_new_control(){
		
		$id = post('id');
		$cs = get_data('tbl_m_internal_control', 'id', $id)->row_array();
		
		$response = [
			'status' => 'ok',
			'message'	=> lang('data_berhasil_diperbaharui'),
			'id_m_control'		=> $cs['id'],
			'internal_control'  => $cs['internal_control'],
			'location_control'		=> $cs['location_control'],
			'no_pnp' => $cs['no_pnp'],
			'jenis_pnp' => $cs['jenis_pnp'],
			'penerbit_pnp' => $cs['penerbit_pnp'],
			'tanggal_pnp' => $cs['tanggal_pnp']
		];

		
		render($response, 'json');
		
	}

}