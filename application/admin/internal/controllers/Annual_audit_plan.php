<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Annual_audit_plan extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {

		render();
	}

	function data($tahun = "", $tipe = 'table') {

		$risk_minor = get_data('tbl_risk_register',[
			'where' => [
				'bobot !=' => 'Minor'
			]
		])->result();

		$id_major = [];
		foreach($risk_minor as $rm) {
			$id_major[] = $rm->id ;
		}

        $arr            = [
	        'select'	=> 'a.*,b.id_aktivitas,b.id_sub_aktivitas,b.aktivitas,b.sub_aktivitas,b.company,
							b.location,b.divisi,b.department,b.section,b.aktivitas,b.sub_aktivitas,
							c.risk, c.keterangan, c.bobot, d.internal_control,d.location_control',
			'join'		=> ['tbl_m_aktivitas b on a.id_m_aktivitas = b.id type LEFT',
							'tbl_risk_register c on a.id_risk = c.id type LEFT',
							'tbl_internal_control d on b.id_aktivitas = d.id_aktivitas and b.id_sub_aktivitas = d.id_sub_aktivitas type LEFT'
						   ],
	        'where'     => [
	            'a.is_active' => 1,
				'a.id_risk' => $id_major
	        ],
	    ];


        // $tahun = get('tahun');
	    $data['grup']= get_data('tbl_annual_audit_plan a',$arr)->result();

		// debug($data['grup']);die;


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

		// debug($data['int_control']);die;
		

        // $data['section'] = get_data('tbl_m_audit_section','is_active',1)->result_array(); 

	
        $response	= array(
            'table'		=> $this->load->view('internal/annual_audit_plan/table',$data,true),
        );

	    render($response,'json');
	}

}