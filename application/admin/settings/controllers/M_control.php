<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_control extends BE_Controller {

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
		$data = get_data('tbl_m_internal_control','id',post('id'))->row_array();
		render($data,'json');
	}

	function save() {
		$response = save_data('tbl_m_internal_control',post(),post(':validation'));
		render($response,'json');
	}

	function delete() {
		$response = destroy_data('tbl_m_internal_control','id',post('id'));
		render($response,'json');
	}

	function template() {
		ini_set('memory_limit', '-1');
		$arr = ['internal_control' => 'internal_control','location_control' => 'location_control','no_pnp' => 'no_pnp','jenis_pnp' => 'jenis_pnp','penerbit_pnp' => 'penerbit_pnp','tanggal_pnp' => 'tanggal_pnp','is_active' => 'is_active'];
		$config[] = [
			'title' => 'template_import_m_control',
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function import() {
		ini_set('memory_limit', '-1');
		$file = post('fileimport');
		$col = ['internal_control','location_control','no_pnp','jenis_pnp','penerbit_pnp','tanggal_pnp','is_active'];
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
					$save = insert_data('tbl_m_internal_control',$data);
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
		$arr = ['internal_control' => 'Internal Control','location_control' => 'Location Control','no_pnp' => 'No Pnp','jenis_pnp' => 'Jenis Pnp','penerbit_pnp' => 'Penerbit Pnp','tanggal_pnp' => '-dTanggal Pnp','is_active' => 'Aktif'];
		$data = get_data('tbl_m_internal_control')->result_array();
		$config = [
			'title' => 'data_m_control',
			'data' => $data,
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

}