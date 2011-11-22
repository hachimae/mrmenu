<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

    function getMedia($id, $ref_table = 'ci_menu')
    {
    	$this->db->select('id,name,file,extension');
    	$this->db->where('ref_id = ', $id);
    	$this->db->where('ref_table = ', $ref_table);
    	$this->db->where('item_status = ', 'published');
    	$query = $this->db->get( 'ci_media' );

    	if( $query->num_rows()<=0 ){
    		return false;
    	}else{
    		$media = $query->row_array();		
    		//Replace image with thumbnail
    		$media['file'] = $media['name'].'_thumb'.$media['extension'];
    		return $media;
    		//return $query->row_array();
    	}
    }

}