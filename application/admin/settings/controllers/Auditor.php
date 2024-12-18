<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auditor extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		$data['institusi']   = get_data('tbl_institusi_audit','is_active = 1')->result_array();
		render($data);
	}

	function data() {
		$data = data_serverside();
		render($data,'json');
	}

	function get_data() {
		$data = get_data('tbl_m_auditor','id',post('id'))->row_array();
		render($data,'json');
	}

	function save() {
		$data = post();
		$institusi = get_data('tbl_institusi_audit','id',$data['id_institusi'])->row();
		$data['institusi'] = '';
		if($institusi->nama_institusi) {
			$data['institusi'] = $institusi->nama_institusi;
		}
		$response = save_data('tbl_m_auditor',$data,post(':validation'));
		render($response,'json');
	}

	function delete() {
		$response = destroy_data('tbl_m_auditor','id',post('id'));
		render($response,'json');
	}

	function template() {
		ini_set('memory_limit', '-1');
		$arr = ['nama' => 'nama','jabatan' => 'jabatan','institusi' => 'institusi','is_active' => 'is_active'];
		$config[] = [
			'title' => 'template_import_auditor',
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function import() {
		ini_set('memory_limit', '-1');
		$file = post('fileimport');
		$col = ['nama','jabatan','institusi','is_active'];
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
					$save = insert_data('tbl_m_auditor',$data);
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
		$arr = ['nama' => 'Nama','jabatan' => 'Jabatan','institusi' => 'Institusi','is_active' => 'Aktif'];
		$data = get_data('tbl_m_auditor')->result_array();
		$config = [
			'title' => 'data_auditor',
			'data' => $data,
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

}