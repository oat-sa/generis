<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - core\kernel\rules\class.OperationFactory.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 23.03.2010, 15:58:22 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author firstname and lastname of author, <author@example.org>
 * @package core
 * @subpackage kernel_rules
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 10-13-1--99-2335bfbb:1207fc834f5:-8000:0000000000001740-includes begin
// section 10-13-1--99-2335bfbb:1207fc834f5:-8000:0000000000001740-includes end

/* user defined constants */
// section 10-13-1--99-2335bfbb:1207fc834f5:-8000:0000000000001740-constants begin
// section 10-13-1--99-2335bfbb:1207fc834f5:-8000:0000000000001740-constants end

/**
 * Short description of class core_kernel_rules_OperationFactory
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 * @package core
 * @subpackage kernel_rules
 */
class core_kernel_rules_OperationFactory
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method createOperation
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Term term1
     * @param  Term term2
     * @param  Resource operator
     * @return core_kernel_rules_Operation
     */
    public static function createOperation( core_kernel_rules_Term $term1,  core_kernel_rules_Term $term2,  core_kernel_classes_Resource $operator)
    {
        $returnValue = null;

        // section 10-13-1--99-2335bfbb:1207fc834f5:-8000:000000000000174F begin
        $operationClass = new core_kernel_classes_Class(CLASS_OPERATION,__METHOD__); 
        $label = 'Def Operation Label ' . $term1->getLabel() . ' ' . $operator->getLabel() . ' ' . $term2->getLabel();
        $comment = 'Def Operation Comment ' . $term1->uriResource . ' ' . $operator->uriResource. ' ' . $term2->uriResource;
		$operatorProperty = new core_kernel_classes_Property(PROPERTY_OPERATION_OPERATOR,__METHOD__);
        $firstOperand = new core_kernel_classes_Property(PROPERTY_OPERATION_FIRST_OP,__METHOD__);
		$secondOperand = new core_kernel_classes_Property(PROPERTY_OPERATION_SECND_OP,__METHOD__);		
        $termOperationInstance = core_kernel_classes_ResourceFactory::create($operationClass,$label,$comment);
        $returnValue = new core_kernel_rules_Operation($termOperationInstance->uriResource);
        $returnValue->debug = __METHOD__;
        $returnValue->setPropertyValue($operatorProperty,$operator->uriResource);
        $returnValue->setPropertyValue($firstOperand,$term1->uriResource);
		$returnValue->setPropertyValue($secondOperand,$term2->uriResource);
        // section 10-13-1--99-2335bfbb:1207fc834f5:-8000:000000000000174F end

        return $returnValue;
    }

} /* end of class core_kernel_rules_OperationFactory */

?>