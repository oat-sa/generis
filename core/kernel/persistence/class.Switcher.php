<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - core/kernel/persistence/class.Switcher.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 20.04.2011, 13:05:54 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package core
 * @subpackage kernel_persistence
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1--5a63b0fb:12f72879be9:-8000:0000000000001588-includes begin
// section 127-0-1-1--5a63b0fb:12f72879be9:-8000:0000000000001588-includes end

/* user defined constants */
// section 127-0-1-1--5a63b0fb:12f72879be9:-8000:0000000000001588-constants begin
// section 127-0-1-1--5a63b0fb:12f72879be9:-8000:0000000000001588-constants end

/**
 * Short description of class core_kernel_persistence_Switcher
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package core
 * @subpackage kernel_persistence
 */
class core_kernel_persistence_Switcher
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method hardifier
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Class class
     * @param  array options
     * @return boolean
     */
    public static function hardifier( core_kernel_classes_Class $class, $options = array())
    {
        $returnValue = (bool) false;
        
        // section 127-0-1-1--5a63b0fb:12f72879be9:-8000:0000000000001589 begin
//        echo "begin hardify : ".core_kernel_persistence_hardapi_Utils::getShortName($class)."<br/>";
        
		//recursive will hardify the class that are range of the properties
		(isset($options['recursive'])) ? $recursive = $options['recursive'] : $recursive = false;
		
		//check if we append the data in case the hard table exists or truncate the table and add the new rows
		(isset($options['append'])) ? $append = $options['append'] : $append = false;
		
		//if true, the instances of the class will  be removed!
		(isset($options['rmSources'])) ? $rmSources = $options['rmSources'] : $rmSources = false;
		
		//if defined, we took all the properties of the class and it's parents till the topclass
		(isset($options['topClass'])) ? $topClass = $options['topClass'] : $topClass = new core_kernel_classes_Class(CLASS_GENERIS_RESOURCE);
		
		// Mask of classes to exclude
		(isset($options['excludedClass'])) ? $excludedClass = $options['excludedClass'] : array();
		
		$tableName = core_kernel_persistence_hardapi_Utils::getShortName($class);
		if(!$append){
			
			$referencer = core_kernel_persistence_hardapi_ResourceReferencer::singleton();
			
			//get the table columns from the class properties
			$columns = array();
			
			$ps = new core_kernel_persistence_switcher_PropertySwitcher($class, $topClass);
			$properties = $ps->getProperties();
			
			$columns = $ps->getTableColumns();
			foreach($columns as $column){
//       			echo 'treat column '.$column['name'].'<br/>';
				
				if($recursive && isset($column['foreign'])){
//       				echo $column['foreign'].' <span style="color:red">(foreign)</span> <br/>';
					//create the foreign tables recursively
					$foreignTableMgr = new core_kernel_persistence_hardapi_TableManager($column['foreign']);
					if(!$foreignTableMgr->exists()){
						$rangeUri = core_kernel_persistence_hardapi_Utils::getLongName($column['foreign']);
						$range = new core_kernel_classes_Class($rangeUri);
						$subHardifyOption = array_merge($options, array());
						$subHardifyOption['topClass'] = new core_kernel_classes_Class(CLASS_GENERIS_RESOURCE);
						self::hardifier($range, $subHardifyOption);
					}
				}
			}
			
			//create the table
//       		echo "create table and column for ".core_kernel_persistence_hardapi_Utils::getShortName($class)."<br/>";
			$myTableMgr = new core_kernel_persistence_hardapi_TableManager($tableName);
			if($myTableMgr->exists()){
				$myTableMgr->remove();
			}
			$myTableMgr->create($columns);
			//reference the class
			$referencer->referenceClass($class);
				
			
			//insert the resources
			$instances = $class->getInstances(false);
			$rows = array();
			$i=0;
			foreach($instances as $resource){
				$row = array('uri' => $resource->uriResource);
				$propertiesValue = $resource->getPropertiesValue ($properties, false);
				foreach($properties as $property){
					$row[core_kernel_persistence_hardapi_Utils::getShortName($property)] = $propertiesValue[$property->uriResource];
				}
				
//				foreach($properties as $property){
//					$propValue = $resource->getOnePropertyValue($property);
//					$row[core_kernel_persistence_hardapi_Utils::getShortName($property)] = $propValue;
//				}
				$rows[] = $row;
//				$i++; if ($i>100) break;
			}
			
//       		echo "insert rows (#".count($rows).") for ".core_kernel_persistence_hardapi_Utils::getShortName($class)."<br/>";
			$rowMgr = new core_kernel_persistence_hardapi_RowManager($tableName, $columns);
			$rowMgr->insertRows($rows);
			
//			foreach($instances as $resource){
//				$referencer->referenceResource($resource);
//			}
			
		}
		
//        echo "end hardify : {$class->uriResource}<br/><br/>";
        
        // section 127-0-1-1--5a63b0fb:12f72879be9:-8000:0000000000001589 end

        return (bool) $returnValue;
    }
    
} /* end of class core_kernel_persistence_Switcher */

?>