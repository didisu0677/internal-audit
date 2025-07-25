<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Capa_monitoring extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		$data['status_cp'] = get_data('tbl_status_capa',[
			'where' => [
				'is_active' => 1,
				'id !=' => 1
			],
			])->result_array(); 

		if(user('id_group') != AUDITEE){
			$data['department'] = get_data('tbl_m_audit_section',[
				'select' => 'distinct id, section_code, section_name, description',
				'where' => [
					'is_active' => 1,
					'__m' => 'id in (select id_section_department from tbl_finding_records where id in (select id_finding from tbl_capa))',
				],
				])->result_array();

				$data['tahun'] = get_data('tbl_finding_records',[
					'select' => 'distinct year(tgl_mulai_audit) as tahun',
					'sort_by' => 'year(tgl_mulai_audit)',
					'sort' => 'DESC',
				])->result();

		}else{
			$dept = get_data('tbl_detail_auditee a',[
				'select' => 'distinct a.id_section',
				'join' => 'tbl_user b on a.nip = b.username',
				'where' => [
					'a.nip' => user('username') 
				]
			])->result();
			// debug($data['department']);die;

			$arr_d = [];
			foreach($dept as $d) {
				$arr_d[] = $d->id_section;
			}

			// $dept1 = [''];

			// if(!empty($dept->id_department) && isset($dept->id_department)) $dept1 = json_decode($dept->id_department,true);

			$data['department'] = get_data('tbl_m_audit_section a',[
				'select' => 'distinct a.id as id, a.section_code, a.section_name, a.description',
				'where' => [
					'a.is_active' => 1,
					'a.id' => $arr_d,
					'__m' => 'a.id in (select id_section_department from tbl_finding_records where id in (select id_finding from tbl_capa))'
				],
				])->result_array();



			$data['tahun'] = get_data('tbl_finding_records',[
				'select' => 'distinct year(tgl_mulai_audit) as tahun',
				'where' => [
					'id_section_department' => $arr_d,
				],
				'sort_by' => 'year(tgl_mulai_audit)',
				'sort' => 'DESC'
			])->result();
		}
		render($data);
	}

	// function data() {
	// 	$data = data_serverside();
	// 	render($data,'json');
	// }

	function data() {

		// cari department 
		if(user('id_group') != AUDITEE){
			$dept = get_data('tbl_m_audit_section',[
				'select' => 'id as id_section',
				'where' => [
					'is_active' => 1,
					'__m' => 'id in (select b.id_section_department from tbl_finding_records b where b.id in (select id_finding from tbl_capa))'
				],
				])->result();

		}else{
			$dept = get_data('tbl_detail_auditee a',[
				'select' => 'a.nip,a.id_department,a.id_section',
				'join' => 'tbl_user b on a.nip = b.username',
				'where' => [
					'a.nip' => user('username') 
				]
			])->result();
		}

		$arr_d = [];
		foreach($dept as $d) {
			$arr_d[] = $d->id_section;
		}

		//
		$arr            = [
			'select'    => 'a.*,b.id_section_department,b.periode_audit, b.nama_auditor, b.finding,b.bobot_finding, d.nama as pic, e.status as status_capa, s.urutan', 
			'join'      =>  [
				'tbl_finding_records b on a.id_finding = b.id type LEFT',
				'tbl_user d on a.pic_capa = d.username type LEFT',
				'tbl_status_capa e on a.id_status_capa = e.id type LEFT',
				'tbl_m_audit_section s on b.id_department_auditee = s.id type LEFT'
				],
				'where' => [
					'a.is_active'   => 0,  
				],
				'order_by' => 's.urutan'
		];

		$data['capa'] = get_data('tbl_capa a',$arr)->result();

		$arr1 = [
			'select' => 'a.*,b.section_name as department',
				'join' => 'tbl_m_audit_section b on a.id_section_department = b.id type LEFT',
				'where' => [
					'a.is_active' => 0
				],
				'sort_by' => 'a.periode_audit'
		];

		if(post('tahun')) {
			$arr1['where']['year(a.tgl_mulai_audit)'] = post('tahun');
		}

		if(post('dept') && post('dept') != 'ALL') {
			$arr1['where']['a.id_section_department'] = post('dept');
		}else{
			$arr1['where']['a.id_section_department'] = $arr_d;
		}

		$data['finding'] = get_data('tbl_finding_records a',$arr1)->result();
		
		render($data,'layout:false');

	}
	

	function get_data() {
		$data = get_data('tbl_capa','id',post('id'))->row_array();
		$data['progress'] = get_data('tbl_capa_progress',[
			'where' => [
				'id_capa' => $data['id'],
				'id_finding' => $data['id_finding']
			],
		])->result();


		render($data,'json');
	}
	function detail($id='') {
		$data = get_data('tbl_finding_records a',[
			'select' => 'a.*, b.nama as nama_auditee, c.department',
			'join'	=> [
					'tbl_auditee b on a.auditee = b.id type LEFT',
					'tbl_m_department c on a.id_department_auditee = c.id type LEFT'
			],
			'where' => [
				'a.id'=>$id,
			],
			])->row_array();
		if(isset($data['id'])) {
			render($data,'layout:false');
		} else {
			echo lang('tidak_ada_data');
		}

	}

	function save() {
		$data = post();
		if(user('id_group') != AUDITEE) { 
			$status_capa = '';
			if($data['activeTab'] == 'progress-1'){
				$status_capa = $data['status_capa1'];
			}elseif($data['activeTab'] == 'progress-2'){
				$status_capa = $data['status_capa2'];
			}else{
				$status_capa = $data['status_capa3'];
			}

			$data['id_status_capa'] = $status_capa;
			unset($data['tanggal1']) ;
			unset($data['tanggal2']) ;
			unset($data['tanggal3']) ;
		
			if($data['activeTab'] == 'progress-1' && $status_capa != 3) {
				$data['progress_ke'] = 2;
				if (isset($data['add_capa1'])) $data['dateline_capa2'] = $data['add_capa1'];
			}elseif($data['activeTab'] == 'progress-1' && $status_capa== 3) {
				$data['progress_ke'] = 1;
				if (isset($data['add_capa1'])) $data['dateline_capa2'] = $data['add_capa1'];
			}elseif($data['activeTab'] == 'progress-2' && $status_capa != 3) {
				$data['progress_ke'] = 3;
				if (isset($data['add_capa2'])) $data['dateline_capa2'] = $data['add_capa2'];
			}elseif($data['activeTab'] == 'progress-2' && $status_capa == 3) {
				$data['progress_ke'] = 2 ;
				if (isset($data['add_capa2'])) $data['dateline_capa2'] = $data['add_capa2'];
			}elseif($data['activeTab'] == 'progress-3' && $status_capa != 3) {
				$data['progress_ke'] = 3;
				if (isset($data['add_capa3'])) $data['dateline_capa2'] = $data['add_capa3'];
			}elseif($data['activeTab'] == 'progress-3' && $status_capa == 3) {
				$data['progress_ke'] = 3;
				if (isset($data['add_capa3'])) $data['dateline_capa2'] = $data['add_capa3'];
			}else{
				$data['progress_ke'] = 0;
			};
		}

		if (isset($data['add_capa1'])) unset($data['add_capa1']);
		if (isset($data['add_capa2'])) unset($data['add_capa2']);
		if (isset($data['add_capa3'])) unset($data['add_capa3']);


		$response = save_data('tbl_capa',$data,post(':validation'));

		if($response['status'] == 'success') {

			$data_progress = [
				'id' => 0,
				'id_capa' => $data['id'],
				'id_finding' => $data['id_finding'],
				'is_active' => 1,
			];

			if(user('id_group') != AUDITEE) { 
				$data_progress['status'] = $status_capa;
			}

			if($data['activeTab'] == 'progress-1'){
				$data_progress['progress'] = $data['keterangan_progress_1'];
				if(user('id_group') != AUDITEE) $data_progress['comment'] = $data['comment_progress_1'];
				$data_progress['no_progress'] = $data['no_progress1'];
				$data_progress['tanggal'] = post('tanggal1');
			}elseif($data['activeTab'] == 'progress-2'){
				$data_progress['progress'] = $data['keterangan_progress_2'];
				if(user('id_group') != AUDITEE) $data_progress['comment'] = $data['comment_progress_2'];
				$data_progress['no_progress'] = $data['no_progress2'];
				$data_progress['tanggal'] = post('tanggal2');
			}else{;
				$data_progress['progress'] = $data['keterangan_progress_3'];
				if(user('id_group') != AUDITEE) $data_progress['comment'] = $data['comment_progress_3'];
				$data_progress['no_progress'] = $data['no_progress3'];
				$data_progress['tanggal'] = post('tanggal3');
			};

			
			$cek = get_data('tbl_capa_progress',[
				'where' => [
					'id_capa' =>$data_progress['id_capa'],
					'id_finding' => $data_progress['id_finding'],
					'no_progress' => $data_progress['no_progress'] 
				],
			])->row();

			if(!isset($cek->id)) {
				$data_progress['id'] = 0;
			}else{
				$data_progress['id'] = $cek->id;
			} 

			$file = post('evidence_base-'.$data['activeTab']);
			if(!empty($file)){
				if(!is_dir(FCPATH . "assets/uploads/capa_progress/")){
					$oldmask = umask(0);
					mkdir(FCPATH . "assets/uploads/capa_progress/",0777);
					umask($oldmask);
				}

				if(@copy($file, FCPATH . 'assets/uploads/capa_progress/'.basename($file))) {
					$filename	= basename($file);
				}
			}
			$data_progress['evidence'] = $filename ?? '';
			$res_capa = save_data('tbl_capa_progress',$data_progress);

			$cek_status_finding = get_data('tbl_capa',[
				'where' => [
					'id_finding' => $data['id_finding'],
					'id_status_capa not' => [3,5]
				],
			])->result();

			if (empty($cek_status_finding)) {
				update_data('tbl_finding_records',['status_finding'=>2],['id'=>$data['id_finding']]);
			}

			/// kirim email dan notifikasi

			if(user('id_group') == AUDITEE) {

				$usr 	= get_data('tbl_capa a',[
					'select' => 'a.*,b.email, b.nama, d.id as id_user, c.id_section_department as id_section, e.section_name',
					'join'   => ['tbl_finding_records c on a.id_finding = c.id type LEFT',
								 'tbl_m_auditor b on c.auditor = b.id type LEFT',
								 'tbl_user d on b.nip = d.username type LEFT',
								 'tbl_m_audit_section e on c.id_section_department = e.id type LEFT',
								],	
					'where'  => [
						'a.id' => $data['id'],
					],
				])->row();

				$oleh = "Auditee" ;
				$message = 'Auditee dari departemen ' . $usr->section_name . ' telah memperbarui pelaksanaan CAPA Plan di sistem Audit Management System untuk temuan audit berikut';
			
			}else{

				$usr 	= get_data('tbl_capa a',[
					'select' => 'a.*,b.email, b.nama,b.id as id_user, c.id_section_department as id_section',
					'join'   => ['tbl_finding_records c on a.id_finding = c.id type LEFT',
								 'tbl_user b on a.pic_capa = b.username type LEFT'
								],	
					'where'  => [
						'a.id' => $data['id'],
					],
				])->row();

				$oleh = "Auditor" ;
				$message = 'Auditor telah memperbarui pelaksanaan CAPA Plan department anda di sistem Audit Management System untuk temuan audit berikut';

			}

			$cc_user 			= get_data('tbl_user','id_group',[41,40])->result();
			$cc_email1 = [];
			foreach($cc_user as $u) {
				if($u->email != $usr->email) $cc_email1[] 	= $u->email;
			}

			$cc = get_data('tbl_detail_auditee a',[
				'select' => 'a.nip, b.email',
				'join'	 => 'tbl_user b on a.nip = b.username',
				'where' => [
					'a.id_section'=>$usr->id_section
				],
			])->result();

			$cc_email2 = [];
			foreach($cc as $c) {
				if($c->email != $usr->email) $cc_email2[] 	= $c->email;
			}

			$cc_email = array_merge($cc_email1, $cc_email2);


			if(isset($usr->id)) {
				$link				= base_url().'internal/capa_monitoring';
				$desctiption 		= $message;		
				$data_notifikasi 	= [
						'title'			=> 'CAPA Progress Telah Diperbarui oleh ' . $oleh . ' – Mohon Verifikasi',
						'description'	=> $desctiption,
						'notif_link'	=> $link,
						'notif_date'	=> date('Y-m-d H:i:s'),
						'notif_type'	=> 'info',
						'notif_icon'	=> 'fa-exchange-alt',
						'id_user'		=> $usr->id_user,
						'transaksi'		=> 'capa_monitoring',
						'id_transaksi'	=> post('id')
					];
					insert_data('tbl_notifikasi',$data_notifikasi);	

				if(setting('email_notification')) {
					send_mail([
						'subject'		=> 'CAPA Progress Telah Diperbarui oleh ' . $oleh . ' – Mohon Verifikasi',
						// 'to'			=> 'dsuherdi@ho.otsuka.co.id',
						'to'			=> $usr->email,
						'cc'			=> $cc_email,
						'nama_user'		=> $usr->nama,
						'description'	=> $desctiption,
						'description2'	=> 'Silakan cek dan update progress di link berikut :',
						'detail' => 	get_data('tbl_capa_progress a',[
										'select' => 'a.*, c.audit_area, c.finding',
										'join' => ['tbl_capa b on a.id_capa = b.id type LEFT',
												   'tbl_finding_records c on a.id_finding = c.id type LEFT',
												  ],
										'where' => [
											'a.id' => $res_capa['id']
										],
										])->row_array(),
						'url'			=> $link
					]);
				}
			}
			///
		}

		render($response,'json');
	}

	function delete() {
		$response = destroy_data('tbl_capa','id',post('id'));
		render($response,'json');
	}

	function template() {
		ini_set('memory_limit', '-1');
		$arr = ['id_finding' => 'id_finding','nomor' => 'nomor','description' => 'description','deadline_capa' => 'deadline_capa','pic_capa' => 'pic_capa','status_capa' => 'status_capa','followup' => 'followup','followup_date' => 'followup_date','followup_email' => 'followup_email','followup_meeting' => 'followup_meeting','keterangan_progress' => 'keterangan_progress','capa_score' => 'capa_score','achievement' => 'achievement','evidence' => 'evidence','is_active' => 'is_active'];
		$config[] = [
			'title' => 'template_import_capa_monitoring',
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function import() {
		ini_set('memory_limit', '-1');
		$file = post('fileimport');
		$col = ['id_finding','nomor','description','deadline_capa','pic_capa','status_capa','followup','followup_date','followup_email','followup_meeting','keterangan_progress','capa_score','achievement','evidence','is_active'];
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
					$save = insert_data('tbl_capa',$data);
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
		$arr = ['id_finding' => 'Id Finding','nomor' => 'Nomor','description' => 'Description','deadline_capa' => '-dDeadline Capa','pic_capa' => 'Pic Capa','status_capa' => 'Status Capa','followup' => 'Followup','followup_date' => '-dFollowup Date','followup_email' => 'Followup Email','followup_meeting' => 'Followup Meeting','keterangan_progress' => 'Keterangan Progress','capa_score' => 'Capa Score','achievement' => 'Achievement','evidence' => 'Evidence','is_active' => 'Aktif'];
		$data = get_data('tbl_capa')->result_array();
		$config = [
			'title' => 'data_capa_monitoring',
			'data' => $data,
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function capa_nottification() {
		$cek_capa = get_data('tbl_capa a',[
			'select' => 'a.*,b.email, c.id_section_department',
			'join'   => ['tbl_user b on a.pic_capa = b.username type LEFT',
						'tbl_finding_records c on a.id_finding = c.id',
						],
			'where' => [
					'a.id' => post('id'),
				],
		])->row();

		if(empty($cek_capa)) {
			$response = [
				'status' => 'success',
				'message' => 'tidak ada capa yang due date',
			];
			render($response,'json');
		}else{

			$cc_user 			= get_data('tbl_user','id_group',[41,40])->result();
			$cc_email1 = [];
			foreach($cc_user as $u) {
				$cc_email1[] 	= $u->email;
			}

			$cc = get_data('tbl_detail_auditee a',[
				'select' => 'a.nip, b.email',
				'join'	 => 'tbl_user b on a.nip = b.username',
				'where' => [
					'a.id_section'=>$cek_capa->id_section
				],
			])->result();

			$cc_email2 = [];
			foreach($cc as $c) {
				$cc_email2[] 	= $c->email;
			}

			$cc_email = array_merge($cc_email1, $cc_email2);


			$data = array(
				'subject'	=> 'Notification Capa Plan Progress',
				'message'	=> 'Reminder CAPA - Mohon untuk segera follow up' . ' ' . $cek_capa->isi_capa,
				'to'		=> 'dsuherdi@ho.otsuka.co.id',
				'cc'		=> $cc_email
			);

			$response = send_mail($data);
			render($response,'json');
		}
	}

}