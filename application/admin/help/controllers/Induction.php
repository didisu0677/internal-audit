<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Induction extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		$data['data'] = get_data('tbl_help', [
			'where' => [
				'is_active' => 1
			]
		])->result_array();
		
		render($data);
	}

}