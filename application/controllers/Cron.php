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
            $cek_capa = get_data('tbl_capa a',[
                'select' => 'a.*,b.email, c.id_section_department',
                'join'   => ['tbl_user b on a.pic_capa = b.username type LEFT',
                            'tbl_finding_records c on a.id_finding = c.id',
                            ],

                'where' => [
                    'a.id_status_capa !=' => 9,
                    '__m' => 'a.dateline_capa = CURDATE() - INTERVAL '.$n->days_nottification.' DAY',
                    // 'nomor' => '0003/CAPA.12/2024',
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
                            'a.id_section'=>$c->id_section
                        ],
                    ])->result();

                    $cc_email2 = [];
                    foreach($cc as $c) {
                        $cc_email2[] 	= $c->email;
                    }

                    $cc_email = array_merge($cc_email1, $cc_email2);
                    
                    $data = array(
                        'subject'	=> 'CAPA Plan Department Anda ',
                        'message'	=> $n->nottification . ' ' . $c->isi_capa,
                        'to'		=> $c->email, //'dsuherdi@ho.otsuka.co.id',
                        'cc'		=> $cc_email,
                    );

                    $response = send_mail($data);
                    render($response,'json');
                }
		    }
        }
	}
}