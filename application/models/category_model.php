<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Category_model extends APP_Model
{
	var $table = 'ci_category',
		$column = array(
			'id' => 'id',
			'parent_id' => 'parent_id',
			'name' => 'name',
			'description' => 'description',
			'modified_date' => 'modified_date',
			),
		$perpage = 3,
		$type = 'all';

    function __construct()
    {
        // Call the Model constructor
        parent::__construct($this->table, $this->field);
    }

    function listCategory($shopId = false)
    {
    	$shopId = empty($shopId) ? $this->shopId : $shopId;
		
    	$this->db
    		 ->select('id, parent_id, name')
    		 ->where('item_status = ', 'published')
    		 ->where('shop_id = ', $shopId)
    		 ->order_by('level_path ASC');

    	if( $this->type=='parent' ){
    		$this->db->where('parent_id = ', '0');
    	}

    	$query = $this->db->get( $this->table );
    	if( $query->num_rows()<=0 ){
    		return false;
    	}

    	foreach($query->result_array() as $row){
    		if( !empty($row['parent_id']) ){
    			$data[$row['id']] = '&ndash; '.$row['name'];
    		}else{
    			$data[$row['id']] = $row['name'];
    		}
    	}
    		
    	return $data;
    }

    function update_levelPath($category_id, $parent_id)
    {
    	if( empty($parent_id) ){
    		$this->db
    			 ->where('id = ', $category_id)
    			 ->update( $this->table, array('level_path'=>'/'.$category_id.'/') );
    		return false;
    	}else{
    		$this->db
    			 ->select('level_path')
    			 ->where('id = ', $parent_id);
    		$query = $this->db->get( $this->table );
    		if( $query->num_rows()<=0 ){
    			return false;
    		}

    		$result = $query->row_array();
    		$this->db
    			 ->where('id = ', $category_id)
    			 ->update( $this->table, array('level_path'=>$result['level_path'].'/'.$category_id.'/') );
    		return false;
    	}
    }

}

?>