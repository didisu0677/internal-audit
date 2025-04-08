<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_aktivitas extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {


		$data['audit_section'][0] = get_data('tbl_m_audit_section',array('where_array'=>array('parent_id'=>0),'sort_by'=>'urutan'))->result();
		foreach($data['audit_section'][0] as $m0) {
			$data['audit_section'][$m0->id] = get_data('tbl_m_audit_section',array('where_array'=>array('parent_id'=>$m0->id),'sort_by'=>'urutan'))->result();
			$data['option'][$m0->id]['id'] = $m0->id;	
			$data['option'][$m0->id]['nama'] = $m0->section_code  .' - '. $m0->section_name;
			foreach($data['audit_section'][$m0->id] as $m1) {
				$data['audit_section'][$m1->id] = get_data('tbl_m_audit_section',array('where_array'=>array('parent_id'=>$m1->id),'sort_by'=>'urutan'))->result();
				$data['option'][$m1->id]['id'] = $m1->id;	
				$data['option'][$m1->id]['nama'] = '&nbsp &nbsp |-----'.$m1->section_code .' -  '. $m1->section_name;
				foreach($data['audit_section'][$m1->id] as $m2) {
					$data['audit_section'][$m2->id] = get_data('tbl_m_audit_section',array('where_array'=>array('parent_id'=>$m2->id),'sort_by'=>'urutan'))->result();
					$data['option'][$m2->id]['id'] = $m2->id;	
					$data['option'][$m2->id]['nama'] = '&nbsp; &nbsp; &nbsp; &nbsp |-----'.$m2->section_code .' - '. $m2->section_name;
					foreach($data['audit_section'][$m2->id] as $m3) {
						$data['audit_section'][$m3->id] = get_data('tbl_m_audit_section',array('where_array'=>array('parent_id'=>$m3->id),'sort_by'=>'urutan'))->result();
						$data['option'][$m3->id]['id'] = $m3->id;	
						$data['option'][$m3->id]['nama'] = '&nbsp; &nbsp; &nbsp; &nbsp; |-----'.$m3->section_code .' - ' .$m3->section_name;
						foreach($data['audit_section'][$m3->id] as $m4) {
							$data['audit_section'][$m4->id] = get_data('tbl_m_audit_section',array('where_array'=>array('parent_id'=>$m4->id),'sort_by'=>'urutan'))->result();
							$data['option'][$m4->id]['id'] = $m4->id;	
							$data['option'][$m4->id]['nama'] = '&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;|-----'.$m4->section_code .' - ' .$m4->section_name;
						}
					}
				}
			}
		}

		// debug($data['option']);die;

		render($data);
	}

	function data() {

	
		$data = data_serverside();
		render($data,'json');
	}

	function get_data() {
		$dt = get_data('tbl_m_aktivitas','id',post('id'))->row_array();

		$data = get_data('tbl_aktivitas a',[
			'select' => 'a.*',
			'where' => [
				'a.id' => $dt['id_aktivitas'],
			]
		])->row_array();

		$data['id_section'] = json_decode($data['id_section']);
		
		render($data,'json');
	}

	function save() {
		$data = post();
		$id_section = post('id_section');

		$data['id_section'] = json_encode($id_section);

		$response = save_data('tbl_aktivitas',$data,post(':validation'));

		if($response['status']= 'success') {
			foreach($id_section as $k => $v){
				$section = get_data('tbl_m_audit_section a',[
					'select' => 'a.*,b.section_name as company, c.section_name as location, d.section_name as divisi, e.section_name as department, f.section_name as section',
					'join' => [
						'tbl_m_audit_section b on a.level1 = b.id type LEFT',
						'tbl_m_audit_section c on a.level2 = c.id type LEFT',
						'tbl_m_audit_section d on a.level3 = d.id type LEFT',
						'tbl_m_audit_section e on a.level4 = e.id type LEFT',
						'tbl_m_audit_section f on a.level5 = f.id type LEFT'
					],
					'where' => [
						'a.id' => $v
					]
					])->row();


				if(!empty($section)) {
					$data['id_company'] = $section->level1 ;
					$data['company'] = $section->company ;
					$data['id_location'] = $section->level2 ;
					$data['location'] = $section->location ;
					$data['id_divisi'] = $section->level3 ;
					$data['divisi'] = $section->divisi ;
					$data['id_department'] = $section->level4 ;
					$data['department'] = $section->department ;
					$data['id_section'] = $section->level5 ;
					$data['section'] = $section->section ;
					$data['id_aktivitas'] = $response['id'] ;
				}

				$response = save_data('tbl_m_aktivitas',$data,post(':validation'));
			}
		}

		render($response,'json');
	}

	function delete() {
		$response = destroy_data('tbl_m_aktivitas','id',post('id'));
		render($response,'json');
	}

	function template() {
		ini_set('memory_limit', '-1');
		$arr = ['parent_id' => 'parent_id','id_company' => 'id_company','id_location' => 'id_location','id_divisi' => 'id_divisi','id_department' => 'id_department','id_section' => 'id_section','aktivitas' => 'aktivitas','audit_area' => 'audit_area','type_aktivitas' => 'type_aktivitas','is_active' => 'is_active'];
		$config[] = [
			'title' => 'template_import_m_aktivitas',
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function import() {
		ini_set('memory_limit', '-1');
		$file = post('fileimport');
		$col = ['parent_id','id_company','id_location','id_divisi','id_department','id_section','aktivitas','audit_area','type_aktivitas','is_active'];
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
					$save = insert_data('tbl_m_aktivitas',$data);
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
		$arr = ['parent_id' => 'Parent Id','id_company' => 'Id Company','id_location' => 'Id Location','id_divisi' => 'Id Divisi','id_department' => 'Id Department','id_section' => 'Id Section','aktivitas' => 'aktivitas','audit_area' => 'Audit Area','type_aktivitas' => 'Type aktivitas','is_active' => 'Aktif'];
		$data = get_data('tbl_m_aktivitas')->result_array();
		$config = [
			'title' => 'data_m_aktivitas',
			'data' => $data,
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

}