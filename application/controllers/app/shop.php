<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Shop extends APP_Controller 
{
	
	function __construct()
	{
		parent::__construct();

		$this->load->model('shop_model', 'shop');
		$this->setModel( $this->shop );
	}

	// get shop information
	// display form
	function index()
	{
		$this->setContent('title', 'Restaurant Information');

		parent::add( '/app/shop/update', array(
			'name' => array( 'type'=>'text', 'label'=>'Name (TH)', 'validate'=>'required', 'class'=>'required', 'value'=>$this->shopData['name'] ),
			'name_en' => array( 'type'=>'text', 'label'=>'Name (EN)', 'validate'=>'required', 'class'=>'required', 'value'=>$this->shopData['name_en'] ),
			'detail' => array( 'type'=>'textarea', 'label'=>'Detail (TH)', 'validate'=>'required', 'class'=>'required', 'value'=>$this->shopData['detail'] ),
			'detail_en' => array( 'type'=>'textarea', 'label'=>'Detail (EN)', 'validate'=>'required', 'class'=>'required', 'value'=>$this->shopData['detail_en'] ),
			'userfile' => array( 'type'=>'media', 'label'=>'Logo', 'value'=>$thumbnail = $this->model->getMedia($this->shopId, 'ci_shop') ),
			'charge' => array( 'type'=>'float', 'label'=>'Charge (%)', 'validate'=>'required', 'value'=>$this->shopData['charge'] ),
			'vat' => array( 'type'=>'float', 'label'=>'VAT (%)', 'validate'=>'required', 'value'=>$this->shopData['vat'] ),
			'cess' => array( 'type'=>'float', 'label'=>'CESS (%)', 'validate'=>'required', 'value'=>$this->shopData['cess'] ),
			) );
	}

	function update()
	{
		$data = $this->input->post();
		$this->model->updateRow($data, $this->shopId);

		$this->_upload($this->shopId, 'ci_shop');

		redirect('/app/shop');
	}

}