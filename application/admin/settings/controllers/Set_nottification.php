<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Set_nottification extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		render();
	}

	function data() {
		$config['button'][]	= button_serverside('btn-success','btn-email',['fa-envelope',lang('email'),true],'act-mail');
		$data = data_serverside($config);
		render($data,'json');
	}

	function get_data() {
		$data = get_data('tbl_capa_nottification','id',post('id'))->row_array();
		render($data,'json');
	}

	function save() {
		$response = save_data('tbl_capa_nottification',post(),post(':validation'));
		render($response,'json');
	}

	function delete() {
		$response = destroy_data('tbl_capa_nottification','id',post('id'));
		render($response,'json');
	}

	function template() {
		ini_set('memory_limit', '-1');
		$arr = ['number_reminder' => 'number_reminder','nottification' => 'nottification','is_active' => 'is_active'];
		$config[] = [
			'title' => 'template_import_set_nottification',
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function import() {
		ini_set('memory_limit', '-1');
		$file = post('fileimport');
		$col = ['number_reminder','nottification','is_active'];
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
					$save = insert_data('tbl_capa_nottification',$data);
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
		$arr = ['number_reminder' => 'Number Reminder','nottification' => 'Nottification','is_active' => 'Aktif'];
		$data = get_data('tbl_capa_nottification')->result_array();
		$config = [
			'title' => 'data_set_nottification',
			'data' => $data,
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function capa_nottification() {
		$notif = get_data('tbl_capa_nottification',[
			'where' => [
				'id' => post('id'),
				'is_active' => 1,
			],
			])->row();

			$days = $notif->days_nottification ;
            switch ($days) {
                case -30:
                    $days_notif = "akan mencapai due date (30 hari lagi)";
                    break;
                case 0:
                    $days_notif = "akan telah mencapai due date";
                    break;
                case 30:
                    $days_notif = "akan telah melewati due date (selama 30 hari)";
                    break;
                case 60:
                    $days_notif = "akan telah melewati due date (selama 60 hari)";
                    break;
                case 90:
                    $days_notif = "akan telah melewati due date (selama 90 hari)";
                    break;
                case 120:
                    $days_notif = "akan telah melewati due date (selama 120 hari)";
                    break;
                default:
                    echo "Nilai tidak dikenali";
            }

		$cek_capa = get_data('tbl_capa a',[
			'select' => 'a.*,b.nama, b.email, c.id_section_department',
			'join'   => ['tbl_user b on a.pic_capa = b.username type LEFT',
						'tbl_finding_records c on a.id_finding = c.id',
						],
			'where' => [
				'a.id_status_capa !=' => 9,
				'__m' => 'a.dateline_capa <= CURDATE() - INTERVAL '.$notif->days_nottification.' DAY',
				// 'nomor' => '0007/CAPA.02/2025',
			],
		])->result();

		if(empty($cek_capa)) {
			$response = [
				'status' => 'success',
				'message' => 'tidak ada capa yang due date',
			];
			render($response,'json');
		}else{
			foreach($cek_capa as $c) {		
				
				$cc_user 			= get_data('tbl_user','id_group',[41,40])->result();
				$cc_email1 = [];
				foreach($cc_user as $u) {
					$cc_email1[] 	= $u->email;
				}

				$cc = get_data('tbl_detail_auditee a',[
					'select' => 'a.nip, b.email',
					'join'	 => 'tbl_user b on a.nip = b.username',
					'where' => [
						'a.id_section'=>$c->id_section_department
					],
				])->result();

				$cc_email2 = [];
				foreach($cc as $c1) {
					$cc_email2[] 	= $c1->email;
				}

				$cc_email = array_merge($cc_email1, $cc_email2);

				$message  = '<p style="text-align: justify;">Yth. Bapak/Ibu '.$c->nama.',</p>';
				$message .= '<p style="text-align: justify;">Pengingat: CAPA Plan yang telah Anda input di sistem Audit Management System  : '.$days_notif.'</p>';
				$message .= '<ul>
								<li><strong>Capa:</strong>'.$c->isi_capa.'</li>
								<li><strong>Due Date:</strong>'.date_indo($c->dateline_capa).'</li>
								<li><strong>PIC:</strong>'.$c->nama.'</li>
							</ul>';
				
				$message .= '<p style="text-align: justify;">Mohon pastikan pelaksanaan CAPA sesuai rencana dan dokumen bukti pelaksanaannya diunggah tepat waktu.';
				$message .= '<p style="text-align: justify;">Cek dan update progres Anda di:';
				$message .= '<br>
							 <a href="https://development.otsuka.co.id/internal-audit" target="_blank" style="background: #16D39A; color: #fff; padding: .5rem 1rem; border-radius: .175rem; text-decoration: none;">Audit Management System</a>
							</p>';


				$data = array(
					'subject'	=> 'CAPA Plan ' . $days_notif,
					'description' => $message,
					// 'to' => 'dsuherdi@ho.otsuka.co.id',
					'to'		=> $c->email, 
					'cc'		=> $cc_email,
				);

				$response = send_mail($data);
				render($response,'json');
			}
		
		}
	}

}