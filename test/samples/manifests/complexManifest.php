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