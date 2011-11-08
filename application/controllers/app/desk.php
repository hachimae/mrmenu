<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Desk extends APP_Controller 
{
	var $form = array(
			'name' => array( 'type'=>'text', 'label'=>'Name (TH)', 'validate'=>'required', 'class'=>'required' ),
			'name_en' => array( 'type'=>'text', 'label'=>'Name (EN)', 'validate'=>'required', 'class'=>'required' ),
			'description' => array( 'type'=>'textarea', 'label'=>'Description' ),
			);
	
	function __construct()
	{
		parent::__construct();

		$this->load->model('desk_model', 'desk');
		$this->setModel( $this->desk );
	}

	function index()
	{
		$page = $this->input->get('page');
		$page = !empty($page) ? $page : 1;

		$this->setContent('title', 'Desk Management');

		$tools = array(
			'add'	 => array( 'class'=>'button', 'name'=>'Add', 'action'=>'/app/desk/add' ),
			'edit'	 => array( 'class'=>'button', 'name'=>'Edit', 'action'=>'/app/desk/edit' ),
			'remove' => array( 'class'=>'button', 'name'=>'Remove', 'action'=>'/app/desk/remove', 'event'=>'makeRemove()' ),
			'print'	 => array( 'class'=>'button', 'name'=>'Print', 'action'=>'/app/desk/printqr', 'event'=>'makePrint()' ),
			);
		$col = array(
			'id'	 => array( 'type'=>'normal', 'name'=>'ID', 'class'=>'col-id' ),
			'name'	 => array( 'type'=>'normal', 'name'=>'Name (TH)', 'class'=>'col-name' ),
			'name_en' => array( 'type'=>'normal', 'name'=>'Name (EN)', 'class'=>'col-name' ),
		
			);
		$data = $this->model->getPage($page, 3);

		$this->_setSearch('/app/desk', array( 'name', 'name_en' ));

		$this->_setListTools( $tools );
		$this->_setListCol( $col );
		$this->_setListData( $data );
		$this->_setListPage( 'app/desk', $this->model->countAll(), 3 );

		$this->_list();
	}

	function add()
	{
		$this->setContent('title', 'Add New Desk');
		parent::add(site_url('app/desk/update'), $this->form);
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
		$this->form['description']['value'] = $data['description'];
		$this->form['id'] = array( 'type'=>'hidden', 'value'=>$id );

		$this->setContent('title', 'Edit Desk');
		parent::edit(site_url('app/desk/update'), $this->form, $id);
	}
	/*
	function update()
	{
		parent::update();
		redirect('app/desk');
	}
	*/
	function remove($id = false)
	{
		parent::remove($id);
		redirect('app/desk');
	}

	function printqr()
	{
		$size = 150;
		$url = 'http://something.com/api/restaurant/';
		$id = $this->input->get('id');

		$this->db
			 ->select('id, shop_id, name AS name_th')
			 ->where_in('id', $id);
		$query = $this->db->get('ci_desk');
		if( $query->num_rows()<=0 ){
			echo 'Error';
		}else{
			foreach( $query->result_array() as $row ){
				$chl = $url.$row['shop_id'].'/table/'.$row['id'];
				echo '<div style="width: '.$size.'px; padding: 2px; border: solid 2px #CCC; margin: 10px 10px 0 0; float: left;">';
				echo '<img src="http://chart.apis.google.com/chart?chs='.$size.'x'.$size.
					 '&cht=qr&chld=L|0&chl='.$chl.'" alt="'.htmlspecialchars($row['name_th']).'" style="width: '.$size.'px; height: '.$size.'px;" />';
				echo '<strong style="display: block; padding: 5px 0; text-align: center; overflow: hidden; height: 18px; line-height: 18px; font-size: 12px;">'.$row['name_th'].'</strong>';
				echo '</div>';
			}
			echo '<script type="text/javascript">window.print();</script>';
		}
	}

}