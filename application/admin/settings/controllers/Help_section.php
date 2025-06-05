<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Help_section extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		// $data['help']   = get_data('tbl_help','is_active = 1')->result_array();
		$data['type'] = [
			[
				"key" => 'video_induction',
				"value" => 'Video',
			],
		];

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

		$tempPath = $data['file_attachment'];
    
		$destinationDir = 'assets/uploads/help/';
		$fileName = basename($tempPath);

		$newPath = $destinationDir . $fileName;

		if (!file_exists($destinationDir)) {
			mkdir($destinationDir, 0777, true); // create recursively
		}

		if (file_exists($tempPath)) {
			rename($tempPath, $newPath);
			$data['file_attachment'] = $newPath; // update path in data if needed
		} else {
			$response = [
				'message' => "File not found: $tempPath",
				'status' => 'error'
			];

			render($response,'json');
			return;
		}

		$response = save_data('tbl_help',$data,post(':validation'));
		render($response,'json');
	}

	function delete() {
		// $response = destroy_data('tbl_help','id',post('id'));
		$response = destroy_data('tbl_help','id',post('id'));
		render($response,'json');
	}
}