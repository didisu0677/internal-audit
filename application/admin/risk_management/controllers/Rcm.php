<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rcm extends BE_Controller {

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

		$data['opt_aktivitas']   = get_data('tbl_sub_aktivitas a',[
			'select' => 'a.id, a.id_aktivitas, CONCAT(b.aktivitas, " - ", a.sub_aktivitas) as aktivitas',
			'join' =>  'tbl_aktivitas b on a.id_aktivitas = b.id type LEFT',
			'where'  => [
				'a.is_active => 1',
			],
		])->result_array();

		$data['filter'] = get_data('tbl_m_aktivitas a',[
			'select' => 'distinct a.department, a.id_department, a.department, a.location',
			'sort_by' => 'a.department, a.location',
			'sort' => 'asc'
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

	function data() {
	$risk_list = [];
	$control_list = [];
	$keterangan = [];

	$grup = get_data('tbl_rcm a', [
		'select' => '
			a.*,
			b2.section_name as location,
			b3.section_name as divisi,
			b4.section_name as department,
			b5.section_name as section,
			c.id_aktivitas, c.sub_aktivitas,
			e.aktivitas
		',
		'join' => [
			'tbl_m_audit_section b on a.id_section = b.id type LEFT',
			'tbl_m_audit_section b1 on b.level1 = b1.id type LEFT',
			'tbl_m_audit_section b2 on b.level2 = b2.id type LEFT',
			'tbl_m_audit_section b3 on b.level3 = b3.id type LEFT',
			'tbl_m_audit_section b4 on b.level4 = b4.id type LEFT',
			'tbl_m_audit_section b5 on b.level5 = b5.id type LEFT',
			'tbl_sub_aktivitas c on a.id_sub_aktivitas = c.id type LEFT',
			'tbl_aktivitas e on c.id_aktivitas = e.id type LEFT',
			'tbl_risk_control d on a.id_risk_control = d.id type LEFT',
		],
		'order_by' => 'b4.urutan, department, e.aktivitas, c.sub_aktivitas',
	])->result_array();
	
	foreach($grup as $val){
		// ambil id_risk dari detail
		$risk_control = get_data('tbl_risk_control a', [
			'select' => 'b.*, concat(c.risk," - ", d.bobot) as risk , c.keterangan',
			'join' => [
				'tbl_risk_control_detail b on a.id = b.id_risk_control',
				'tbl_risk_register c on b.id_risk = c.id',
				'tbl_bobot_status_audit d on b.bobot = d.id',
			],	
			'where' => [
				'a.id' => $val['id_risk_control']
			]
		])->result_array();
		
		foreach($risk_control as $r) {
			$risk_list[$val['id_risk_control']][] = $r['risk'];
			$keterangan[$val['id_risk_control']][] = $r['keterangan'];
		}

		// ambil internal control
		$control = get_data('tbl_internal_control a', [
			'select' => 'a.id_internal_control as id,b.internal_control',
			'join' => 'tbl_m_internal_control b on a.id_internal_control = b.id',
			'where' => [
				'a.is_active' => 1,
				'a.id_aktivitas' => $val['id_aktivitas'],
				'a.id_sub_aktivitas' => $val['id_sub_aktivitas'],
			]
		])->result_array();

		foreach($control as $v1) {
			$control_list[$val['id']][] = $v1['internal_control'];
		}
	}

	$rows = [];
	foreach($grup as $m0) {
		$rows[] = [
			'location'        => $m0['location'],
			'divisi'          => $m0['divisi'],
			'department'      => $m0['department'],
			'section'         => $m0['section'],
			'aktivitas'       => $m0['aktivitas'],
			'sub_aktivitas'   => $m0['sub_aktivitas'],
			'risk'            => isset($risk_list[$m0['id_risk_control']]) ? $this->bg_array($risk_list[$m0['id_risk_control']]) : '',
			'internal_control'=> isset($control_list[$m0['id']]) ? $this->bg_array($control_list[$m0['id']]) : '',
			'keterangan'      => isset($keterangan[$m0['id_risk_control']]) ? $this->bg_array($keterangan[$m0['id_risk_control']]) : '',
			'aksi'            => '<button class="btn btn-warning btn-sm btn-input btn-icon-only" data-key="edit" data-id="'.$m0['id'].'"><i class="fa-edit"></i></button>
			                       <button class="btn btn-danger btn-sm btn-delete btn-icon-only" data-key="delete" data-id="'.$m0['id'].'"><i class="fa-trash-alt"></i></button>'
		];
	}

	render(['data' => $rows],'json');
}


	function bg_array($arr){
		$html = [];
		foreach($arr as $v){
			$html[] = '<p class="bg-light" style="border-radius: 10px; padding:10px">'.$v.'</p>';
		}
    	return implode("", $html);
	}

	function get_data() {
		$data = get_data('tbl_rcm a',[
			'select' => 'a.id, a.id_risk_control, b.id_section,b.id_sub_aktivitas, b.id_risk',
			'join'   => 'tbl_risk_control b on a.id_risk_control = b.id type LEFT',
			'where' => [
				'a.id' => post('id'),
			]
		])->row_array();
		
		
		if(!empty($data['id_section'])) $data['id_section'] = json_decode($data['id_section']);
		if(!empty($data['id_sub_aktivitas'])) $data['id_aktivitas'] = json_decode($data['id_sub_aktivitas']);

		// $data['detail'] = get_data('tbl_sub_aktivitas',[
		// 	'where' => [
		// 		'id_aktivitas' => $dt['id_aktivitas']
		// 	],
		// 	'sort_by' => 'id',
		// 	])->result_array();

		
		$data['risk'] = get_data('tbl_risk_register a',[
			'select' => 'a.*',
			'where' => [
				'a.is_active' => 1,
				'a.id' => json_decode($data['id_risk']),
				// 'id_aktivitas' => $dt['id_aktivitas']
			],
			'sort_by' => 'id',
			])->result_array();
	
		
		// $data['ctrl_item'] = get_data('tbl_internal_control',[
		// 	'where' => [
		// 		'id_aktivitas' => $dt['id_aktivitas']
		// 	],
		// 	'sort_by' => 'id',
		// 	])->result_array();

		$data['bobot']		= $this->get_bobot('return');
		
		render($data,'json');
	}

	function save(){
		$data = post();
		$id_risk = post('id_risk');
        $risiko = post('risk');
		$id_section = post('id_section');

        $aktivitas = post('id_aktivitas');

        $keterangan = post('keterangan');
        $score_dampak = post('score_dampak');
        $score_kemungkinan = post('score_kemungkinan');
        $bobot_risk = post('bobot_risk');
		
		$new_data = false;
		if($data['id'] == '0'){
			$new_data = true;
		}

		if(empty($id_risk)) {
			$response = [
				'status' => 'info',
				'message' => 'Risk cannot be empty'
			];

			render($response,'json');
			return;
		}

		$cek_section = get_data('tbl_m_audit_section','id',$id_section)->row();
		if(empty($cek_section) || $cek_section->level5== 0) {
			$response = [
				'status' => 'info',
				'message' => 'section department cannot be empty'
			];

			render($response,'json');
			return;
		}

		// save data Risk Register
		foreach($id_risk as $r1 => $vr) {
			$data_r = [
				'id'	=> $id_risk[$r1],
				'risk'  =>$risiko[$r1],
				'keterangan' => $keterangan[$r1],
				'score_dampak' => $score_dampak[$r1],
				'score_kemungkinan' => $score_kemungkinan[$r1],
				'bobot' => $bobot_risk[$r1],
				'is_active'=>1
			];
			$risk = save_data('tbl_risk_register', $data_r);

			$id_risk1[] = $risk['id'];
		}
		
		// save data Risk Control
		$data_rk = 	[
			'id' => $new_data == true ? 0 : $data['id_rk'],
			'id_section' => json_encode($id_section),
			'id_sub_aktivitas' => json_encode($aktivitas),
			'id_risk' => json_encode($id_risk1),
			'is_active' => 1,
		];
		
		$id_risk_control = save_data('tbl_risk_control',$data_rk);

		// untuk risk control detail
		foreach($id_risk1 as $i => $val){
			$cek = get_data('tbl_risk_control_detail', [
				'where' => [
					'id_risk_control' => $id_risk_control['id'],
					'id_risk' => $val,
				]
			])->row_array();
			$data_detail = [
				'id' => $cek['id'] ?? 0,
				'id_risk_control' => $id_risk_control['id'],
				'id_risk' => $val,
				'score_dampak' => $score_dampak[$i],
				'score_kemungkinan' => $score_kemungkinan[$i],
				'bobot' => $bobot_risk[$i],
			];
			save_data('tbl_risk_control_detail', $data_detail);
		}

		// Ambil data RCM lama untuk update / hapus
		$existing_rcms = get_data('tbl_rcm', [
			'where' => ['id_risk_control' => $id_risk_control['id']]
		])->result();
		
		$existing_map = []; // [section][sub_aktivitas] = id_rcm
		foreach ($existing_rcms as $rcm) {
			$existing_map[$rcm->id_section][$rcm->id_sub_aktivitas] = $rcm->id;
		}

		$new_keys = [];
		foreach ($id_section as $sec) {
			foreach ($aktivitas as $sub_aktivitas) {
				$new_keys[] = [$sec, $sub_aktivitas];

				$id_rcm = isset($existing_map[$sec][$sub_aktivitas]) ? $existing_map[$sec][$sub_aktivitas] : 0;
				$data_rcm = [
					'id'                => $id_rcm,
					'id_section'        => $sec,
					'id_sub_aktivitas'  => $sub_aktivitas,
					'id_risk_control'   => $id_risk_control['id'],
				];
				$data_save = save_data('tbl_rcm', $data_rcm);

				$universe = get_data('tbl_audit_universe', 'id_rcm', $data_save['id'])->row_array();
				
				$data_universe = [
					'id' => $universe['id'] ?? 0,
					'id_rcm' => $data_save['id'],
				];
				$id_universe = save_data('tbl_audit_universe', $data_universe);

				$audit_plan = get_data('tbl_annual_audit_plan', 'id_universe', $id_universe['id'])->row_array();
				$data_audit_plan = [
					'id' => $audit_plan['id'] ?? 0,
					'id_universe' => $id_universe['id'],
				];
				save_data('tbl_annual_audit_plan', $data_audit_plan);
			}
		}

		// Hapus RCM lama yang tidak dipakai lagi
		foreach ($existing_map as $sec => $subs) {
			foreach ($subs as $sub => $id_rcm) {
				if (!in_array([$sec, $sub], $new_keys)) {
					delete_data('tbl_rcm', 'id', $id_rcm);
					delete_data('tbl_audit_universe', 'id_rcm', $id_rcm);
					$audit = get_data('tbl_audit_universe a', [
						'select' => 'b.id as id_audit',
						'join' => [
							'tbl_annual_audit_plan b on a.id = b.id_universe'
						],
						'where' => [
							'a.id_rcm' => $id_rcm
						] 
					])->row_array();
					if($audit){
						delete_data('tbl_annual_audit_plan', 'id', $audit['id_audit']);
					}
				}
			}
		}
		
		render([
			'status' => 'success',
			'message' => 'Data berhasil disimpan'
		], 'json');
	}

	function savessssss() {
		$data = post();
		$id_section = post('id_section');

		$id_risk = post('id_risk');
		$risiko = post('risk');


		$aktivitas = post('id_aktivitas');

		$keterangan = post('keterangan');
		$score_dampak = post('score_dampak');
		$score_kemungkinan = post('score_kemungkinan');
		$bobot_risk = post('bobot_risk');

		$data['id_section'] = json_encode($id_section);
		
		$new_data = false;
		if($data['id'] == '0'){
			$new_data = true;
		}

		$data_rk = [
			'id' => $new_data == true ? 0 : $data['id_rk'],
			'id_section' => json_encode($id_section),
			'id_sub_aktivitas' => json_encode($aktivitas),
			'id_risk' => json_encode($id_risk),
			'is_active' => 1,
		];

		$cek_section = get_data('tbl_m_audit_section','id',$id_section)->row();
		if(empty($cek_section) || $cek_section->level5== 0) {
			$response = [
				'status' => 'info',
				'message' => 'section department cannot be empty'
			];

			render($response,'json');
			die;
		}


		$response = save_data('tbl_risk_control',$data_rk);
	

		if($response['status'] == 'success') {

			$id_risk = post('id_risk');
			$risiko = post('risk');
			$id_risk1 = [];
			
			foreach($id_risk as $r1 => $vr) {
				$data_r = [
					'id'	=> $id_risk[$r1],
					'risk'  =>$risiko[$r1],
					'keterangan' => $keterangan[$r1],
					'score_dampak' => $score_dampak[$r1],
					'score_kemungkinan' => $score_kemungkinan[$r1],
					'bobot' => $bobot_risk[$r1],
					'is_active'=>1
				];
				$risk = save_data('tbl_risk_register', $data_r);
				$id_risk1[] = $risk['id'];
			}
			update_data('tbl_risk_control',['id_risk' => json_encode($id_risk1)],['id'=>$response['id']]);

			foreach($aktivitas as $a => $va){

				$cek_aktivitas = get_data('tbl_sub_aktivitas a',[
					'select' => 'a.*, b.aktivitas',
					'join' => 'tbl_aktivitas b on a.id_aktivitas = b.id',
					'where' => [
						'a.id' => $aktivitas[$a],
					],
				])->row();

				
				if($cek_aktivitas){
					$data['id_sub_aktivitas'] = $cek_aktivitas->id;
					$data['sub_aktivitas'] = $cek_aktivitas->sub_aktivitas;
					$data['id_aktivitas'] = $cek_aktivitas->id_aktivitas;
					$data['aktivitas'] = $cek_aktivitas->aktivitas;
				}
				

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
						$data['id_risk'] = $risk['id'];
						$data['id_rk'] = $response['id'];
						$data['id_risk1'] = json_encode($id_risk1);
						$data['is_active'] = 1;
					}
					$cekm = get_data('tbl_m_aktivitas',[	
						'where' => [
							'id_company' => $section->level1,
							'id_location' => $section->level2,
							'id_divisi' => $section->level3,
							'id_department' => $section->level4,
							'id_section' => $section->level5,
							'id_aktivitas' => $cek_aktivitas->id,
							'id_sub_aktivitas' => $cek_aktivitas->id,
							'id_aktivitas' => $cek_aktivitas->id_aktivitas,
							// 'id_rk' => $data['id_rk'],
						],
					])->row();

					if(!isset($cekm->id)) {
						$data['id'] = 0;
					}else{
						$data['id'] = $cekm->id;
					}

					// if(!isset($cekm->id)) {
					// 	insert_data('tbl_m_aktivitas',$data);
					// }else{
					// 	$data_u = [
					// 		'id_company' => $section->level1 ,
					// 		'company' => $section->company ,
					// 		'id_location' => $section->level2 ,
					// 		'location' => $section->location ,
					// 		'id_divisi' => $section->level3 ,
					// 		'divisi' => $section->divisi ,
					// 		'id_department' => $section->level4 ,
					// 		'department' => $section->department ,
					// 		'id_section' => $section->level5 ,
					// 		'section' => $section->section ,
					// 		'id_risk' => $risk['id'],
					// 		'id_rk' => $response['id'],
					// 		'id_sub_aktivitas' => $cek_aktivitas->id,
					// 		'sub_aktivitas' => $cek_aktivitas->sub_aktivitas,
					// 		'id_aktivitas' => $cek_aktivitas->id_aktivitas,
					// 		'aktivitas' => $cek_aktivitas->aktivitas,
					// 		'is_active' => 1,
					// 	];
					// 	update_data('tbl_m_aktivitas',$data_u,['id'=>$data['id_rk']]);
					// }


					$response_m = save_data('tbl_m_aktivitas',$data,post(':validation'));


					if($response_m['status'] == 'success') {
						delete_data('tbl_annual_audit_plan','id_m_aktivitas',$response_m['id']);
						
						foreach($id_risk as $i => $v) {
							$r = get_data('tbl_risk_register','id',$v)->row();
							$ann = save_data('tbl_annual_audit_plan',['id'=> 0,'id_risk'=>$v ?: $response['id'],'id_m_aktivitas'=>$response_m['id'],'bobot'=>$bobot_risk[$i],'is_active'=>1]);

						}
					}
					
				}
															
			}
		}

		render($response,'json');
	}

	function delete() {
		$cek = get_data('tbl_rcm','id',post('id'))->row();
		delete_data('tbl_rcm','id',$cek->id);
		// if($response['status']= 'success') {
		// 	$rk = get_data('tbl_risk_control','id',$cek->id_risk_control)->row();
		// 	if($rk) delete_data('tbl_risk_control','id',$cek->id_risk_control);

		// 	// $annual = get_data('tbl_annual_audit_plan','id_m_aktivitas',$cek->id)->row();
		// 	// if($annual) delete_data('tbl_annual_audit_plan','id_m_aktivitas',$cek->id);
		// }
		render([
			'status' => 'success',
			'message' => lang('data_berhasil_dihapus')
		],'json');
	}

	function template() {
		ini_set('memory_limit', '-1');
		$arr = ['parent_id' => 'parent_id','id_company' => 'id_company','id_location' => 'id_location','id_divisi' => 'id_divisi','id_department' => 'id_department','id_section' => 'id_section','aktivitas' => 'aktivitas','audit_area' => 'audit_area','type_aktivitas' => 'type_aktivitas','is_active' => 'is_active'];
		$config[] = [
			'title' => 'template_import_rcm',
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
			'title' => 'data_rcm',
			'data' => $data,
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function browse_risk() {
		$data['layout']	= 'browse';
		// $data['id'] = $id;
		render($data);
	}

	function data_risk() {
		$config 					= [
			'access_edit'			=> false,
			'access_view'			=> false,
			'access_delete'			=> false,
		];
		$config['button'][] 	= button_serverside('btn-success','btn-act-choose1',['fa-check',lang('pilih'),true],'btn-act-choose1');
		$data = data_serverside($config);
		render($data,'json');
	}

	function add_new_risk(){
		
		$id = post('id');
		$cs = get_data('tbl_risk_register', 'id', $id)->row_array();
		$response = [
			'status' => 'ok',
			'message'	=> lang('data_berhasil_diperbaharui'),
			'id'		=> $cs['id'],
			'risk'  => $cs['risk'],
			'keterangan'		=> $cs['keterangan'],
			'score_dampak' => $cs['score_dampak'],
			'score_kemungkinan' => $cs['score_kemungkinan'],
			'bobot' => $cs['bobot'],
		];

		
		render($response, 'json');
		
	}

	function get_bobot($type='echo') {

		$bobot = get_data('tbl_bobot_status_audit')->result_array();
	    $data  = '<option value=""></option>';
		foreach($bobot as $val){
			$data .= '<option value="'.$val['id'].'">'.$val['bobot'].'</option>';
		}

	    if($type == 'echo') echo $data;
	    else return $data;
	    
	}
	
	function get_detail(){
		$id_risk_control = post('id_rk');
		$id_risk = post('id_risk');
		$data = get_data('tbl_risk_control_detail' ,[
			'where' => [
				'id_risk_control' => $id_risk_control,
				'id_risk' => $id_risk
			]
		])->row_array();
		render($data,'json');
	}
}