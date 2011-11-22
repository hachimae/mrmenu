<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class APP_Model extends MY_Model {

	var $table,
		$field;
	
	var $totalRow,
		$validate,
		$shopData,
		$shopId;

	function __construct($table = false, $field = false)
	{
		parent::__construct();

		if( !empty($table) ) $this->setTable($table);
		if( !empty($field) ) $this->setField($field);
	}

	function getShop($user_id)
	{
		$this->db
			 ->select('id, name, name_en, detail, detail_en, vat, charge, cess')
			 ->where('owner_id = ', $user_id);
		$query = $this->db->get('ci_shop');
		if( $query->num_rows()>0 ){
			$this->shopData = $query->row_array();
			$this->shopId = $this->shopData['id'];

			return $this->shopData;
		}else{
			return false;
		}
	}
	
	function getShopById($id)
	{
			$this->db
			 ->select('id, name, name_en, detail, detail_en, vat, charge, cess')
			 ->where('id = ', $id);
		$query = $this->db->get('ci_shop');
		if( $query->num_rows()>0 ){
			$this->shopData = $query->row_array();
			return $this->shopData;
		}else{
			return false;
		}	
	}
	
	function setTable($table)
	{
		$this->table = $table;
	}

	function setField($option)
	{
		$this->field = $option;
	}

	function countAll($filter = false)
	{
		$this->_createFilter($filter);
		$this->db->from( $this->table );

		$this->totalRow = $this->db->count_all_results();
		return $this->totalRow;
	}

	function getPage($page = 1, $perpage = 10, $filter = false, $order_key = "modified_date DESC")
	{
		//echo $this->table;
		
		$this->_createFilter($filter);

		$this->db->select( $this->field );

		$this->db->order_by( $order_key );

		$start = ($page-1)*$perpage;

		$query = $this->db->get( $this->table, $perpage, $start );
		if( $query->num_rows()<=0 ){
			return false;
		}

		foreach( $query->result_array() as $row ){
			$data[] = $row;
		}

		return $data;
	}

	function getRow($id)
	{
		$this->db
			 ->select( $this->field )
			 ->where('id = ', $id);
		$query = $this->db->get( $this->table );

		return $query->num_rows()>0 ? $query->row_array() : false;
	}

	function newRow($data)
	{
		// create created/modified date
		$data['created_date'] = $data['modified_date'] = date("Y-m-d H:i:s");
		//Insert into DB
		if( $this->db->insert( $this->table, $data ) ){
			return $this->db->insert_id();
		}
		
		return false;
	}

	function removeRow($id)
	{
		//Remove is just for update to 'deleted'
		return $this->updateRow( array('item_status'=>'deleted'), $id);
	}

	function updateRow($data, $id = false)
	{
		if( $id==false ){ 
			$result = $this->newRow( $data );
		}else{
			$data['modified_date'] = date("Y-m-d H:i:s");
			//Update to DB
			$this->db->where( 'id = ', $id );
			$result = $this->db->update( $this->table, $data);
		}

		return $result;
	}

	function _createFilter($filter)
	{
		// default filter
		//$this->db->where( 'item_status = ', 'published' );
		//$this->db->where( 'shop_id = ', $this->shopId );
		//Reverse filter
		//$this->db->where( 'item_status = ', 'deleted' );
		// determine $filter
		$q = $this->input->get('q');
		$col = $this->input->get('col');
		if( $q!=false && $col!=false ){
			foreach(explode(',', $col) as $field){
				$temp[] = $field." LIKE '%".$q."%'";
				// $this->db->or_like( $field, $q );
			}

			$filter[] = '('.implode(' OR ', $temp).')';
		}

		if( $filter!=false ){
			
			foreach($filter as $key=>$val){
				
				if( is_numeric($key) ){
					$this->db->where( $val );
				}else{
					$this->db->where( $key, $val );
				}
			}
		}
	}

}