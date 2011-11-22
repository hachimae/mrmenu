<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class App extends MY_Controller
{
	var $template = 'admin/main.html';
	var $navigator = array();
	var $perpage = 3;

	function __construct()
	{
		parent::__construct();

		// check before run another action
		if (!$this->auth->is_logged_in()) {
			redirect('/auth/login/');
		}else{
			$this->data['user']['id']	= $this->auth->get_user_id();
			$this->data['user']['name']	= $this->auth->get_username();
		}

		$this->load->model('desk');

		$this->data['shop'] = $this->shop->data( $this->data['user']['id'] );
		$this->data['shop_id'] = $this->data['shop']['id'];
	}

	function index()
	{
		// :o uhhh ?
	}

	function action_dash()
	{
		$this->twig->render(
			$this->template,
			$this->data
			);
	}

	function action_desk($action = 'list', $key = false)
	{
		$this->data['page'] = array(
			'title' => 'Desk management'
			);
		$this->data['editpath'] = '/app/desk/edit/';
		$this->data['removepath'] = '/app/desk/remove/';

		switch($action)
		{
			case 'list':
				$this->template = 'admin/list.html';

				// set list tools
				$this->set_tools(array(
					'add' => array( 'name'=>'Add', 'action'=>'/app/desk/add' ),
					'remove' => array( 'name'=>'Remove', 'action'=>'/app/desk/remove', 'event'=>'makeRemove' ),
					'print' => array( 'name'=>'Print', 'action'=>'/app/print', 'event'=>'makePrint' ),
					));

				// set display column
				$this->set_column(array(
					'name' => array( 'name'=>'Desk name', 'class' => 'col-name' ),
					));

				// config page
				$this->desk->init(array('perpage'=>$this->perpage));
				$config['base_url'] = '/app/desk?';
				$config['total_rows'] = $this->desk->all();
				$config['per_page'] = $this->perpage; 
				$config['page_query_string'] = true; 
				$this->pagination->initialize($config);
				$this->data['pagination']['html'] = $this->pagination->create_links(); 

				// set data into grid
				$this->load_data( $this->desk, $this->data['shop_id'] );

				break;
			case 'edit':
				$id = $key;
				$type = 'Edit';
				$this->data['form']['id'] = array( 'type'=>'hidden' );

				if( $id!=false ){
					$data = $this->desk->get($id);
				}
			case 'add':
				$this->template = 'admin/form.html';
				$this->data['page']['type'] = isset($type) ? $type : 'Add';
				
				// define form
				$this->data['action'] = '/app/desk/update';
				$this->data['form']['name'] = array( 'type'=>'text', 'label'=>'Name' );
				$this->data['form']['description'] = array( 'type'=>'textarea', 'label'=>'Description' );

				$data = isset($data) ? $data : false;
				if( $data!=false ){
					foreach($data as $key=>$value){
						$this->data['form'][$key]['value'] = $value;
					}
				}

				break;
			case 'remove':
				$response['success'] = true;
				$this->desk->remove($key);
				header('location: /app/desk');
				die( json_encode($response) );
				break;

			case 'update':
				$response['success'] = false;
				$post = $this->input->post();
				if( $post!=false ){
					$id = $this->input->post('id');

					if( $id ){
						// update
						$this->desk->edit($id, $post);
						$response['success'] = true;
					}else{
						// insert
						$this->desk->insert($this->data['shop_id'], $post);
						$response['success'] = true;
					}
				}

				header('location: /app/desk');
				die( json_encode($response) );
				break;
		}

		$this->twig->render(
			$this->template,
			$this->data
			);	
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */