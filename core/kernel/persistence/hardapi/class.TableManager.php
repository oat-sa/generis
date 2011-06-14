<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 05.05.2011, 10:10:07 with ArgoUML PHP module
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package core
 * @subpackage kernel_persistence_hardapi
 */

if (0 > version_compare(PHP_VERSION, '5')) {
	die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1--5a63b0fb:12f72879be9:-8000:00000000000015A3-includes begin
// section 127-0-1-1--5a63b0fb:12f72879be9:-8000:00000000000015A3-includes end

/* user defined constants */
// section 127-0-1-1--5a63b0fb:12f72879be9:-8000:00000000000015A3-constants begin
// section 127-0-1-1--5a63b0fb:12f72879be9:-8000:00000000000015A3-constants end

/**
 * Short description of class core_kernel_persistence_hardapi_TableManager
 *
 * @access public
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package core
 * @subpackage kernel_persistence_hardapi
 */
class core_kernel_persistence_hardapi_TableManager
{
	// --- ASSOCIATIONS ---


	// --- ATTRIBUTES ---

	/**
	 * Short description of attribute name
	 *
	 * @access protected
	 * @var string
	 */
	protected $name = '';

	/**
	 * Short description of attribute _tables
	 *
	 * @access private
	 * @var array
	 */
	private static $_tables = array();

	// --- OPERATIONS ---

	/**
	 * Short description of method __construct
	 *
	 * @access public
	 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
	 * @param  string name
	 * @return mixed
	 */
	public function __construct($name)
	{
		// section 127-0-1-1--5a63b0fb:12f72879be9:-8000:00000000000015AA begin

		if(count(self::$_tables) == 0){
			$dbWrapper = core_kernel_classes_DbWrapper::singleton();
			$result = $dbWrapper->execSql('SELECT DISTINCT `table` FROM class_to_table');
			while(!$result->EOF){
				self::$_tables[] = $result->fields['table'];
				$result->moveNext();
			}
		}
		if(!preg_match("/^_[0-9a-zA-Z\-_]{4,}$/", $name)){
			throw new core_kernel_persistence_hardapi_Exception("Dangerous table name '$name' . Table name must begin by a underscore, followed  with only alphanumeric, - and _ characters are allowed");
		}
		$this->name = $name;
		
		// section 127-0-1-1--5a63b0fb:12f72879be9:-8000:00000000000015AA end
	}

	/**
	 * Short description of method exists
	 *
	 * @access public
	 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
	 * @return boolean
	 */
	public function exists()
	{
		$returnValue = (bool) false;

		// section 127-0-1-1--5a63b0fb:12f72879be9:-8000:00000000000015AD begin

		$returnValue = in_array($this->name, self::$_tables);

		// section 127-0-1-1--5a63b0fb:12f72879be9:-8000:00000000000015AD end

		return (bool) $returnValue;
	}

	/**
	 * Short description of method create
	 *
	 * @access public
	 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
	 * @param  array columns
	 * @return boolean
	 */
	public function create($columns = array())
	{
		$returnValue = (bool) false;

		// section 127-0-1-1--5a63b0fb:12f72879be9:-8000:00000000000015AF begin
		
		if(!$this->exists() && !empty($this->name)){
			$dbWrapper = core_kernel_classes_DbWrapper::singleton();
				
			//build the query to create the main table
			$query = "CREATE TABLE {$this->name} (
						id int NOT NULL AUTO_INCREMENT,
						PRIMARY KEY (id),
						uri VARCHAR(255) NOT NULL ,
						KEY idx{$this->name}_uri (uri)";
			foreach($columns as $column){
				if(isset($column['name'])){
					if(isset($column['multi'])){
						continue;
					}
					$query .= ", {$column['name']}";
					if(isset($column['foreign']) && !empty($column['foreign'])){
						$query .= " VARCHAR(255),";
						$query .= " CONSTRAINT fk_{$column['name']}
									FOREIGN KEY ({$column['name']}) 
									REFERENCES {$column['foreign']}(uri)
								";
					}
					else{
						$query .= " LONGTEXT";
					}
				}
			}
			$query .= ')DEFAULT CHARSET=utf8';

			$dbWrapper->execSql($query);
			if($dbWrapper->dbConnector->errorNo() > 0){
				if(DEBUG_MODE){
					echo $query."<br>";
				}
				//the user may not have the right to create a table
				throw new core_kernel_persistence_hardapi_Exception("Unable to create the table {$this->name} : " .$dbWrapper->dbConnector->errorMsg());
			}
				
			//create the multi prop table if needed
			$query = "CREATE TABLE {$this->name}Props (
				id int NOT NULL AUTO_INCREMENT,
				property_uri VARCHAR(255),
				property_value LONGTEXT,
				property_foreign_uri VARCHAR(255),
				l_language VARCHAR(4),
				instance_id int NOT NULL ,
				PRIMARY KEY (id),
				KEY idx{$this->name}props_property_uri (property_uri),
				KEY idx{$this->name}props_foreign_property_uri (property_foreign_uri),
				CONSTRAINT fk{$this->name}_instance_id 
						FOREIGN KEY (instance_id) 
						REFERENCES {$this->name}(id)
			)DEFAULT CHARSET=utf8";
				
			$dbWrapper->execSql($query);
			if($dbWrapper->dbConnector->errorNo() !== 0){
				if(DEBUG_MODE){
					echo $query."<br>";
				}
				throw new core_kernel_persistence_hardapi_Exception("Unable to create the table {$this->name}Props : " .$dbWrapper->dbConnector->errorMsg());
			}
			self::$_tables[] = "{$this->name}Props";
				
			//auto reference
			self::$_tables[] = $this->name;
			$returnValue = true;
				
		}
		
		// section 127-0-1-1--5a63b0fb:12f72879be9:-8000:00000000000015AF end

		return (bool) $returnValue;
	}

	/**
	 * Short description of method remove
	 *
	 * @access public
	 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
	 * @return boolean
	 */
	public function remove()
	{
		$returnValue = (bool) false;

		// section 127-0-1-1--5a63b0fb:12f72879be9:-8000:00000000000015B9 begin

		if($this->exists() && !empty($this->name)){
			$dbWrapper = core_kernel_classes_DbWrapper::singleton();

			//remove the multi properties table
			$dbWrapper->execSql("DROP TABLE `{$this->name}Props`");
			if($dbWrapper->dbConnector->errorNo() !== 0){
				throw new core_kernel_persistence_hardapi_Exception("Unable to remove the properties table {$this->name}Props :" .$dbWrapper->dbConnector->errorMsg());
			}
			$tblKey = array_search("{$this->name}Props", self::$_tables);
			if($tblKey !== false){
				unset(self::$_tables[$tblKey]);
			}
				
			//remove the table
			$dbWrapper->execSql("DROP TABLE `{$this->name}`");
			if($dbWrapper->dbConnector->errorNo() === 0){

				//remove all the references to the table
				$cascadeDelete = "DELETE class_to_table.*, resource_has_class.*, resource_to_table.* FROM class_to_table
									LEFT JOIN resource_has_class ON resource_has_class.class_id = class_to_table.id
									LEFT JOIN resource_to_table ON resource_has_class.resource_id = resource_to_table.id
									WHERE class_to_table.`table` = '{$this->name}' OR resource_to_table.`table` = '{$this->name}'";
				$dbWrapper->execSql($cascadeDelete);

				if($dbWrapper->dbConnector->errorNo() === 0){

					$tblKey = array_search($this->name, self::$_tables);
					if($tblKey !== false){
						unset(self::$_tables[$tblKey]);
					}
					$returnValue = true;

				}
				else{
					throw new core_kernel_persistence_hardapi_Exception("Unable to remove data related to {$this->name} : " .$dbWrapper->dbConnector->errorMsg());
				}
			}
			else{
				//the user may not have the right to drop a table
				throw new core_kernel_persistence_hardapi_Exception("Unable to remove the table {$this->name} : " .$dbWrapper->dbConnector->errorMsg());
			}
		}

		// section 127-0-1-1--5a63b0fb:12f72879be9:-8000:00000000000015B9 end

		return (bool) $returnValue;
	}

} /* end of class core_kernel_persistence_hardapi_TableManager */

?>