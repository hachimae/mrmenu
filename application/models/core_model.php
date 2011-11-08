<?php if( !defined(BASEPATH) ) exit('');

class Core_Model extends CI_Model
{
	var $table,
		$column;

	function load($table_name, $column, $page = 1)
	{
		$this->table = $table_name;
		
	}

}

?>