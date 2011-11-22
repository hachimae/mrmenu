<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dash extends APP_Controller 
{
	
	function __construct()
	{
		parent::__construct();
	}

	function index()
	{
		$page = $this->input->get('page');
		$page = !empty($page) ? $page : 1;

		$this->setContent('title', 'Hello,   '.$this->data['user']['name']);



		$data = $this->data['user']['name'];
		print_r($data);
		//$this->_setListData(  );
		//$this->_setListPage( 'app/category?', $this->model->countAll(), 10);

		$this->_list($data);
	}

}