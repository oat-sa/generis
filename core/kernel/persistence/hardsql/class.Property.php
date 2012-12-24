<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 18.12.2012, 13:08:58 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package core
 * @subpackage kernel_persistence_hardsql
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include core_kernel_persistence_PersistenceImpl
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 */
require_once('core/kernel/persistence/class.PersistenceImpl.php');

/**
 * include core_kernel_persistence_PropertyInterface
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 */
require_once('core/kernel/persistence/interface.PropertyInterface.php');

/* user defined includes */
// section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000013A8-includes begin
// section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000013A8-includes end

/* user defined constants */
// section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000013A8-constants begin
// section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000013A8-constants end

/**
 * Short description of class core_kernel_persistence_hardsql_Property
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package core
 * @subpackage kernel_persistence_hardsql
 */
class core_kernel_persistence_hardsql_Property
    extends core_kernel_persistence_PersistenceImpl
        implements core_kernel_persistence_PropertyInterface
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute instance
     *
     * @access public
     * @var Resource
     */
    public static $instance = null;

    // --- OPERATIONS ---

    /**
     * Short description of method getSubProperties
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @param  boolean recursive
     * @return array
     */
    public function getSubProperties( core_kernel_classes_Resource $resource, $recursive = false)
    {
        $returnValue = array();

        // section 127-0-1-1-7b8668ff:12f77d22c39:-8000:000000000000144D begin
        throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
        // section 127-0-1-1-7b8668ff:12f77d22c39:-8000:000000000000144D end

        return (array) $returnValue;
    }

    /**
     * Short description of method isLgDependent
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @return boolean
     */
    public function isLgDependent( core_kernel_classes_Resource $resource)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--bedeb7e:12fb15494a5:-8000:00000000000014DB begin
        throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
        // section 127-0-1-1--bedeb7e:12fb15494a5:-8000:00000000000014DB end

        return (bool) $returnValue;
    }

    /**
     * Short description of method isMultiple
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @return boolean
     */
    public function isMultiple( core_kernel_classes_Resource $resource)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--bedeb7e:12fb15494a5:-8000:00000000000014DD begin
        throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
        // section 127-0-1-1--bedeb7e:12fb15494a5:-8000:00000000000014DD end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getRange
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @return core_kernel_classes_Class
     */
    public function getRange( core_kernel_classes_Resource $resource)
    {
        $returnValue = null;

        // section 127-0-1-1-7a0c731b:12fbfab7535:-8000:0000000000001539 begin
        throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
        // section 127-0-1-1-7a0c731b:12fbfab7535:-8000:0000000000001539 end

        return $returnValue;
    }

    /**
     * Short description of method delete
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @param  boolean deleteReference
     * @return boolean
     */
    public function delete( core_kernel_classes_Resource $resource, $deleteReference = false)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--330ca9de:1318ac7ca9f:-8000:0000000000001641 begin
        
        throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
        
        // section 127-0-1-1--330ca9de:1318ac7ca9f:-8000:0000000000001641 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method setRange
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @param  Class class
     * @return core_kernel_classes_Class
     */
    public function setRange( core_kernel_classes_Resource $resource,  core_kernel_classes_Class $class)
    {
        $returnValue = null;

        // section 10-13-1-85-36aaae10:13bad44a267:-8000:0000000000001E25 begin
        
        // always remain in smooth mode.
        $returnValue = core_kernel_persistence_smoothsql_Property::singleton()->setRange($resource, $class);

        // section 10-13-1-85-36aaae10:13bad44a267:-8000:0000000000001E25 end

        return $returnValue;
    }

    /**
     * Short description of method setMultiple
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @param  boolean isMultiple
     * @return void
     */
    public function setMultiple( core_kernel_classes_Resource $resource, $isMultiple)
    {
        // section 10-13-1-85-71dc1cdd:13bade8452c:-8000:0000000000001E32 begin
        
        // First, do the same as in smooth mode.
        core_kernel_persistence_smoothsql_Property::singleton()->setMultiple($resource, $isMultiple);
        
    	// Second, we alter the relevant table(s) if needed.
        // For all the classes that have the resource as domain,
        // we have to alter the correspondent tables.
        $referencer = core_kernel_persistence_hardapi_ResourceReferencer::singleton();
        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
        $propertyDescription = core_kernel_persistence_hardapi_Utils::propertyDescriptor($resource);
        $propertyLocations = $referencer->propertyLocation($resource);
        
        $wasMulti = $propertyDescription['isMultiple'];
        $wasLgDependent = $propertyDescription['isLgDependent'];
        $propName = $propertyDescription['name'];
        $propUri = $resource->getUri();
        $propRanges = array();
        foreach ($propertyDescription['range'] as $range){
        	$propRanges[] = $range->getUri();	
        }
        
        // @TODO $batchSize's value is arbitrary.
        $batchSize = 100;	// Transfer data $batchSize by $batchSize.
        $offset = 0;
        
        foreach ($propertyLocations as $tblname){
        	$tblmgr = new core_kernel_persistence_hardapi_TableManager($tblname);
        	if ($tblmgr->exists()){
        		if ($wasMulti != $isMultiple){
        			// Reset offset.
        			$offset = 0;
        			
	        		try{
		        		// The multiplicity is then changing.
		        		// However, if the property was not 'multiple' but 'language dependent'
		        		// it is already stored as it should.
		        		if ($isMultiple == true && $wasLgDependent == false && $wasMulti == false){
		        			
		        			// We go from single to multiple.
		        			do {
		        				$hasResult = false;
		        				
			        			$setPropertyValue = (empty($propRanges) || in_array(RDFS_LITERAL, $propRanges)) ? true : false;
			        			$lang = ($setPropertyValue) ? DEFAULT_LANG : '';
			        			$sql = 'SELECT "id","' . $propName . '" AS "val" FROM "' . $tblname . '"';
			        			$sql = $dbWrapper->limitStatement($sql, $batchSize, $offset);
			        			$result = $dbWrapper->query($sql);
			        			
			        			// Prepare the insert statement.
			        			$sql  = 'INSERT INTO "' . $tblname . 'Props" ';
			        			$sql .= '("property_uri", "property_value", "property_foreign_uri", "l_language", "instance_id") ';
			        			$sql .= 'VALUES (?, ?, ?, ?, ?)';
			        			$sth = $dbWrapper->prepare($sql);
			        			
			        			while ($row = $result->fetch()){
			        				// Transfer to the 'properties table'.
			        				$hasResult = true;
			        				$propertyValue = ($setPropertyValue == true) ? $row['val'] : null;
			        				$propertyForeignUri = ($setPropertyValue == false) ? $row['val'] : null;
			        				
			        				$sth->execute(array($propUri, $propertyValue, $propertyForeignUri, $lang, $row['id'])); 
			        			}
			        			
			        			$offset += $batchSize;
		        			}
		        			while($hasResult === true);
		        			
		        			// Remove old column containing the scalar values.
		        			// we do not need it anymore.
		        			if ($tblmgr->removeColumn($propName) == false){
	        					$msg = "Cannot successfully set multiplicity of Property '${propUri}' because its table column could not be removed from database.";
	        					throw new core_kernel_persistence_hardsql_Exception($msg);
		        			}
		        		}
		        		else if ($isMultiple == false && ($wasLgDependent == true || $wasMulti == true)){
		        			
		        			// We go from multiple to single.
		        			$toDelete = array(); // will contain ids of rows to delete in the 'properties table' after data transfer.
		        			
		        			// Add a column to the base table to receive single value.
		        			$baseTableName = str_replace('Props', '', $tblname);
		        			$tblmgr->setName($baseTableName);
		        			$shortName = core_kernel_persistence_hardapi_Utils::getShortName($resource);
		        			$columnAdded = $tblmgr->addColumn(array('name' => $shortName,
		        											  		'multi' => false));
		        			if ($columnAdded == true){
		        				// Now get the values in the props table. Group by instance ID in order to get only
		        				// one value to put in the target column.
		        				do {
		        					$hasResult = false;
		        					
				        			$retrievePropertyValue = (empty($propRanges) || in_array(RDFS_LITERAL, $propRanges)) ? true : false;
				        			$sql  = 'SELECT "a"."id", "a"."instance_id", "a"."property_value", "a"."property_foreign_uri" FROM "' . $tblname . '" "a" ';
				        			$sql .= 'RIGHT JOIN (SELECT "instance_id", MIN("id") AS "id" FROM "' . $tblname . '" WHERE "property_uri" = ? ';
				        			$sql .= 'GROUP BY "instance_id") AS "b" ON ("a"."id" = "b"."id")';
				        			$sql  = $dbWrapper->limitStatement($sql, $batchSize, $offset);
									
				        			$result = $dbWrapper->query($sql, array($propUri));
				        			// prepare the update statement.
				        			$sql  = 'UPDATE "' . $baseTableName . '" SET "' . $shortName . '" = ? WHERE "id" = ?';
				        			$sth = $dbWrapper->prepare($sql);
				        			
				        			while ($row = $result->fetch()){
				        				// Transfer to the 'base table'.
				        				$hasResult = true;
				        				$propertyValue = ($retrievePropertyValue == true) ? $row['property_value'] : $row['property_foreign_uri'];
				        				$sth->execute(array($propertyValue, $row['instance_id']));
				        				$toDelete[] = $row['id'];
				        			}
				        			
				        			$offset += $batchSize;
		        				}
		        				while($hasResult === true);
		        				
		        				$inData = implode(',', $toDelete);
			        			$sql = 'DELETE FROM "' . $tblname . '" WHERE "id" IN (' . $inData . ')';
			        			
			        			if ($dbWrapper->exec($sql) == 0){
			        				// If an error occured or no rows removed, we
			        				// have a problem.
			        				$msg = "Cannot set multiplicity of Property '${propUri}' because data transfered to the 'base table' could not be deleted";
			        				throw new core_kernel_persistence_hardsql_Exception($msg);	
			        			}
		        			}
		        			else{
		        				$msg = "Cannot set multiplicity of Property '${propUri}' because the corresponding 'base table' column could not be created.";
		        				throw new core_kernel_persistence_hardsql_Exception($msg);
		        			}
		        		}
	        		}
	        		catch (PDOException $e){
	        			$msg = "Cannot set multiplicity of Property '${propUri}': " . $e->getMessage();
	        			throw new core_kernel_persistence_hardsql_Exception($msg);
	        		}
        		}
        	}
        	else{
        		$msg = "Cannot set multiplicity of Property '${propUri}' because the corresponding database location '${tblname}' does not exist.";
        		throw new core_kernel_persistence_hardsql_Exception($msg);
        	}
        }
        
        $referencer->resetCache();
        
        // section 10-13-1-85-71dc1cdd:13bade8452c:-8000:0000000000001E32 end
    }

    /**
     * Short description of method singleton
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return core_kernel_classes_Resource
     */
    public static function singleton()
    {
        $returnValue = null;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001499 begin
        
        if (core_kernel_persistence_hardsql_Property::$instance == null){
        	core_kernel_persistence_hardsql_Property::$instance = new core_kernel_persistence_hardsql_Property();
        }
        $returnValue = core_kernel_persistence_hardsql_Property::$instance;
        
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001499 end

        return $returnValue;
    }

    /**
     * Short description of method isValidContext
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @return boolean
     */
    public function isValidContext( core_kernel_classes_Resource $resource)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F54 begin
        $returnValue = core_kernel_persistence_hardapi_ResourceReferencer::singleton()->isPropertyReferenced($resource);
        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F54 end

        return (bool) $returnValue;
    }

} /* end of class core_kernel_persistence_hardsql_Property */

?>