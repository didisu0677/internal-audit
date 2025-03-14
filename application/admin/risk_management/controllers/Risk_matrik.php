<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Risk_matrik extends BE_Controller {
    var $controller = 'risk_matrik';
    function __construct() {
        parent::__construct();
    }
    
    function index() {
        render();
    }
    
    function sortable() {
        render();
    }

    function data($tahun = "", $tipe = 'table') {
        $arr            = [
	        'select'	=> '*',
	        'where'     => [
	            'a.is_active' => 1,
                // 'a.id' => 4
	        ],
	    ];


        // $tahun = get('tahun');


	    $data['grup'][0]= get_data('tbl_risk_register a',$arr)->result();
        $data['int_control'] = get_data('tbl_internal_control','is_active',1)->result_array(); 

        // debug($data['grup'][0]);die;


      
    //    debug($data['cc']);die;
        $response	= array(
            'table'		=> $this->load->view('risk_management/risk_matrik/table',$data,true),
        );
	    render($response,'json');
	}

    function save_perubahan() {
		$control = post('control');

		if(!empty($control)){

			if(is_array($control)) {
				foreach($control as $p => $v){
					// debug(json_encode($v,true));die;
					update_data('tbl_risk_register',
						['id_internal_control' => json_encode($v,true)],
						['id'=>$p]
					);
				}
			}

			$response = [
				'status'	=> 'success',
				'message'	=> lang('data_berhasil_disimpan')
			];

		}else{
			$response = [
				'status' => 'info',
				'message' => 'Tidak ada data yang di setting'
			];
		}
		render($response,'json');
	}
}

