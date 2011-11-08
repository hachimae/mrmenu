<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Price extends APP_Controller 
{
	var $menu_id, $price_id;

	function __construct()
	{
		parent::__construct();

		$this->load->model('price_model', 'price');
		$this->setModel( $this->price );

		$this->master_template = 'admin/list.php';

		$this->menu_id = $this->input->get('ref_id');
		if( !is_numeric($this->menu_id) && !empty($this->menu_id) ){
			list( $this->menu_id, $this->price_id ) = explode('/', $this->menu_id);
		}
	}

	// list on parent module form
	function index()
	{
		if( $this->menu_id==false ){
			$data = false;
		}else{
			$data = $this->model->getPrice($this->menu_id);
		}

		$this->setContent('title', false);
		
		if( !empty($this->menu_id) ){
			$tools = array(
				'add'	 => array( 'class'=>'button', 'name'=>'Add', 'action'=>'/app/price/add?ref_id='.$this->menu_id ),
				'edit'	 => array( 'class'=>'button', 'name'=>'Edit', 'action'=>'/app/price/edit?ref_id='.$this->menu_id ),
				'remove' => array( 'class'=>'button', 'name'=>'Remove', 'action'=>'/app/price/remove?ref_id='.$this->menu_id ),
				);
		}else{
			$tools = array(
				'add'	 => array( 'class'=>'button', 'name'=>'Add', 'action'=>'#submit-first' ),
				);
		}

		$col = array(
			'id'	 => array( 'type'=>'normal', 'name'=>'ID', 'class'=>'col-id' ),
			'size' 	 => array( 'type'=>'normal', 'name'=>'Size', 'class'=>'col-category' ),
			'price'	 => array( 'type'=>'normal', 'name'=>'Price', 'class'=>'col-name' ),
			);
		
		$this->_setListTools( $tools );
		$this->_setListCol( $col );
		$this->_setListData( $data );

		$this->render();
	}

	function add()
	{
		$this->setContent('title', 'Add Price');
		$this->master_template = 'admin/form.php';

		parent::add( '/app/price/update', array(
			'size' => array( 'type'=>'select', 'label'=>'Size', 'data'=>$this->_getSize() ),
			'price' => array( 'type'=>'text', 'label'=>'Price', 'validate'=>'required' ),
			'menu_id' => array( 'type'=>'hidden', 'value'=>$this->menu_id ),
			) );
	}

	function edit()
	{
		$this->setContent('title', 'Add Price');
		$this->master_template = 'admin/form.php';

		// check price id
		if( empty($this->price_id) ){
			return show_404();
		}

		// get price data
		$data = $this->model->priceData($this->price_id);

		var_dump( $data );

		parent::edit( '/app/price/update', array(
			'size' => array( 'type'=>'select', 'label'=>'Size', 'data'=>$this->_getSize(), 'value'=>$data['size'] ),
			'price' => array( 'type'=>'text', 'label'=>'Price', 'validate'=>'required', 'value'=>$data['price'] ),
			'menu_id' => array( 'type'=>'hidden', 'value'=>$this->menu_id ),
			'price_id' => array( 'type'=>'hidden', 'value'=>$this->price_id ),
			), $this->price_id );
	}

	function remove()
	{
		$response['success'] = false;

		if( empty($this->price_id) ){
			$response['error'] = 'Missing param';
		}else{
			parent::remove($this->price_id);
			$response['success'] = true;
		}

		die( json_encode($response) );
	}

	function update()
	{
		// redefine data
		// update by $this->model->updateRow(data, id)
		$response = array('success'=>false);

		// post = array( size, price. menu_id[, price_id] )
		$post = $this->input->post();
		if( $post==false || empty($post['price']) ){
			$response['error'] = 'Missing param.';
			die( json_encode($response) );
		}

		$response['action'] = empty($post['menu_id']) ? 'insert-price' : 'edit-price';
		
		$post['price_id'] = $this->input->post('price_id');
		$data = array(
			'menu_id' => $post['menu_id'],
			'size_id' => $post['size'],
			'price' => $post['price'],
			);
		$id = $this->model->updateRow($data, $post['price_id']);
		
		$response['success'] = true;
		$response['key'] = empty($post['price_id']) ? $id : $post['price_id'];
		die( json_encode($response) );
	}

	function _getSize()
	{
		$this->db
			 ->select('id, name')
			 ->where('shop_id = ', $this->shopId)
			 ->where('item_status = ', 'published');
		$query = $this->db->get('ci_menusize');

		if( $query->num_rows()<=0 ){
			return false;
		}else{
			foreach($query->result_array() as $row){
				$data[$row['id']] = $row['name'];
			}
			return $data;
		}
	}

}
