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
				'where' => [
					'is_active' => 1,
					'__m' => 'id in (select id_department_auditee from tbl_finding_records)'
				],
				])->result_array();

				$data['tahun'] = get_data('tbl_finding_records',[
					'select' => 'distinct year(tgl_mulai_audit) as tahun',
				])->result();

		}else{
			$dept = get_data('tbl_detail_auditee a',[
				'select' => 'a.nip,a.id_department,a.id_section',
				'join' => 'tbl_user b on a.nip = b.username',
				'where' => [
					'a.nip' => user('username') 
				]
			])->result();

			$arr_d = [];
			foreach($dept as $d) {
				$arr_d[] = $d->id_section;
			}

			// $dept1 = [''];

			// if(!empty($dept->id_department) && isset($dept->id_department)) $dept1 = json_decode($dept->id_department,true);

			$data['department'] = get_data('tbl_m_audit_section a',[
				'select' => 'b.id as id, b.section_code, b.section_name, b.description',
				'join' => 'tbl_m_audit_section b on a.parent_id = b.id type LEFT',
				'where' => [
					'a.is_active' => 1,
					'a.id' => $arr_d,
					'__m' => 'a.id in (select id_section_department from tbl_finding_records)'
				],
				])->result_array();

			// debug($data['department']);die;

			$data['tahun'] = get_data('tbl_finding_records',[
				'select' => 'distinct year(tgl_mulai_audit) as tahun',
				'where' => [
					'id_section_department' => $arr_d
				]
			])->result();
		}
		render($data);
	}

	// function data() {
	// 	$data = data_serverside();
	// 	render($data,'json');
	// }

	function data() {
		$arr            = [
			'select'    => 'a.*,b.periode_audit, b.nama_auditor, b.finding,b.bobot_finding, c.department, d.nama as pic, e.status as status_capa', 
			'join'      =>  ['tbl_finding_records b on a.id_finding = b.id type LEFT',
							 'tbl_m_department c on b.id_department_auditee = c.id type LEFT',
							 'tbl_user d on a.pic_capa = d.username type LEFT',
							 'tbl_status_capa e on a.id_status_capa = e.id type LEFT'
							],
				'where' => [
					'a.is_active'   => 0,  
					],
		];


		$data['capa'] = get_data('tbl_capa a',$arr)->result();

		$arr1 = [
			'select' => 'a.*,b.department',
				'join' => 'tbl_m_department b on a.id_department_auditee = b.id type LEFT',
				'where' => [
					'a.is_active' => 0
				],
				'sort_by' => 'a.periode_audit'
		];

		if(post('tahun')) {
			$arr1['where']['year(a.tgl_mulai_audit)'] = post('tahun');
		}

		if(post('dept') && post('dept') != 'ALL') {
			$arr1['where']['a.id_department_auditee'] = post('dept');
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
			$usr 	= get_data('tbl_capa a',[
				'select' => 'a.*,b.email, b.id as id_user, b.nama',
				'join'   => 'tbl_user b on a.pic_capa = b.username',
				'where'  => [
					'id' => $data['id'],
				],
			])->row();

			if(isset($usr->id)) {
				$link				= base_url().'internal/capa_monitoring';
				$desctiption 		= 'Progress Capa nomor : <strong>'.$usr->nomor.'</strong>'. ' sekarang ber status : ' .
					$data_notifikasi 	= [
						'title'			=> 'Progress Capa',
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
						'subject'		=> 'Pprogress capa nomor : '.$usr->nomor. ' dengan capa plan '. $usr->isi_capa ,
						'to'			=> $usr->email,
						'cc'			=> '',
						'nama_user'		=> $usr->nama,
						'description'	=> $desctiption,
						'detail' => 	$data,
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
				'subject'	=> 'Notification Capa Progress',
				'message'	=> 'Reminder CAPA Mohon untuk segera follow up' . ' ' . $cek_capa->isi_capa,
				'to'		=> 'dsuherdi@ho.otsuka.co.id',
				'cc'		=> $cc_email
			);

			$response = send_mail($data);
			render($response,'json');
		}
	}

}