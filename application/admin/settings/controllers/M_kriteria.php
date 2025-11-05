<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_kriteria extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		render();
	}

	function data() {
		$conf = [
			'join' => [
				'tbl_user u on u.id = tbl_kriteria.created_by'
			]
		];
		$data = data_serverside($conf);
		render($data,'json');
	}

	function get_data() {
		$data = get_data('tbl_kriteria','id',post('id'))->row_array();
		render($data,'json');
	}

	function save() {
		$data = post();
		$data['created_by'] = user('id');
		$data['created_at'] = date('Y-m-d H:i:s');
		$response = save_data('tbl_kriteria',$data,post(':validation'));
		render($response,'json');
	}

	function delete() {
		$response = destroy_data('tbl_kriteria','id',post('id'));
		render($response,'json');
	}

	function template() {
		ini_set('memory_limit', '-1');
		$arr = ['detail' => 'detail','location' => 'location','reff_name' => 'reff_name','publisher' => 'publisher','effective_date' => 'effective_date','created_by' => 'created_by','created_at' => 'created_at'];
		$config[] = [
			'title' => 'template_import_m_kriteria',
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function import() {
		ini_set('memory_limit', '-1');
		$file = post('fileimport');
		$col = ['detail','location','reff_name','publisher','effective_date','created_by','created_at'];
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
					$save = insert_data('tbl_kriteria',$data);
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
		$arr = ['detail' => 'Detail','location' => 'Location','reff_name' => 'Reff Name','publisher' => 'Publisher','effective_date' => '-dEffective Date','created_by' => 'Created By','created_at' => '-dCreated At'];
		$data = get_data('tbl_kriteria')->result_array();
		$config = [
			'title' => 'data_m_kriteria',
			'data' => $data,
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

}