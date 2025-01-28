<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Section_deptXX extends BE_Controller {

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
		$data = get_data('tbl_section_department','id',post('id'))->row_array();
		render($data,'json');
	}

	function save() {
		$response = save_data('tbl_section_department',post(),post(':validation'));
		render($response,'json');
	}

	function delete() {
		$response = destroy_data('tbl_section_department','id',post('id'));
		render($response,'json');
	}

	function template() {
		ini_set('memory_limit', '-1');
		$arr = ['id_company' => 'id_company','id_location' => 'id_location','id_divisi' => 'id_divisi','id_department' => 'id_department','kode' => 'kode','section' => 'section','is_active' => 'is_active'];
		$config[] = [
			'title' => 'template_import_section_dept',
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function import() {
		ini_set('memory_limit', '-1');
		$file = post('fileimport');
		$col = ['id_company','id_location','id_divisi','id_department','kode','section','is_active'];
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
					$save = insert_data('tbl_section_department',$data);
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
		$arr = ['id_company' => 'Id Company','id_location' => 'Id Location','id_divisi' => 'Id Divisi','id_department' => 'Id Department','kode' => 'Kode','section' => 'Section','is_active' => 'Aktif'];
		$data = get_data('tbl_section_department')->result_array();
		$config = [
			'title' => 'data_section_dept',
			'data' => $data,
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

}