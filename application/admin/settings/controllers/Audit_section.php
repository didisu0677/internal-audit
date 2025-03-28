<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Audit_section extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
        $data['group_section'] = get_data('tbl_group_section','is_active',1)->result_array();
		render($data);
	}

	function sortable() {
		render();
	}

	function data($tipe = 'table') {
		$audit_section = menu();

        // debug($audit_section);die;
		if($audit_section['access_view']) {
			$data['audit_section'][0] = get_data('tbl_m_audit_section',array('where_array'=>array('parent_id'=>0),'sort_by'=>'urutan'))->result();
			foreach($data['audit_section'][0] as $m0) {
				$data['audit_section'][$m0->id] = get_data('tbl_m_audit_section',array('where_array'=>array('parent_id'=>$m0->id),'sort_by'=>'urutan'))->result();
				foreach($data['audit_section'][$m0->id] as $m1) {
					$data['audit_section'][$m1->id] = get_data('tbl_m_audit_section',array('where_array'=>array('parent_id'=>$m1->id),'sort_by'=>'urutan'))->result();
					foreach($data['audit_section'][$m1->id] as $m2) {
						$data['audit_section'][$m2->id] = get_data('tbl_m_audit_section',array('where_array'=>array('parent_id'=>$m2->id),'sort_by'=>'urutan'))->result();
                        foreach($data['audit_section'][$m2->id] as $m3) {
                            $data['audit_section'][$m3->id] = get_data('tbl_m_audit_section',array('where_array'=>array('parent_id'=>$m3->id),'sort_by'=>'urutan'))->result();
                        }
					}
				}
			}
			if($tipe == 'sortable') {
				$response	= array(
					'content' => $this->load->view('settings/audit_section/sortable',$data,true)
				);
			} else {
				$data['access_edit']	= $audit_section['access_edit'];
				$data['access_delete']	= $audit_section['access_delete'];

				$response	= array(
					'table'		=> $this->load->view('settings/audit_section/table',$data,true),
					'option'	=> $this->load->view('settings/audit_section/option',$data,true)
				);
			}
		} else {
			$response	= array(
				'status'	=> 'error',
				'message'	=> 'Permission Denied'
			);
		}
		render($response,'json');
	}

	function get_data() {
		$data = get_data('tbl_m_audit_section','id',post('id'))->row_array();
		render($data,'json');
	}

	function save() {
		$data 		= post();
		$validation	= post(':validation');
        $group_section = get_data('tbl_group_section','id',$data['id_group_section'])->row();

        if(isset($group_section->group_section)) $data['group_section'] = $group_section->group_section;
		$response = save_data('tbl_m_audit_section',$data,$validation);
		if($response['status'] == 'success') {
	
			$mn = get_data('tbl_m_audit_section','id',$response['id'])->row_array();
			if($mn['parent_id'] == 0) {
				update_data('tbl_m_audit_section',array('level1'=>$mn['id']),'id',$mn['id']);
			} else {
				$parent = get_data('tbl_m_audit_section','id',$mn['parent_id'])->row_array();
				$data_update = array(
					'level1' => $parent['level1'],
					'level2' => $parent['level2'],
					'level3' => $parent['level3'],
					'level4' => $parent['level4'],
					'level5' => $parent['level5'],
				);
				if(!$parent['level2']) $data_update['level2'] = $mn['id'];
				else if(!$parent['level3']) $data_update['level3'] = $mn['id'];
				else if(!$parent['level4']) $data_update['level4'] = $mn['id'];
				else if(!$parent['level5']) $data_update['level5'] = $mn['id'];
				update_data('tbl_m_audit_section',$data_update,'id',$mn['id']);
			}
		}
		render($response,'json');
	}

	function delete() {
		$child	= array(
			'level1'	=> 'tbl_m_audit_section',
			'level2'	=> 'tbl_m_audit_section',
			'level3'	=> 'tbl_m_audit_section',
			'level4'	=> 'tbl_m_audit_section'
		);
		$response = destroy_data('tbl_m_audit_section','id',post('id'),$child);
		render($response,'json');
	}

	function update_level() {
		$level1 = get_data('tbl_m_audit_section',[
			'where' => [
				'is_active' => 1,
				'parent_id' => 0
			],
		])->result();
		foreach($level1 as $l1) {
			update_data('tbl_m_audit_section',['level1'=>$l1->id],['id'=>$l1->id]);

			$level2 = get_data('tbl_m_audit_section',[
				'where' => [
					'is_active' => 1,
					'parent_id' =>$l1->id
				]
			])->result();
			foreach($level2 as $l2){
				update_data('tbl_m_audit_section',['level1'=> $l1->id, 'level2'=>$l2->id],['id'=>$l2->id]);

				$level3 = get_data('tbl_m_audit_section',[
					'where' => [
						'is_active' => 1,
						'parent_id' => $l2->id
					]
				])->result();
				foreach($level3 as $l3) {
					update_data('tbl_m_audit_section',['level1'=> $l1->id, 'level2'=> $l2->id, 'level3'=>$l3->id],['id'=>$l3->id]);

					$level4 = get_data('tbl_m_audit_section',[
						'where' => [
							'is_active' => 1,
							'parent_id' => $l3->id
						]
					])->result();

					foreach($level4 as $l4) {
						update_data('tbl_m_audit_section',['level1'=> $l1->id, 'level2'=> $l2->id, 'level3' => $l3->id, 'level4'=>$l4->id],['id'=>$l4->id]);

						$level5 = get_data('tbl_m_audit_section',[
							'where' => [
								'is_active' => 1,
								'parent_id' => $l4->id
							]
						])->result();

						foreach($level5 as $l5) {
							update_data('tbl_m_audit_section',['level1'=> $l1->id, 'level2'=> $l2->id, 'level3' => $l3->id, 'level4' => $l4->id, 'level5'=>$l5->id],['id'=>$l5->id]);
						}

					}
				}
			}
		}

		echo 'update successed' ;
	}
}