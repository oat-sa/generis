<?php

/**
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
return array(
	'name' => 'generis',
	'description' => 'Core extension, provide the low level framework and an API to manage ontologies',
	'additional' => array(
		'version' => '2.0',
		'author' => 'CRP Henry Tudor',
		'dependances' 	=> array(),
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
				dirname(__FILE__). '/core/ontology/22-rdf-syntax-ns.rdf',
				dirname(__FILE__). '/core/ontology/rdf-schema.rdf',
				dirname(__FILE__). '/core/ontology/widgetdefinitions.rdf',
				dirname(__FILE__). '/core/ontology/rules.rdf',
				dirname(__FILE__). '/core/ontology/widgetdefinitions.rdf',
				dirname(__FILE__). '/core/ontology/generis.rdf'
			)
		),
		'classLoaderPackages' => array( 
			dirname(__FILE__).'/actions/'
		 )
	)
);
?>