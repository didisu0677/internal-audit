<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Schedule_audit extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		$data['institusi'] = get_data('tbl_institusi_audit','is_active',1)->result_array();
		$data['department'] = get_data('tbl_m_department','is_active',1)->result_array();
		render($data);
	}

	function data() {
		$data = data_serverside();
		render($data,'json');
	}

	function get_data() {
		$data = get_data('tbl_schedule_audit','id',post('id'))->row_array();
		$data['department_auditee']		= json_decode($data['department_auditee'],true);

		// $cb_schedule	= get_data('tbl_pengajuan',[
		//     'where'			=> [
		//         'nomor'	=> $data['nomor_disposisi'],
		//     ],
		//     'sort_by'=>'nomor_pengajuan','sort'=>'ASC'
		// ])->result();
		// $data['nomor_pengajuan']    = '<option value=""></option>';
		// foreach($cb_nopengajuan as $d) {
		//     $data['nomor_pengajuan'] = '<option value="'.$d->nomor_pengajuan.'"
        //     data-nama_pengadaan="'.$d->nama_pengadaan.'"
        //     data-tanggal_pengadaan="'.c_date($d->tanggal_pengadaan).'"
        //     data-divisi="'.$d->nama_divisi.'"
        //     data-unit_kerja="'.$d->unit_kerja.'"
        //     data-unit="'.$d->id_unit_kerja2.'"
        //     data-mata_anggaran="'.$d->mata_anggaran.'"
        //     data-besar_anggaran="'.custom_format($d->besar_anggaran).'"
        //     data-usulan_hps="'.custom_format($d->usulan_hps).'"
        //     >'.$d->nomor_pengajuan.'  |  '.$d->nama_pengadaan.'</option>';
		// }

		render($data,'json');
	}

	
	function save() {
		$data = post();
		$data['department_auditee'] = json_encode(post('department_auditee'));

		$response = save_data('tbl_schedule_audit',$data,post(':validation'));
		render($response,'json');
	}

	function delete() {
		$response = destroy_data('tbl_schedule_audit','id',post('id'));
		render($response,'json');
	}

	function template() {
		ini_set('memory_limit', '-1');
		$arr = ['nomor' => 'nomor','id_institusi_audit' => 'id_institusi_audit','tanggal_mulai' => 'tanggal_mulai','tanggal_akhir' => 'tanggal_akhir','tgl_closing_meeting' => 'tgl_closing_meeting','audit_area' => 'audit_area','departement_auditee' => 'departement_auditee','is_active' => 'is_active'];
		$config[] = [
			'title' => 'template_import_schedule_audit',
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function import() {
		ini_set('memory_limit', '-1');
		$file = post('fileimport');
		$col = ['nomor','id_institusi_audit','tanggal_mulai','tanggal_akhir','tgl_closing_meeting','audit_area','departement_auditee','is_active'];
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
					$save = insert_data('tbl_schedule_audit',$data);
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
		$arr = ['nomor' => 'Nomor','id_institusi_audit' => 'Institusi Audit','tanggal_mulai' => '-dTanggal Mulai','tanggal_akhir' => '-dTanggal Akhir','tgl_closing_meeting' => '-dTgl Closing Meeting','audit_area' => 'Audit Area','departement_auditee' => 'Departement Auditee','is_active' => 'Aktif'];
		$data = get_data('tbl_schedule_audit')->result_array();
		$config = [
			'title' => 'data_schedule_audit',
			'data' => $data,
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

}