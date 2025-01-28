<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Section_dept extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		$data['company']   = get_data('tbl_m_company','is_active',1)->result_array();
		$data['location']   = get_data('tbl_location','is_active',1)->result_array();
		$data['divisi']   = get_data('tbl_m_divisi','is_active', 1)->result_array();
		$data['department']   = get_data('tbl_m_department','is_active',1)->result_array();
		render($data);
	}

	function data() {
		$data = data_serverside();
		render($data,'json');
	}

	function get_data() {
		$data = get_data('tbl_section_department a',[
			'select' => 'a.*,b.kode as kode_perusahaan, b.perusahaan, c.kode_lokasi,c.lokasi,d.kode as kode_divisi,d.divisi,
						 e.kode as kode_department, e.department',
			'join' => ['tbl_m_company b on a.id_company = b.id type LEFT',
					   'tbl_location c on a.id_location = c.id type LEFT',
					   'tbl_m_divisi d on a.id_divisi = d.id type LEFT',
					   'tbl_m_department e on a.id_department = e.id type LEFT',
					  ],
			'where' => [
				'a.id' => post('id')
			],
		])->row_array();

		render($data,'json');
	}

	function save() {
		$data = post();

		$comp = get_data('tbl_section_department','id',$data['id'])->row();
		if(isset($comp->id)) {
			 $data['id'] = $comp->id_company ;
		}else{
			 $data['id'] = 0;
		}

		$data['kode'] = $data['kode_perusahaan'] ;

		$response = save_data('tbl_m_company',$data,post(':validation'));
		render($response,'json');
	}

	function save_location() {
		$data = post();

		$comp = get_data('tbl_section_department','id',$data['id'])->row();

		if(isset($comp->id)) {
			 $data['id'] = $comp->id_location ;
		}else{
			 $data['id'] = 0;
		}

		$response = save_data('tbl_location',$data,post(':validation'));
		render($response,'json');
	}


	function save_divisi() {
		$data = post();

		$comp = get_data('tbl_section_department','id',$data['id'])->row();
		if(isset($comp->id)) {
			 $data['id'] = $comp->id_divisi ;
		}else{
			 $data['id'] = 0;
		}

		$data['id_lokasi'] = $data['divid_lokasi'] ;

		$response = save_data('tbl_location',$data,post(':validation'));
		render($response,'json');
	}

	function save_department() {
		$data = post();

		$comp = get_data('tbl_section_department','id',$data['id'])->row();
		if(isset($comp->id)) {
			 $data['id'] = $comp->id_department ;
		}else{
			 $data['id'] = 0;
		}

		$data['id_lokasi'] = $data['deptid_lokasi'] ;
		$data['id_divisi'] = $data['deptid_divisi'] ;

		$response = save_data('tbl_m_department',$data,post(':validation'));
		render($response,'json');
	}

	function save_section() {
		$data = post();

		$data['id_lokasi'] = $data['secid_lokasi'] ;
		$data['id_divisi'] = $data['secid_divisi'] ;

		$data['kode'] = $data['kode_section'];
		$data['section'] = $data['section_dept'] ;

		$response = save_data('tbl_section_department',$data,post(':validation'));
		render($response,'json');
	}

	function delete() {
		$response = destroy_data('tbl_m_department','id',post('id'));
		render($response,'json');
	}

	function template() {
		ini_set('memory_limit', '-1');
		$arr = ['kode' => 'kode','department' => 'department','is_active' => 'is_active'];
		$config[] = [
			'title' => 'template_import_department',
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function import() {
		ini_set('memory_limit', '-1');
		$file = post('fileimport');
		$col = ['kode','department','is_active'];
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
					$save = insert_data('tbl_m_department',$data);
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
		$arr = ['kode' => 'Kode','department' => 'Department','is_active' => 'Aktif'];
		$data = get_data('tbl_m_department')->result_array();
		$config = [
			'title' => 'data_department',
			'data' => $data,
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

}