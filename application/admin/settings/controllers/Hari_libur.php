<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Hari_libur extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		$data['tahun']	= [];
		for($i = 0; $i <= 2; $i++) {
			$x	= date('Y') + $i;
			$data['tahun'][$x]	= $x;
		}
		render($data);
	}

	function data() {
		$data = data_serverside();
		render($data,'json');
	}

	function get_data() {
		$data = get_data('bas_m_hari_libur','id',post('id'))->row_array();
		render($data,'json');
	}

	function save() {
		$data = post();
		if($data['tanggal_selesai'] == '') {
			$data['tanggal_selesai'] = $data['tanggal_mulai'];
		} else {
			if(strtotime($data['tanggal_mulai']) > strtotime($data['tanggal_selesai'])) {
				$data['tanggal_selesai'] = $data['tanggal_mulai'];
			}
		}
		$response = save_data('bas_m_hari_libur',$data,post(':validation'));
		render($response,'json');
	}

	function delete() {
		$response = destroy_data('bas_m_hari_libur','id',post('id'));
		render($response,'json');
	}

	function otomatis() {
		$tahun = post('tahun') ?: date('Y');
		$this->load->library('libur');
		$x = $this->libur->get($tahun,true);
		$n = 0;
		foreach($x as $l) {
			$data = $l;
			$cek = get_data('bas_m_hari_libur',[
				'where'	=> $data
			])->row();
			if(!isset($cek->id)) {
				insert_data('bas_m_hari_libur',$data);
				$n++;
			}
		}
		render([
			'status'	=> 'success',
			'message'	=> $n.' '.lang('hari_libur_berhasil_ditambahkan')
		],'json');
	}


}