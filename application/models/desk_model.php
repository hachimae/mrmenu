<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Desk_model extends APP_Model
{
	var $table = 'ci_desk',
		$column = array(
			'id' => 'id',
			'name' => 'name',
			'description' => 'description',
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