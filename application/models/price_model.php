<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Price_Model extends APP_Model
{
	var $table = 'ci_price',
		$column = array(
			'id' => 'id',
			'shop_id' => 'shop_id',
			'menu_id' => 'menu_id',
			'size_id' => 'size_id',
			'price' => 'price',
			'modified_date' => 'modified_date',
			),
		$perpage = 3;

    function __construct()
    {
        // Call the Model constructor
        parent::__construct($this->table, $this->field);
    }

    function priceData($price_id)
    {
        $main_table = $this->table;
        $join_table = 'ci_menusize';

        $this->db
             ->select($main_table.'.id, '.$join_table.'.id AS size, '.$main_table.'.price AS price, '.$main_table.'.modified_date AS modified_date')
             ->from( $main_table )
               ->join($join_table, $join_table.'.id='.$main_table.'.size_id')
             ->where($main_table.'.id = ', $price_id);

        $query = $this->db->get();
        if( $query->num_rows()<=0 ){
            return false;
        }else{
            return $query->row_array();
        }
    }

    function getPrice($menu_id)
    {
    	$main_table = $this->table;
    	$join_table = 'ci_menusize';

    	$this->db
    		 ->select($main_table.'.id, '.$join_table.'.name AS size, '.$main_table.'.price AS price, '.$main_table.'.modified_date AS modified_date')
    		 ->from( $main_table )
    		   ->join($join_table, $join_table.'.id='.$main_table.'.size_id')
    		 ->where($main_table.'.menu_id = ', $menu_id)
    		 ->where($main_table.'.item_status = ', 'published');

    	$query = $this->db->get();
    	if( $query->num_rows()<=0 ){
    		return false;
    	}else{
    		foreach($query->result_array() as $row){
    			$data[] = $row;
    		}
    	}

    	return $data;
    }

    function setPrice($menu_id, $data)
    {
        $this->insert($this->table, array(
            'shop_id' => '',
            'menu_id' => $menu_id,
            'size_id' => $data['size'],
            'price' => $data['price'],
            ));
    }

}

?>