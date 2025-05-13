<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_risk extends BE_Controller {

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
		$data = get_data('tbl_risk_register','id',post('id'))->row_array();
		render($data,'json');
	}

	function save() {
		$response = save_data('tbl_risk_register',post(),post(':validation'));
		render($response,'json');
	}

	function delete() {
		$response = destroy_data('tbl_risk_register','id',post('id'));
		render($response,'json');
	}

	function template() {
		ini_set('memory_limit', '-1');
		$arr = ['risk' => 'risk','keterangan' => 'keterangan','score_dampak' => 'score_dampak','score_kemungkinan' => 'score_kemungkinan','bobot' => 'bobot','is_active' => 'is_active'];
		$config[] = [
			'title' => 'template_import_m_risk',
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function import() {
		ini_set('memory_limit', '-1');
		$file = post('fileimport');
		$col = ['risk','keterangan','score_dampak','score_kemungkinan','bobot','is_active'];
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
					$save = insert_data('tbl_risk_register',$data);
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
		$arr = ['risk' => 'Risk','keterangan' => 'Keterangan','score_dampak' => 'Score Dampak','score_kemungkinan' => 'Score Kemungkinan','bobot' => 'Bobot','is_active' => 'Aktif'];
		$data = get_data('tbl_risk_register')->result_array();
		$config = [
			'title' => 'data_m_risk',
			'data' => $data,
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

}