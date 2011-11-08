<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

	var $content_template = 'default.php',
		$master_template = 'default.php',
		$data = array(
			'header' => false,
			'title' => 'Mr.Menu : Dashboard',
			'navigator' => false,
			'content' => false,
			'footer' => false,
			'scripts' => false,
			'user' => false,
			);

	function __construct()
	{
		parent::__construct();

		// load default 
		// ------------------------------------
		// helper
		$this->load->helper(array('form', 'url'));
		// library
		$this->load->library('tank_auth', false, 'auth');
		$this->load->library(array('template', 'form_validation', 'security', 'pagination', 'session'));

		// model
	}

	function setContent($key = false, $val = false)
	{
		if( $key==false ) return false;

		if( !empty($this->data['content'][$key]) ){
			$this->data['content'][$key] = array_merge( $this->data['content'][$key], $val );
		}else{
			$this->data['content'][$key] = $val;
			
		}
	}

	function render()
	{
		if( $this->master_template=='default.php' ){
			// header
			$this->template->write_view('header', 'layout/header', $this->data['header']);

			// title
			$this->template->write('title', $this->data['title']);

			// navigator
			$this->template->write_view('navigator', 'layout/navigator', $this->data['navigator']);

			// content
			$this->template->write_view('content', $this->content_template, $this->data['content']);

			// footer
			$this->template->write_view('footer', 'layout/footer', $this->data['footer']);
		}else{
			// $this->content_template = 'admin/list';
			$this->template->set_master_template($this->master_template);
			$this->template->write_view('content', $this->content_template, $this->data['content']);
		}
		
		$this->template->render();
	}

}

?>