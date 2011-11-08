<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Category extends APP_Controller 
{
	var $form = array();
	
	function __construct()
	{
		parent::__construct();

		$this->load->model('category_model', 'category');
		$this->setModel( $this->category );

		// set list category type
		$this->model->type = 'parent';

		$this->form = array(
			'name' => array( 'type'=>'text', 'label'=>'Name (TH)', 'validate'=>'required', 'class'=>'required' ),
			'name_en' => array( 'type'=>'text', 'label'=>'Name (EN)', 'validate'=>'required', 'class'=>'required' ),
			'description' => array( 'type'=>'textarea', 'label'=>'Description (TH)' ),
			'description_en' => array( 'type'=>'textarea', 'label'=>'Description (EN)' ),
			'parent_id' => array( 'type'=>'select', 'label'=>'Parent Category', 'data'=>$this->model->listCategory() ),
			'userfile' => array( 'type'=>'media', 'label'=>'Category thumb' ),
			);
	}

	function index()
	{
		$page = $this->input->get('page');
		$page = !empty($page) ? $page : 1;

		$this->setContent('title', 'Category Management');

		$tools = array(
			'add'	 => array( 'class'=>'button', 'name'=>'Add', 'action'=>'/app/category/add' ),
			'edit'	 => array( 'class'=>'button', 'name'=>'Edit', 'action'=>'/app/category/edit' ),
			'remove' => array( 'class'=>'button', 'name'=>'Remove', 'action'=>'/app/category/remove', 'event'=>'makeRemove()' ),
			);

		$col = array(
			'id'	 => array( 'type'=>'normal', 'name'=>'ID', 'class'=>'col-id' ),
			'parent_id'	 => array( 'type'=>'normal', 'name'=>'Parent ID', 'class'=>'col-id' ),
			'name'	 => array( 'type'=>'normal', 'name'=>'Name (TH)', 'class'=>'col-name' ),
			'name_en'	 => array( 'type'=>'normal', 'name'=>'Name (EN)', 'class'=>'col-name' ),
			);

		$data = $this->model->getPage($page, 3);

		$this->_setSearch('/app/category', array( 'name', 'name_en' ));

		$this->_setListTools( $tools );
		$this->_setListCol( $col );
		$this->_setListData( $data );
		$this->_setListPage( 'app/category', $this->model->countAll(), 3 );

		$this->_list();
	}

	function add()
	{
		$this->setContent('title', 'Add New Category');
		$id = parent::add(site_url('app/category/update'), $this->form);
	}

	function edit($id = false)
	{
		if( $id==false ){
			show_404();
			die();
		}

		// get row data
		$data = $this->model->getRow($id);

		// get media
		$thumbnail = $this->model->getMedia($id, 'ci_category');

		$this->form['name']['value'] = $data['name'];
		$this->form['name_en']['value'] = $data['name_en'];
		$this->form['description']['value'] = $data['description'];
		$this->form['description_en']['value'] = $data['description_en'];
		$this->form['parent_id']['value'] = $data['parent_id'];
		$this->form['userfile']['value'] = $thumbnail;
		$this->form['id'] = array( 'type'=>'hidden', 'value'=>$id );

		$this->setContent('title', 'Edit Category');
		$this->setContent('disable_id', $id);
		$id = parent::edit(site_url('app/category/update'), $this->form, $id);
	}

	function update()
	{
		// filter post
		$data = array(
			'name' => $this->input->post('name'),
			'name_en' => $this->input->post('name_en'),
			'description' => $this->input->post('description'),
			'description_en' => $this->input->post('description_en'),
			'parent_id' => $this->input->post('parent_id'),
			);

		if( $this->input->post('id') ){
			$data['id'] = $this->input->post('id');
		}

		$menu_id = parent::update($data);

		// update level_path
		$this->model->update_levelPath($menu_id, $this->input->post('parent_id'));

		// check thumbnail
		$this->_upload($menu_id, 'ci_category');

		redirect('app/category');
	}

	function remove($id = false)
	{
		parent::remove($id);
		redirect('app/category');
	}

}