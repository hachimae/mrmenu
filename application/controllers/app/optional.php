<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Optional extends APP_Controller 
{
	var $menu_id, $option_id;
	var $form = array(
			'name' => array( 'type'=>'text', 'label'=>'Name (TH)', 'validate'=>'required', 'class'=>'required' ),
			'name_en' => array( 'type'=>'text', 'label'=>'Name (EN)', 'validate'=>'required', 'class'=>'required' ),
			'option_type' => array( 'type'=>'radio', 'label'=>'Option Type', 'validate'=>'required', 'class'=>'required', 'data'=>array('radio'=>'radio', 'checkbox'=>'checkbox'), 'value'=>'radio' ),
			'option_meta' => array( 'type'=>'price_value', 'label'=>'Option Values' ),
			);

	function __construct()
	{
		parent::__construct();

		$this->load->model('option_model', 'option');
		$this->setModel( $this->option );

		$this->master_template = 'admin/list.php';

		$this->menu_id = $this->input->get('ref_id');
		if( !is_numeric($this->menu_id) && !empty($this->menu_id) ){
			list( $this->menu_id, $this->option_id ) = explode('/', $this->menu_id);
		}
	}

	// list on parent module form
	function index()
	{
		if( $this->menu_id==false ){
			$data = false;
		}else{
			$data = $this->model->getOption($this->menu_id);
		}

		$this->setContent('title', false);
		
		if( !empty($this->menu_id) ){
			$tools = array(
				'add'	 => array( 'class'=>'button', 'name'=>'Add', 'action'=>'/app/optional/add?ref_id='.$this->menu_id ),
				'edit'	 => array( 'class'=>'button', 'name'=>'Edit', 'action'=>'/app/optional/edit?ref_id='.$this->menu_id ),
				'remove' => array( 'class'=>'button', 'name'=>'Remove', 'action'=>'/app/optional/remove?ref_id='.$this->menu_id ),
				);
		}else{
			$tools = array(
				'add'	 => array( 'class'=>'button', 'name'=>'Add', 'action'=>'#submit-first' ),
				);
		}

		$col = array(
			'id'	 => array( 'type'=>'normal', 'name'=>'ID', 'class'=>'col-id' ),
			'name' 	 => array( 'type'=>'normal', 'name'=>'Name', 'class'=>'col-name' ),
			'option_type'	 => array( 'type'=>'normal', 'name'=>'Type', 'class'=>'col-type' ),
			);
		
		$this->_setListTools( $tools );
		$this->_setListCol( $col );
		$this->_setListData( $data );

		$this->render();
	}

	function add()
	{
		$this->setContent('title', 'Add Menu Option');
		$this->master_template = 'admin/form.php';

		/* parent::add( '/app/optional/update', array(
			'option_id' => array( 'type'=>'select', 'label'=>'Option group', 'data'=>$this->model->allOption() ),
			'menu_id' => array( 'type'=>'hidden', 'value'=>$this->menu_id ),
			)); */
		$this->form['menu_id'] = array( 'type'=>'hidden', 'value'=>$this->menu_id );
		parent::add(site_url('app/optional/update'), $this->form);
	}

	function edit()
	{
		$this->setContent('title', 'Edit Menu Option');
		$this->master_template = 'admin/form.php';

		// check option id
		if( empty($this->option_id) ){
			return show_404();
		}

		// get price data
		$data = $this->model->getRow($this->option_id);
		// $data = $this->model->optionData($this->option_id);

		$this->form['name']['value'] = $data['name'];
		$this->form['name_en']['value'] = $data['name_en'];
		$this->form['option_type']['value'] = $data['option_type'];
		$this->form['id'] = array( 'type'=>'hidden', 'value'=>$this->option_id );
		$this->form['menu_id'] = array( 'type'=>'hidden', 'value'=>$this->menu_id );

		// get meta data
		$this->form['option_meta']['value'] = $this->model->getMeta($this->option_id);

		/* parent::edit( '/app/price/update', array(
			'size' => array( 'type'=>'select', 'label'=>'Size', 'data'=>$this->_getSize(), 'value'=>$data['size'] ),
			'price' => array( 'type'=>'text', 'label'=>'Price', 'validate'=>'required', 'value'=>$data['price'] ),
			'menu_id' => array( 'type'=>'hidden', 'value'=>$this->menu_id ),
			'option_id' => array( 'type'=>'hidden', 'value'=>$this->option_id ),
			), $this->option_id ); */
		parent::edit(site_url('app/optional/update'), $this->form, $this->option_id);
	}

	function remove()
	{
		$response['success'] = false;

		if( empty($this->option_id) ){
			parent::remove($this->option_id);
			$response['success'] = true;
		}else{
			parent::remove($this->option_id);
			$response['success'] = true;
		}

		die( json_encode($response) );
	}

	function update()
	{
		// redefine data
		// update by $this->model->updateRow(data, id)
		$response = array('success'=>false);

		// post = array( size, price. menu_id[, option_id] )
		$data = array(
			'name' => $this->input->post('name'),
			'name_en' => $this->input->post('name_en'),
			'option_type' =>  $this->input->post('option_type'),
			'shop_id' => $this->shopId,
			'menu_id' => $this->input->post('menu_id'),
			);
			
		if( $this->input->post('id') ){
			$data['id'] = $this->input->post('id');
		}

		if( empty($data['name']) ){
			$response['error'] = 'Missing param.';
			die( json_encode($response) );
		}

		$group_id = parent::update($data);

		// update option_meta
		if( $group_id ){
			$_meta_value = $this->input->post('meta_id');
			$_meta_name = $this->input->post('meta_name');
			$_meta_type = $this->input->post('meta_pricetype');
			$_meta_price = $this->input->post('meta_price');
			foreach($_meta_name as $key=>$value){
				if( empty($value) ) continue;

				$temp = array(
					'name' => $value,
					'price_type' => $_meta_type[$key],
					'price_value' => $_meta_price[$key],
					);
				
				if( isset($_meta_value[$key]) ) {
					$temp['id'] = $_meta_value[$key];
				}

				$meta_data[] = $temp;
			}

			$this->model->updateMeta($group_id, $meta_data);
		}

		$id = $this->db->insert_id();
		
		$response['success'] = true;
		$response['key'] = empty($post['option_id']) ? $group_id : $post['option_id'];
		die( json_encode($response) );
	}

}
