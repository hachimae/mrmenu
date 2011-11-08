<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Shop_Model extends App_Model
{
	var $table = 'ci_shop';
	
    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    function data($user_id)
    {
    	$this->db->where('owner_id = ', $user_id);
    	$this->db->select('id, name, detail, charge, vat, cess');
    	$query = $this->db->get($this->table);

    	if( $query->num_rows()>0 ){
    		return $query->row_array();
    	}else{
    		return false;
    	}
    }
}

?>