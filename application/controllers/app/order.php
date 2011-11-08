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

	function index()
	{
		$page = $this->input->get('page');
		$page = !empty($page) ? $page : 1;

		$this->setContent('title', 'Order Management');
		//Button
		$tools = array(
			 'add'	 => array( 'class'=>'button', 'name'=>'Add', 'action'=>'/app/order/add' ),
			// 'edit'	 => array( 'class'=>'button', 'name'=>'Edit', 'action'=>'/app/size/edit' ),
			'cooking' => array( 'class'=>'button', 'name'=>'Cooking', 'action'=>'/app/order/cooking', 'event'=>'makeCooking()' ),
			'cancel' => array( 'class'=>'button', 'name'=>'Cancel', 'action'=>'/app/order/cancel', 'event'=>'makeCancel()' ),
			);
		//Content
		$col = array(
			'id'	 => array( 'type'=>'normal', 'name'=>'ID', 'class'=>'col-id' ),
			'transaction_id'	 => array( 'type'=>'order-detail', 'name'=>'Order ID', 'class'=>'col-name' ),
			'order_status'	 => array( 'type'=>'normal', 'name'=>'Status', 'class'=>'col-name' ),
			);
		//Pagination
		$data = $this->model->getPage($page,3);

		$this->_setListTools( $tools );
		$this->_setListCol( $col );
		$this->_setListData( $data );
		$this->_setListPage( 'app/order', $this->model->countAll(), 3);

		$this->_list();
	}
    
    function detail($order_id = false)
    {
        // 1) get order ID
        // 2) get menu data
        $this->data['order'] = $this->model->get_orderDetail($order_id);
        $order = $this->data['order'];
        
        //print_r($order);
        //----------------------------------------
		$page = $this->input->get('page');
		$page = !empty($page) ? $page : 1;

		$this->setContent('title', 'Order : '.$order['detail']['transaction_id']);
		//Button
		$tools = array(
		    
			'change' => array( 'class'=>'button', 'name'=>'Change', 'action'=>'/app/order/change', 'event'=>'makeChange()' ),
			'cancel' => array( 'class'=>'button', 'name'=>'Delete', 'action'=>'/app/order/delete', 'event'=>'makeCancel()' ),
			);
		//Content
		$col = array(
			'id'	 => array( 'type'=>'normal', 'name'=>'ID', 'class'=>'col-id' ),
			'name'	 => array( 'type'=>'normal', 'name'=>'Name', 'class'=>'col-name' ),
			'quantity'	 => array( 'type'=>'normal', 'name'=>'Quantity', 'class'=>'col-name' ),
			'price'	 => array( 'type'=>'normal', 'name'=>'Price', 'class'=>'col-name' ),
			'total'	 => array( 'type'=>'normal', 'name'=>'Total', 'class'=>'col-name' ),
			'dish_status'	 => array( 'type'=>'normal', 'name'=>'Dish Status', 'class'=>'col-name' ),
			);
		//Pagination
		$data = $this->model->getPage($page, 1);

		//$this->_setListTools( $tools );
		$this->_setListCol( $col );
		$this->_setListData( $order['dish'] );
		$this->_setListPage( 'app/order/detail/'.$order_id, $this->model->countAll(), 1);

		$this->_list();
        //----------------------------------------
        //var_dump(  $order['dish']);
        
        // 3) update status button
    }
    function add(){
		$this->setContent('title', 'Add New Order');

		// add js/css
		$this->template->add_js('media/js/script-form.js');
		$this->template->add_js('media/lib/fancybox/jquery.fancybox-1.3.4.pack.js');
		$this->template->add_css('media/lib/fancybox/jquery.fancybox-1.3.4.css');

		parent::add(site_url('app/order/update'), $this->form);  
    }
    function change($orderdish_id){
       // $this->data['order'] = $this->model->get_orderDetail($order_id);
        //$order = $this->data['order'];
        
        //$this->_list();
   	}
	//Change and Update
	function update()
	{
		parent::update();
		redirect('app/');
	}
	
	function cooking(){
	
	}

}