<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auditee extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		$data['department'] = get_data('tbl_m_department a',[
			'select' => 'a.id, CONCAT(b.divisi,"-",a.department) as department',
			'join'   => 'tbl_m_divisi b on a.id_divisi = b.id type LEFT',
			'where' => [
					'a.is_active' => 1
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
		$data = get_data('tbl_auditee','id',post('id'))->row_array();
		$data['id_section']		= json_decode($data['id_section'],true);

		render($data,'json');
	}

	function save() {
		$data = post();
		$data['id_section'] = json_encode(post('id_section'));
		$id_section = post('id_section');
		$response = save_data('tbl_auditee',$data,post(':validation'));

		if($response['status'] == 'success') {
			delete_data('tbl_detail_auditee','nip',$data['nip']);

			$section = '';
			foreach($id_section as $v => $k){
				insert_data('tbl_detail_auditee',[
					'nip' => $data['nip'],
					'id_department' => $data['id_department'],
					'id_section' => $k,
					'is_active' => 1
				]);


				$sect = get_data('tbl_section_department','id',$k)->row();
				if($section =''){
					$section = $sect->section;
				}else{
					$section = $section .', ' . $sec->section;
				}

				update_data('tbl_auditee',['section' => $section],['id' => $response['id']]);
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
		$res['section'] = get_data('tbl_section_department', [
			'select' => '*',
			'where' => [
				'id_department' => $dept,
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

}