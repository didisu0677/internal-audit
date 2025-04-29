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

		$data['opt_aktivitas']   = get_data('tbl_aktivitas',[
			'is_active => 1',
		])->result_array();

		// debug($data['option']);die;

		render($data);
	}

	// function data() {
	// 	$config['access_view'] = false;
	// 	$config['sort_by'] = 'aktivitas';
	
	// 	$data = data_serverside($config);
	// 	render($data,'json');
	// }

	function data($tahun = "", $tipe = 'table') {
        $arr            = [
	        'select'	=> 'a.*,b.aktivitas',
			'join'		=> 'tbl_aktivitas b on a.id_aktivitas = b.id type LEFT',
	        'where'     => [
	            'a.is_active' => 1,
				'a.parent_id' => 0,
                // 'a.id' => 4
	        ],
	    ];


        // $tahun = get('tahun');
	    $data['grup'][0]= get_data('tbl_m_aktivitas a',$arr)->result();
		foreach($data['grup'][0] as $s) {
			$data['det'][$s->id] = get_data('tbl_m_aktivitas a',[
				'where' => [
					'is_active' => 1,
					'parent_id' => $s->id
				]
			])->result();
		} 


        $data['section'] = get_data('tbl_m_audit_section','is_active',1)->result_array(); 
		$data['int_control'] = get_data('tbl_internal_control','is_active',1)->result_array();
		$data['risk'] = get_data('tbl_risk_register','is_active',1)->result_array(); 
		$data['sub'] = get_data('tbl_sub_aktivitas','is_active',1)->result_array();

		$data['dampak'] = get_data('tbl_risk_register',[
			'select' => 'id, id_aktivitas, dampak',
			'where' => [
				'is_active' => 1,
				'dampak !=' => ''
			]

		])->result_array(); 

		
		$data['kemungkinan'] = get_data('tbl_risk_register',[
			'select' => 'id, id_aktivitas, kemungkinan',
			'where' => [
				'is_active' => 1,
				'kemungkinan !=' => ''
			]

		])->result_array(); 

        // debug($data['grup'][0]);die;


      
    //    debug($data['cc']);die;
        $response	= array(
            'table'		=> $this->load->view('risk_management/m_aktivitas/table',$data,true),
        );

	    render($response,'json');
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
		$data['detail'] = get_data('tbl_sub_aktivitas',[
			'where' => [
				'id_aktivitas' => $dt['id_aktivitas']
			],
			'sort_by' => 'id',
			])->result_array();

		
		$data['risk'] = get_data('tbl_risk_register',[
			'where' => [
				'id_aktivitas' => $dt['id_aktivitas']
			],
			'sort_by' => 'id',
			])->result_array();

		
		$data['ctrl_item'] = get_data('tbl_internal_control',[
			'where' => [
				'id_aktivitas' => $dt['id_aktivitas']
			],
			'sort_by' => 'id',
			])->result_array();
		
		render($data,'json');
	}

	function save() {
		$data = post();
		$id_sub_aktivitas = post('id_sub_aktivitas');

		$id_section = post('id_section');
		$sub_aktivitas = post('sub_aktivitas');

		$id_risk = post('id_risk');
		$risk = post('risk');
		$dampak = post('dampak');
		$score_dampak = post('score_dampak');
		$score_kemungkinan = post('score_kemungkinan');
		$bobot_risk = post('bobot_risk');

		$id_control = post('id_control');
		$ctrl_existing = post('ctrl_existing');
		$ctrl_location = post('ctrl_location');
		$no_pnp = post('no_pnp');
		$jenis_pnp = post('jenis_pnp');
		$penerbit = post('penerbit');
		$tgl_pnp = post('tgl_pnp');


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
					
					if(!empty($data['parent_id'])) { 
						$cek_aktivias = get_data('tbl_aktivitas','id',$data['parent_id'])->row();
						if($cek_aktivias){
							$data['sub_aktivitas'] = $data['aktivitas'];
							$data['aktivitas'] = $cek_aktivias->aktivitas;
						}
					}
									
				}

				$response = save_data('tbl_m_aktivitas',$data,post(':validation'));

				$res = [];
				if(is_array($sub_aktivitas)) {
					foreach($sub_aktivitas as $s => $k) {
						$data_s = [
							'id'		=> $id_sub_aktivitas[$s],
							'id_aktivitas' => $response['id'],
							'sub_aktivitas' => $sub_aktivitas[$s],
							'is_active' => 1
						];
						$res1 = save_data('tbl_sub_aktivitas',$data_s);
						$res[] = $res1['id'] ;
					} 

					delete_data('tbl_sub_aktivitas',['id not' => $res, 'id_aktivitas' =>$response['id']]);
				}
			}

			$res_vr = [];
			if(is_array($risk)) {
				foreach($risk as $r => $vr) {
					$data_r = [
						'id'	=> $id_risk[$r],
						'id_aktivitas'=>$response['id'],
						'risk'=>$risk[$r],
						'dampak' => $dampak[$r],
						'score_dampak' => $score_dampak[$r],
						'score_kemungkinan' => $score_kemungkinan[$r],
						'bobot' => $bobot_risk[$r],
						'is_active'=>1
					];

					$res_vr1 = save_data('tbl_risk_register',$data_r);
					$res_vr[] = $res_vr1['id'];
				}
				delete_data('tbl_risk_register',['id not' => $res_vr, 'id_aktivitas' =>$response['id']]);
			}

			

			$res_vc = [];
			if(is_array($ctrl_existing)){
				foreach($ctrl_existing as $c => $vc) {
					$data_c = [
						'id'	=> $id_control[$c],
						'id_aktivitas'=>$response['id'],
						'internal_control'=>$ctrl_existing[$c],
						'location_control' => $ctrl_location[$c],
						'no_pnp' => $no_pnp[$c],
						'jenis_pnp' => $jenis_pnp[$c],
						'penerbit_pnp' => $penerbit[$c],
						'tanggal_pnp' => $tgl_pnp[$c],
						'is_active'=>1
					];

					$res_vc1 = save_data('tbl_internal_control',$data_c);
					$res_vc[] = $res_vc1['id'];
				}

				delete_data('tbl_internal_control',['id not' => $res_vc, 'id_aktivitas' => $response['id']]);
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