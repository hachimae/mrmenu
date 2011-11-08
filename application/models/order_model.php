<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Order_model extends APP_Model
{
	var $table = 'ci_order',
        $table_dish = 'ci_orderdish',
        $table_option = 'ci_orderoption',
		$column = array(
			'id' => 'id',
			'transaction_id' => 'transaction_id',
			'shop_id' => 'shop_id',
			'member_id' => 'member_id',
			'table_id' => 'table_id',
			'subtotal' => 'subtotal',
			'vat' => 'vat',
			'charge' => 'charge',
			'total' => 'total',
			'remark' => 'remark',
			'order_status' => 'order_status',
			'modified_date' => 'modified_date',
			),
		$perpage = 3;

    function __construct()
    {
        // Call the Model constructor
        parent::__construct($this->table, $this->field);
    }
    
    function get_orderDetail($order_id = false)
    {
        if( empty($order_id) ){
            return false;
        }
        
        $data['detail'] = $this->orderData($order_id);
        if( $data['detail']==false ){
            return false;
        }
        
        $data['dish'] = $this->orderDish($order_id);
        if( $data['dish']==false ){
            return false;
        }
        
        return $data;
    }
    
    function orderData($order_id)
    {
        $this->db
             ->select($this->column)
             ->where('id = ', $order_id);
        $query = $this->db->get( $this->table );
        
        if( $query->num_rows()<=0 ){
            return false;
        }else{
            return $query->row_array();
        }
    }
    
    function orderDish($order_id)
    {
        $this->db
             ->select()
             ->where('order_id = ', $order_id);
        $query = $this->db->get( $this->table_dish );
        
        if( $query->num_rows()<=0 ){
            return false;
        }else{
            foreach($query->result_array() as $dish){
                $data[$dish['id']] = $dish;
                $data[$dish['id']]['option'] = $this->orderDishOption($dish['id']);
            }
        }
        
        return $data;
    }
    
    function orderDishOption($dish_id)
    {
        $this->db
             ->select()
             ->where('dish_id = ', $dish_id);
        $query = $this->db->get( $this->table_option );
        
        if( $query->num_rows()<=0 ){
            return false;
        }else{
            return $query->result_array();
        }
    }

}

?>