<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - core\kernel\rules\class.Rule.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 23.03.2010, 15:58:21 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author firstname and lastname of author, <author@example.org>
 * @package core
 * @subpackage kernel_rules
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * Resource implements rdf:resource container identified by an uri (a string).
 * Methods enable meta data management for this resource
 *
 * @author patrick.plichart@tudor.lu
 * @see http://www.w3.org/RDF/
 * @version v1.0
 */
require_once('core/kernel/classes/class.Resource.php');

/* user defined includes */
// section 10-13-1--99-6bb5697e:11bda1bbfa6:-8000:000000000000133B-includes begin
// section 10-13-1--99-6bb5697e:11bda1bbfa6:-8000:000000000000133B-includes end

/* user defined constants */
// section 10-13-1--99-6bb5697e:11bda1bbfa6:-8000:000000000000133B-constants begin
// section 10-13-1--99-6bb5697e:11bda1bbfa6:-8000:000000000000133B-constants end

/**
 * Short description of class core_kernel_rules_Rule
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 * @package core
 * @subpackage kernel_rules
 */
class core_kernel_rules_Rule
    extends core_kernel_classes_Resource
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute expression
     *
     * @access public
     * @var Expression
     */
    public $expression = null;

    // --- OPERATIONS ---

    /**
     * Short description of method getExpression
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return core_kernel_rules_Expression
     */
    public function getExpression()
    {
        $returnValue = null;

        // section 10-13-1--99--2ca656b4:11c9ebb5ddd:-8000:0000000000000ED7 begin
        $logger = new common_Logger('Generis Rule', Logger::debug_level);
		$logger->info('Evaluating Rule uri: '. $this->uriResource , __FILE__, __LINE__);
		$logger->info('Evaluating Rule name: '. $this->getLabel() , __FILE__, __LINE__);
         if(empty($this->expression)){
         	$property = new core_kernel_classes_Property(PROPERTY_RULE_IF);
         	$this->expression = new core_kernel_rules_Expression($this->getUniquePropertyValue($property)->uriResource ,__METHOD__);
        }
        $returnValue = $this->expression;
        // section 10-13-1--99--2ca656b4:11c9ebb5ddd:-8000:0000000000000ED7 end

        return $returnValue;
    }

} /* end of class core_kernel_rules_Rule */

?>