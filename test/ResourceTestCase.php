<?php

require_once dirname(__FILE__).'/../common/common.php';
require_once INCLUDES_PATH.'/simpletest/autorun.php';



class ResourceTestCase extends UnitTestCase{
	
	protected $object;
	
	public function setUp(){
		TestRunner::initTest();
		
		$this->object = new core_kernel_classes_Resource(GENERIS_BOOLEAN);
	}

	public function testGetPropertyValuesCollection(){
		
		$class = new core_kernel_classes_Class(GENERIS_BOOLEAN,__METHOD__);
		$instance = $class->createInstance('test' , 'test');
		$seeAlso = new core_kernel_classes_Property(RDFS_SEEALSO,__METHOD__); 
		$api = core_kernel_impl_ApiModelOO::singleton();
		$api->setStatement($instance->uriResource,RDFS_SEEALSO,GENERIS_TRUE,'');
		$api->setStatement($instance->uriResource,RDFS_SEEALSO,GENERIS_FALSE,'');
		$api->setStatement($instance->uriResource,RDFS_SEEALSO,'plop','');
		
		
		$collection = $instance->getPropertyValuesCollection($seeAlso);
		foreach ($collection->getIterator() as $value) {
			$this->assertIsA($value, 'core_kernel_classes_Container' );
			if($value instanceof core_kernel_classes_Resource ){
				$this->assertTrue($value->uriResource == GENERIS_TRUE || $value->uriResource ==GENERIS_FALSE);
			}
			if ( $value instanceof core_kernel_classes_Literal){
				$this->assertEqual($value->literal, 'plop');
			}
			
		}
		
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
		$this->assertIsA($seeAlso, core_kernel_classes_Literal);
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
		$class = new core_kernel_classes_Class(GENERIS_BOOLEAN,__METHOD__);
		$instance = $class->createInstance('test' , 'test');
		$seeAlso = $class->createProperty('seeAlso','multilingue',true);
		$this->assertEqual($one,null);
		$instance->setPropertyValue($seeAlso,'plop');
		$one = $instance->getOnePropertyValue($seeAlso);
		$this->assertEqual($one->literal,'plop');

		$instance->delete();
		$seeAlso->delete();
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
}
?>