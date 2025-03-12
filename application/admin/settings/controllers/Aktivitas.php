<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Aktivitas extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		render();
	}

	function data() {
		$arr            = [
	        'select'	=> '*',
	        'where'     => [
	            'a.is_active' => 1,
	        ],
	    ];

	    $data['grup'][0]= get_data('tbl_aktivitas a',$arr)->result();
        $data['risk'] = get_data('tbl_risk_register','is_active',1)->result_array(); 
		$data['section'] = get_data('tbl_m_audit_section',[
			'where' => [
				'is_active' => 1,
				'id_group_section' => 5
			]
		])->result_array();

        $response	= array(
            'table'		=> $this->load->view('settings/aktivitas/table',$data,true),
        );
	    render($response,'json');
	}
	

	function get_data() {
		$data = get_data('tbl_aktivitas','id',post('id'))->row_array();
		render($data,'json');
	}

	function save() {
		$section = post('section');
		$risk = post('risk');

		if(!empty($section) || !empty($risk)){

			if(is_array($section)) {
				foreach($section as $p => $v){
					// debug(json_encode($v,true));die;
					update_data('tbl_aktivitas',
						['id_section' => json_encode($v,true)],
						['id'=>$p]
					);
				}
			}

			if(is_array($risk)) {
				foreach($risk as $r => $s){
					// debug(json_encode($v,true));die;
					update_data('tbl_aktivitas',
						['id_risk' => json_encode($s,true)],
						['id'=>$r]
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

	function delete() {
		$response = destroy_data('tbl_aktivitas','id',post('id'));
		render($response,'json');
	}

	function template() {
		ini_set('memory_limit', '-1');
		$arr = ['aktivitas' => 'aktivitas','is_active' => 'is_active'];
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
		$col = ['aktivitas','is_active'];
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
		$arr = ['aktivitas' => 'Aktivitas','is_active' => 'Aktif'];
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