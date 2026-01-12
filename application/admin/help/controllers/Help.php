<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Help extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

    public function _remap($method, $params = []) {
        $segments = array_merge([$method], $params);

        $target_path = 'help/'.implode('/', $segments);
		
        // // Example: Load your DB model and get the matching help item
        $help_item = $this->get_help_item($target_path);

        if ($help_item) {
			// If the help item exists, load the view
			$data_help = get_data('tbl_help', [
				'where' => ['type' => $target_path]
			])->result_array();

			$data = [
				'title' => 'Help Section',
				'help_item' => $help_item,
				'target_path' => $target_path,
				'data' => $data_help,
			];

			render($data,'view:help/index');
        } else {
            show_404();
        }
    }

    private function get_help_item($target_path) {
        // $target_keys = ['target1', 'target2', 'target3', 'target4'];
        $all_data = $this->help_data();

		foreach ($all_data as $key => $value) {
			if($value['value'] == $target_path) {
				return $all_data[$key];
			}
		}
		
		return null;
    }

    private function help_data() {
        $help = get_data('tbl_menu','target = "help"')->row_array();

		$menu = get_data(
			'tbl_menu', [
				'select' => 'tbl_menu.id, tbl_menu.nama, tbl_menu.urutan, lv1.target as target1, lv2.target as target2, lv3.target as target3, lv4.target as target4',
				'where' => [
					'tbl_menu.is_active' => 1,
					'tbl_menu.level1' => $help['id'],
					'tbl_menu.level2 !=' => 0,
				],
				'join' => [
					'tbl_menu as lv1 ON lv1.id = tbl_menu.level1 TYPE left',
					'tbl_menu as lv2 ON lv2.id = tbl_menu.level2 TYPE left',
					'tbl_menu as lv3 ON lv3.id = tbl_menu.level3 TYPE left',
					'tbl_menu as lv4 ON lv4.id = tbl_menu.level4 TYPE left',
				],
				'sort_by' => 'tbl_menu.urutan',
				'sort' => 'ASC'
			]
		)->result_array();

		foreach ($menu as $key => $value) {
			if($value['target3']) {
				$data[$key]['value'] = implode('/', array_filter([
					$value['target1'],
					// $value['target2'],
					$value['target3'],
					$value['target4']
				]));

				$data[$key]['nama'] = $value['nama'];
				$data[$key]['urutan'] = $value['urutan'];
				$data[$key]['key'] = $value['id'];		
			}else{
				$data[$key]['value'] = implode('/', array_filter([
					$value['target1'],
					$value['target2'],
					// $value['target3'],
					// $value['target4']
				]));

				$data[$key]['nama'] = $value['nama'];
				$data[$key]['urutan'] = $value['urutan'];
				$data[$key]['key'] = $value['id'];	
			};
		}

		return $data;
    }
}
