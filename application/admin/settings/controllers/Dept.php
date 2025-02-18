<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dept extends BE_Controller {

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
		$data = get_data('tbl_m_audit_section','id',post('id'))->row_array();
		render($data,'json');
	}

	function save() {
		$response = save_data('tbl_m_audit_section',post(),post(':validation'));
		render($response,'json');
	}

	function delete() {
		$response = destroy_data('tbl_m_audit_section','id',post('id'));
		render($response,'json');
	}

	function template() {
		ini_set('memory_limit', '-1');
		$arr = ['parent_id' => 'parent_id','section_code' => 'section_code','section_name' => 'section_name','description' => 'description','group_section' => 'group_section','id_group_section' => 'id_group_section','urutan' => 'urutan','is_active' => 'is_active'];
		$config[] = [
			'title' => 'template_import_dept',
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function import() {
		ini_set('memory_limit', '-1');
		$file = post('fileimport');
		$col = ['parent_id','section_code','section_name','description','group_section','id_group_section','urutan','is_active'];
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
					$save = insert_data('tbl_m_audit_section',$data);
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
		$arr = ['parent_id' => 'Parent Id','section_code' => 'Section Code','section_name' => 'Section Name','description' => 'Description','group_section' => 'Group Section','id_group_section' => 'Id Group Section','urutan' => 'Urutan','is_active' => 'Aktif'];
		$data = get_data('tbl_m_audit_section')->result_array();
		$config = [
			'title' => 'data_dept',
			'data' => $data,
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

}