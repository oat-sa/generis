<?php

require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
require_once INCLUDES_PATH.'/simpletest/autorun.php';



class ResourceTestCase extends UnitTestCase{

	protected $object;

	public function setUp(){
		TestRunner::initTest();

		$this->object = new core_kernel_classes_Resource(GENERIS_BOOLEAN);
	}

	public function testGetPropertyValuesCollection(){

		$session = core_kernel_classes_Session::singleton();
		$class = new core_kernel_classes_Class(GENERIS_BOOLEAN,__METHOD__);
		$instance = $class->createInstance('test' , 'test');
		$seeAlso = new core_kernel_classes_Property(RDFS_SEEALSO,__METHOD__);
		$api = core_kernel_impl_ApiModelOO::singleton();
		$api->setStatement($instance->uriResource,RDFS_SEEALSO,GENERIS_TRUE,'');
		$api->setStatement($instance->uriResource,RDFS_SEEALSO,GENERIS_FALSE,'');
		$api->setStatement($instance->uriResource,RDFS_SEEALSO,'plop','');
		$api->setStatement($instance->uriResource,RDFS_SEEALSO,'plup','FR');
		$api->setStatement($instance->uriResource,RDFS_SEEALSO,'plip','FR');
		$api->setStatement($instance->uriResource,RDFS_SEEALSO,GENERIS_TRUE,'FR');

		// Default language is EN (English) so that we should get a collection
		// containing 3 triples because we will receive the ones with no language
		// tags (2 instances of GENERIS_BOOLEAN and 'plop'.
		// Session::lg should contain a empty string.
		$collection = $instance->getPropertyValuesCollection($seeAlso);

		$this->assertTrue($collection->count() == 3);
		foreach ($collection->getIterator() as $value) {
			$this->assertIsA($value, 'core_kernel_classes_Container' );
			if($value instanceof core_kernel_classes_Resource ){
				$this->assertTrue($value->uriResource == GENERIS_TRUE || $value->uriResource == GENERIS_FALSE);
			}
			if ( $value instanceof core_kernel_classes_Literal){
				$this->assertEqual($value->literal, 'plop');
			}
		}

		// We now explicitly change the current language to EN (English), we should
		// get exactly the same behaviour.
		$session->setLg('EN');
		$collection = $instance->getPropertyValuesCollection($seeAlso);
		$this->assertTrue($collection->count() == 3);
		foreach ($collection->getIterator() as $value) {
			$this->assertIsA($value, 'core_kernel_classes_Container' );
			if($value instanceof core_kernel_classes_Resource ){
				$this->assertTrue($value->uriResource == GENERIS_TRUE || $value->uriResource == GENERIS_FALSE);
			}
			if ( $value instanceof core_kernel_classes_Literal){
				$this->assertEqual($value->literal, 'plop');
			}
		}

		// We now go to FR (French). we should receive a collection of 3 values:
		// a Generis True, 'plup'@fr, 'plip'@fr.
		$session->setLg('FR');
		$collection = $instance->getPropertyValuesCollection($seeAlso);
		$this->assertTrue($collection->count() == 3);
		foreach ($collection->getIterator() as $value) {
			$this->assertIsA($value, 'core_kernel_classes_Container' );
			if($value instanceof core_kernel_classes_Resource ){
				$this->assertTrue($value->uriResource == GENERIS_TRUE, $value->uriResource . ' must be equal to ' . GENERIS_TRUE);
			}
			if ( $value instanceof core_kernel_classes_Literal){
				$this->assertTrue($value->literal == 'plup' || $value->literal == 'plip', $value->literal . ' must be equal to plip or plop');
			}
		}
		
		// Back to normal.
		$session->setLg('');

		$instance->delete();
	}

	public function testGetRdfTriples(){
		$collectionTriple = $this->object->getRdfTriples();

		$this->assertTrue($collectionTriple instanceof common_Collection );
		foreach ($collectionTriple->getIterator() as $triple){
			$this->assertTrue( $triple instanceof core_kernel_classes_Triple );
			$this->assertEqual($triple->subject, GENERIS_BOOLEAN );
			if ($triple->predicate === RDFS_LABEL) {
				$this->assertEqual($triple->object,'Boolean' );
				$this->assertEqual($triple->lg, 'EN' );
			}
			if ($triple->predicate === RDFS_COMMENT) {
				$this->assertEqual($triple->object,'Boolean' );
				$this->assertEqual($triple->lg, 'EN' );
			}
		}

	}


	public function testDelete(){

		$class = new core_kernel_classes_Class(GENERIS_BOOLEAN,__METHOD__);

		$instance = $class->createInstance('test' , 'test');
		$label = new core_kernel_classes_Property(RDFS_LABEL,__METHOD__);
		$this->assertTrue($instance->delete());
		$this->assertTrue($instance->getPropertyValuesCollection($label)->isEmpty());

		$class2 = new core_kernel_classes_Class(GENERIS_BOOLEAN,__METHOD__);
		$instance2 = $class->createInstance('test2' , 'test2');
		$property2 = $class->createProperty('multi','multi',true);

		$instance3 = $class->createInstance('test3' , 'test3');
		$instance3->setPropertyValue($property2, $instance2->uriResource);

		$api = core_kernel_impl_ApiModelOO::singleton();
		$api->setStatement($instance2->uriResource,$property2->uriResource,'vrai','FR');
		$api->setStatement($instance2->uriResource,$property2->uriResource,'true','EN');

		$this->assertTrue($instance2->delete(true));
		$this->assertNull($instance3->getOnePropertyValue($property2));
		$this->assertTrue($instance3->delete());
		$this->assertTrue($property2->delete());
	}


	public function testSetPropertyValue(){
		$class = new core_kernel_classes_Class(GENERIS_BOOLEAN,__METHOD__);
		$instance = $class->createInstance('test', 'test');
		$seeAlso = new core_kernel_classes_Property(RDFS_SEEALSO,__METHOD__);
		$instance->setPropertyValue($seeAlso,GENERIS_TRUE);
		$instance->setPropertyValue($seeAlso,GENERIS_FALSE);
		$instance->setPropertyValue($seeAlso,"&plop n'\"; plop'\' plop");
		$collection = $instance->getPropertyValuesCollection($seeAlso);
		foreach ($collection->getIterator() as $value) {
			$this->assertIsA($value, 'core_kernel_classes_Container' );
			if($value instanceof core_kernel_classes_Resource ){
				$this->assertTrue($value->uriResource == GENERIS_TRUE || $value->uriResource ==GENERIS_FALSE);
			}
			if ( $value instanceof core_kernel_classes_Literal){
				$this->assertEqual($value->literal, "&plop n'\"; plop'\' plop");
			}
		}
		$instance->delete(true);
	}

	public function testSetPropertiesValues(){

		$class = new core_kernel_classes_Class(GENERIS_BOOLEAN );
		$instance = $class->createInstance('a label', 'a comment');
		$this->assertIsA($instance, 'core_kernel_classes_Resource' );

		$instance->setPropertiesValues(array(
		RDFS_SEEALSO	=> "&plop n'\"; plop'\' plop",
		RDFS_LABEL		=> 'new label',
		RDFS_COMMENT 	=> 'new comment'
		));

		$seeAlso = $instance->getOnePropertyValue(new core_kernel_classes_Property(RDFS_SEEALSO));
		$this->assertNotNull($seeAlso);
		$this->assertIsA($seeAlso, 'core_kernel_classes_Literal');
		$this->assertEqual($seeAlso->literal, "&plop n'\"; plop'\' plop");

		$instance->delete(true);
	}

	public function testGetUsedLanguages(){
		$class = new core_kernel_classes_Class(GENERIS_BOOLEAN,__METHOD__);
		$instance = $class->createInstance('test' , 'test');
		$seeAlso = $class->createProperty('seeAlso','multilingue',true);

		$api = core_kernel_impl_ApiModelOO::singleton();
		$api->setStatement($instance->uriResource,$seeAlso->uriResource,GENERIS_TRUE,'FR');
		$api->setStatement($instance->uriResource,$seeAlso->uriResource,GENERIS_TRUE,'EN');
		$lg = $instance->getUsedLanguages($seeAlso);
		$this->assertTrue(in_array('FR',$lg));
		$this->assertTrue(in_array('EN',$lg));
		$seeAlso->delete();
		$instance->delete();

	}

	public function testGetPropertyValuesByLg(){
		$class = new core_kernel_classes_Class(GENERIS_BOOLEAN,__METHOD__);
		$instance = $class->createInstance('test' , 'test');
		$seeAlso = $class->createProperty('seeAlso','multilingue',true);
		$api = core_kernel_impl_ApiModelOO::singleton();
		$api->setStatement($instance->uriResource,$seeAlso->uriResource,'vrai','FR');
		$api->setStatement($instance->uriResource,$seeAlso->uriResource,'vrai peut etre','FR');
		$api->setStatement($instance->uriResource,$seeAlso->uriResource,'true','EN');

		$collectionFr = $instance->getPropertyValuesByLg($seeAlso,'FR');
		$this->assertTrue($collectionFr->count() == 2);
		$collectionEn = $instance->getPropertyValuesByLg($seeAlso,'EN');
		$this->assertTrue($collectionEn->count() == 1);
		$this->assertTrue($collectionFr->get(0)->literal == 'vrai peut etre' || $collectionFr->get(0)->literal == 'vrai');
		$this->assertTrue($collectionFr->get(1)->literal == 'vrai peut etre' || $collectionFr->get(1)->literal == 'vrai');
		$this->assertTrue($collectionEn->get(0)->literal == 'true');
		$instance->delete();
		$seeAlso->delete();

	}

	public function testSetPropertyValueByLg(){
		$class = new core_kernel_classes_Class(GENERIS_BOOLEAN,__METHOD__);
		$instance = $class->createInstance('test' , 'test');
		$seeAlso = $class->createProperty('seeAlso','multilingue',true);

		$instance->setPropertyValueByLg($seeAlso,'vrai','FR');
		$instance->setPropertyValueByLg($seeAlso,'vrai peut etre','FR');
		$instance->setPropertyValueByLg($seeAlso,'true','EN');

		$collectionFr = $instance->getPropertyValuesByLg($seeAlso,'FR');
		$this->assertTrue($collectionFr->count() == 2);
		$collectionEn = $instance->getPropertyValuesByLg($seeAlso,'EN');
		$this->assertTrue($collectionEn->count() == 1);
		$this->assertTrue($collectionFr->get(0)->literal == 'vrai peut etre' || $collectionFr->get(0)->literal == 'vrai');
		$this->assertTrue($collectionFr->get(1)->literal == 'vrai peut etre' || $collectionFr->get(1)->literal == 'vrai');
		$this->assertTrue($collectionEn->get(0)->literal == 'true');
		$instance->delete();
		$seeAlso->delete();
	}

	public function testRemovePropertyValueByLg(){
		$class = new core_kernel_classes_Class(GENERIS_BOOLEAN,__METHOD__);
		$instance = $class->createInstance('test' , 'test');
		$seeAlso = $class->createProperty('seeAlso','multilingue',true);

		$instance->setPropertyValueByLg($seeAlso,'vrai','FR');
		$instance->setPropertyValueByLg($seeAlso,'vrai peut etre','FR');
		$instance->setPropertyValueByLg($seeAlso,'true','EN');

		$this->assertTrue($instance->removePropertyValueByLg($seeAlso,'FR'));
		$collectionFr = $instance->getPropertyValuesByLg($seeAlso,'FR');
		$this->assertTrue($collectionFr->count() == 0);
		$collectionEn = $instance->getPropertyValuesByLg($seeAlso,'EN');
		$this->assertTrue($collectionEn->count() == 1);
		$this->assertTrue($collectionEn->get(0)->literal == 'true');

		$instance->delete();
		$seeAlso->delete();
	}
	
	public function testRemovePropertyValues(){
		$session = core_kernel_classes_Session::singleton();
		$class = new core_kernel_classes_Class(GENERIS_BOOLEAN,__METHOD__);
		$instance = $class->createInstance('test', 'test');
		$instance2 = $class->createInstance('test2', 'test2');
		
		$prop1 = $class->createProperty('property1','monologingual');
		$prop2 = $class->createProperty('property2','multilingual',true);
		
		// We first go with monolingual property.
		$instance->setPropertyValue($prop1, 'mono');
		$propValue = $instance->getOnePropertyValue($prop1);
		$this->assertTrue($propValue->literal == 'mono');
		$this->assertTrue($instance->removePropertyValues($prop1));
		$this->assertTrue(count($instance->getPropertyValues($prop1)) == 0);
		
		// And new we go multilingual.
		$instance->setPropertyValue($prop2,'multi');
		$instance->setPropertyValueByLg($prop2,'multiFR1','FR');
		$instance->setPropertyValueByLg($prop2,'multiFR2','FR');
		$instance->setPropertyValueByLg($prop2,'multiSE1','SE');
		$instance->setPropertyValueByLg($prop2,'multiSE1','SE');
		$instance->setPropertyValueByLg($prop2,'multiJA','JA');
		$this->assertTrue(count($instance->getPropertyValues($prop2)) == 1);
		
		// We are by default in EN language (English). If we call removePropertyValues
		// for property values on a language dependant property, we should only remove
		// one triple with value 'multi'@EN.
		$this->assertTrue(count($instance->getPropertyValues($prop2)) == 1);
		$this->assertTrue($instance->removePropertyValues($prop2));
		$this->assertTrue(count($instance->getPropertyValues($prop2)) == 0);
		
		// We now switch to Swedish language and remove the values in the language.
		$session->setLg('SE');
		$this->assertTrue(count($instance->getPropertyValues($prop2)) == 2);
		$this->assertTrue($instance->removePropertyValues($prop2));
		$this->assertTrue(count($instance->getPropertyValues($prop2)) == 0);
		
		// Same as above with Japanese language.
		$session->setLg('JA');
		$this->assertTrue(count($instance->getPropertyValues($prop2)) == 1);
		$this->assertTrue($instance->removePropertyValues($prop2));
		$this->assertTrue(count($instance->getPropertyValues($prop2)) == 0);
		
		// And now final check in French language.
		$session->setLg('FR');
		$this->assertTrue(count($instance->getPropertyValues($prop2)) == 2);
		$this->assertTrue($instance->removePropertyValues($prop2));
		$this->assertTrue(count($instance->getPropertyValues($prop2)) == 0);
		
		$prop1->delete();
		$prop2->delete();
		$instance->delete();
		$instance2->delete();
	}

	public function testEditPropertyValueByLg(){
		$class = new core_kernel_classes_Class(GENERIS_BOOLEAN,__METHOD__);
		$instance = $class->createInstance('test' , 'test');
		$seeAlso = $class->createProperty('seeAlso','multilingue',true);

		$instance->setPropertyValueByLg($seeAlso,'vrai','FR');
		$instance->setPropertyValueByLg($seeAlso,'vrai peut etre','FR');
		$instance->setPropertyValueByLg($seeAlso,'true','EN');

		$this->assertTrue($instance->editPropertyValueByLg($seeAlso, 'aboslutly true','EN'));
		$collectionEn = $instance->getPropertyValuesByLg($seeAlso,'EN');
		$this->assertTrue($collectionEn->count() == 1);
		$this->assertTrue($collectionEn->get(0)->literal == 'aboslutly true');

		$instance->delete();
		$seeAlso->delete();
	}

	public function testGetOnePropertyValue(){
		$session = core_kernel_classes_Session::singleton();
		$class = new core_kernel_classes_Class(GENERIS_BOOLEAN,__METHOD__);
		$instance = $class->createInstance('test' , 'test');
		$seeAlso = $class->createProperty('seeAlsoDo','multilingue',true);

		// If there is no value for the targeted property,
		// it should return null.
		$one = $instance->getOnePropertyValue($seeAlso);
		$this->assertEqual($one,null);

		
		$instance->setPropertyValue($seeAlso,'plop');
		$one = $instance->getOnePropertyValue($seeAlso);
		$this->assertEqual($one->literal,'plop');

		// We now try with multiple property values. Which
		// We first want to get the first inserted value for
		// the property.
		// !!! DOES NOT WORK, THE RETURN OF SELECT DOES NOT RESPECT THE INSERT' ORDER
		/*$instance->setPropertyValue($seeAlso,'plip');
		$one = $instance->getOnePropertyValue($seeAlso);
		$this->assertEqual($one->literal, 'plop');
		$one = $instance->getOnePropertyValue($seeAlso, true);
		$this->assertEqual($one->literal, 'plip');*/

		// We now go multilingual.
		$session->setLg('FR');
		$instance->setPropertyValue($seeAlso, 'plopFR');
		$one = $instance->getPropertyValuesByLg($seeAlso, 'FR');
		
		// Back to default language.
		$session->setLg('');

		$instance->delete();
	}

	public function testGetType(){
		$class = new core_kernel_classes_Class(GENERIS_BOOLEAN,__METHOD__);
		$instance = $class->createInstance('test' , 'test');
		$typeUri = array_keys($instance->getType());
		$this->assertEqual($typeUri[0],GENERIS_BOOLEAN);
		$this->assertTrue(count($typeUri) == 1);
		$instance->delete();
	}

	public function testGetComment(){
		$inst = new core_kernel_classes_Resource(CLASS_GENERIS_RESOURCE);
		$this->assertFalse($inst->label == 'generis_Ressource');
		$this->assertTrue($inst->getLabel()== 'generis_Ressource');
		$this->assertTrue($inst->label == 'generis_Ressource');
	  
		$this->assertFalse($inst->comment == 'generis_Ressource');
		$this->assertTrue($inst->getComment() == 'generis_Ressource');
		$this->assertTrue($inst->comment == 'generis_Ressource');
	}
	
	public function testGetLastModificationDate(){
	    $itemClass = new core_kernel_classes_Class(GENERIS_BOOLEAN);

	    $newInstance = $itemClass->createInstance('date','date' );
	    $propType = new core_kernel_classes_Property(RDF_TYPE);
	    $instances = $itemClass->getInstances();
	   
	    $now = new DateTime();
	    $this->assertTrue( $newInstance->getLastModificationDate() == $now);
	    sleep(2);
	    $newInstance->setLabel('change Date');
	    $labelChange = $newInstance->getLastModificationDate();
	    $this->assertTrue($labelChange  > $now);
	    $typeChnge = $newInstance->getLastModificationDate(new core_kernel_classes_Property(RDF_TYPE));
	    $this->assertTrue($labelChange  > $typeChnge);
	    $this->assertTrue($now ==  $typeChnge);
	    $newInstance->delete();
	}
}
?>