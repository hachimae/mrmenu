<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Menu_model extends APP_Model
{
	var $table = 'ci_menu',
		$column = array(
			'id' => 'id',
			'category_id' => 'category_id',
			'name' => 'name',
			'description' => 'description',
			'price' => 'price',
			'outofstock' => 'outofstock',
			'modified_date' => 'modified_date',
			),
		$perpage = 3;

    function __construct()
    {
        // Call the Model constructor
        parent::__construct($this->table, $this->field);
    }

}

?>