<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Audit_universe extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {

		render();
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

}