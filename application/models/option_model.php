<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Option_model extends APP_Model
{
	var $table = 'ci_option_group',
		$table_meta = 'ci_option_meta',
		$column = array(
			'id' => 'id',
			'name' => 'name_th',
			'name_en' => 'name_en',
			'option_type' => 'option_type',
			'modified_date' => 'modified_date',
			),
		$perpage = 3;

    function __construct()
    {
        // Call the Model constructor
        parent::__construct($this->table, $this->field);
    }

    function updateMeta($group_id, $meta_data)
    {
    	$this->db->delete($this->table_meta, array(
    		'group_id' => $group_id
	    	));

    	foreach($meta_data as $row){
    		$row['group_id'] = $group_id;
    		$this->db->insert($this->table_meta, $row);
    	}
    }

    function getMeta($group_id)
    {
    	$this->db
    		 ->select()
    		 ->where('group_id = ', $group_id);
    	$query = $this->db->get( $this->table_meta );

    	if( $query->num_rows()<=0 ){
    		return false;
    	}else{
    		foreach($query->result_array() as $row){
    			$data[] = array(
    				'id' => $row['id'],
    				'name' => $row['name'],
    				'type' => $row['price_type'],
    				'value' => $row['price_value']
	    			);
    		}
    		return $data;
    	}
    }

    function getOption($menu_id)
    {
    	$this->db
    		 ->select('id, name, option_type, modified_date')
             ->where('menu_id = ', $menu_id)
             ->where('item_status = ', 'published');
    	$query = $this->db->get( $this->table );
    	
    	if( $query->num_rows()<=0 ){
    		return false;
    	}else{
    		foreach($query->result_array() as $row){
    			$data[] = $row;
    		}
    		return $data;
    	}
    }

    function allOption()
    {
        if( !isset($this->shopId) ){
            return false;
        }

        $this->db
             ->select('id, name, option_type')
             ->where('shop_id = ', $this->shopId)
             ->where('item_status = ', 'published');
        $query = $this->db->get( $this->table );

        if( $query->num_rows()<=0 ){
            return false;
        }else{
            foreach($query->result_array() as $row){
                $data[$row['id']] = $row['name'].' ('.$row['option_type'].')';
            }
            return $data;
        }
    }

}

?>