<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
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
 * @subpackage kernel_events
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-0-1-293e509c:11c0d865d9e:-8000:0000000000000E81-includes begin
// section 127-0-0-1-293e509c:11c0d865d9e:-8000:0000000000000E81-includes end

/* user defined constants */
// section 127-0-0-1-293e509c:11c0d865d9e:-8000:0000000000000E81-constants begin
define("EVENT_NOT_UNDERSTOOD", "@");
// section 127-0-0-1-293e509c:11c0d865d9e:-8000:0000000000000E81-constants end

/**
 * Short description of class core_kernel_events_EventParamConverter
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 * @package core
 * @subpackage kernel_events
 */
class core_kernel_events_EventParamConverter
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method convert
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  object
     * @return string
     */
    public static function convert($object)
    {
        $returnValue = (string) '';

        // section 127-0-0-1-293e509c:11c0d865d9e:-8000:0000000000000E83 begin
        try {
          if ("__PHP_Incomplete_Class" == get_class($object)) { // we use serialization
            $returnValue = EVENT_NOT_UNDERSTOOD;
          } else {
            $result = null;
            //foreach (get_class_methods("core_kernel_events_EventParamConverter")
            foreach (get_class_methods(__CLASS__) as $method) {
              if ("cnv" != substr($method, 0, 3)) continue;
              $result = self::$method($object);
              if (!empty($result)) break;
            }
            $returnValue = $result;
          }
        } catch (Exception $e) { // just in case of unprobable exception
          $returnValue = EVENT_NOT_UNDERSTOOD;
        }
        // section 127-0-0-1-293e509c:11c0d865d9e:-8000:0000000000000E83 end

        return (string) $returnValue;
    }

    /**
     * Short description of method cnvScalar
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  object
     * @return string
     */
    public static function cnvScalar($object)
    {
        $returnValue = (string) '';

        // section 127-0-0-1-4a23c3d2:11c0e6bcc2f:-8000:0000000000000E89 begin
        if (!is_object($object) && !is_array($object)) {
          $returnValue = "$object"; // type cast to string
        }
        // section 127-0-0-1-4a23c3d2:11c0e6bcc2f:-8000:0000000000000E89 end

        return (string) $returnValue;
    }

    /**
     * Short description of method cnvArray
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  object
     * @return string
     */
    public static function cnvArray($object)
    {
        $returnValue = (string) '';

        // section 127-0-0-1-558a6b3:11c2370bc00:-8000:0000000000000EAD begin
        $delimiter = "|"; // must be different from that of CSV!
        if (is_array($object)) {
          $buffer = "";
          foreach ($object as $elt) {
            $elt = common_Utils::csvFormat(self::convert($elt), $delimiter);
            $buffer .= $elt.$delimiter;
          }
          $returnValue = substr($buffer, 0, -1); // removes the last delimiter
        }
        // section 127-0-0-1-558a6b3:11c2370bc00:-8000:0000000000000EAD end

        return (string) $returnValue;
    }

    /**
     * Short description of method cnvResource
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  object
     * @return string
     */
    public static function cnvResource($object)
    {
        $returnValue = (string) '';

        // section 127-0-0-1-4a23c3d2:11c0e6bcc2f:-8000:0000000000000EB0 begin
        if ($object instanceof core_kernel_classes_Resource)
          $returnValue = $object->uriResource;
        // section 127-0-0-1-4a23c3d2:11c0e6bcc2f:-8000:0000000000000EB0 end

        return (string) $returnValue;
    }

    /**
     * Short description of method cnvToString
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  object
     * @return string
     */
    public static function cnvToString($object)
    {
        $returnValue = (string) '';

        // section 127-0-0-1-293e509c:11c0d865d9e:-8000:0000000000000E86 begin
        if (method_exists($object, "__toString")) {
          $returnValue = $object->__toString();
        }
        // section 127-0-0-1-293e509c:11c0d865d9e:-8000:0000000000000E86 end

        return (string) $returnValue;
    }

    /**
     * Short description of method cnvObjectDebug
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  object
     * @return string
     */
    public static function cnvObjectDebug($object)
    {
        $returnValue = (string) '';

        // section 127-0-0-1-25ef1450:11c28a1d2dd:-8000:0000000000000E73 begin
        if (@property_exists($object, "debug"))
          $returnValue = $object->debug;
        // section 127-0-0-1-25ef1450:11c28a1d2dd:-8000:0000000000000E73 end

        return (string) $returnValue;
    }

    /**
     * Short description of method cnvDefault
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  object
     * @return string
     */
    public static function cnvDefault($object)
    {
        $returnValue = (string) '';

        // section 127-0-0-1-293e509c:11c0d865d9e:-8000:0000000000000E89 begin
        $returnValue = EVENT_NOT_UNDERSTOOD;  //serialize($object);
        // section 127-0-0-1-293e509c:11c0d865d9e:-8000:0000000000000E89 end

        return (string) $returnValue;
    }

} /* end of class core_kernel_events_EventParamConverter */

?>