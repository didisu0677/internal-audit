<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Audit_universe extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		$data = get_data('tbl_audit_universe u', [
			'select' => 'u.*, s.description, s3.section_name as divisi, s4.section_name as department, s.section_name, a.aktivitas, sa.sub_aktivitas, rc.id_risk, s4.urutan',
			'join' => [
				'tbl_rcm rcm on u.id_rcm = rcm.id',
				'tbl_risk_control rc on rcm.id_risk_control = rc.id',
				'tbl_sub_aktivitas sa on rcm.id_sub_aktivitas = sa.id',
				'tbl_aktivitas a on sa.id_aktivitas = a.id',
				'tbl_m_audit_section s on rcm.id_section = s.id',
				'tbl_m_audit_section s3 on s.level3 = s3.id',
				'tbl_m_audit_section s4 on s.level4 = s4.id',
			],
			'order_by' => 's4.urutan',
			'sort' => 'asc',
		])->result_array();
		// debug(last_query());
		foreach($data as $i => $row){
			$id_risk = json_decode($row['id_risk']);
			$data_risk = get_data('tbl_risk_register a',[
				'join' => [
					'tbl_bobot_status_audit b on a.bobot = b.id'
				],
				'where' =>[
					'a.id' => $id_risk
				]
			])->result_array();
			foreach($data_risk as $v){
				$data[$i]['risk'][] = $v;
			}
		}
		
		render(['data'=> $data]);
	}
	function data_table() {
		// $conf	= [
		// 	'join'		=> [
		// 		'tbl_m_aktivitas b on tbl_annual_audit_plan.id_m_aktivitas = b.id type LEFT',
		// 		'tbl_risk_register c on tbl_annual_audit_plan.id_risk = c.id type LEFT',
		// 		'tbl_internal_control d on b.id_aktivitas = d.id_aktivitas and b.id_sub_aktivitas = d.id_sub_aktivitas type LEFT',
		// 		'tbl_m_audit_section s on b.id_department = s.id type left'
		// 		],
	    //     'where'     => [
	    //         'tbl_annual_audit_plan.is_active' => 1,
	    //     ],
		// 	'order_by' => 's.urutan'
	    // ];

		$conf = [
			'join' => [
				'tbl_rcm rcm on tbl_audit_universe.id_rcm = rcmd.id',
				'tbl_risk_control rc on rcm.id_risk_control = rc.id',
				'tbl_m_audit_section s on rcm.id_section = s.id'
			]
		];
		$data = data_serverside($conf);
		render($data,'json');
	}

	function data($tahun = "", $tipe = 'table') {
        // $arr            = [
	    //     'select'	=> 'a.*,b.aktivitas',
		// 	'join'		=> 'tbl_aktivitas b on a.id_aktivitas = b.id type LEFT',
	    //     'where'     => [
	    //         'a.is_active' => 1,
	    //     ],
	    // ];

		$arr            = [
	        'select'	=> 'a.*,b.id_aktivitas,b.id_sub_aktivitas,b.aktivitas,b.sub_aktivitas,b.company,
							b.location,b.divisi,b.department,b.section,b.aktivitas,b.sub_aktivitas,
							c.risk, c.keterangan, c.bobot, d.internal_control,d.location_control, s.urutan',
			'join'		=> [
				'tbl_m_aktivitas b on a.id_m_aktivitas = b.id type LEFT',
				'tbl_risk_register c on a.id_risk = c.id type LEFT',
				'tbl_internal_control d on b.id_aktivitas = d.id_aktivitas and b.id_sub_aktivitas = d.id_sub_aktivitas type LEFT',
				'tbl_m_audit_section s on b.id_department = s.id type left'
				],
	        'where'     => [
	            'a.is_active' => 1,
	        ],
			'order_by' => 's.urutan'
	    ];

	    $data['grup']= get_data('tbl_annual_audit_plan a',$arr)->result();
		
	    // $data['grup']= get_data('tbl_m_aktivitas a',$arr)->result();

		// $data['risk'] = [];
		// foreach($data['grup'] as $g) {
		// 	$rk = get_data('tbl_risk_control', 'id', $g->id_rk)->row();
		// 	$id_r = json_decode($rk->id_risk);
		// 	$count = count($id_r);

		// 	// debug($rk);die;
		// 	$data['risk'][$g->id][$g->id_rk] = get_data('tbl_risk_register',[
		// 		'select' => '*',
		// 		'where' => [
		// 			'id' => $id_r
		// 		],
		// 	])->result_array();

		// 	$data['col'][$g->id_rk] = $count;


		// 	$data['int_control'][$g->id_aktivitas][$g->id_sub_aktivitas] = get_data('tbl_internal_control',[
		// 		'select' => 'id_internal_control as id,internal_control',
		// 		'where' => [
		// 			'is_active' => 1,
		// 			'id_aktivitas' => $g->id_aktivitas,
		// 			'id_sub_aktivitas' => $g->id_sub_aktivitas,
		// 		]
		// 	])->result_array();


		// } 



        $data['section'] = get_data('tbl_m_audit_section','is_active',1)->result_array(); 

	
        $response	= array(
            'table'		=> $this->load->view('internal/audit_universe/table',$data,true),
        );

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

	function set_initial_audit(){
		$data = post();
		
		update_data('tbl_audit_universe', ['initial_audit' => $data['initial_audit']], 'id', $data['id_universe']);
		
		$audit_plan = get_data('tbl_annual_audit_plan ap', [
			'select' => 'ap.*, rc.id_risk',
			'join' => [
				'tbl_audit_universe u on ap.id_universe = u.id',
				'tbl_rcm r on u.id_rcm = r.id',
				'tbl_risk_control rc on r.id_risk_control = rc.id',
			],
			'where' => [
				'ap.id_universe' => $data['id_universe']
			]
		])->row_array();
		
		if($audit_plan){
			$id_risk = json_decode($audit_plan['id_risk'], true);
			
			foreach($id_risk as $id){
				$risk = get_data('tbl_risk_register a',[
					'select' => 'a.*, b.id as id_bobot, b.status_audit, b.description',
					'join' => [
						'tbl_bobot_status_audit b on a.bobot = b.id'
					],
					'where' => [
						'a.id' => $id
					]
				])->row_array();
				$start_date = null;
				if($risk['status_audit'] == 'Tahunan'){ // moderate & major
					$start_date = date('Y-m-d', strtotime('+1 years', strtotime($data['initial_audit'])));
				}elseif($risk['status_audit'] == '2 Tahun'){
					$start_date = date('Y-m-d', strtotime('+2 years', strtotime($data['initial_audit'])));	
				}else{
					continue;
				}

				$data_audit_plan = [
					'id' => $audit_plan['id'] ?? 0,
					'id_universe' => $data['id_universe'],
					'start_date' => $start_date,
				];
				$resp = save_data('tbl_annual_audit_plan', $data_audit_plan);
			}
		}
		render([
			'status' => 'success',
			'message' => 'Success'
		], 'json');		
	}
}