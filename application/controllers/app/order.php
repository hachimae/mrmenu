<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
- order list
- order detail
- order status update (cooking, cancel, done)
*/
class Order extends APP_Controller 
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->model('order_model', 'order');
		$this->setModel( $this->order );
		
		$this->form = array(
			'category_id' => array( 'type'=>'select', 'label'=>'Menu', 'validate'=>'required', 'class'=>'required'),
			'price' => array( 'type'=>'text', 'label'=>'Price', 'validate'=>'required', 'class'=>'required' ),
			);
	}
	/*============================= Index function =============================*/
	function index()
	{
		$per_page = 10;
		$view = $this->input->get('view');
		$page = $this->input->get('page');
		$page = !empty($page) ? $page : 1;

		if(isset($view) && ($view <> "")){
			redirect('/app/order/view?view='.$view);
		}
		$this->setContent('title', 'Order Management (All)');
		//Button
		$tools = array(
			 'add'	 => array( 'class'=>'button', 'name'=>'Add', 'action'=>'/app/order/add','view'=>$view ),
			 //'edit'	 => array( 'class'=>'button', 'name'=>'Edit', 'action'=>'/app/size/edit' ),
			'cooking' => array( 'class'=>'button', 'name'=>'Cooking', 'action'=>'/app/order/cooking', 'view'=>$view ),
			'cancel' => array( 'class'=>'button', 'name'=>'Cancel', 'action'=>'/app/order/cancel', 'view'=>$view )
			);
		//Content
		$col = array(
			'id'	 => array( 'type'=>'normal', 'name'=>'ID', 'class'=>'col-id' ),
			'transaction_id'	 => array( 'type'=>'order-detail', 'name'=>'Order ID', 'class'=>'col-name' ),
		'remark'	 => array( 'type'=>'order-detail', 'name'=>'Remark', 'class'=>'col-name' ),
			'order_status'	 => array( 'type'=>'normal', 'name'=>'Status', 'class'=>'col-name' ),
			);
		//Pagination
		$filter = array('shop_id'=>$this->shopId);		
		$data = $this->model->getPage($page,$per_page,$filter);
		$size_row = $this->model->countAll();
		
		$this->_setListTools( $tools );
		$this->_setListCol( $col );
		$this->_setListData( $data );
		$this->_setListPage( 'app/order?',$size_row,$per_page);

		$this->_list();
	}
	/*============================= View function =============================*/
    function view()
    {
    	$per_page = 10;
    	$viewer = $this->input->get('view');
 		$page = $this->input->get('page');
		$page = !empty($page) ? $page : 1;
		echo $viewer;
		$this->setContent('title', 'Order Management ('.$viewer.')');
		//Button
		$tools = array(
			 'add'	 => array( 'class'=>'button', 'name'=>'Add', 'action'=>'/app/order/add','view'=>$viewer ),
			 //'edit'	 => array( 'class'=>'button', 'name'=>'Edit', 'action'=>'/app/size/edit' ),
			'cooking' => array( 'class'=>'button', 'name'=>'Cooking', 'action'=>'/app/order/cooking', 'view'=>$viewer ),
			'finished' => array( 'class'=>'button', 'name'=>'Finished', 'action'=>'/app/order/finished', 'view'=>$viewer ),
			'cancel' => array( 'class'=>'button', 'name'=>'Cancel', 'action'=>'/app/order/cancel', 'view'=>$viewer ),
		    //'remove' => array( 'class'=>'button', 'name'=>'Remove', 'action'=>'/app/order/remove', 'event'=>'makeRemove()' ),
			);
		//Content
		$col = array(
			'id'	 => array( 'type'=>'normal', 'name'=>'ID', 'class'=>'col-id' ),
			'transaction_id'	 => array( 'type'=>'order-detail', 'name'=>'Order ID', 'class'=>'col-name' ),
			'count'	 => array( 'type'=>'order-detail', 'name'=>'Dish', 'class'=>'col-name' ),
			'remark'	 => array( 'type'=>'order-detail', 'name'=>'Remark', 'class'=>'col-name' ),
			'order_status'	 => array( 'type'=>'normal', 'name'=>'Status', 'class'=>'col-name' ),
			);
		//Pagination
		$filter = array('order_status'=>$viewer,
						'shop_id'=>$this->shopId);

		$data = $this->model->getPage($page,$per_page,$filter);
		$i=0;
		if($data <> null){
			foreach($data as $row){
			    $this->db->where('order_id =',$row['id']);
			    $this->db->from('ci_orderdish');
				$row['count'] = $this->db->count_all_results();	
				$data[$i]['count'] = $row['count'] ;
				$i++;
			}		
		}

		//print_r($data);
		$size_row = $this->model->countAll($filter);
		$per_page = 10;
		$this->_setListTools( $tools );
		$this->_setListCol( $col );
		$this->_setListData( $data );
		

		$this->_setListPage( 'app/order/view?view='.$viewer, $size_row,$per_page);
		$this->_list();   
    }
    /*============================= Detail function =============================*/
    function detail($order_id = false)
    {
    	//Get view 
    	$viewer = $this->input->get('view');
    	
        //Get Order dish Data
        $this->data['order'] = $this->model->get_orderDetail($order_id);
        $order = $this->data['order'];
        //Get Name of Menu
        //Just load ci_menu table first
 		$this->load->model('menu_model', 'menu');
		$this->setModel( $this->menu );
        foreach($order['dish'] as $row){
        	 $this->data['menu'] = $this->model->getMenuData($row['menu_id']);
        	 $menu =  $this->data['menu'];
        	 $dish_id = $row['id'];
        	 $order['dish'][$dish_id]["name"] = $menu['name'];	 
        }
        //load back to ci_order table
		self::__construct();
        //To get Restaurant data
		$shop_id = $order['detail']['shop_id'];
        $this->data['shop'] = $this->model->getShopById($shop_id);
        $shop =  $this->data['shop'];
        //----------------------------------------
		$page = $this->input->get('page');
		$page = !empty($page) ? $page : 1;

		$this->setContent('title', 'Order : '.$order['detail']['transaction_id']);
		//Button
		$tools = array(    
			'receive' => array( 'class'=>'button', 'name'=>'Receive', 'action'=>'/app/order/receive', 'view'=>$viewer ),
			'cancel' => array( 'class'=>'button', 'name'=>'Delete', 'action'=>'/app/order/cancelDish','view'=>$viewer ),
			);
		//Content
		$col = array(
			'id'	 => array( 'type'=>'normal', 'name'=>'ID', 'class'=>'col-id' ),
			'name'	 => array( 'type'=>'option-detail', 'name'=>'Name', 'class'=>'col-name' ),
			'quantity'	 => array( 'type'=>'normal', 'name'=>'Quantity', 'class'=>'col-name' ),
			'price'	 => array( 'type'=>'normal', 'name'=>'Price', 'class'=>'col-name' ),
			'total'	 => array( 'type'=>'normal', 'name'=>'Total', 'class'=>'col-name' ),
			'dish_status'	 => array( 'type'=>'normal', 'name'=>'Dish Status', 'class'=>'col-name' ),
			);
		
		
    	foreach($order['dish'] as $row){
    		
    		//Find menu_id
    		$menu_id = $row['menu_id'];
    		$this->db->select()
    				 ->where('id =',$menu_id);
    		$query = $this->db->get('ci_menu');
    		$menu_arr = $query->row_array();
    		//Get Menu Price
    		$menu_price = $menu_arr['price'];
    		//Replace Menu price and Total
    		$row['price'] = $menu_price;
			$row['total'] = $menu_price * $row['quantity'];
			
			$order['dish'][$row['id']]= $row;
			
    		$orderdish_id = $row['id'];//id of orderdish
    		
			$order_option = $this->model->orderDishOption($orderdish_id);
			$quantity = $row['quantity'];
			$option_price = 0;
			if($order_option <> null)
			{
				foreach($order_option as $option)
				{
					$option_price += $option['price'];
				}			
			}

			//Multiply by Quantity of Dish
			$option_price = $option_price * $quantity;
			$addition_price[$orderdish_id] = $option_price;
		}			
		
		$this->_setListTools( $tools );
		$this->_setListCol( $col );
		$this->_setListData( $order['dish'] );
		
		$meta = array(
			'thispage' => 'detail',
			'prepage' => '',
			'num' => '',
			'addition_price' => $addition_price,
			'charge' => $shop['charge'],
			'vat' => $shop['vat'],
			'cess' => $shop['cess']
		);
		
		$this->_setListMeta( $meta );
		$this->_list();
        //----------------------------------------
        //var_dump(  $order['dish']);
        
        // 3) update status button
    }
    /*============================= Option function =============================*/
    function option($orderdish_id){
    	//Get Order Data
        $this->data['dish_option'] = $this->model->orderDishOption($orderdish_id,'all');
        $dish_option = $this->data['dish_option'];
		$order_id = $this->data['dish_option'][0]['order_id'];
		//Get Order ID
        if($order_id <> ""){
        	echo $order_id;
        }else{
			//If doesn't have option but want to bind order_id in Back button
        	$order_none = $this->model->orderDishById($orderdish_id); //$order_none means not to show anything
        	$order_id = $order_none[0]['order_id'];
        }
		//Get Menu ID from orderdish table
		$this->data['order_dish'] = $this->model->orderDishById($orderdish_id);
		$order_dish = $this->data['order_dish'];
		$menu_id = $order_dish[0]['menu_id'];
		
		//Get Menu data
     	$this->load->model('menu_model','menu');
		$this->setModel( $this->menu );
        $this->data['menu'] = $this->model->getMenuData($menu_id);
       	$menu =  $this->data['menu'];
    	/*
    	$query = $this->db->get_where('ci_orderoption', array('dish_id' => $orderdish_id));
    	$orderoption = $query->result();
		foreach ($query->result() as $row)
		{
			$orderoption['id'] = $row->id;
			$orderoption['name'] = $row->name;
	    	
		}*/
		//print_r($orderoption);
        //----------------------------------------
        $viewer = "";
		$this->setContent('title', 'Option of '.$menu['name']);
		//Button
		$tools = array(
		'remove' => array( 'class'=>'button', 'name'=>'Remove from Dish', 'action'=>'/app/order/cancelOption','view'=>$viewer )
			);
		//Content
		$col = array(
			'id'	 => array( 'type'=>'normal', 'name'=>'ID', 'class'=>'col-id' ),
			'group_name'	 => array( 'type'=>'normal', 'name'=>'Group ', 'class'=>'col-name' ),
			'name'	 => array( 'type'=>'normal', 'name'=>'Option', 'class'=>'col-name' ),
			'price'	 => array( 'type'=>'normal', 'name'=>'Addition price', 'class'=>'col-name' ),
			'item_status'	 => array( 'type'=>'normal', 'name'=>'Status', 'class'=>'col-name' ),
			);

		$this->_setListTools( $tools );
		$this->_setListCol( $col );
		$this->_setListData( $dish_option );
		
		//Meta contain previous page
		$meta = array(
		    'thispage' => 'option',
			'prepage' => 'detail',
			'num' => $order_id,
		);
		$this->_setListMeta( $meta );
		$this->_list();
    }
    /*============================= Add function =============================*/
    function add()
    {
		$this->setContent('title', 'Add New Order');

		// add js/css
		$this->template->add_js('media/js/script-form.js');
		$this->template->add_js('media/lib/fancybox/jquery.fancybox-1.3.4.pack.js');
		$this->template->add_css('media/lib/fancybox/jquery.fancybox-1.3.4.css');

		parent::add(site_url('app/order/update'), $this->form);  
    }
    /*============================= Change function =============================*/
    function change($orderdish_id)
    {
       // $this->data['order'] = $this->model->get_orderDetail($order_id);
        //$order = $this->data['order'];
        
        //$this->_list();
   	}
	//Change and Update
	/*============================= Cancel function =============================*/
	function cancel($order_id)
	{
		
		//parent::remove($order_id);
		//redirect('app/order');
		
		$page = $this->input->get('page');
		$view = $this->input->get('view');
		
		$this->db->where('id = ', $order_id)
				 ->update('ci_order', array(
					'order_status' => 'cancel'
				));
				
		redirect('app/order/view?view='.$view.'&page='.$page);
		
	}
	/*============================= cancelDish function =============================*/
	function cancelDish($dish_id) //Cancel ordered dish and also can dish option
	{ 
		$page = $this->input->get('page');
		//To cancel Dish
		$this->db->where('id = ', $dish_id)
				 ->update('ci_orderdish', array(
					'dish_status' => 'cancel'
				));
		//To cancel dish option
		$this->db->where('dish_id = ', $dish_id)
				 ->update('ci_orderoption', array(
					'item_status' => 'deleted'
				));
		//To get Order ID for redirect to that page
		$query = $this->db->get_where('ci_orderdish', array('id' => $dish_id));
		foreach ($query->result() as $row)
		{
	    	$order_id = $row->order_id;
		}
		redirect('app/order/detail/'.$order_id);	
	}
	/*============================= cancelDish function =============================*/
	function receive($dish_id = false) //Cancel ordered dish and also can dish option
	{
		$page = $this->input->get('page');

		if( empty($dish_id) ){
			$_post = $this->input->post();

			$_response['success'] = false;
			if( empty($_post) ){
				$_response['error'] = 'You must select row to receive.';
			}else{
				foreach($_post['selected'] as $dish_id){
					$this->receiveDish($dish_id);
				}		
				$_response['success'] = true;
			}
			echo json_encode($_response);
			die();		
		}else{
			$this->receiveDish($dish_id);
		}						
	}
	
	function receiveDish($dish_id)
	{
			//To recieve Dish
			$this->db->where('id = ', $dish_id)
					 ->update('ci_orderdish', array(
						'dish_status' => 'receive'
					));
			//To cancel dish option
			$this->db->where('dish_id = ', $dish_id)
					 ->update('ci_orderoption', array(
						'item_status' => 'published'
					));				
		
		
	}
	/*============================= CancelOption function =============================*/
	function cancelOption($id)
	{
		$page = $this->input->get('page');

		$this->db->where('id = ', $id)
				 ->update('ci_orderoption', array(
					'item_status' => 'deleted'
				));

		$query = $this->db->get_where('ci_orderoption', array('id' => $id));
		foreach ($query->result() as $row)
		{
	    	$dish_id = $row->dish_id;
		}
		redirect('app/order/option/'.$dish_id);		
	}
	/*============================= Update function =============================*/
	function update()
	{
		parent::update();
		redirect('app/');
	}
	//Cooking Function
	/*============================= Cooking function =============================*/
	function cooking($order_id = false)
	{
		$page = $this->input->get('page');
		$view = $this->input->get('view');
		
		if( empty($order_id) ){
			$_post = $this->input->post();

			$_response['success'] = false;
			if( empty($_post) ){
				$_response['error'] = 'You must select row to receive.';
			}else{
				foreach($_post['selected'] as $order_id){
					$this->cookingSelected($order_id);
				}		
				$_response['success'] = true;
			}
			echo json_encode($_response);
			die();		
		}else{
			$this->cookingSelected($order_id);
		}			
		redirect('app/order/view?view='.$view.'&page='.$page);
	}
	
	function cookingSelected($order_id)
	{
		$this->db->where('id = ', $order_id)
				 ->update('ci_order', array(
					'order_status' => 'cooking'
				));		
	}
	//Finished Function
	/*============================= Finished function =============================*/
	function finished($order_id = false)
	{
		$page = $this->input->get('page');
		$view = $this->input->get('view');

		if( empty($order_id) ){
			$_post = $this->input->post();

			$_response['success'] = false;
			if( empty($_post) ){
				$_response['error'] = 'You must select row to receive.';
			}else{
				foreach($_post['selected'] as $order_id){
					$this->finishedSelected($order_id);
				}		
				$_response['success'] = true;
			}
			echo json_encode($_response);
			die();		
		}else{
			$this->finishedSelected($order_id);
		}	

		redirect('app/order/view?view='.$view.'&page='.$page);
	}

	function finishedSelected($order_id)
	{
		$this->db->where('id = ', $order_id)
				 ->update('ci_order', array(
					'order_status' => 'finished'
				));		
	}
	/*============================= Remove function =============================*/	
	function remove($id = false) // change item_status to deleted
	{
		parent::remove($id);
		redirect('app/order');
		//editing
	}
}