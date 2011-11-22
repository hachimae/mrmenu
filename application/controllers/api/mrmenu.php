<?php defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
// require APPPATH.'/libraries/REST_Controller.php';

class Mrmenu extends REST_Controller
{
	var $mobileId = false, $apiKey = false, $userId = false;
	var $table_member = 'ci_member',
        $table_restaurant = 'ci_shop',
        $table_category = 'ci_category',
        $table_menu = 'ci_menu',
        $table_option_group = 'ci_option_group',
        $table_option_meta = 'ci_option_meta',
        $table_order = 'ci_order',
        $table_orderdish = 'ci_orderdish',
        $table_orderoption = 'ci_orderoption';

    public function __construct()
    {
        parent::__construct();
       	self::_getKey(); // check api key
    }
    /*
    | Get all restaurant
    | -------------------------------------------------------------
    | url: /api/restaurant
    */
    function allrestaurant_get($offset,$offset2 = 0)
    {
    	$_response['restaurant'] = self::_getAllRestaurant($offset,$offset2); 
    	
          self::_success( $_response );
           break;
    }
    //Get only Restaurant Thumb and Name
    function restaurantthumb_get($offset){
    
    }
    
    function fullrestaurants_get($offset,$offset2 = 0)
    {
		//Get All Restuarant Data
    	$_response['restaurant'] = self::_getAllRestaurant($offset,$offset2); 
    	//Get Each Category
    	foreach($_response['restaurant'] as $restaurants){
    		$rest_id = strval($restaurants['id']);
    		$cat = self::_getRestaurantCategory($rest_id);
    		if($rest_id <> ''){
    			$_response['restaurant'][$rest_id]['category'] = $cat;
    		}
    		//Get Each Menu in Category
			if(empty($cat)){
			     continue;
			}
    		foreach($cat as $key){
    			$menu = self::_getMenuByCat($key['id']);
    			$_response['restaurant'][$rest_id]['category'][$key['id']]['menu'] = $menu;
    		}
    	}
    	
        self::_success( $_response );
        break;
    }
    /*
    | check last update & get menu
    | -------------------------------------------------------------
    | url: /api/restaurant/id/1/table/1/[action/last-update]
    */
    function restaurant_get()
    {
    	//Variable form GET Method
        $restaurant_id = $this->get('id');
      	//$table_id = $this->get('id');
        $action = $this->get('action');
		//If Restaurant ID doesn't have ,will throw Error
        if( empty($restaurant_id) ){
            self::_throwError('Could not find Restaurant ID and Table ID.', '500');
        }
        //check what's action from API calling
        switch($action)
        {
            // get last update date
            case 'last-update':
            	//Gain Restaurant data from _getReastaurant fn
                $restaurant = self::_getRestaurant($restaurant_id);
                if( !$restaurant ){
                    self::_throwError('Empty data.', '404');
                }else{
                    // @log: <client> request last update date of <restaurant> at <time>
                    // display response
                    self::_success( array( 'lastupdate'=>$restaurant['last_update'] ) );
                }
                break;

            default:
                // @log: <client> request data of <restaurant> at <time>            
                // get shop information
                $_response['restaurant'] = self::_getRestaurant($restaurant_id);               
                // get category data
                $_response['restaurant']['category'] = self::_getRestaurantCategory($restaurant_id);   
                //$_response['category'] = self::_getRestaurantCategory($restaurant_id);            
                // get menu data
                $_response['restaurant']['menu'] = self::_getRestaurantMenu($restaurant_id); 
                //$_response['menu'] = self::_getRestaurantMenu($restaurant_id);
                //$_response['restaurant']['menu']['option'] = self::_getRestaurantMenuOption($restaurant_id);      
                self::_success( $_response );
                break;
        }
    }
	
    function category_get($restaurant_id)
    {
        $this->rest->db
             ->select('id,name')
             ->where('shop_id = ',$restaurant_id);
        $query = $this->rest->db->get( $this->table_category );
        $cat = $query->result_array();
        
        foreach($cat as $row){
	        $this->rest->db
	             ->select('name,category_id')
	             ->where('category_id = ',$row['id']);
	        $query = $this->rest->db->get( $this->table_menu );
	        $menu = $query->result_array();  
	        $row['amount'] = sizeof($menu);  
	        $row['menu'] = $menu;    
	        //print_r($row); 
	        $_response['category'][] = $row;
        }
    	self::_success( $_response );
    }
    /*
    | restaurant api
    | --------------------------------------------------------------
    | url: /api/restaurant/id/1
    | post: [table_id, last_update_time, mobile_id]
    */
    function restaurant_post()
    {
    	$restaurant_id = $this->get('id');
    	$this->_getRestaurant($restaurant_id);
    }
   
    /*
    | remove ordered dish from order
    | --------------------------------------------------------------
    | url: /api/dish/transaction/0000000000/dish/1
    */
    function dish_delete()
    {
    
        $transaction_id = $this->get('transaction');
        $dish_id = $this->get('dish');
        
        // self::debug( $this->get('transaction') );
        // self::debug( $this->get('dish') );
        // die();
        
        if( empty($transaction_id) || empty($dish_id) || empty($this->userId) ){
            self::_throwError('Missing param.', '500');
        }else{
            // get order id korn
            $this->rest->db
                 ->select('id, shop_id, subtotal, vat, charge, total')
                 ->where('transaction_id = ', $transaction_id)
                 ->where('member_id = ', $this->userId);
            $query = $this->rest->db->get($this->table_order);
            if( $query->num_rows()<0 ){
                self::_throwError('Order not found.', '500');
            }else{
                $result = $query->row_array();
                
                $order_id = $result['id'];
                $restaurant_id = $result['shop_id'];
                $subtotal = $result['subtotal'];
                $vat = $result['vat'];
                $charge = $result['charge'];
                $total = $result['total'];
            }
            
            // check dish data
            $dish_price = self::_get_dishPrice($order_id, $dish_id);
            if( $dish_price==false ){
                self::_throwError('Dish not found.', '500');
            }
            
            // change dish status
            $this->rest->db
                 ->where('order_id = ', $order_id)
                 ->where('id = ', $dish_id)
                 ->update($this->table_orderdish, array(
                    'dish_status' => 'cancel'
                 ));
                 
            // update order
            self::_updateOrder($restaurant_id, $order_id);            
            self::_success();
        }
    }
    

    /*
    | remove order
    | --------------------------------------------------------------
    | url: /api/order/transaction/0000000000
    */
    function orderdel_delete($transaction_id)
    {
        //$transaction_id = $this->get('transaction');
        
        if( empty($transaction_id) || empty($this->userId) ){
            self::_throwError('Missing param.', '500');
        }else{
            $this->rest->db
                 ->where('transaction_id = ', $transaction_id)
                 ->where('member_id = ', $this->userId)
                 ->update($this->table_order, array(
                    'order_status' => 'cancel'
                 ));
            self::_success();
        }
    }

    /*
    | insert order
    | --------------------------------------------------------------
    | url: /app/order/restaurant/1
    | post: [table_id, mobile_id, order]
    */
    function order_post()
    {      
        /* 
        self::debug( $this->post() );
        $order = array(
            'subtotal' => 290.00,
            'charge' => 29.00,
            'vat' => 22.33,
            'total' => 341.33,
            'item' => array(
                array(
                    'id'=>13, 'name'=>'menu option A', 'price'=>100.00, 'quantity'=>1, 'total'=>100.00,
                    'option' => array(
                        array('id'=>8, 'group'=>'topping', 'data'=>array(
                            array('id'=>39, 'name'=>'x', 'price'=>0),
                            array('id'=>40, 'name'=>'y', 'price'=>0),
                        )),
                        array('id'=>9, 'group'=>'size', 'data'=>array(
                            array('id'=>43, 'name'=>'small', 'price'=>-10.00)
                        ))
                    )
                ),
                array(
                    'id'=>13, 'name'=>'menu option A', 'price'=>100.00, 'quantity'=>1, 'total'=>100.00,
                    'option' => array(
                        array('id'=>8, 'group'=>'topping', 'data'=>array(
                            array('id'=>39, 'name'=>'x', 'price'=>0),
                            array('id'=>40, 'name'=>'y', 'price'=>0),
                        ))
                    ),
                ),
                array(
                    'id'=>5, 'name'=>'เมนู B', 'price'=>100.00, 'quantity'=>1, 'total'=>100.00,
                )
            )
        );
        self::debug( json_encode($order), "Order json" );
        die();
        */
        
        // register param
    	$restaurant_id = $this->post('restaurant');
    	$table_id = $this->post('table');
        $order_data = $this->post('order');
        print_r($order_data);
		if($table_id == ""){
			$table_id = 0;
		}
        //print_r($order_data);
        // flow control
        // ========================
        // 1) check validate
        //f( empty($restaurant_id) || empty($table_id) || empty($order_data) ){
        if( empty($restaurant_id) || empty($order_data) ){
            self::_throwError('Missing param.', '500');
        }else{
            $order_item = $order_data['item'];
        }
        
        // 2) check menu status & price (by fetching in order)
        $subtotal_price = 0;
        $order_data['total'] = (float)$order_data['total'];
        $order_data['subtotal'] = (float)$order_data['subtotal'];
        
        foreach($order_item as $item){
            // check status & price
            if( !self::_checkMenuStatus($item['id'], $item['price']) ){
                self::_throwError('Your restaurant data is not uptodate.', '500');
            }else{
               // $subtotal_price += $item['price'];
                $subtotal_price += ($item['price'] * $item['quantity']);
            }
            
            if( isset($item['option']) ){
                foreach($item['option'] as $option){
                    if( !self::_checkOptionStatus($option['id'], $option['data']) ){
                        self::_throwError('Your restaurant data is not uptodate.', '500');
                    }else{
                        foreach($option['data'] as $option_item){
                           // $subtotal_price += $option_item['price'];
                            $subtotal_price += ($option_item['price'] * $item['quantity']);
                        }
                    }
                }
            }
        }
        
        // check for subtotal & total price
        $subtotal_price = (float)$subtotal_price;
        if( abs($subtotal_price-$order_data['subtotal'])>=0.00001 ){
            self::_throwError('Subtotal not match ('.$subtotal_price.', '.$order_data['subtotal'].').', '500');
        }else{
            // get vat & charge information
            $restaurant_data = self::_getRestaurant($restaurant_id);
            $vat = self::_percent($restaurant_data['vat']);
            $charge = self::_percent($restaurant_data['charge']);
            
            $total_price = $subtotal_price * $charge * $vat;
            if( abs($total_price-$order_data['total'])>=0.00001 ){
                self::_throwError('Total not match ('.$total_price.', '.$order_data['total'].').', '500');
            }
        }
        
        // 3) insert menu into order
        // list($transaction, $order) = self::_insertOrder($restaurant_id, $table_id, $order_data);
        $result = self::_insertOrder($restaurant_id, $table_id, $order_data);
        if( $result['transaction_id']==false ){
            self::_throwError('Can not make an order.', '500');
        }
        
        // 4) response success
        self::_success( $result );
    }
    
    /* = private data =========================================== */
    
    private function _percent($number)
    {
        return $number<=0 ? 1 : (1 + ($number/100));
    }
    
    private function _updateOrder($restaurant_id, $order_id)
    {
            $restaurant_data = self::_getRestaurant($restaurant_id);
            $vat = self::_percent($restaurant_data['vat']);
            $charge = self::_percent($restaurant_data['charge']);
            
            // get Dish ID
            $this->rest->db
                 ->select('id')
                 ->where('order_id = ', $order_id)
                 ->where('dish_status != ', 'cancel');
            $query = $this->rest->db->get($this->table_orderdish);
            //If doesn't get anything
            if( $query->num_rows()<=0 ){
                $data = array(
                    'subtotal' => 0,
                    'charge' => 0,
                    'vat' => 0,
                    'total' => 0
                );
            }else{
                $subtotal = 0;
                foreach($query->result_array() as $dish){
                    $subtotal += self::_get_dishPrice($order_id, $dish['id']);
                    // echo "\nsubtotal: ".$subtotal;
                }
                
                $charge = $subtotal*$charge - $subtotal;
                // echo "\ncharge: ".$charge;
                $vat = ($vat - 1)*($subtotal + $charge);
                // echo "\nvat: ".$vat;
                $total = $subtotal + $charge + $vat;
                // echo "\ntotal: ".$total;
                
                //The data for update in table_order
                $data = array(
                    'subtotal' => $subtotal,
                    'charge' => $charge,
                    'vat' => $vat,
                    'total' => $total
                );
            }
            
            $this->rest->db
                 ->where('id = ', $order_id)
                 ->update($this->table_order, $data);
            // $subtotal = $subtotal - $dish_price;
            // $charge = $subtotal*$charge - $subtotal;
            // $vat = ($vat - 1)*($subtotal + $charge);
            // $total = $subtotal + $charge + $vat;
            /* $this->rest->db
                 ->where('transaction_id = ', $transaction_id)
                 ->update($this->table_order, array(
                    'subtotal' => $subtotal,
                    'charge' => $charge,
                    'vat' => $vat,
                    'total' => $total
                 )); */
    }
    
    private function _get_dishPrice($order_id, $dish_id)
    {
        $db = $this->rest->db;
        $price = 0;
        //echo $order_id.$dish_id;
        
        // select price from option
        $db->select('price')
           ->where('order_id = ', $order_id)
           ->where('dish_id = ', $dish_id);
        $query = $db->get($this->table_orderoption);
        if( $query->num_rows()>0 ){
            foreach($query->result_array() as $option){
                $price += $option['price'];
            }
        }
        
        // select price from dish
        $db->select('total')
           ->where('order_id = ', $order_id)
           ->where('id = ', $dish_id);
        $query = $db->get($this->table_orderdish);
        $result = $query->row_array();
        if( $result==false ){
            return false;
        }else{
            $price += $result['total'];
        }
        
        return $price;
    }
    
    // insert order
    // -------------------------------------------------------------
    private function _insertOrder($restaurant_id, $table_id, $order_data)
    {
        $db = $this->rest->db;
        $current = date("Y-m-d H:i:s");
        $transaction_string = time();
        
        // begin transaction
        $db->trans_begin();
        
        // 1) insert into [order]
        $result = $db->insert($this->table_order, array(
            'transaction_id' => $transaction_string,
            'shop_id' => $restaurant_id,
            'member_id' => $this->userId,
            'table_id' => $table_id,
            'subtotal' => $order_data['subtotal'],
            'vat' => $order_data['vat'],
            'charge' => $order_data['charge'],
            'total' => $order_data['total'],
            'remark' => isset($order_data['remark']) ? $order_data['remark'] : '',
            'created_date' => $current,
            'modified_date' => $current
        ));
        if( $result==false ){
            $db->trans_rollback();
            self::_throwError('Can not insert [order].', '500');
        }else{
            $order_id = $db->insert_id();
        }
        
        // 2) insert into [order-dish]
        foreach((array)$order_data['item'] as $index => $dish){
            $result = $db->insert($this->table_orderdish, array(
                'order_id' => $order_id,
                'menu_id' => $dish['id'],
                'name' => $dish['name'],
                // 'description' => $dish['description'],
                'price' => $dish['price'],
                'quantity' => $dish['quantity'],
                'total' => $dish['total'],
                'created_date' => $current,
                'modified_date' => $current
            ));
            if( $result == false ){
                $db->trans_rollback();
                self::_throwError('Can not insert [order-dish].', '500');
            }else{
                $dish_id = $db->insert_id(); // get auto ID
                $order_data['item'][$index]['dish_id'] = $dish_id;
            }
            
            // 3) insert into [order-option] (if exist)
            if( isset($dish['option']) ){
                foreach((array)$dish['option'] as $option_group){
                    $group_id = $option_group['id'];
                    $group_name = $option_group['group'];
                    
                    if( isset($option_group['data']) ){
                        foreach((array)$option_group['data'] as $option_index=>$option){
                        	//Insert option into ci_option_meta table 
                            $result = $db->insert($this->table_orderoption, array(
                                'order_id' => $order_id,
                                'dish_id' => $dish_id,
                                'option_id' => $option['id'],
                                'group_id' => $group_id,
                                'group_name' => $group_name,
                                'name' => $option['name'],
                                'price' => $option['price'],
                                'created_date' => $current,
                                'modified_date' => $current
                            ));
                            if( $result==false ){
                                self::_throwError('Can not insert [order-option].', '500');
                            }
                        }
                    }
                }
            }
        }
        
        // complete tanrasction
        $db->trans_commit();
        
        return array('transaction_id' => $transaction_string, 'order'=>$order_data); 
    }
    
    // check menu option information
    // -------------------------------------------------------------
    private function _checkOptionStatus($group_id, $option_data)
    {
        foreach((array)$option_data as $option_item){
            $price_type = $option_item['price']<0 ? 'dec' : 'inc';
            $this->rest->db
                ->where('group_id = ', $group_id)
                ->where('id = ', $option_item['id'])
                ->where('price_type', $price_type)
                ->where('price_value = ', abs($option_item['price']));
            $result = $this->rest->db->count_all_results( $this->table_option_meta );
            if( $result<=0 ){
                return false;
            }
        }
        
        return true;
    }
    
    // check menu information 
    // -------------------------------------------------------------
    private function _checkMenuStatus($menu_id, $menu_price)
    {
        $this->rest->db
             ->where('item_status = ', 'published')
             ->where('outofstock = ', 'no')
             ->where('id = ', $menu_id)
             ->where('price = ', $menu_price);
        return $this->rest->db->count_all_results( $this->table_menu );
    }
    
    private function debug($var, $string = "Debug")
    {
        echo "\n\n# ".$string.":\n";
        if( $var==false ){
            echo "false";
        }else{
            print_r($var);
        }
    }
    
    // get current api key
    // -------------------------------------------------------------
    private function _getKey()
    {
        if( isset( $_SERVER['HTTP_X_API_KEY'] ) ){ 
            $this->apiKey = $_SERVER['HTTP_X_API_KEY'];
            $this->userId = self::_getUser($this->apiKey);
        }else{
            self::_throwError('Could not find API Key.', '500');
        }
    }
    
    // get user
    // -------------------------------------------------------------
    private function _getUser($apiKey)
    {
        $this->rest->db
             ->select('id')
             ->where('api_key = ', $apiKey);
        $query = $this->rest->db->get( $this->table_member );
        if( $query->num_rows()<=0 ){
            return false;
        }else{
            $result = $query->row_array();
            return $result['id'];
        }
    }
    
    // get thumbnail 
    // -------------------------------------------------------------
    private function _getThumbnail($ref_id, $ref_table)
    {
        $this->rest->db
             ->select('file')
             ->where('ref_id = ', $ref_id)
             ->where('ref_table = ', $ref_table)
             ->where('item_status = ', 'published');
        $query = $this->rest->db->get('ci_media');
        
        if( $query->num_rows()<=0 ){
            return false;
        }else{
            $result = $query->row_array();
            return $result['file'];
        }
    }
    //Get All Restaurant
    //--------------------------------------------------------------
    private function _getAllRestaurant($offset = 1,$offset2 = 0)
    {
    	
    	/*
        $select = array(
            'id', 'name AS name_th', 'name_en', 
            'detail AS detail_th', 'detail_en',
            'charge', 'vat', 'cess',
            'modified_date AS last_update'
        );
        $this->rest->db
             ->select( implode(', ', $select) );
        $query = $this->rest->db->get( $this->table_restaurant );    

        if( $query->num_rows()<=0 ){
            return false;
        }else{
	        foreach( $query->result_array() as $row ){
	        	$row['thumbnail'] = self::_getThumbnail($row['id'],$this->table_restaurant);
				$allrest[] = $row;
				print_r($row);
				
			}        	
           return $allrest;
        }*/
    	$this->load->database();
       // $query = $this->db->query('SELECT * FROM ci_shop LIMIT '.$offset);
        $query = $this->db->get('ci_shop',$offset,$offset2);
        $i = 0;
        foreach( $query->result_array() as $row ){
        	$row['thumbnail'] = self::_getThumbnail($row['id'],$this->table_restaurant);
			$allrest[$row['id']] = $row;
			$i++;	
		}
		$allrest['amount'] = $i;
		//print_r($allrest);     	
          return $allrest;
   
    }
    // get restaurant information
    // -------------------------------------------------------------
    private function _getRestaurant($restaurant_id = false)
    {
        $select = array(
            'id', 'name AS name_th', 'name_en', 
            'detail AS detail_th', 'detail_en',
            'charge', 'vat', 'cess',
            'modified_date AS last_update'
        );
        
        $this->rest->db
             ->select( implode(', ', $select) )
             ->where('id = ', $restaurant_id);
        $query = $this->rest->db->get( $this->table_restaurant );
        
        if( $query->num_rows()<=0 ){
            return false;
        }else{
            $result = $query->row_array();
            $result['thumbnail'] = self::_getThumbnail($restaurant_id,$this->table_restaurant);
            return $result;
        }
    }

    // response error message
    // -------------------------------------------------------------
    private function _throwError($errorMessage, $errorCode = '404')
    {
    	$_response = array(
    		'status' => 0,
    		'error' => $errorMessage
	    	);
    	$this->response($_response, 404);
    	die();
    }
    
    // response success data
    // -------------------------------------------------------------
    private function _success($response = array())
    {
        $_response['status'] = 1;
       //$_response = $response;
        
        $_response = array_merge((array)$_response, (array)$response);
        //print_r($_response);
    	$this->response($_response, 200);
    }

    // get restaurant category information
    // ------------------------------------------------------------
    private function _getRestaurantCategory($restaurant_id)
    {
        $this->rest->db
             ->select('id, parent_id, level_path, name AS name_th, name_en, description AS description_th, description_en')
             ->where('shop_id = ', $restaurant_id)
             ->where('item_status = ', 'published')
             ->order_by('level_path ASC');
        $query = $this->rest->db->get( $this->table_category );
        
        if( $query->num_rows()<=0 ){
            return false;
        }else{
        	
            foreach( $query->result_array() as $row ){
                $category_id[] = $row['id'];
                //Not have parent
                if( empty($row['parent_id']) ){
                	
                    $category[$row['id']] = array(
                    	'id' => $row['id'],
                        'name_th' => $row['name_th'],
                        'name_en' => $row['name_en'],
                        'thumbnail' => self::_getThumbnail($row['id'], 'ci_category'),
                        'counting' => $this->_countMenu($row['id']),
                        'child' => false
                    );
                    
                }else{
                //Have parent
                    $category[$row['parent_id']]['child'][$row['id']] = array(
                        'name_th' => $row['name_th'],
                        'name_en' => $row['name_en'],
                        'counting' => $this->_countMenu($row['id']),
                        'thumbnail' => self::_getThumbnail($row['id'], 'ci_category'),
                    );
                }
            }
            return $category;
        }
    }
    
    // count child
    // -----------------------------------------------------------
    function _countMenu($category)
    {
        // 1) get all category down
        $this->rest->db
             ->select('id')
             ->where('item_status = ', 'published')
             ->like('level_path', '/'.$category.'/');
        $query = $this->rest->db->get( $this->table_category );
        if( $query->num_rows()<=0 ){
            return 0;
        }else{
            foreach( $query->result_array() as $row ){
                $category_id[] = $row['id'];
            }
        }

        // 2) count all menu inside 
        $this->rest->db
             ->where('item_status = ', 'published')
             ->where_in('category_id', $category_id);
        return $this->rest->db->count_all_results( $this->table_menu );
    }
    
    // get restaurant menu information
    // -------------------------------------------------------------
    private function _getRestaurantMenu($restaurant_id)
    {
    	//Get data from table_menu
        $this->rest->db
             ->select('*')
             ->where('item_status = ', 'published')
             ->where('outofstock = ', 'no')
             ->where('shop_id = ', $restaurant_id);
        $query = $this->rest->db->get( $this->table_menu );

       //Get data from table_option_group
        $this->rest->db
             ->select('id,menu_id,name,name_en,option_type')
             ->where('shop_id = ',$restaurant_id);
        $query2 = $this->rest->db->get( $this->table_option_group );
        $option_group_rows = $query2->num_rows();
              
        if( $query->num_rows()<= 0 ){
            return false;
        }else{ 	
        	// Need to cont. 3/11/2011
            foreach( $query->result_array() as $row ){
                foreach( $query2->result_array() as $option_group ){    			
        			if($option_group['menu_id'] == $row['id']){
				        //Get data from table_option_meta
				        $this->rest->db
				             ->select('id,name,name_en,price_type,price_value')
				             ->where('group_id = ',$option_group['id']);
				        $query3 = $this->rest->db->get( $this->table_option_meta );
				        $option_meta = $query3->result_array();
				        $opt_meta = null;
						for($i=0;$i<sizeof($option_meta);$i++){
							$opt_meta_id[] = $option_meta[$i]['id'];
		                    $opt_meta[$option_meta[$i]['id']] = array( //string
		                        'name_th' => $option_meta[$i]['name'],
		                        'name_en' => $option_meta[$i]['name_en'],
			                    //'name_en' => $option_meta[$i]['name_en'],
			                    //'name_en' => $option_meta[$i]['name_en'],
		                    );						
						}
				        $option_group['option_value'] =  $opt_meta;	
        				$row['options'] = $option_group; 
        							
        			}
        		}
                $row['thumbnail'] = self::_getThumbnail($row['id'], 'ci_menu');
                //$row['options'] = "options";
                $data[$row['id']] = $row;
            }
            //print_r($data);
            return $data;
        }
    }

    private function _getMenuByCat($cat_id)
    {
    	//Get data from table_menu
        $this->rest->db
             ->select('*')
             ->where('item_status = ', 'published')
             ->where('outofstock = ', 'no')
             ->where('shop_id = ', $cat_id);
        $query = $this->rest->db->get( $this->table_menu );

       //Get data from table_option_group
        $this->rest->db
             ->select('id,menu_id,name,name_en,option_type')
             ->where('shop_id = ',$cat_id);
        $query2 = $this->rest->db->get( $this->table_option_group );
        $option_group_rows = $query2->num_rows();
              
        if( $query->num_rows()<= 0 ){
            return false;
        }else{ 	
        	// Need to cont. 3/11/2011
            foreach( $query->result_array() as $row ){
                foreach( $query2->result_array() as $option_group ){    			
        			if($option_group['menu_id'] == $row['id']){
				        //Get data from table_option_meta
				        $this->rest->db
				             ->select('id,name,name_en,price_type,price_value')
				             ->where('group_id = ',$option_group['id']);
				        $query3 = $this->rest->db->get( $this->table_option_meta );
				        $option_meta = $query3->result_array();
				        $opt_meta = null;
						for($i=0;$i<sizeof($option_meta);$i++){
							$opt_meta_id[] = $option_meta[$i]['id'];
		                    $opt_meta[$option_meta[$i]['id']] = array( //string
		                        'name_th' => $option_meta[$i]['name'],
		                        'name_en' => $option_meta[$i]['name_en'],
			                    //'name_en' => $option_meta[$i]['name_en'],
			                    //'name_en' => $option_meta[$i]['name_en'],
		                    );						
						}
				        $option_group['option_value'] =  $opt_meta;	
        				$row['options'] = $option_group; 
        							
        			}
        		}
                $row['thumbnail'] = self::_getThumbnail($row['id'], 'ci_menu');
                //$row['options'] = "options";
                $data[$row['id']] = $row;
            }
            //print_r($data);
            return $data;
        }
    }
    

    private function _getRestaurantMenuOptions($menu_id)
    {
    	// Need to edit ---------------------------
        $this->rest->db
             ->select('*')
             ->where('item_status = ', 'published')
             ->where('outofstock = ', 'no')
             ->where('shop_id = ', $restaurant_id);
        $query = $this->rest->db->get( $this->table_menu );
        
        if( $query->num_rows()<=0 ){
            return false;
        }else{
            foreach( $query->result_array() as $row ){
                $row['thumbnail'] = self::_getThumbnail($row['id'], 'ci_menu');
                $data[$row['id']] = $row;
            }
            return $data;
        }
        // Need to edit ---------------------------
    }  
    
    private function __getRestaurant($restaurant_id)
    {
    	if( empty($restaurant_id) ){
    		$this->_throwError('Restaurant not found');
    	}

    	$_post = $this->post();
    	$_response = array( 'success'=>'false' );
    	
    	if( empty($_post['table_id']) ){
    		$this->_throwError('Desk could not be found');
    	}else{
    		// flow controll
    		// ----------------------------------------------------
    		// 1) get Restaurant information
    		$restaurant = $this->api->getRestaurant($restaurant_id);

    		// 2) check last update time
    		if( $restaurant['last_update']==$this->post('last_update') ){
    			$_response['success'] = true;
    			$_response['uptodate'] = true;
    			$this->response($_response, 200);
    		}else{
	    		// 3) get Category
	    		$category = $this->api->getRestaurantCategory($restaurant_id);

	    		// 4) get Menu
	    		$menu = $this->api->getRestaurantMenu($restaurant_id);

				$_response['success'] = true;
				$_response['uptodate'] = false;
	    		$_response['result'] = array( 'something'=>':))' );

	    		$_response = array(
	    			'success' => true,
	    			'uptodate' => false,
	    			'last_update' => $restaurant['last_update'],
	    			'restaurant' => array(
	    				'id' => $restaurant['id'],
	    				'name_th' => $restaurant['name_th'],
	    				'name_en' => $restaurant['name_en'],
		    		),
		    		'category' => $category,
		    		'menu' => $menu
		    	);

	    		$this->response($_response, 200);
    		}

    	}
    }

    // -------------------------------------------------------------

}