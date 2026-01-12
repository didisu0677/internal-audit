<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Help_section extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
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
				$target = implode('/', array_filter([
					$value['target1'],
					// $value['target2'],
					$value['target3'],
					$value['target4']
				]));

				$data['type'][$key]['value'] = $target;

				$data['type'][$key]['nama'] = $value['nama'];
				$data['type'][$key]['urutan'] = $value['urutan'];
				$data['type'][$key]['key'] = $target;
				// $data['type'][$key]['key'] = $value['id'];
			}else{
				$target = implode('/', array_filter([
					$value['target1'],
					$value['target2'],
					// $value['target3'],
					// $value['target4']
				]));

				$data['type'][$key]['value'] = $target;

				$data['type'][$key]['nama'] = $value['nama'];
				$data['type'][$key]['urutan'] = $value['urutan'];
				$data['type'][$key]['key'] = $target;
				// $data['type'][$key]['key'] = $value['id'];
			};
		}

		render($data);
	}

	function data() {
		$data = data_serverside();
		render($data,'json');
	}

	function get_data() {
		$data = get_data('tbl_help','id',post('id'))->row_array();
		render($data,'json');
	}

	function save() {
		$data = post();

		// $tempPath = $data['file_attachment'];
    
		// $destinationDir = 'assets/uploads/help/';
		// $fileName = basename($tempPath);

		// $newPath = $destinationDir . $fileName;

		// if (!file_exists($destinationDir)) {
		// 	mkdir($destinationDir, 0777, true); // create recursively
		// }

		// if (file_exists($tempPath)) {
		// 	rename($tempPath, $newPath);
		// 	$data['file_attachment'] = $newPath; // update path in data if needed
		// } else {
		// 	$response = [
		// 		'message' => "File not found: $tempPath",
		// 		'status' => 'error'
		// 	];

		// 	render($response,'json');
		// 	return;
		// }

		$response = save_data('tbl_help',$data,post(':validation'));
		render($response,'json');
	}

	function delete() {
		// $response = destroy_data('tbl_help','id',post('id'));
		$response = destroy_data('tbl_help','id',post('id'));
		render($response,'json');
	}
}