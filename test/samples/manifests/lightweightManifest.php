<?php
/**
 * This lightweight manifest is based on the TAO filemanager one.
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
$extpath = dirname(__FILE__).DIRECTORY_SEPARATOR;
$taopath = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'tao'.DIRECTORY_SEPARATOR;

return array(
	'name' => 'lightweight',
	'description' => 'lightweight testing manifest',
	'version' => '1.0',
	'author' => 'TAO Team',
	'dependencies' => array('tao'),
	'classLoaderPackages' => array(
		dirname(__FILE__).'/actions/'
	 )
);
?>