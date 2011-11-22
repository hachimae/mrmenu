<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends MY_Controller
{
	var $template = 'index.html',
		$data = array();
		
	function __construct()
	{
		parent::__construct();
	}

	function index()
	{

		$this->load->view('index');
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */