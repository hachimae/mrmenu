<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dash extends APP_Controller 
{
	
	function __construct()
	{
		parent::__construct();
	}

	function index()
	{
		$this->render();
	}

}