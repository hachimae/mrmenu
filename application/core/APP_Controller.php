<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class APP_Controller extends MY_Controller {

	var $model, $shopData, $shopId;

	function __construct()
	{
		parent::__construct();
		
		if( !$this->auth->is_logged_in() ){
			redirect('/app/login/');
		}else{
			$this->data['user']['id']	= $this->auth->get_user_id();
			$this->data['user']['name']	= $this->auth->get_username();
		}

		// defined list var
		$this->data['content']['listTools'] = false;
		$this->data['content']['listCol'] 	= false;
		$this->data['content']['listData'] 	= false;
		$this->data['content']['listPage'] 	= false;

		$this->setContent('searchKeyword', $this->input->get('q'));

		// is there any error ?
		$this->formError = $this->session->flashdata('form_error');
		$this->formValue = $this->session->flashdata('form_value');
		$this->setContent('formError', $this->formError);
		$this->setContent('formValue', $this->formValue);
	}

	function getShop()
	{
		$this->shopData = $this->model->getShop($this->data['user']['id']);
		$this->shopId = $this->shopData!=false ? $this->shopData['id'] : false;
	}

	function setModel($model)
	{
		$this->model = $model;

		// get shop data
		$this->getShop();
	}

	function setFormAction($action)
	{
		$this->setContent('form', array( 'action'=>$action ) );
	}

	function setFormField($option)
	{
		$this->setContent('form', array( 'field'=>$option ) );
	}

	function add($action, $field)
	{
		$field = $this->_checkPrevValue($field);
		$this->setContent('form_type', 'add');
		$this->_displayForm($action, $field);
	}

	function edit($action, $field, $id)
	{
		$field = $this->_checkPrevValue($field);
		$this->setContent('form_type', 'edit');
		$this->setContent('current_id', $id);
		$this->_displayForm($action, $field);
	}

	function update($data = false)
	{
		$valid = $this->_checkValidate();
		if( !$valid ){
			redirect($_SERVER['HTTP_REFERER']);
			die();
		}

		$post = $data==false ? $this->input->post() : $data;

		if( $post==false ){
			show_404();
			die();
		}

		$post['shop_id'] = $this->shopId;

		if( !empty($post['id']) ){
			$this->model->updateRow($post, $post['id']);
			return $post['id'];
		}else{
			$this->model->newRow($post);
			return $this->db->insert_id();
		}
	}

	function remove($id = false)
	{
		//In case remove many selection
		if( empty($id) ){
			$_post = $this->input->post();
			$_response['success'] = false;
			if( empty($_post) ){
				$_response['error'] = 'You must select row to remove.';
			}else{
				foreach($_post['selected'] as $id){
					$this->model->removeRow($id);
				}
				$_response['success'] = true;
			}

			echo json_encode($_response);
			die();
		//In case remove only one
		}else{
			$this->model->removeRow($id);
		}
	}

	function _checkPrevValue($field)
	{
		if( !empty($this->formValue) ){
			foreach($this->formValue as $name=>$value){
				$field[$name]['value'] = $value;
			}
		}

		return $field;
	}

	// display list data
	function _list()
	{
		$this->content_template = 'admin/list'; // views>admin>list
		$this->render();
	}

	function _setSearch($action = false, $field = false)
	{
		$this->setContent('search_enable', true);
		$this->setContent('search_action', $action);
		$this->setContent('search_field', $field);

		return false;
	}

	/*
	| option = array(
	|	'col-field' => array(
	|		'type'  => 'normal|checkbox|empty'
	|		'class' => 'class-string'
	|		)
	|	);
	*/
	function _setListCol($option)
	{
		$check 		= array( 'check' => array( 'type'=>'check', 'class'=>'col-check' ) );
		$modified 	= array( 'modified_date' => array( 'type'=>'normal', 'name'=>'Modified Date', 'class'=>'col-date' ) );
		$tools 		= array( 'tools' => array( 'type'=>'tools', 'class'=>'col-tools' ) );

		$option_array = array_merge( $check, $option, $modified, $tools );

		$this->setContent('listCol', $option_array);
	}

	function _setListData($data)
	{
		$this->setContent('listData', $data);
	}

	function _setListTools($option)
	{
		$this->setContent('listTools', $option);
	}

	function _setListPage($base_url, $total, $per_page = 10)
	{
		// determine base url
		$get = $this->input->get();
		$page_url = site_url($base_url).'?';
		if( $get!=false ){
			foreach($get as $key=>$val){
				$page_url .= '&'.$key.'='.$val;
			}
		}

		$config = array(
			'base_url' => $page_url,
			'total_rows' => $total,
			'per_page' => $per_page,
			'page_query_string' => true,
			'query_string_segment' => 'page'
			);
		$this->pagination->initialize($config);
		$this->setContent('listPage', $this->pagination->create_links());
	}

	function _displayForm($action, $field)
	{
		$this->setFormAction($action);
		$this->setFormField($field);

		$this->content_template = 'admin/form';
		$this->render();
	}

	function _upload($id, $table = 'ci_menu')
	{
		$config['upload_path'] = './media/uploads/';
		$config['allowed_types'] = 'gif|jpg|png';
		$config['max_size']	= '100';
		$config['max_width']  = '1024';
		$config['max_height']  = '768';

		$this->load->library('upload', $config);
		if(!$this->upload->do_upload()){
			$error = $this->upload->display_errors();
		}else{
			$data = $this->upload->data();
			$this->_insertMedia($id, $data, $table);
		}
	}

	function _insertMedia($id, $data, $table = 'ci_menu')
	{
		$data = array(
			'ref_id' => $id,
			'ref_table' => $table,
			'is_thumb' => 'yes',
			'name' => $data['raw_name'],
			'file' => $data['file_name'],
			'size' => $data['file_size'],
			'type' => $data['file_type'],
			'extension' => $data['file_ext']
			);

		// check ref id korn na
		$this->db->where('ref_id = ', $id);
		if( $this->db->count_all_results('ci_media') ){
			$this->db->where('ref_id = ', $id);
			$this->db->update('ci_media', $data);
		}else{
			$this->db->insert('ci_media', $data);
		}
	}

	function _checkValidate()
	{
		if( !isset($this->form) || !$this->input->post() ){
			return false;
		}

		// generate varidate
		foreach($this->form as $key=>$data){
			if( !isset($data['validate']) ){ continue; }
			$this->form_validation->set_rules($key, $data['label'], $data['validate']);
		}

		if( $this->form_validation->run() ){
			return true;
		}else{
			$this->session->set_flashdata('form_error', validation_errors());
			
			$_post = $this->input->post();
			unset($_post['action']);
			$this->session->set_flashdata('form_value', $_post);
			return false;
		}
	}

}

?>