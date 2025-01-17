<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Aktivitas extends BE_Controller {

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
		$data = get_data('tbl_aktivitas','id',post('id'))->row_array();
		render($data,'json');
	}

	function save() {
		$response = save_data('tbl_aktivitas',post(),post(':validation'));
		render($response,'json');
	}

	function delete() {
		$response = destroy_data('tbl_aktivitas','id',post('id'));
		render($response,'json');
	}

	function template() {
		ini_set('memory_limit', '-1');
		$arr = ['company' => 'company','site_auditee' => 'site_auditee','id_divisi_auditee' => 'id_divisi_auditee','id_department_auditee' => 'id_department_auditee','id_section_auditee' => 'id_section_auditee','aktivitas' => 'aktivitas','audit_area' => 'audit_area','id_type_aktivitas' => 'id_type_aktivitas','is_active' => 'is_active'];
		$config[] = [
			'title' => 'template_import_aktivitas',
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function import() {
		ini_set('memory_limit', '-1');
		$file = post('fileimport');
		$col = ['company','site_auditee','id_divisi_auditee','id_department_auditee','id_section_auditee','aktivitas','audit_area','id_type_aktivitas','is_active'];
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
					$save = insert_data('tbl_aktivitas',$data);
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
		$arr = ['company' => 'Company','site_auditee' => 'Site Auditee','id_divisi_auditee' => 'Id Divisi Auditee','id_department_auditee' => 'Id Department Auditee','id_section_auditee' => 'Id Section Auditee','aktivitas' => 'Aktivitas','audit_area' => 'Audit Area','id_type_aktivitas' => 'Id Type Aktivitas','is_active' => 'Aktif'];
		$data = get_data('tbl_aktivitas')->result_array();
		$config = [
			'title' => 'data_aktivitas',
			'data' => $data,
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

}