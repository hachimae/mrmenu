<?php

class Api extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
		
		// load rest library
		$this->load->library('rest', array(  
			'server' => 'http://kelson.mrmenu.dev/api/mrmenu',  
			'http_user' => 'admin',  
			'http_pass' => '1234',  
			'http_auth' => 'digest' // or 'digest'  
		)); 
	}
	
	function user_get($id = 1)
	{
		$user = $this->rest->get('user', array('id' => $id));
		var_dump( $user );
	}
	
	function user_post()
	{
		$user = $this->rest->post('user/id/1', array(
			'id' => '1'
		));
		
		var_dump( $user );
	}
	
	function getData()
	{
		$data = $this->rest->post('restaurant/id/1', array(
			'table_id' => '1',
			'action' => 'get_restaurant_data'
		));
		
		var_dump( $data );
	}
	
	function setOrder()
	{
		$data = $this->rest->post('restaurant/id/1', array(
			'table_id' => '1',
			'action' => 'set_order'
		));
		
		var_dump( $data );
	}

}