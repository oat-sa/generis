<?php

/**
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
return array(
	'name' => 'generis',
	'description' => 'Core extension, provide the low level framework and an API to manage ontologies',
	'version' => '2.3',
	'author' => 'CRP Henry Tudor',
	'dependencies' 	=> array(),
	'models' => array(
			'http://www.w3.org/1999/02/22-rdf-syntax-ns',
			'http://www.w3.org/2000/01/rdf-schema',
			'http://www.tao.lu/datatypes/WidgetDefinitions.rdf',
			'http://www.tao.lu/middleware/Rules.rdf',
			'http://www.tao.lu/Ontologies/generis.rdf'
		),
	'install' => array(
		'php' => dirname(__FILE__). '/install/install.php',
		'rdf' => array(
				array('ns' => 'http://www.w3.org/1999/02/22-rdf-syntax-ns', 'file' => dirname(__FILE__). '/core/ontology/22-rdf-syntax-ns.rdf'),
				array('ns' => 'http://www.w3.org/2000/01/rdf-schema', 'file' => dirname(__FILE__). '/core/ontology/rdf-schema.rdf'),
				array('ns' => 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf', 'file' => dirname(__FILE__). '/core/ontology/widgetdefinitions.rdf'),
				array('ns' => 'http://www.tao.lu/middleware/Rules.rdf', 'file' => dirname(__FILE__). '/core/ontology/rules.rdf'),
				array('ns' => 'http://www.tao.lu/Ontologies/generis.rdf', 'file' => dirname(__FILE__). '/core/ontology/generis.rdf'),
		)
	),
	'classLoaderPackages' => array(
		dirname(__FILE__).'/actions/'
	)
);
?>