<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Menu extends APP_Controller 
{
	var $form = array();
	
	function __construct()
	{
		parent::__construct();

		$this->load->model('menu_model', 'menu');
		$this->setModel( $this->menu );
		//Form on Edit Menu
		$this->form = array(
			'name' => array( 'type'=>'text', 'label'=>'Name (TH)', 'validate'=>'required', 'class'=>'required' ),
			'name_en' => array( 'type'=>'text', 'label'=>'Name (EN)', 'validate'=>'required', 'class'=>'required' ),
			'category_id' => array( 'type'=>'select', 'label'=>'Category', 'validate'=>'required', 'class'=>'required', 'data'=>$this->_getCategory() ),
			'description' => array( 'type'=>'textarea', 'label'=>'Description (TH)' ),
			'description_en' => array( 'type'=>'textarea', 'label'=>'Description (EN)' ),
			'userfile' => array( 'type'=>'media', 'label'=>'Thumbnail' ),
			'outofstock' => array( 'type'=>'radio', 'label'=>'Out of stock', 'data'=>array('yes'=>'Yes', 'no'=>'No'), 'value'=>'no' ),
			'price' => array( 'type'=>'text', 'label'=>'Price', 'validate'=>'required', 'class'=>'required' ),
			'option' => array( 'type'=>'child', 'label'=>'Option', 'action'=>'/app/optional?ref_id=' ),
			);
	}

	function index()
	{
		$page = $this->input->get('page');
		$page = !empty($page) ? $page : 1;

		$this->setContent('title', 'Menu Management');

		$tools = array(
			
			'add'	 => array( 'class'=>'button', 'name'=>'Add', 'action'=>'/app/menu/add' ),
			'edit'	 => array( 'class'=>'button', 'name'=>'Edit', 'action'=>'/app/menu/edit' ),
			'regain' => array( 'class'=>'button', 'name'=>'Regain', 'action'=>'/app/menu/regain' ),
			'remove' => array( 'class'=>'button', 'name'=>'Remove', 'action'=>'/app/menu/remove', 'event'=>'makeRemove()' ),
			);
		$col = array(
			'id'	 => array( 'type'=>'normal', 'name'=>'ID', 'class'=>'col-id' ),
			'category_id' => array( 'type'=>'normal', 'name'=>'Category', 'class'=>'col-category' ),
			'name'	 => array( 'type'=>'normal', 'name'=>'Name', 'class'=>'col-name' ),
			);
		$data = $this->model->getPage($page, 3);
		$this->_setSearch('/app/menu', array( 'name', 'name_en' ));
		//var_dump($data);
		$this->_setListTools( $tools );
		$this->_setListCol( $col );
		$this->_setListData( $data );
		$this->_setListPage( 'app/menu', $this->model->countAll(), 3 );

		$this->_list();
	}

	function add()
	{
		$this->setContent('title', 'Add New Menu');

		// add js/css
		$this->template->add_js('media/js/script-form.js');
		$this->template->add_js('media/lib/fancybox/jquery.fancybox-1.3.4.pack.js');
		$this->template->add_css('media/lib/fancybox/jquery.fancybox-1.3.4.css');

		parent::add(site_url('app/menu/update'), $this->form);
	}
	
	function regain(){
		$this->setContent('title', 'Regain Management');

		$tools = array(
			);
		$col = array(
			'id'	 => array( 'type'=>'normal', 'name'=>'ID', 'class'=>'col-id' ),
			'category_id' => array( 'type'=>'normal', 'name'=>'Category', 'class'=>'col-category' ),
			'name'	 => array( 'type'=>'normal', 'name'=>'Name', 'class'=>'col-name' ),
			);
		$data = $this->model->getPage($page, 3);
		//var_dump($data);
		$this->_setListTools( $tools );
		$this->_setListCol( $col );
		$this->_setListData( $data );
		$this->_setListPage( 'app/menu', $this->model->countAll(), 3 );

		$this->_list();
	}
	function edit($id = false)
	{
		if( $id==false ){
			show_404();
			die();
		}

		$this->form['option']['action'] .= $id;

		// get row data
		$data = $this->model->getRow($id);

		// get media
		$thumbnail = $this->model->getMedia($id);

		$this->setContent('title', 'Edit Menu');

		// add js/css
		$this->template->add_js('media/js/script-form.js');
		$this->template->add_js('media/lib/fancybox/jquery.fancybox-1.3.4.pack.js');
		$this->template->add_css('media/lib/fancybox/jquery.fancybox-1.3.4.css');

		$this->form['name']['value'] = $data['name'];
		$this->form['name_en']['value'] = $data['name_en'];
		$this->form['category_id']['value'] = $data['category_id'];
		$this->form['description']['value'] = $data['description'];
		$this->form['description_en']['value'] = $data['description_en'];
		$this->form['userfile']['value'] = $thumbnail;
		$this->form['price']['value'] = $data['price'];
		$this->form['outofstock']['value'] = $data['outofstock'];
		$this->form['id'] = array( 'type'=>'hidden', 'value'=>$id );

		parent::edit(site_url('app/menu/update'), $this->form, $id);
	}

	function update()
	{
		// filter post
		$data = array(
			'name' => $this->input->post('name'),
			'name_en' => $this->input->post('name_en'),
			'category_id' => $this->input->post('category_id'),
			'description' => $this->input->post('description'),
			'description_en' => $this->input->post('description_en'),
			'price' => $this->input->post('price'),
			'outofstock' => $this->input->post('outofstock'),
			);

		if( $this->input->post('id') ){
			$data['id'] = $this->input->post('id');
		}

		$menu_id = parent::update($data);

		// check thumbnail
		$this->_upload($menu_id, 'ci_menu');

		// check price (child)

		$action = $this->input->post('action');
		if( $action=='reload' ){
			redirect('app/menu/edit/'.$menu_id);
		}else{
			redirect('app/menu');
		}
	}

	function remove($id = false)
	{
		parent::remove($id);
		redirect('app/menu');
	}

	function _getCategory()
	{
		$this->load->model('category_model', 'category');
		return $this->category->listCategory($this->shopId);
	}

}