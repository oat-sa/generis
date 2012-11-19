<?php

/**
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */

return array(
	'name' => 'complex',
	'description' => 'complex testing manifest',
	'version' => '1.0',
	'author' => 'TAO Team',
	'dependencies' => array('taoItemBank', 'taoDocuments'),
	'models' => array(
		'http://www.tao.lu/Ontologies/taoFuncACL.rdf',
		'http://www.tao.lu/Ontologies/taoItemBank.rdf'
	),
	'install' => array(
		'rdf' => array(
				array('ns' => 'http://www.tao.lu/Ontologies/taoFuncACL.rdf', 'file' => '/extension/path/models/ontology/taofuncacl.rdf'),
				array('ns' => 'http://www.tao.lu/Ontologies/taoItemBank.rdf', 'file' => '/extension/path/models/ontology/taoitembank.rdf')
		),
		'checks' => array(
			array('type' => 'CheckPHPRuntime', 'value' => array('id' => 'php_runtime', 'min' => '5.3', 'max' => '5.3.18')),
			array('type' => 'CheckPHPExtension', 'value' => array('id' => 'ext_pdo', 'name' => 'PDO')),
			array('type' => 'CheckPHPExtension', 'value' => array('id' => 'ext_svn','name' => 'svn', 'optional' => true)),
			array('type' => 'CheckPHPExtension', 'value' => array('id' => 'ext_suhosin','name' => 'suhosin', 'optional' => true)),
			array('type' => 'CheckPHPINIValue', 'value' => array('id' => 'ini_register_globals', 'name' => 'register_globals', 'value' => "0")),
			array('type' => 'CheckFileSystemComponent', 'value' => array('id' => 'fs_root','location' => '.', 'rights' => 'rw', 'name' => 'fs_root')),
		)
	),
	'classLoaderPackages' => array(
		'extension/path/actions/',
		'extension/path/helpers/',
		'extension/path/helpers/form'
	 ),
	 'constants' => array(
		 // web services
         'WS_ENDPOINT_TWITTER' => 'http://twitter.com/statuses/',
         'WS_ENDPOINT_FACEBOOK' => 'http://api.facebook.com/restserver.php'
	 )
);
?>