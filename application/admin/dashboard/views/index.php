<?php
    if(user('id_group') == AUDITEE){
        $this->load->view('dashboard_auditee.php');
    }else{
        $this->load->view('dashboard_auditor.php');    
    }
?>
