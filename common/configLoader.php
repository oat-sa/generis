<?php
/**
 * Generis Object Oriented API - common\config.php
 *
 * This file is part of Generis Object Oriented API.
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package common
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */

#define path
define('GENERIS_BASE_PATH' , realpath(dirname(__FILE__).'/../'));

# generis
include_once dirname(__FILE__).'/conf/generis.conf.php';

# database
include_once dirname(__FILE__).'/conf/db.conf.php';

$defaultIterator =  new DirectoryIterator(dirname(__FILE__).'/conf/default');

foreach($defaultIterator as $fileinfo){
	if(!$fileinfo->isDot() && strpos( $fileinfo->getFilename(),'.conf.php')>0){
		$conf = $fileinfo->getFilename();
		if(file_exists(dirname(__FILE__).'/conf/'.$conf)){
			include_once dirname(__FILE__).'/conf/'.$conf;
		}
		else{
			include_once dirname(__FILE__).'/conf/default/'.$conf;
		}
	}
}


?>