<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Option extends APP_Controller 
{
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
	}

	function index()
	{
		$page = $this->input->get('page');
		$page = !empty($page) ? $page : 1;

		$this->setContent('title', 'Menu Option Management');

		$tools = array(
			'add'	 => array( 'class'=>'button', 'name'=>'Add', 'action'=>'/app/option/add' ),
			'edit'	 => array( 'class'=>'button', 'name'=>'Edit', 'action'=>'/app/option/edit' ),
			'remove' => array( 'class'=>'button', 'name'=>'Remove', 'action'=>'/app/option/remove', 'event'=>'makeRemove()' ),
			);
		$col = array(
			'id'		 => array( 'type'=>'normal', 'name'=>'ID', 'class'=>'col-id' ),
			'name'		 => array( 'type'=>'normal', 'name'=>'Name (TH)', 'class'=>'col-name' ),
			'name_en'	 => array( 'type'=>'normal', 'name'=>'Name (EN)', 'class'=>'col-name' ),
			'option_type' => array( 'type'=>'normal', 'name'=>'Option Type', 'class'=>'col-type' ),
			);
		$data = $this->model->getPage($page, 3);

		$this->_setListTools( $tools );
		$this->_setListCol( $col );
		$this->_setListData( $data );
		$this->_setListPage( 'app/option', $this->model->countAll(), 3 );

		$this->_list();
	}

	function add()
	{
		$this->setContent('title', 'Add New Menu Option');
		parent::add(site_url('app/option/update'), $this->form);
	}

	function edit($id = false)
	{
		if( $id==false ){
			show_404();
			die();
		}

		// get row data
		$data = $this->model->getRow($id);

		$this->form['name']['value'] = $data['name'];
		$this->form['name_en']['value'] = $data['name_en'];
		$this->form['option_type']['value'] = $data['option_type'];
		$this->form['id'] = array( 'type'=>'hidden', 'value'=>$id );

		// get meta data
		$this->form['option_meta']['value'] = $this->model->getMeta($id);

		$this->setContent('title', 'Edit Menu Option');
		parent::edit(site_url('app/option/update'), $this->form, $id);
	}

	function update()
	{
		// filter data
		// cause we have option_group and option_meta
		$data = array(
			'name' => $this->input->post('name'),
			'name_en' => $this->input->post('name_en'),
			'option_type' =>  $this->input->post('option_type'),
			'shop_id' => $this->shopId
			);
		if( $this->input->post('id') ){
			$data['id'] = $this->input->post('id');
		}

		// update option_group
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

		redirect('app/option');
	}

	function remove($id = false)
	{
		parent::remove($id);
		redirect('app/option');
	}

}