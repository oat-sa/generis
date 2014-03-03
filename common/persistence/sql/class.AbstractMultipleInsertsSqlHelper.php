<?php


/**
 *
 * @author lecaque
 *        
 */
abstract class common_persistence_sql_AbstractMultipleInsertsSqlHelper {
	
	/**
	 * 
	 * @param string $table
	 * @param array $columns
	 * @return array
	 */
	abstract public  function getFirstStaticPart($table , $columns = array());
	/**
	 * 
	 * @param string $table
	 * @param array $columns
	 * @param array $values
	 * @return array
	 */
	abstract public function getValuePart($table, $columns = array(), $values = array());
	
	/**
	 * @return array
	 */
	abstract public function getEndStaticPart();
	

}

?>