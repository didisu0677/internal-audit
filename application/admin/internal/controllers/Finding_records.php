<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Finding_records extends BE_Controller {
	function __construct() {
		parent::__construct();
	}

	function index() {
		$id = get('id');
		$data['id_transaction'] = decode_id($id)[0] ?? '';
		$data['auditor'] = get_data('tbl_m_auditor','is_active',1)->result_array();
		// $data['auditee'] = get_data('tbl_auditee','is_active',1)->result_array();

		if(user('id_group') != AUDITEE){
			$data['department'] = get_data('tbl_m_audit_section',[
				'where' => [
					'is_active' => 1,
					'__m' => 'id in (select id_section_department from tbl_finding_records)'
				],
				])->result_array();

				$data['tahun'] = get_data('tbl_finding_records',[
					'select' => 'distinct year(tgl_mulai_audit) as tahun',
				])->result();
		}else{
			// $dept = get_data('tbl_auditee a',[
			// 	'select' => 'a.nip,a.id_department',
			// 	'join' => 'tbl_user b on a.nip = b.username',
			// 	'where' => [
			// 		'a.nip' => user('username') 
			// 	],
			// ])->row(); 

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
				'select' => 'a.id as id, a.section_code, a.section_name, a.description',
				// 'join' => 'tbl_m_audit_section b on a.parent_id = b.id type LEFT',
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

		$data['aktivitas'] = get_data('tbl_sub_aktivitas a',[
			'select' => 'a.id, CONCAT(b.aktivitas," - ", a.sub_aktivitas) as sub_aktivitas',
			'join'  => 'tbl_aktivitas b on a.id_aktivitas = b.id type LEFT',
		])->result_array();


		if(user('id_group') != AUDITEE){

			$data['user']	= get_data('tbl_user',[
				'where'	=> [
					'is_active'	=> 1,
				]
			])->result_array();
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
			

			// $data['user']	= get_data('tbl_user a',[
			// 	'select' => 'a.*',
			// 	'join' => 'tbl_auditee b on a.username = b.nip',
			// 	'where'	=> [
			// 		'a.is_active'	=> 1,
			// 		'b.id_section_department' => $arr_d
			// 	]
			// ])->result_array();

			$data['user']	= get_data('tbl_detail_auditee a',[
				'select' => 'b.*',
				'join' => 'tbl_user b on a.nip = b.username',
				'where'	=> [
					'b.is_active'	=> 1,
					'a.id_section' => $arr_d
				]
			])->result_array();

		}

		render($data);
	}

	function data($tahun="",$department="") {
		
		$id_transaction = get('id');

		$config	= [];		


		if(user('id_group') == AUDITEE) {
			 $config = [
				'access_edit'	=> false,
				'access_delete'	=> false,
				'access_view'	=> false,
	   		 ];
		}else{
			$config = [
				'access_edit'	=> false,
				'access_delete'	=> false,
				'access_view'	=> false,
	   		 ];
		}

		/// cari department //
		if(user('id_group') != AUDITEE){
			$dept = get_data('tbl_m_audit_section',[
				'select' => 'id as id_section',
				'where' => [
					'is_active' => 1,
					'__m' => 'id in (select id_section_department from tbl_finding_records)'
				],
				])->result();

		}else{

			$dept = get_data('tbl_detail_auditee a',[
				'select' => 'a.nip,a.id_department,a.id_section',
				'join' => 'tbl_user b on a.nip = b.username type LEFT',
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


		$config['join'] = [
			'tbl_auditee ON tbl_auditee.id = tbl_finding_records.auditee TYPE LEFT',
			'tbl_sub_aktivitas sa on tbl_finding_records.id_sub_aktivitas = sa.id type LEFT',
		];
		// $config['join'][] = 'tbl_auditee ON tbl_auditee.id = tbl_finding_records.auditee TYPE LEFT';
		$config['button'][]	= button_serverside('btn-default','btn-capa',['far fa-copy',lang('capa_plan'),true],'act-dokumen',['status_finding'=>0]);
		if(user('id_group') != AUDITEE) {
	        // $config['button'][]	= button_serverside('btn-warning','btn-input',['fa-edit',lang('ubah'),true],'edit',['status_finding'=>0]);
	        $config['button'][]	= button_serverside('btn-warning','btn-input',['fa-edit',lang('ubah'),true],'edit');
		}
		if(user('id_group') != AUDITEE) {
	        $config['button'][]	= button_serverside('btn-danger','btn-delete',['fa-trash-alt',lang('hapus'),true],'delete',['status_finding'=>0]);
	    }

		if($department && $department != 'ALL') {
	    	$config['where']['id_section_department']	= $department;	
	    }else{
			$config['where']['id_section_department']	= $arr_d;	
		}

		if($tahun) {
			$config['where']['year(tgl_mulai_audit)']	= $tahun;	
		}

		if(!empty($id_transaction)){
			unset($config['where']);
			$config['where']['tbl_finding_records.id'] = $id_transaction;
		}

		$config['sort_by'] = 'sa.sub_aktivitas';
		$config['sort'] = 'asc';

		$data = data_serverside($config);
		
		render($data,'json');
	}

	function get_data() {
		$data = get_data('tbl_finding_records a',[
			'select' => 'a.*',
			'where' => [
				'a.id' => post('id')
			],
			])->row_array();

			// $data = get_data('tbl_m_finding a',[
			// 	'select' => 'a.*,b.department',
			// 	'join'   =>  'tbl_m_department b on a.id_department_auditee = b.id',
			// 	'where' => [
			// 		'a.id' => $x['id_m_finding']
			// 	],
			// 	])->row_array();
			
			// $data['auditor'] = $data['id_auditor'];

		
		$data['detail'] = get_data('tbl_finding_records fr',[
			'select' => 'fr.*, frf.filename',
			'join' => 'tbl_finding_record_files frf on fr.id = frf.id_finding_record type LEFT',
			'where' => [
				// 'id_m_finding' => $x['id_m_finding']
				'id_section_department' => $data['id_section_department'],
				'auditee' => $data['auditee'],
				'auditor' => $data['auditor'],
				'periode_audit' => $data['periode_audit'],
				'DATE_FORMAT(create_at, "%d-%m-%Y %H:%i") =' => date('d-m-Y H:i', strtotime($data['create_at']))
			]
		])->result();

		$cb_schedule  = get_data('tbl_schedule_audit a',[
			'select' => 'a.*,b.nama_institusi',
			'join'  => 'tbl_institusi_audit b on a.id_institusi_audit = b.id type LEFT',
	        'where'	=> [
	            'a.is_active'	=> 1,
				'a.nomor' => $data['periode_audit']
	        ],
		])->result();

	    $data['nomor_schedule']    = '';
	    foreach($cb_schedule as $d) {
	        $data['nomor_schedule'] .= '<option value="'.$d->nomor.'"
            data-deskripsi="'.$d->deskripsi.'"
			data-nama_institusi="'.$d->nama_institusi.'"
            data-tanggal_mulai ="'.c_date($d->tanggal_mulai).'"
            data-tanggal_akhir="'.c_date($d->tanggal_akhir).'"
			data-tgl_closing_meeting ="'.c_date($d->tgl_closing_meeting).'"
            >'.$d->nomor.'  |  '.$d->deskripsi.'</option>';
	    }

		render($data,'json');
	}

	function get_combo(){
	    $cb_schedule  = get_data('tbl_schedule_audit a',[
			'select' => 'a.*,b.nama_institusi',
			'join'  => 'tbl_institusi_audit b on a.id_institusi_audit = b.id',
	        'where'	=> [
	            'a.is_active'	=> 1,
	        ],
		])->result();
	    $data['nomor_schedule']    = '<option value=""></option>';
	    foreach($cb_schedule as $d) {
	        $data['nomor_schedule'] .= '<option value="'.$d->nomor.'"
            data-deskripsi="'.$d->deskripsi.'"
			data-nama_institusi="'.$d->nama_institusi.'"
            data-tanggal_mulai ="'.c_date($d->tanggal_mulai).'"
            data-tanggal_akhir="'.c_date($d->tanggal_akhir).'"
			data-tgl_closing_meeting ="'.c_date($d->tgl_closing_meeting).'"
            >'.$d->nomor.'  |  '.$d->deskripsi.'</option>';
	    }

	    render($data,'json');
	}

	function get_department_auditee(){
		$id = post('id');

		$arr_dept = [''];
		$dept = get_data('tbl_auditee a', [
			'where' => [
				'is_active' => 1,
				'id' => $id
			],
		])->row_array();
		if(isset($dept)) $arr_dept = json_decode($dept['id_section'],true);

		$cb_department = get_data('tbl_m_audit_section a',[
			'select' => 'a.*',
			'where'	=> [
				'a.is_active'	=> 1,
				'a.id' => $arr_dept 
			],
		])->result();

		// debug($cb_department);die;

	    $data['department']    = '';
	    foreach($cb_department as $d) {
	        $data['department'] .= '<option value="'.$d->id.'"
            data-kode="'.$d->section_code.'"
            >'.$d->section_name.'</option>';
	    }
	    render($data,'json');
	}

	function get_auditee(){
		$id = post('id');
		$data = get_data('tbl_schedule_audit','nomor',$id)->row_array();

		$department = [''];
		if($data && $data['department_auditee'] != null ) {
			$department = json_decode($data['department_auditee'],true);
			$cb_auditee = get_data('tbl_detail_auditee a',[
				'select' => 'distinct b.id,b.nama',
				'join'  => 'tbl_auditee b on a.nip = b.nip type LEFT',
				'where' => [
					'a.is_active' => 1,
					'a.id_section' => $department
				]
			])->result();

			$data['auditee1']    = '';
			foreach($cb_auditee as $d) {
				$data['auditee1'] .= '<option value="'.$d->id.'"
				>'.$d->nama.'</option>';
			}
		}
	    render($data,'json');
	}

	function add_capa() {
		$data = get_data('tbl_finding_records a',[
			'select' => 'a.*,b.section_name as department, c.nama as nama_auditee, d.filename',
			'join'   =>  ['tbl_m_audit_section b on a.id_department_auditee = b.id',
						  'tbl_auditee c on a.auditee = c.id',
						  'tbl_finding_record_files d on a.id = id_finding_record type LEFT'
						 ],
			'where' => [
				'a.id' => post('id')
			],
			])->row_array();

			$data['detail']	= get_data('tbl_capa',[
				'select'	=> '*',
				'where' 	=> [
					'id_finding'   => post('id')
				],
				'sort_by'  => 'id',
			])->result_array();


			$data['user']	= get_data('tbl_detail_auditee a',[
				'select' => 'distinct b.username,b.nama',
				'join' => 'tbl_user b on a.nip = b.username type LEFT',
				'where'	=> [
					'b.is_active'	=> 1,
					'a.id_section' => $data['id_section_department']
				]
			])->result_array();


		render($data,'json');
	}

	function save() {
		$data = post();
		$bobot_finding = post('bobot_finding');

		$data['nomor']  = '';
		$schedule  = get_data('tbl_schedule_audit', 'nomor', $data['periode_audit'])->row();
		$auditor   = get_data('tbl_m_auditor','id',$data['auditor'])->row();
		if($auditor) $data['nama_auditor'] = $auditor->nama;
		if($schedule) {
			$data['id_institusi_audit'] = $schedule->id_institusi_audit;
		}


		$dept = get_data('tbl_m_audit_section','id',$data['id_section_department'])->row();

		if(isset($dept->id)) {
			$div = get_data('tbl_m_audit_section','section_code',substr($dept->section_code, 2, 2))->row();

			// if(isset($div->id)) $data['id_divisi'] = $div->id;
			$data['id_divisi'] = $dept->level3;


			// $d = get_data('tbl_m_audit_section',[
			// 		'where' => [
			// 			'section_code' => substr($dept->section_code, 4, 1),
			// 			'parent_id' => $div->id,
			// 		],
			// 	])->row();
			
			// if(isset($d->id)) $data['id_department_auditee'] = $d->id;
			$data['id_department_auditee'] = $dept->level4;
			
		}

		$data['id_auditor'] = $data['auditor'];
		$file 				= post('file_finding');

		$response 	= save_data('tbl_m_finding',$data,post(':validation'));

		if($response['status'] = 'success') {

			$isi_finding 	= post('isi_finding','html');
			$id_finding_records = post('id_finding_records');
			$isi_finding = post('isi_finding');
			$bobot_finding = post('bobot_finding');

			foreach($isi_finding as $k => $v) {
				$last_file 		= [];
				if($id_finding_records[$k]) {
					$dt 		= get_data('tbl_finding_records','id',$id_finding_records[$k])->row();
					if(isset($dt->id)) {
						$last_file = $dt->file;
					}
				}		
				$file 				= post('file_finding');
				$filename 			= [];
				$dir 				= '';

				if(isset($file[$k]) && !empty($file[$k]) && $file[$k] != '') {
					if(!is_dir(FCPATH . "assets/uploads/finding_records/")){
						$oldmask = umask(0);
						mkdir(FCPATH . "assets/uploads/finding_records/",0777);
						umask($oldmask);
					}
					$copy = 0 ;
					if($file[$k]) {						
						if(@copy($file[$k], FCPATH . 'assets/uploads/finding_records/'.basename($file[$k]))) {
							$filename[$k]	= basename($file[$k]);
							if(!$dir) $dir = str_replace(basename($file[$k]),'',$file[$k]);
							$copy = 1 ;
						}
					}
				}
				
				$data['bobot_finding'] = $bobot_finding[$k];
				$data['id_m_finding'] = post('id');
				$data['id'] = $id_finding_records[$k];
				$data['finding'] = $isi_finding[$k];

				$response_f = save_data('tbl_finding_records',$data,post(':validation'));
				
				// insert attachment file to database tbl_finding_record_files
				if($response_f['status'] == 'success'){
					$attachment = [
						'id_finding_record' => $response_f['id'],
						'filename' => $filename[$k] ?? '',
					];
					insert_data('tbl_finding_record_files', $attachment);	
				}
				
				/// kirim email dan notifikasi
				$usr 	= get_data('tbl_auditee a',[
					'select' => 'a.*,b.id as id_user',
					'join' => 'tbl_user b on a.nip = b.username type LEFT',
					'where'  => [
						'a.id' => $data['auditee'],
					],
				])->row();
			

				$cc_user 			= get_data('tbl_user','id_group',[41,40])->result();
				$cc_email1 = [];
				foreach($cc_user as $u) {
					if($u->email != $usr->email) $cc_email1[] 	= $u->email;
				}

				$cc = get_data('tbl_detail_auditee a',[
					'select' => 'a.nip, b.email',
					'join'	 => 'tbl_user b on a.nip = b.username',
					'where' => [
						'a.id_section'=>$data['id_section_department']
					],
				])->result();

				$cc_email2 = [];
				foreach($cc as $c) {
					if($c->email != $usr->email) $cc_email2[] 	= $c->email;
				}

				$cc_email = array_merge($cc_email1, $cc_email2);


				if(isset($usr->id)) {
					$section = get_data('tbl_m_audit_section','id',$data['id_section_department'])->row();

					$link				= base_url().'internal/finding_records';
					$desctiption 		= 'Telah dicatat finding audit pada sistem Audit Management System terkait departemen Anda: ' .$section->section_name ;		
					$data_notifikasi 	= [
							'title'			=> 'Finding Internal Audit',
							'description'	=> $desctiption,
							'notif_link'	=> $link,
							'notif_date'	=> date('Y-m-d H:i:s'),
							'notif_type'	=> 'info',
							'notif_icon'	=> 'fa-exchange-alt',
							'id_user'		=> $usr->id_user,
							'transaksi'		=> 'finding_records',
							'id_transaksi'	=> post('id')
						];
						insert_data('tbl_notifikasi',$data_notifikasi);	
					
					$data_mytask = get_data('tbl_mytask', [
						'where' => [
							'id_user'		=> $usr->id_user,
							'type'			=> 'finding',
							'id_transaction'=> $response_f['id'],
						]
					])->row_array();
					$sub_aktivitas = get_data('tbl_sub_aktivitas','id',$data['id_sub_aktivitas'])->row_array();
					$mytask = [
						'id'			=> $data_mytask['id'] ?? 0,
						'id_user'		=> $usr->id_user,
						'type'			=> 'finding',
						'id_transaction'=> $response_f['id'],
						'title'			=> 'Finding Internal Audit',
						'description'	=> 'Terdapat finding yang harus ditindaklanjuti pada audit area '.$sub_aktivitas['sub_aktivitas'],
						'status'		=> 'pending',
					];
					save_data('tbl_mytask',$mytask);
					
					if(setting('email_notification')) {
						send_mail([
							'subject'		=> 'Notifikasi Temuan Audit – Mohon Tindak Lanjut CAPA Plan',
							'to'			=> $usr->email,
							'cc'			=> $cc_email,
							'nama_user'		=> $usr->nama,
							'description'	=> 'Telah dicatat finding audit pada sistem Audit Management System terkait departemen Anda: ' ,
							'description2'	=> 'Untuk mengetahui lebih lanjut silakan cek di link berikut :',
							'detail' => 	get_data('tbl_finding_records a',[
								'select' => 'a.*,b.section_name',
								'join'	 => 'tbl_m_audit_section b on a.id_section_department = b.id type LEFT',
								'where'  => [
									'a.id' => $response_f['id'],
								]
							])->row_array(),
							'url'			=> $link
						]);
					}
				}
				///
			}

		}

		render($response,'json');
	}

	function save_capa() {
		$data = post();

		$nomor = post('nomor') ;
		$isi_capa = post('isi_capa') ;
		$dateline_capa = post('due_date') ;
		$pic_capa = post('pic_capa') ;
		$id_capa = post('id_capa');
		$file = post('file');

		// $schedule  = get_data('tbl_schedule_audit', 'nomor', $data['periode_audit'])->row();
		// $auditor   = get_data('tbl_m_auditor','id',$data['auditor'])->row();
		// if($auditor) $data['nama_auditor'] = $auditor->nama;
		// if($schedule) {
		// 	$data['id_institusi_audit'] = $schedule->id_institusi_audit;
		// }
		$filename = [];
		if(isset($file) && !empty($file) && $file != '') {
			if(!is_dir(FCPATH . "assets/uploads/capa_plan/")){
				$oldmask = umask(0);
				mkdir(FCPATH . "assets/uploads/capa_plan/",0777);
				umask($oldmask);
			}
		}

		foreach($isi_capa as $i => $v) {
			$data['id'] = $id_capa[$i];
			$data['isi_capa'] = $v ;
			$data['dateline_capa'] = $dateline_capa[$i];
			$data['pic_capa'] = $pic_capa[$i];
			$data['id_status_capa'] = 1;
			
			if($file[$i]) {						
				if(@copy($file[$i], FCPATH . 'assets/uploads/capa_plan/'.basename($file[$i]))) {
					$filename[$i]	= basename($file[$i]);
				}
			}
			$data['evidence'] = isset($filename[$i]) ? $filename[$i] : '';

			$response_capa = save_data('tbl_capa',$data,post(':validation'));

			if($response_capa['status'] == 'success') {
				update_data('tbl_finding_records',['status_finding'=>1],['id'=>$data['id_finding']]);
			};
			
			if($id_capa[$i] != 0) 
			delete_data('tbl_capa',['nomor not' =>$nomor, 'id_finding' =>$data['id_finding'], 'nomor !=' => '']);
		
			$data_mytask = get_data('tbl_mytask', [
				'where' => [
					'id_user'		=> $pic_capa[$i],
					'type'			=> 'capa',
					'id_transaction'=> $response_capa['id'],
				]
			])->row_array();
			$sub_aktivitas = get_data('tbl_sub_aktivitas a',[
				'join' => [
					'tbl_finding_records b on a.id = b.id_sub_aktivitas type LEFT',
				],
				'where' => [
					'b.id' => $data['id_finding'],
				]
			])->row_array();

			$user = get_data('tbl_user','kode',$pic_capa[$i])->row_array();
			$mytask = [
				'id'			=> $data_mytask['id'] ?? 0,
				'id_user'		=> $user['id'],
				'type'			=> 'capa',
				'id_transaction'=> $response_capa['id'],
				'title'			=> 'CAPA Plan Telah Diinput',
                'description' 	=> "Terdapat CAPA yang perlu diperbarui progressnya pada audit area " . $sub_aktivitas['sub_aktivitas'],
				'status'		=> 'pending',
			];
			save_data('tbl_mytask',$mytask);
		}
		
		if($response_capa['status'] == 'success') {
			/// kirim email dan notifikasi
			$usr 	= get_data('tbl_finding_records a',[
				'select' => 'a.id, a.auditor, b.nama, a.id_section_department, b.email, c.id as id_user',
				'join' => ['tbl_m_auditor b on a.auditor = b.id type LEFT',
						   'tbl_user c on b.nip = c.username type LEFT'
			],
				'where'  => [
					'a.id' => $data['id_finding'],
				],
			])->row();


			$cc_user 			= get_data('tbl_user','id_group',[41,40])->result();
			$cc_email1 = [];
			foreach($cc_user as $u) {
				if($u->email != $usr->email) $cc_email1[] 	= $u->email;
			}

			$cc = get_data('tbl_detail_auditee a',[
				'select' => 'a.nip, b.email',
				'join'	 => 'tbl_user b on a.nip = b.username',
				'where' => [
					'a.id_section'=>$usr->id_section_department
				],
			])->result();

			$cc_email2 = [];
			foreach($cc as $c) {
				if($c->email != $usr->email) $cc_email2[] 	= $c->email;
			}

			$cc_email = array_merge($cc_email1, $cc_email2);


			if(isset($usr->id)) {
				$section = get_data('tbl_m_audit_section','id',$usr->id_section_department)->row();

				$link				= base_url().'internal/capa_monitoring';
				$desctiption 		= 'Departemen ' .$section->section_name . ' telah mengisi rencana perbaikan (CAPA Plan) pada sistem Audit Management System ';		
				$data_notifikasi 	= [
						'title'			=> 'CAPA Plan Telah Diinput',
						'description'	=> $desctiption,
						'notif_link'	=> $link,
						'notif_date'	=> date('Y-m-d H:i:s'),
						'notif_type'	=> 'info',
						'notif_icon'	=> 'fa-exchange-alt',
						'id_user'		=> $usr->id_user,
						'transaksi'		=> 'finding_records',
						'id_transaksi'	=> post('id')
					];
					insert_data('tbl_notifikasi',$data_notifikasi);	

				if(setting('email_notification')) {
					send_mail([
						'subject'		=> 'CAPA Plan Telah Diinput',
						'to'			=> $usr->email,
						'cc'			=> $cc_email,
						'nama_user'		=> $usr->nama,
						'description'	=> $desctiption ,
						'description2'	=> 'Untuk mengetahui lebih lanjut silakan cek di link berikut :',
						'detail' => 	get_data('tbl_finding_records a',[
										'select' => 'a.*, b.section_name, c.nama as nama_auditee',
										'join'	=> ['tbl_m_audit_section b on a.id_section_department = b.id type LEFT',
												    'tbl_auditee c on a.auditee = c.id type LEFT',
													],
										'where' => [
											'a.id' =>$data['id_finding'],
										],
										])->row_array(),
						'url'			=> $link
					]);
				}
			}
			///
		}

		render($response_capa,'json');
	}
	function delete() {
		$response = destroy_data('tbl_finding_records','id',post('id'));
		if($response['status'] == 'success') {
			$m_finding = get_data('tbl_finding_records','id',post('id'))->row();
			if($m_finding) destroy_data('tbl_m_finding','id',$m_finding->id_m_finding);
		}
		render($response,'json');
	}

	function template() {
		ini_set('memory_limit', '-1');
		$arr = ['institusi_audit' => 'institusi_audit','auditor' => 'auditor','nama_auditor' => 'nama_auditor','tgl_mulai_audit' => 'tgl_mulai_audit','tgl_akhir_audit' => 'tgl_akhir_audit','tgl_closing_meeting' => 'tgl_closing_meeting','site_auditee' => 'site_auditee','department_auditee' => 'department_auditee','audit_area' => 'audit_area','finding' => 'finding','bobot_finding' => 'bobot_finding','status_finding' => 'status_finding','capa' => 'capa','status_capa' => 'status_capa','follow_up' => 'follow_up','capa_score' => 'capa_score','achivement' => 'achivement','is_active' => 'is_active'];
		$config[] = [
			'title' => 'template_import_finding_records',
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function import() {
		ini_set('memory_limit', '-1');
		$file = post('fileimport');
		$col = ['institusi_audit','auditor','nama_auditor','tgl_mulai_audit','tgl_akhir_audit','tgl_closing_meeting','site_auditee','department_auditee','audit_area','finding','bobot_finding','status_finding','capa','status_capa','follow_up','capa_score','achivement','is_active'];
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
					$save = insert_data('tbl_finding_records',$data);
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
		$arr = ['institusi_audit' => 'Institusi Audit','auditor' => 'Auditor','nama_auditor' => 'Nama Auditor','tgl_mulai_audit' => '-dTgl Mulai Audit','tgl_akhir_audit' => '-dTgl Akhir Audit','tgl_closing_meeting' => '-dTgl Closing Meeting','site_auditee' => 'Site Auditee','department_auditee' => 'Department Auditee','audit_area' => 'Audit Area','finding' => 'Finding','bobot_finding' => 'Bobot Finding','status_finding' => 'Status Finding','capa' => 'Capa','status_capa' => 'Status Capa','follow_up' => 'Follow Up','capa_score' => 'Capa Score','achivement' => 'Achivement','is_active' => 'Aktif'];
		$data = get_data('tbl_finding_records')->result_array();
		$config = [
			'title' => 'data_finding_records',
			'data' => $data,
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

}