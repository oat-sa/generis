<?php
/*  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);\n *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - core\kernel\impl\class.ApiModelOOHelper.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 29.03.2010, 14:19:53 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author firstname and lastname of author, <author@example.org>
 * @package core
 * @subpackage kernel_impl
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 10-13-1--99--f2ea6d:11b36a6e31a:-8000:000000000000193C-includes begin
// section 10-13-1--99--f2ea6d:11b36a6e31a:-8000:000000000000193C-includes end

/* user defined constants */
// section 10-13-1--99--f2ea6d:11b36a6e31a:-8000:000000000000193C-constants begin
// section 10-13-1--99--f2ea6d:11b36a6e31a:-8000:000000000000193C-constants end

/**
 * Short description of class core_kernel_impl_ApiModelOOHelper
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 * @package core
 * @subpackage kernel_impl
 */
class core_kernel_impl_ApiModelOOHelper
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method resourcesCollectionBuilder
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  array oldArray
     * @return core_kernel_classes_ContainerCollection
     */
    public static function resourcesCollectionBuilder($oldArray)
    {
        $returnValue = null;

        // section 10-13-1--99--f2ea6d:11b36a6e31a:-8000:0000000000001949 begin
       	$returnValue = new core_kernel_classes_ContainerCollection(new core_kernel_classes_Container(__METHOD__),__METHOD__);
        if(isset($oldArray['pDescription'])){
			foreach ($oldArray['pDescription'] as $aResource){
				$trueResource = isset($aResource['InstanceKey']) ? new core_kernel_classes_Resource($aResource['InstanceKey']) : null;
				
				if ($trueResource != null) {
					$trueResource->comment 	= $aResource['InstanceComment'];
					$trueResource->label 	= $aResource['InstanceLabel'];
					$trueResource->debug 	= __METHOD__;
					$returnValue->add($trueResource);
				}
			}
		}
        // section 10-13-1--99--f2ea6d:11b36a6e31a:-8000:0000000000001949 end

        return $returnValue;
    }

    /**
     * Short description of method propertiesCollectionBuilder
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  array oldArray
     * @return core_kernel_classes_ContainerCollection
     */
    public static function propertiesCollectionBuilder($oldArray)
    {
        $returnValue = null;

        // section 10-13-1--99--f2ea6d:11b36a6e31a:-8000:000000000000194B begin
        $returnValue = new core_kernel_classes_ContainerCollection(new core_kernel_classes_Container(__METHOD__),__METHOD__);
		if(isset($oldArray['pDescription'])){
			foreach ($oldArray['pDescription'] as $aProperty){
				$trueProperty = isset($aProperty['PropertyKey']) ? new core_kernel_classes_Property($aProperty['PropertyKey']) : null;
				if ($trueProperty != null) {
					$trueProperty->comment 	= $aProperty['PropertyComment'];
					$trueProperty->label 	= $aProperty['PropertyLabel'];
					$trueProperty->range 	= $aProperty['PropertyRange'];
					$trueProperty->widget 	= $aProperty['PropertyWidget'];
					$trueProperty->debug 	= __METHOD__;
					$returnValue->add($trueProperty);
				}
			}
		}
        // section 10-13-1--99--f2ea6d:11b36a6e31a:-8000:000000000000194B end

        return $returnValue;
    }

    /**
     * Short description of method classesCollectionBuilder
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  array oldArray
     * @return core_kernel_classes_ContainerCollection
     */
    public static function classesCollectionBuilder($oldArray)
    {
        $returnValue = null;

        // section 10-13-1--99--f2ea6d:11b36a6e31a:-8000:000000000000194D begin
    	$returnValue = new core_kernel_classes_ContainerCollection(new core_kernel_classes_Container(__METHOD__),__METHOD__);
		if(isset($oldArray['pDescription'])) {
	    	foreach ($oldArray['pDescription'] as $aClass){
				$trueClass = isset($aClass['ClassKey']) ? new core_kernel_classes_Class($aClass['ClassKey']) : null;
				if ($trueClass != null) {
					$trueClass->comment = $aClass['ClassComment'];
					$trueClass->label 	= $aClass['ClassLabel'];
					$trueClass->debug 	= __METHOD__;
					$returnValue->add($trueClass);
				}
			}
		}
        // section 10-13-1--99--f2ea6d:11b36a6e31a:-8000:000000000000194D end

        return $returnValue;
    }

} /* end of class core_kernel_impl_ApiModelOOHelper */

?>