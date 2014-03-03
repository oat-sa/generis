<?php

/**
 *
 * @author lecaque
 *        
 */
class common_persistence_sql_MultipleInsertsSqlHelper extends common_persistence_sql_AbstractMultipleInsertsSqlHelper {
	

	/**
	 * 
	 * @param string $table
	 * @param array $columns
	 * @return string
	 */
	public function getFirstStaticPart($table , $columns = array()){
		$returnValue = 'INSERT INTO ' . $table .' ';
		if(!empty($columns)){
			$returnValue .= '(' . implode(',',$columns). ') VALUES ';
		}
		return $returnValue;
	}

	public function getValuePart($table, $columns = array(),$values = array()){
		if(!empty($values)){
			return ' (' . implode(',',$values). '),';
		}
		return '';
	}
	
	public function getEndStaticPart(){
		return '';
	}

}
?>