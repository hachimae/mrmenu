<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ADMIN_Controller extends MY_Controller {

	var $template = 'default.php',
		$data = array();

	function __construct()
	{
		parent::__construct();

		// load default 
		// ------------------------------------
		// helper

		// library

		// model
	}

	function load_data($table, $shop_id)
	{
		// $this->data['list']['data'] = $this->model->load( $table, $this->data['list']['column'], $this->data['pagination']['current'] );
		$this->data['list']['data'] = $table->fetch( $shop_id, $this->data['pagination']['current'] );
	}

}

?>