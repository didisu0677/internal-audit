<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tools extends BE_Controller
{

    function __construct()
    {
        parent::__construct();
    }

    function generate_auditee()
    {
        $data = get_data('tbl_auditee_copy', 'is_active', 1)->result_array();
        foreach ($data as $v) {
            $user_id = $this->get_user_id_by_kode($v['nip']);
            if ($user_id) {
                $check = get_data('tbl_auditee', 'nip', $v['nip'])->row_array();

                $insert = [
                    'id_user' => $user_id,
                ];
                $res = update_data('tbl_auditee', $insert, 'id', $check['id'] ?? 0);
                if ($res) {
                    echo "User dengan kode " . $v['nip'] . " berhasil disimpan<br>";
                } else {
                    echo "User dengan kode " . $v['nip'] . " gagal disimpan<br>";
                }
            } else {
                echo "User dengan kode " . $v['nip'] . " tidak ditemukan<br>";
            }
        }
    }

    function get_user_id_by_kode($kode)
    {
        $user = get_data('tbl_user', 'kode', $kode)->row_array();
        return $user['id'] ?? 0;
    }

    function generate_mytask()
    {
        $kuisioner = get_data('tbl_kuisioner_respon r', [
            'select' => 'r.*, u.*',
            'join' => [
                'tbl_auditee a on r.id_auditee = a.id',
                'tbl_user u on a.nip = u.kode'
            ],
        ])->result_array();

        foreach ($kuisioner as $v) {
            $check = get_data('tbl_mytask', [
                'where' => [
                    'id_user' => $v['id_user'] ?? 0,
                    'type' => 'questioner',
                    'id_transaction' => $v['id'] ?? 0,
                ]
            ])->row_array();

            if (!$check) {
                $insert = [
                    'id_user' => $v['id_user'] ?? 0,
                    'type' => 'questioner',
                    'id_transaction' => $v['id'] ?? 0,
                    'title' => 'Pengisian Kuisioner Setelah Audit',
                    'description' => 'Pengisian kuisioner ini bertujuan untuk memperoleh umpan balik setelah pelaksanaan audit.',
                    'status' => 'pending'
                ];
                $res = insert_data('tbl_mytask', $insert);
                if ($res) {
                    echo "Mytask untuk kuisioner dengan ID " . $v['id'] . " berhasil disimpan<br>";
                } else {
                    echo "Mytask untuk kuisioner dengan ID " . $v['id'] . " gagal disimpan<br>";
                }
            } else {
                echo "Mytask untuk kuisioner dengan ID " . $v['id'] . " sudah ada<br>";
            }
        }
        
        $data = get_data('tbl_finding_records a', [
            'join' => [
                'tbl_sub_aktivitas b on a.id_sub_aktivitas = b.id'
            ],
            'where' => [
                'status_finding !=' => 2,
            ]
        ])->result_array();
        foreach ($data as $v) {
            if ($v['status_finding'] == '0') {
                $auditee = get_data('tbl_auditee', 'id', $v['auditee'])->row_array();

                $check = get_data('tbl_mytask', [
                    'where' => [
                        'id_user' => $auditee['id_user'] ?? 0,
                        'type' => 'finding',
                        'id_transaction' => $v['id'],
                    ]
                ])->row_array();

                if (!$check) {
                    $insert = [
                        'id_user' => $auditee['id_user'] ?? 0,
                        'type' => 'finding',
                        'id_transaction' => $v['id'],
                        'title' => 'Finding Internal Audit',
                        'description' => "Terdapat finding yang harus ditindaklanjuti pada audit area " . $v['sub_aktivitas'],
                        'status' => 'pending'
                    ];
                    $res = insert_data('tbl_mytask', $insert);
                    if ($res) {
                        echo "Mytask untuk finding dengan ID " . $v['id'] . " berhasil disimpan<br>";
                    } else {
                        echo "Mytask untuk finding dengan ID " . $v['id'] . " gagal disimpan<br>";
                    }
                } else {
                    echo "Mytask untuk finding dengan ID " . $v['id'] . " sudah ada<br>";
                }
            }else if($v['status_finding'] == '1'){
                $capa = get_data('tbl_capa', 'id_finding', $v['id'])->row_array();
                $auditee = get_data('tbl_auditee', 'id', $v['auditee'])->row_array();
                
                $check = get_data('tbl_mytask', [
                    'where' => [
                        'id_user' => $auditee['id_user'] ?? 0,
                        'type' => 'capa',
                        'id_transaction' => $capa['id'] ?? 0,
                    ]
                ])->row_array();

                if (!$check) {
                    if(empty($capa)){
                        echo "Tidak ada CAPA untuk finding dengan ID " . $v['id'] . "<br>";
                        continue;
                    }
                    $insert = [
                        'id_user' => $auditee['id_user'] ?? 0,
                        'type' => 'capa',
                        'id_transaction' => $capa['id'] ?? 0,
                        'title' => 'CAPA Progress Telah Diperbarui',
                        'description' => "Terdapat CAPA yang perlu diperbarui progressnya pada audit area " . $v['sub_aktivitas'],
                        'status' => 'pending'
                    ];
                    $res = insert_data('tbl_mytask', $insert);
                    if ($res) {
                        echo "Mytask untuk CAPA berhasil disimpan<br>";
                    } else {
                        echo "Mytask untuk CAPA gagal disimpan<br>";
                    }
                } else {
                    echo "Mytask untuk CAPA sudah ada<br>";
                }
            }
        }
    }

   function synchronize_kuisioner(){
    $this->load->library('simpleexcel');
    
    // Gunakan path fisik, bukan URL
    $file = FCPATH . 'assets/Kuesioner.xlsx';
    
    if (!file_exists($file)) {
        echo "File tidak ditemukan di server: " . $file;
        return;
    }
	$col = ['timestamp', 'email', 'department', 'auditee', 'q1', 'q2', 'q3', 'q4', 'q5', 'q6', 'q7', 'q8', 'q9', 'q10', 'komentar'];
	$this->simpleexcel->define_column($col);
    $jml = $this->simpleexcel->read($file);
    $c = 0;
    foreach($jml as $i => $k) {
        if($i==0) {
            for($j = 2; $j <= $k; $j++) {
                $data = $this->simpleexcel->parsing($i,$j);
                $data_insert = [
                    'id_auditee' => $this->get_auditee_id_by_email($data['email']),
                    'periode_audit' => $data['timestamp'],
                    'respon' => json_encode([$data['q1'], $data['q2'], $data['q3'], $data['q4'], $data['q5'], $data['q6'], $data['q7'], $data['q8'], $data['q9'], $data['q10']]),
                    'komentar' => $data['komentar'],
                    'status' => 1,
                    'submitted_at' => date('Y-m-d H:i:s')
                ];
                $res = insert_data('tbl_kuisioner_respon', $data_insert);
                if($res) {
                    $c++;
                    echo "Data ke-".$c." berhasil disimpan.<br>";
                } else {
                    echo "Data ke-".$c." gagal disimpan.<br>";  
                }
            }
        }
    }
}

function get_auditee_id_by_email($email)
{
    $user = get_data('tbl_user', 'email', $email)->row_array();
    if ($user) {
        $auditee = get_data('tbl_auditee', 'id_user', $user['id'])->row_array();
        return $auditee['id'] ?? 0;
    }
    return 0;
}
}