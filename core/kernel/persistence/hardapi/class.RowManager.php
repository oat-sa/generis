<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 21.04.2011, 17:17:55 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package core
 * @subpackage kernel_persistence_hardapi
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1-8da8919:12f7878e80a:-8000:000000000000161B-includes begin
// section 127-0-1-1-8da8919:12f7878e80a:-8000:000000000000161B-includes end

/* user defined constants */
// section 127-0-1-1-8da8919:12f7878e80a:-8000:000000000000161B-constants begin
// section 127-0-1-1-8da8919:12f7878e80a:-8000:000000000000161B-constants end

/**
 * Short description of class core_kernel_persistence_hardapi_RowManager
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package core
 * @subpackage kernel_persistence_hardapi
 */
class core_kernel_persistence_hardapi_RowManager
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute table
     *
     * @access protected
     * @var string
     */
    protected $table = '';

    /**
     * Short description of attribute columns
     *
     * @access protected
     * @var array
     */
    protected $columns = array();

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string table
     * @param  array columns
     * @return mixed
     */
    public function __construct($table, $columns)
    {
        // section 127-0-1-1-8da8919:12f7878e80a:-8000:0000000000001622 begin
        
    	$this->table = $table;
		$this->columns = $columns;
    	
        // section 127-0-1-1-8da8919:12f7878e80a:-8000:0000000000001622 end
    }

    /**
     * Short description of method insertRows
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  array rows
     * @return boolean
     */
    public function insertRows($rows)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-8da8919:12f7878e80a:-8000:0000000000001626 begin
        
        $size = count($rows);
		if($size > 0){
			$dbWrapper = core_kernel_classes_DbWrapper::singleton();
			
			//building the insert query
			
			//1st step : set the column names
			$query = "INSERT INTO {$this->table} (uri";
			foreach($this->columns as $column){
				if(isset($column['multi']) && $column['multi'] === true){
					continue;
				}
				$query .= ", {$column['name']}";
			}
			$query .= ') VALUES ';
			
			//set the values
			foreach($rows as $i => $row){
				$query.= "('{$row['uri']}'";
				foreach($this->columns as $column){
					if(isset($row[$column['name']])){
						if(isset($column['multi']) && $column['multi'] === true){
							continue;
						}
						
						//set the ID
						if(isset($column['foreign'])){
							
							$uri = $row[$column['name']];
							
							//get foreign id
							$foreignQuery 	= "SELECT id FROM {$column['foreign']} WHERE uri = ?";
							$foreignResult 	= $dbWrapper->execSql($foreignQuery, array($uri));
							if($foreignResult->recordCount() == 0){
								$query.= ", NULL";
							}
							else{
								while($foreignRow =  $foreignResult->fetchRow()){
									$id = $foreignRow['id'];
									
									$query.= ", {$id}";
									break;
								}
							}
						}
						else{
							//set the literal value
							$query.= ", '{$row[$column['name']]}'";
						}
					}
				}
				$query.= ")";
				if($i < $size-1){
					$query .= ',';
				}
			}

			$dbWrapper->execSql($query);
			if($dbWrapper->dbConnector->errorNo() !== 0){
				throw new core_kernel_persistence_hardapi_Exception("Unable to insert the rows : " .$dbWrapper->dbConnector->errorMsg());
			}
			
			/*
			 * 
			 * @todo insert the multiple values! 
			 * 
			 */
		}
        
        // section 127-0-1-1-8da8919:12f7878e80a:-8000:0000000000001626 end

        return (bool) $returnValue;
    }

} /* end of class core_kernel_persistence_hardapi_RowManager */

?>