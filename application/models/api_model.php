<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api_model extends CI_Model
{
	var $table_member = 'ci_member',
        $table_restaurant = 'ci_shop',
        $table_category = 'ci_category',
        $table_menu = 'ci_menu',
        $table_order = 'ci_order';

    function __construct()
    {
        parent::__construct();
    }

    function checkMobile($mobileId = false)
    {
    	if( empty($mobileId) ){
    		return false;
    	}

    	$id = $this->_mobileExist($mobileId);
    	if( empty($id) ){
    		return $this->_insertMobile($mobileId);
    	}else{
    		return $id;
    	}
    }

    function getRestaurant($restaurant_id = false)
    {
        if( empty($restaurant_id) ){
            return false;
        }

        $this->db
             ->select('id, name AS name_th, name_en, detail AS detail_th, detail_en, modified_date AS last_update')
             ->where('id = ', $restaurant_id);
        $query = $this->db->get( $this->$table_restaurant );
        if( $query->num_rows()<=0 ){
            return false;
        }else{
            return $query->row_array();
        }
    }

    function getRestaurantCategory($restaurant_id)
    {
        $this->db
             ->select('id, parent_id, level_path, name AS name_th, name_en, description AS description_th, description_en')
             ->where('shop_id = ', $restaurant_id)
             ->where('item_status = ', 'published')
             ->order_by('level_path ASC');
        $query = $thid->db->get( $this->table_category );
        
        if( $query->num_rows()<=0 ){
            return false;
        }else{
            foreach( $query->result_array() as $row ){
                if( empty($row['parent_id']) ){
                    $category[$row['id']] = array(
                        'name_th' => $row['name_th'],
                        'name_en' => $row['name_en'],
                        'counting' => $this->_countMenu($row['id']),
                        'child' => false
                    );
                }else{
                    $category[$row['parent_id']]['child'][$row['id']] = array(
                        'name_th' => $row['name_th'],
                        'name_en' => $row['name_en'],
                        'counting' => $this->_countMenu($row['id']),
                    );
                }
            }

            return $category;
        }
    }

    function getRestaurantMenu($restaurant_id)
    {
        
    }

    function _countMenu($category)
    {
        // 1) get all category down
        $this->db
             ->select('id')
             ->where('item_status = ', 'published')
             ->like('level_path', '/'.$category.'/');
        $query = $thid->db->get( $this->table_category );
        if( $query->num_rows()<=0 ){
            return 0;
        }else{
            foreach( $query->result_array() as $row ){
                $category_id[] = $row['id'];
            }
        }

        // 2) count all menu inside 
        $this->db
             ->where('item_status = ', 'published')
    }

    function _insertMobile($mobileId)
    {
    	$currentDate = date("Y-m-d H:i:s");
    	$data = array(
    		'role' => 'client',
    		'device_key' => $mobileId,
    		'created_date' => $currentDate,
    		'modified_date' => $currentDate,
	    	);
    	$this->db->insert( $this->table_member, $data );
    	return $this->db->insert_id();
    }

    function _mobileExist($mobileId)
    {
    	$this->db
    		 ->select('id, item_status')
    		 ->where('device_key = ', $mobileId);
    	$query = $this->db->get( $this->table );

    	if( $query->num_rows()<=0 ){
    		return false;
    	}else{
    		$result = $query->row_array();
    		return $result['id'];
    	}
    }

}

?>