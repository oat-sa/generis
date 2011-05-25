<?php

error_reporting(E_ALL);

/**
 * description of the current configuration of an extension
 *
 * @author lionel.lecaque@tudor.lu
 * @package common
 * @see @license  GNU General Public (GPL) Version 2 http://www.opensource.org/licenses/gpl-2.0.php
 * @subpackage ext
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section -87--2--3--76--570dd3e1:12507aae5fa:-8000:0000000000002388-includes begin
// section -87--2--3--76--570dd3e1:12507aae5fa:-8000:0000000000002388-includes end

/* user defined constants */
// section -87--2--3--76--570dd3e1:12507aae5fa:-8000:0000000000002388-constants begin
// section -87--2--3--76--570dd3e1:12507aae5fa:-8000:0000000000002388-constants end

/**
 * description of the current configuration of an extension
 *
 * @access public
 * @author lionel.lecaque@tudor.lu
 * @package common
 * @see @license  GNU General Public (GPL) Version 2 http://www.opensource.org/licenses/gpl-2.0.php
 * @subpackage ext
 */
class common_ext_ExtensionConfiguration
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * is the extension currently loaded
     *
     * @access public
     * @var boolean
     */
    public $loaded = false;

    /**
     * is the extension has to be loaded at startup
     *
     * @access public
     * @var boolean
     */
    public $loadedAtStartUp = false;

    /**
     * this stauts enables you to control the visibility of the extension.
     *
     * @access public
     * @var boolean
     */
    public $ghost = false;

    /**
     * Short description of attribute version
     *
     * @access public
     * @var string
     */
    public $version = '';

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  boolean loaded
     * @param  boolean loadedAtStartUp
     * @param  boolean ghost
     * @param  string version
     * @return mixed
     */
    public function __construct($loaded, $loadedAtStartUp, $ghost = false, $version = '')
    {
        // section -87--2--3--76--570dd3e1:12507aae5fa:-8000:0000000000002392 begin
        $this->loaded =$loaded;
        $this->loadedAtStartUp = $loadedAtStartUp;
        $this->ghost = $ghost;
		$this->version = $version;
        // section -87--2--3--76--570dd3e1:12507aae5fa:-8000:0000000000002392 end
    }

    /**
     * Update the config stored in the database
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  SimpleExtension extension
     * @return mixed
     */
    public function save( common_ext_SimpleExtension $extension)
    {
        // section -87--2--3--76--570dd3e1:12507aae5fa:-8000:0000000000002396 begin
        $db = core_kernel_classes_DbWrapper::singleton(DATABASE_NAME);
		if($this->loaded){
			$set1 = "`loaded` = '1'";
		}
		else {
			$set1 = "`loaded` = '0'";
		}
		if($this->loadedAtStartUp){
			$set2 = "`loadAtStartUp` = '1'";
		}
		else {
			$set2 = "`loadAtStartUp` = '0'";
		}
    	if($this->ghost){
			$set3 = "`ghost` = '1'";
		}
		else {
			$set3 = "`ghost` = '0'";
		}

		$sql = "UPDATE `extensions` SET " . $set1 ." , ". $set2 . ", ". $set3 . " WHERE `id` ='". $extension->id."';";
		$db->execSql($sql);
        // section -87--2--3--76--570dd3e1:12507aae5fa:-8000:0000000000002396 end
    }

} /* end of class common_ext_ExtensionConfiguration */

?>