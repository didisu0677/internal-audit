<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cron extends MY_Controller {
    
    function backup($tipe = 'all') {
		ini_set('memory_limit', '-1');

        if(in_array($tipe, ['all','db'])) {
            $backupdir = FCPATH . 'assets/backup/backup_'.date('Y_m_d_h_i');
            if(!is_dir($backupdir)) mkdir($backupdir, 0777, true);
            
            $table = db_list_table();
            $this->load->dbutil();
            $this->load->helper('file');
            foreach($table as $t) {
                $prefs = array(
                    'tables'      => array($t),
                    'format'      => 'sql',
                    'filename'    => $t.'.sql'
                );
                $backup		= $this->dbutil->backup($prefs);
                $db_name 	= $t.'.sql';
                $save 		= $backupdir.'/'.$db_name;
                write_file($save, $backup);
            }
        }
        if(in_array($tipe, ['all','file'])) {
            $conf       = [
                'src'       => FCPATH . 'assets/uploads/',
                'dst'       => FCPATH . 'assets/backup/',
                'filename'  => 'backup_file_'.date('Y_m_d_h_i')
            ];
            $this->load->library('Rzip',$conf);
            $this->rzip->compress();
        }
    }

    function capa_nottification() {
		$notif = get_data('tbl_capa_nottification',[
			'where' => [
				'is_active' => 1,
			],
			])->result();
        
        foreach($notif as $n) {

            $days = $n->days_nottification ;
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
                    '__m' => 'a.dateline_capa = CURDATE() - INTERVAL '.$n->days_nottification.' DAY',
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
                        // 'to'		=> 'dsuherdi@ho.otsuka.co.id',
                        'description' => $message,
                        'to'		=> $c->email, 
                        'cc'		=> $cc_email,
                    );

                    $response = send_mail($data);
                    render($response,'json');
                }
		    }
        }
	}

    function sync_employee_auditee()
    {
        set_time_limit(-1);
        ini_set('memory_limit', '-1');
        $apiDB = $this->load->database('api', TRUE);

        // Ambil hanya golongan tertentu
        $data_api = $apiDB
            ->where_not_in('golongan', ['02', '03', '04', '06', '06F', '07', '07F', 'FACT10', 'FACT20'])
            ->get('raw_employee')
            ->result_array();

        if (empty($data_api)) {
            return;
        }
       
        $count = 0;
        foreach ($data_api as $v) {
            $user = get_data('tbl_user', 'kode', $v['nip'])->row_array();

            // ==========================
            // CASE: USER SUDAH ADA
            // ==========================
            if ($user) {
                if (!empty($v['tanggal_keluar'])) {
                    update_data('tbl_user', [
                        'is_active' => 0
                    ], 'kode', $v['nip']);
                }
            }

            if (!empty($v['tanggal_keluar'])) {
                continue; // jangan insert kalau sudah keluar
            }
            
            $insert = [
                'id'        => $user['id'] ?? null,
                'id_group'  => 38,
                'kode'      => $v['nip'],
                'nama'      => $v['nama'] ?? '',
                'username'  => $v['nip'],
                'password'  => password_hash('p455w0rd', PASSWORD_DEFAULT),
                'email'     => $v['email'] ?? null,
                'is_active' => 1
            ];

            $id_user = save_data('tbl_user', $insert);
            $cek = get_data('tbl_auditee', 'id_user', $id_user['id'])->row_array();
            $auditee = [
                    'id'        => $cek['id'] ?? null,
                    'id_user'   => $id_user['id'],
                    'nip'       => $v['nip'],
                    'nama'      => $v['nama'],
                    'email'     => $v['email'] ?? null,
                    'is_active' => 1
                ];
            save_data('tbl_auditee', $auditee);
            $count++;              
            
        }
        echo "Total new auditee added: " . $count;
    }
}