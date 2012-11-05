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
			$result = $dbWrapper->query('SELECT DISTINCT "table" FROM class_to_table');
			while($row = $result->fetch()){
				self::$_tables[] = $row['table'];
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
			$query = 'CREATE TABLE "'.$this->name.'" (
						"id" SERIAL,
						PRIMARY KEY ("id"),
						"uri" VARCHAR(255) NOT NULL';
			foreach($columns as $column){
				if(isset($column['name'])){
					if(isset($column['multi'])){
						continue;
					}
					$query .= ', "'.$column['name'].'"';
					if(isset($column['foreign']) && !empty($column['foreign'])){
						$query .= " TEXT";
					}
					else{
						$query .= " TEXT";
					}
				}
			}
			$query .= ')/*!ENGINE = MYISAM, DEFAULT CHARSET=utf8*/;';

			$dbWrapper->exec($query);
			
			// create table index
			$query = 'CREATE INDEX "idx'.$this->name.'" ON "'.$this->name.'" ("uri");';
			$dbWrapper->exec($query);
				
			//always create the multi prop table
			$query = 'CREATE TABLE "'.$this->name.'Props" (
				"id" SERIAL,
				"property_uri" VARCHAR(255),
				"property_value" TEXT,
				"property_foreign_uri" VARCHAR(255),
				"l_language" VARCHAR(5),
				"instance_id" int NOT NULL ,
				PRIMARY KEY ("id")';
                                
			$query .= ")/*!ENGINE = MYISAM, DEFAULT CHARSET=utf8*/;";
				
			$dbWrapper->exec($query);

			self::$_tables[] = "{$this->name}Props";
			
			// Create multiples properties table indexes
			$indexQueries = array();
			$indexQueries[] = 'CREATE INDEX "idx_props_property_uri" ON "'.$this->name.'Props" ("property_uri");';
			$indexQueries[] = 'CREATE INDEX "idx_props_foreign_property_uri" ON "'.$this->name.'Props" ("property_foreign_uri");';
			$indexQueries[] = 'CREATE INDEX "idx_props_instance_id" ON "'.$this->name.'Props" ("instance_id");';
			foreach ($indexQueries as $indexQuery){
				try{
					$dbWrapper->exec($indexQuery);
				}
				catch(PDOException $e){
					if($e->getCode() != $dbWrapper->getIndexAlreadyExistsErrorCode() && $e->getCode() != '00000'){
						//the user may not have the right to create the table index or it already exists.
						throw new core_kernel_persistence_hardapi_Exception("Unable to create the multiples properties table indexes  {$this->name} : " .$e->getMessage());
					}
				}
			}
			
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
			try{
				$dbWrapper->exec('DROP TABLE "'.$this->name.'Props"');
				$tblKey = array_search("{$this->name}Props", self::$_tables);
				if($tblKey !== false){
					unset(self::$_tables[$tblKey]);
				}
				
				//remove the table
				$result = $dbWrapper->exec('DROP TABLE "'.$this->name.'";');
				$tblKey = array_search($this->name, self::$_tables);
				if($tblKey !== false){
					unset(self::$_tables[$tblKey]);
				}
				
				$returnValue = true;
			}
			catch (PDOException $e){
				throw new core_kernel_persistence_hardapi_Exception("Unable to remove the multiple properties table {$this->name}Props :" . $e->getMessage());
			}
		}

		// section 127-0-1-1--5a63b0fb:12f72879be9:-8000:00000000000015B9 end

		return (bool) $returnValue;
	}

} /* end of class core_kernel_persistence_hardapi_TableManager */

?>