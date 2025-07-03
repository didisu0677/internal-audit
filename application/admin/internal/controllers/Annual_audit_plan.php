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
							b.location,b.divisi,b.department,b.section,b.aktivitas,b.sub_aktivitas, s.urutan,
							c.risk, c.keterangan, c.bobot, d.internal_control,d.location_control',
			'join'		=> ['tbl_m_aktivitas b on a.id_m_aktivitas = b.id type LEFT',
							'tbl_risk_register c on a.id_risk = c.id type LEFT',
							'tbl_internal_control d on b.id_aktivitas = d.id_aktivitas and b.id_sub_aktivitas = d.id_sub_aktivitas type LEFT',
							'tbl_m_audit_section s on b.id_department = s.id type left'   
						],
	        'where'     => [
	            'a.is_active' => 1,
				'a.id_risk' => $id_major
	        ],
			'sort_by' => 's.urutan'
	    ];

	    $data['grup']= get_data('tbl_annual_audit_plan a',$arr)->result();

        $response	= array(
            'table'		=> $this->load->view('internal/annual_audit_plan/table',$data,true),
        );

	    render($response,'json');
	}

}