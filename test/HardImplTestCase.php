<?php
require_once dirname(__FILE__) . '/GenerisTestRunner.php';

class HardImplTestCase extends UnitTestCase {
	
	protected $targetSubjectClass = null;
	protected $targetSubjectSubClass = null;
	protected $targetWorkClass = null;
	protected $targetMovieClass = null;
	protected $taoClass = null;
	protected $targetAuthorProperty = null;
	protected $targetProducerProperty = null;
	protected $targetActorsProperty = null;
	protected $targetRelatedMoviesProperty = null;
	
	public function setUp(){

		GenerisTestRunner::initTest();

	}
	
	public function testCreateContextOfThetest(){
		// ----- Top Class : TaoSubject
		$subjectClass = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject');
		// Create a new subject class for the unit test
		$this->targetSubjectClass = $subjectClass->createSubClass ("Sub Subject Class (Unit Test)");
		// Add a custom property to the newly created class

		// Add an instance to this subject class
		$this->subject1 = $this->targetSubjectClass->createInstance ("Sub Subject (Unit Test)");
		$this->assertEqual (count($this->targetSubjectClass->getInstances ()), 1);
		
		// Create a new subject sub class to the previous sub class
		$this->targetSubjectSubClass = $this->targetSubjectClass->createSubClass ("Sub Sub Subject Class (Unit Test)");
		// Add an instance to this sub subject class
		$this->subject2 = $this->targetSubjectSubClass->createInstance ("Sub Sub Subject (Unit Test)");
		$this->assertEqual (count($this->targetSubjectSubClass->getInstances ()), 1);
		
		$this->assertEqual (count($this->targetSubjectClass->getInstances ()), 1);
		// If get instances in the sub classes of the targetSubjectClass, we should get 2 instances
		$this->assertEqual (count($this->targetSubjectClass->getInstances (true)), 2);
		
		// ----- Top Class : Work
		// Create a class and test its instances & properties.
		$this->taoClass = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAO.rdf#TAOObject');
		$this->targetWorkClass = $this->taoClass->createSubClass('Work', 'The Work class');
		
		// Add properties to the Work class.
		$this->targetAuthorProperty = $this->targetWorkClass->createProperty('Author', 'The author of the work.');
		$literalClass = new core_kernel_classes_Class(RDFS_LITERAL);
		$this->targetAuthorProperty->setRange($literalClass);
		
		// Create the Movie class that extends the Work class. 
		$this->targetMovieClass = $this->targetWorkClass->createSubClass('Movie', 'The Movie class');
		$this->targetMovieClass = new core_kernel_classes_Class($this->targetMovieClass->uriResource);
		$this->assertTrue($this->targetMovieClass->isSubClassOf($this->targetWorkClass));
		$this->assertEqual(count($this->targetWorkClass->getSubClasses()), 1);
		
		// Add properties to the Movie class.
		$this->targetProducerProperty = $this->targetMovieClass->createProperty('Producer', 'The producer of the movie.');
		$this->targetProducerProperty->setRange($literalClass);
		$this->targetProducerProperty->setMultiple(true);
		$this->assertTrue($this->targetProducerProperty->isMultiple());
		$this->targetActorsProperty = $this->targetMovieClass->createProperty('Actors', 'The actors playing in the movie.');
		$this->targetActorsProperty->setRange($literalClass);
		$this->targetActorsProperty->setMultiple(true);
		$this->targetRelatedMoviesProperty = $this->targetMovieClass->createProperty('Related Movies', 'Movies related to the movie.');
		$this->targetRelatedMoviesProperty->setRange($this->targetMovieClass);
		$this->targetRelatedMoviesProperty->setMultiple(true);
	}
	
	public function testHardifier (){
		$switcher = new core_kernel_persistence_Switcher();
		$switcher->hardify($this->targetSubjectClass, array(
			'topClass'				=> new core_kernel_classes_Class('http://www.tao.lu/Ontologies/generis.rdf#User'),
			'additionalProperties' 	=> array (new core_kernel_classes_Property (RDF_TYPE)),
			'recursive'				=> true,
			'createForeigns'		=> true
		));
		
		$switcher->hardify($this->targetWorkClass, array(
			'topClass'			=> $this->targetWorkClass,
			'recursive' 		=> true,
			'createForeigns'	=> true
		));
	}
	
	public function testHardSwitchOK(){
		// Test that resource are now available from the hard sql implementation
		$persistenceProxy = core_kernel_persistence_ClassProxy::singleton();
		$this->assertIsA($persistenceProxy->getImpToDelegateTo($this->targetSubjectClass), 'core_kernel_persistence_hardsql_Class');
		$this->assertIsA($persistenceProxy->getImpToDelegateTo($this->targetSubjectSubClass), 'core_kernel_persistence_hardsql_Class');
		$this->assertIsA($persistenceProxy->getImpToDelegateTo($this->targetWorkClass), 'core_kernel_persistence_hardsql_Class');
		$this->assertIsA($persistenceProxy->getImpToDelegateTo($this->targetMovieClass), 'core_kernel_persistence_hardsql_Class');
	}
	
	public function testHardModel(){
		$referencer = core_kernel_persistence_hardapi_ResourceReferencer::singleton();
		$proxy = core_kernel_persistence_ResourceProxy::singleton();
		
		$domainProperty = new core_kernel_classes_Property(RDFS_DOMAIN);
		$rangeProperty = new core_kernel_classes_Property(RDFS_RANGE);
		$literalClass = new core_kernel_classes_Class(RDFS_LITERAL);
		$subClassOfProperty = new core_kernel_classes_Property(RDFS_SUBCLASSOF);
		
		$this->assertTrue($this->targetActorsProperty->exists());
		$this->assertTrue($this->targetMovieClass->exists());
		
		$this->assertTrue($this->targetWorkClass->isSubclassOf($this->taoClass));
		$this->assertTrue($this->targetWorkClass->getOnePropertyValue($subClassOfProperty)->uriResource == $this->taoClass->uriResource);
		$this->assertTrue($referencer->isClassReferenced($this->targetWorkClass));
		// Note for developers: data defining classes always remain in smooth mode.
		// Thus, the delegation is always 'smooth'.
		$this->assertFalse(is_a($proxy->getImpToDelegateTo($this->targetWorkClass), 'core_kernel_persistence_smoothsql_Class'));
		
		$this->assertTrue($this->targetAuthorProperty->getOnePropertyValue($domainProperty)->uriResource == $this->targetWorkClass->uriResource);
		$this->assertTrue($this->targetAuthorProperty->getOnePropertyValue($rangeProperty)->uriResource == RDFS_LITERAL);
		
		$this->assertTrue($this->targetMovieClass->isSubclassOf($this->targetWorkClass));
		$this->assertTrue($this->targetMovieClass->getOnePropertyValue($subClassOfProperty)->uriResource == $this->targetWorkClass->uriResource);
		$this->assertTrue($referencer->isClassReferenced($this->targetMovieClass));
		
		$this->assertTrue($this->targetProducerProperty->getOnePropertyValue($domainProperty)->uriResource == $this->targetMovieClass->uriResource);
		$this->assertTrue($this->targetProducerProperty->getOnePropertyValue($rangeProperty)->uriResource == RDFS_LITERAL);
		
		$this->assertTrue($this->targetActorsProperty->getOnePropertyValue($domainProperty)->uriResource == $this->targetMovieClass->uriResource);
		$this->assertTrue($this->targetActorsProperty->getOnePropertyValue($rangeProperty)->uriResource == RDFS_LITERAL);
		
		$this->assertTrue($this->targetRelatedMoviesProperty->getOnePropertyValue($domainProperty)->uriResource == $this->targetMovieClass->uriResource);
		$this->assertTrue($this->targetRelatedMoviesProperty->getOnePropertyValue($rangeProperty)->uriResource == $this->targetMovieClass->uriResource);
		
		$prop = new core_kernel_classes_Property($this->targetRelatedMoviesProperty);
		$this->assertTrue($prop->isMultiple());
	}
	
	public function testHardGetInstances (){
		// Get the hardified instance from the hard sql imlpementation
		$this->assertEqual(count($this->targetSubjectClass->getInstances()), 1);
		$this->assertEqual(count($this->targetSubjectSubClass->getInstances()), 1);
		$this->assertEqual(count($this->targetSubjectClass->getInstances(true)), 2);
		$this->assertEqual(count($this->targetWorkClass->getInstances(true)), 0);
		$this->assertEqual(count($this->targetMovieClass->getInstances()), 0);
	}
	
	public function testHardCreateInstance() {
		$referencer = core_kernel_persistence_hardapi_ResourceReferencer::singleton();
		$proxy = core_kernel_persistence_ResourceProxy::singleton();
		
		$labelProperty = new core_kernel_classes_Property(RDFS_LABEL);
		$valueProperty = new core_kernel_classes_Property(RDF_VALUE);
		
		// Create instance with the hard sql implementation
		$subject = $this->targetSubjectClass->createInstance("Hard Sub Subject (Unit Test)");
		$this->assertTrue($referencer->isResourceReferenced($subject));
		$this->assertIsA($proxy->getImpToDelegateTo($subject), 'core_kernel_persistence_hardsql_Resource');
		$this->assertEqual(count($this->targetSubjectClass->getInstances()), 2);

		$subSubject = $this->targetSubjectSubClass->createInstance("Hard Sub Sub Subject (Unit Test)");
		$this->assertTrue($referencer->isResourceReferenced($subSubject));
		$this->assertIsA($proxy->getImpToDelegateTo($subSubject), 'core_kernel_persistence_hardsql_Resource');
		$this->assertEqual(count($this->targetSubjectSubClass->getInstances()), 2);
		$this->assertEqual(count($this->targetSubjectClass->getInstances(true)), 4);
		
		$work1Label = 'Mona Lisa';
		$work1Author = 'Leonardo da Vinci';
		$work1 = $this->targetWorkClass->createInstance($work1Label, 'Mona Lisa, a half-length portait of a woman');
		$this->assertTrue($work1->exists());
		$this->assertTrue($referencer->isResourceReferenced($work1));
		$this->assertIsA($proxy->getImpToDelegateTo($work1), 'core_kernel_persistence_hardsql_Resource');
		$this->assertEqual($work1->getLabel(), $work1Label);
		$work1->setPropertyValue($this->targetAuthorProperty, $work1Author);
		
		// Test property (that exists) values for $work1.
		$this->assertEqual($work1->getUniquePropertyValue($labelProperty)->literal, $work1Label);
		$work1Labels = $work1->getPropertyValues($labelProperty);
		$this->assertEqual(count($work1Labels), 1);
		$this->assertEqual($work1Labels[0], $work1Label);
		$work1Labels = $work1->getPropertyValues($labelProperty, array('one' => true));
		$this->assertEqual(count($work1Labels), 1);
		$this->assertEqual($work1Labels[0], $work1Label);
		$work1Labels = $work1->getPropertyValues($labelProperty, array('last', true));
		$this->assertEqual(count($work1Labels), 1);
		$this->assertEqual($work1Labels[0], $work1Label);
		$literal = $work1->getOnePropertyValue($this->targetAuthorProperty);
		$this->assertIsA($literal, 'core_kernel_classes_Literal');
		$this->assertEqual($literal->literal, $work1Author);
		$work1PropertiesValues = $work1->getPropertiesValues(array($labelProperty, $this->targetAuthorProperty));
		$this->assertEqual(count($work1PropertiesValues), 2);
		$this->assertTrue(array_key_exists(RDFS_LABEL, $work1PropertiesValues));
		$this->assertTrue(array_key_exists($this->targetAuthorProperty->uriResource, $work1PropertiesValues));
		$this->assertEqual($work1->getUsedLanguages($labelProperty), array(DEFAULT_LANG));
		
		// Test property (that doesn't exist) values for $work1.
		$unknownProperty = new core_kernel_classes_Property('unknown property');
		$unknownProperty2 = new core_kernel_classes_Property('unknown property 2');
		try{
			$work1->getUniquePropertyValue($unknownProperty);
			$this->fail('common_exception_EmptyProperty expected');
		}
		catch (common_exception_EmptyProperty $e){
			$this->pass();
		}
		
		$work1Unknown = $work1->getPropertyValues($unknownProperty);
		$this->assertEqual($work1Unknown, array());
		$work1Unknown = $work1->getPropertyValues($unknownProperty, array('one' => true));
		$this->assertEqual($work1Unknown, array());
		$work1Unknown = $work1->getPropertyValues($unknownProperty, array('last' => true));
		$this->assertEqual($work1Unknown, array());
		$literal = $work1->getOnePropertyValue($unknownProperty);
		$this->assertNull($literal);
		$work1PropertiesValues = $work1->getPropertiesValues(array($unknownProperty, $unknownProperty2));
		$this->assertTrue(count($work1PropertiesValues) == 0);
		$work1PropertiesValues = $work1->getPropertiesValues(array($labelProperty, $unknownProperty));
		$this->assertTrue(array_key_exists(RDFS_LABEL, $work1PropertiesValues));
		$this->assertTrue(count($work1PropertiesValues) == 1);
	}
	
	public function testHardSearchInstances(){
		$movieClass = $this->targetMovieClass;
		$workClass = $this->targetWorkClass;
		$authorProperty = $this->targetAuthorProperty;
		$producerProperty = $this->targetProducerProperty;
		$actorsProperty = $this->targetActorsProperty;
		$relatedMoviesProperty = $this->targetRelatedMoviesProperty;
		$labelProperty = new core_kernel_classes_Property(RDFS_LABEL);
		
		$bookOfTheRings = $workClass->createInstance('The Lord of the Rings');
		$bookOfTheRings->setPropertyValue($authorProperty, 'John Ronald Reuel Tolkien');
		
		// Works with a rdfs:label which is 'The Lord of the ...'.
		$propertyFilters = array($labelProperty->getUri() => 'The Lord of the');
		$instances = $workClass->searchInstances($propertyFilters, array('like' => true));
		$this->assertEqual(count($instances), 1);
		$this->assertEqual($instances[key($instances)]->getLabel(), 'The Lord of the Rings');
		
		$lordOfTheRings = $movieClass->createInstance('The Lord of the Rings');
		$lordOfTheRings->setPropertyValueByLg($labelProperty, 'Le Seigneur des Anneaux', 'FR-be');
		$lordOfTheRings->setPropertyValue($authorProperty, 'Peter Jackson');
		$lordOfTheRings->setPropertyValue($producerProperty, 'Peter Jackson');
		$lordOfTheRings->setPropertyValue($producerProperty, 'Barrie M. Osborne');
		$lordOfTheRings->setPropertyValue($producerProperty, 'Fran Walsh');
		$lordOfTheRings->setPropertyValue($producerProperty, 'Mark Ordersky');
		$lordOfTheRings->setPropertyValue($producerProperty, 'Tim Sanders');
		$lordOfTheRings->setPropertyValue($actorsProperty, 'Viggo Mortensen');
		$lordOfTheRings->setPropertyValue($actorsProperty, 'Elijah Wood');
		$lordOfTheRings->setPropertyValue($actorsProperty, 'Sean Bean');
		$lordOfTheRings->setPropertyValue($actorsProperty, 'Dominic Monaghan');
		$lordOfTheRings->setPropertyValue($actorsProperty, 'Sean Astin');
		$lordOfTheRings->setPropertyValue($actorsProperty, 'Ian McKellen');
		$lordOfTheRings->setPropertyValue($actorsProperty, 'John Rhys-Davies');
		$lordOfTheRings->setPropertyValue($actorsProperty, 'Orlando Bloom');
		$lordOfTheRings->setPropertyValue($actorsProperty, 'Billy Boyd');
		
		// Works with a rdfs:label which is 'The Lord of the ...' (recursive).
		$propertyFilters = array($labelProperty->getUri() => 'The Lord of the');
		$instances = $workClass->searchInstances($propertyFilters, array('like' => true, 'recursive' => 1));
		$this->assertEqual(count($instances), 2);
		$this->assertEqual($instances[key($instances)]->getLabel(), 'The Lord of the Rings'); next($instances);
		$this->assertEqual($instances[key($instances)]->getLabel(), 'The Lord of the Rings'); next($instances);
		
		$instances = $workClass->searchInstances($propertyFilters, array('like' => true, 'recursive' => 0));
		$this->assertEqual(count($instances), 1);
		$this->assertEqual($instances[key($instances)]->getLabel(), 'The Lord of the Rings');
		
		$theHobbit = $movieClass->createInstance('The Hobbit: An Unexpected Journey');
		$theHobbit->setPropertyValue($authorProperty, 'Peter Jackson');
		$theHobbit->setPropertyValue($producerProperty, 'Peter Jackson');
		$theHobbit->setPropertyValue($producerProperty, 'Fran Walsh');
		$theHobbit->setPropertyValue($producerProperty, 'Carolynne Cunningham');
		$theHobbit->setPropertyValue($producerProperty, 'Zane Weiner');
		$theHobbit->setPropertyValue($actorsProperty, 'Martin Freeman');
		$theHobbit->setPropertyValue($actorsProperty, 'Ian McKellen');
		$theHobbit->setPropertyValue($actorsProperty, 'Richard Armitage');
		$theHobbit->setPropertyValue($actorsProperty, 'Ian Holm');
		$theHobbit->setPropertyValue($actorsProperty, 'Andy Serkis');
		$theHobbit->setPropertyValue($actorsProperty, 'Benedict Cumberbatch');
		$theHobbit->setPropertyValue($actorsProperty, 'Graham McTavish');
		$theHobbit->setPropertyValue($actorsProperty, 'Ken Stott');
		$theHobbit->setPropertyValue($relatedMoviesProperty, $lordOfTheRings);
		
		// Movie with rdfs:label equals to 'The Hobbit: An Unexpected Journey'.
		$propertyFilters = array($labelProperty->getUri() => 'The Hobbit: An Unexpected Journey');
		$instances = $movieClass->searchInstances($propertyFilters);
		$this->assertTrue(count($instances) == 1);
		$instance = new core_kernel_classes_Resource($instances[key($instances)]);
		$this->assertIsA($instance, 'core_kernel_classes_Resource');
		$this->assertTrue($instance->exists());
		$this->assertEqual($instance->getLabel(), 'The Hobbit: An Unexpected Journey');
		
		// Movie with rdfs:label equals to 'The Hobbit: An Unexpected Journey'
		// and mov:producer equals to 'Peter Jackson'.
		$propertyFilters = array($labelProperty->getUri() => 'The Hobbit: An Unexpected Journey',
								 $authorProperty->getUri() => 'Peter Jackson');
								 
		$instances = $movieClass->searchInstances($propertyFilters);
		$this->assertTrue(count($instances) == 1);
		$instance = new core_kernel_classes_Resource($instances[key($instances)]);
		$this->assertIsA($instance, 'core_kernel_classes_Resource');
		$this->assertTrue($instance->exists());
		
		// Same as previous one but with 'like' option set to false.
		$propertyFilters = array($labelProperty->getUri() => 'The Hobbit: An Unexpected Journey',
								 $authorProperty->getUri() => 'Peter Jackson');
								 
		$instances = $movieClass->searchInstances($propertyFilters, array('like' => false));
		$this->assertTrue(count($instances) == 1);
		$instance = new core_kernel_classes_Resource($instances[key($instances)]);
		$this->assertIsA($instance, 'core_kernel_classes_Resource');
		$this->assertTrue($instance->exists());
		
		// Movie with 'Sean Bean' produced by 'Peter Jackson'
		$propertyFilters = array($actorsProperty->getUri() => 'Sean Bean',
								 $authorProperty->getUri() => 'Peter Jackson');
								 
		$instances = $movieClass->searchInstances($propertyFilters);
		$this->assertTrue(count($instances) == 1);
		$instance = new core_kernel_classes_Resource($instances[key($instances)]);
		$this->assertIsA($instance, 'core_kernel_classes_Resource');
		$this->assertTrue($instance->exists());
		$this->assertEqual($instance->getLabel(), 'The Lord of the Rings');
		
		// Movie with 'Sean Bean' OR 'Richard Armitage' produced by 'Peter Jackson'
		$propertyFilters = array($actorsProperty->getUri() => array('Richard Armitage', 'Sean Bean'),
								 $producerProperty->getUri() => 'Peter Jackson');

		$instances = $movieClass->searchInstances($propertyFilters, array('chaining' => 'or'));
		$this->assertTrue(count($instances) == 2);
		$foundCount1 = 0;
		$foundCount2 = 0;
		foreach ($instances as $i){
			if ($i->getLabel() == 'The Hobbit: An Unexpected Journey'){
				$foundCount1++;
			}
			
			if ($i->getLabel() == 'The Lord of the Rings'){
				$foundCount2++;
			}
		}
		$this->assertEqual($foundCount1 + $foundCount2, 2);
		
		// Movie with rdfs:label equals to 'Le Seigneur des Anneaux' in the Belgian French locale.
		$propertyFilters = array($labelProperty->getUri() => 'Le Seigneur des Anneaux');
		$instances = $movieClass->searchInstances($propertyFilters, array('lang' => 'FR-be'));
		$this->assertEqual(count($instances), 1);
		
		// All Works limited to 2 results. We should have 'The Lord of The Rings' (movie + book),
		// 'The Hobbit: An Unexpected Journey' and 'Mona Lisa' in the Knowledge Base at
		// the moment.
		$propertyFilters = array();
		$instances = $workClass->searchInstances($propertyFilters, array('limit' => 3, 'recursive' => 1));
		$this->assertEqual(count($instances), 3);
		
		// Same as previous, but without limit and orderedy by author.
		$propertyFilters = array();
		$instances = $workClass->searchInstances($propertyFilters, array('recursive' => 1, 'order' => $authorProperty->getUri()));
		$this->assertEqual(count($instances), 4);
		
		// Same as previous, but with a descendent orderdir.
		$propertyFilters = array();
		$instances = $workClass->searchInstances($propertyFilters, array('order' => $authorProperty->getUri(), 'orderdir' => 'ASC'));
		$this->assertEqual(count($instances), 2);
		$this->assertEqual($instances[key($instances)]->getUniquePropertyValue($authorProperty)->literal, 'John Ronald Reuel Tolkien'); next($instances);
		$this->assertEqual($instances[key($instances)]->getUniquePropertyValue($authorProperty)->literal, 'Leonardo da Vinci'); next($instances);
		
		// Get all movies that are produced by 'Peter Jackson' ordered by rdfs:label.
		$propertyFilters = array($producerProperty->getUri() => 'Peter Jackson');
		$instances = $movieClass->searchInstances($propertyFilters, array('order' => $labelProperty->getUri()));
		$this->assertEqual(count($instances), 2);
		$this->assertEqual($instances[key($instances)]->getUniquePropertyValue($labelProperty)->literal, 'The Hobbit: An Unexpected Journey'); next($instances);
		$this->assertEqual($instances[key($instances)]->getUniquePropertyValue($labelProperty)->literal, 'The Lord of the Rings'); next($instances);
		
		// Get Lord of the Rings by Peter Jackson (produced).
		$propertyFilters = array($producerProperty->getUri() => 'Peter Jackson');
		$instances = $movieClass->searchInstances($propertyFilters, array('order' => $labelProperty->getUri()));
		$this->assertEqual(count($instances), 2);
		$this->assertEqual($instances[key($instances)]->getUniquePropertyValue($labelProperty)->literal, 'The Hobbit: An Unexpected Journey'); next($instances);
		$this->assertEqual($instances[key($instances)]->getUniquePropertyValue($labelProperty)->literal, 'The Lord of the Rings'); next($instances);
		
		// try to search a property that does not exist.
		$propertyFilters = array('http://www.unknown.com/i-do-not-exist' => 'do-not-exist');
		$instances = $movieClass->searchInstances($propertyFilters);
		$this->assertEqual(count($instances), 0);
	}
	
	public function testGetRdfTriples(){
		$workClass = $this->targetWorkClass;
		$authorProperty = $this->targetAuthorProperty;
		
		// We now test rdfTriples on a hardified resource.
		$filters = array($authorProperty->getUri() => 'John Ronald Reuel Tolkien');
		$options = array('like' => false);
		$instances = $workClass->searchInstances($filters, $options);
		$this->assertEqual(count($instances), 1);
		$book = current($instances);
		$this->assertEqual($book->getLabel(), 'The Lord of the Rings');
		$triples = $book->getRdfTriples()->toArray();
		$this->assertEqual($triples[1]->predicate, 'http://www.w3.org/2000/01/rdf-schema#label');
		$this->assertEqual($triples[0]->predicate, $authorProperty->getUri());
		$this->assertEqual($triples[0]->object, 'John Ronald Reuel Tolkien');
		$this->assertEqual($triples[2]->predicate, RDF_TYPE);
		$this->assertEqual($triples[2]->object, $workClass->getUri());
		
		// We now test rdfTriples on a hardified class.
		$triples = $workClass->getRdfTriples()->toArray();
		$this->assertEqual($triples[0]->predicate, RDF_TYPE);
		$this->assertEqual($triples[0]->object, RDF_CLASS);
	}
	
	public function testForceMode (){
		// Check if the returner implementation are correct
		core_kernel_persistence_PersistenceProxy::forceMode (PERSISTENCE_SMOOTH);
		$classProxy = core_kernel_persistence_ClassProxy::singleton();
		$impl = $classProxy->getImpToDelegateTo($this->targetSubjectClass);
		$this->assertTrue ($impl instanceof core_kernel_persistence_smoothsql_Class);
		$this->assertEqual (count($this->targetSubjectClass->getInstances ()), 1);
		$this->assertEqual (count($this->targetSubjectSubClass->getInstances ()), 1);
		core_kernel_persistence_PersistenceProxy::restoreImplementation();
		$this->assertTrue (core_kernel_persistence_ClassProxy::singleton()->getImpToDelegateTo($this->targetSubjectClass) instanceof core_kernel_persistence_hardsql_Class);
		$this->assertTrue (core_kernel_persistence_ResourceProxy::singleton()->getImpToDelegateTo($this->subject1) instanceof core_kernel_persistence_hardsql_Resource);
	}
	
	public function testSetProperties (){
		// Set properties
		foreach ($this->targetSubjectClass->getInstances(true) as $instance){
			// Set mutltiple property
			$instance->setPropertyValue(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOGroup.rdf#Members'), new core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAO.rdf#LangEN'));
			$instance->setPropertyValue(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOGroup.rdf#Members'), new core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAO.rdf#LangFR'));
			// Set property value by lg
			$instance->setPropertyValueByLg(new core_kernel_classes_Property(RDFS_LABEL), 'LABEL FR', 'FR');
			// Set property type (SPECIAL CASE)
			$instance->setPropertyValue(new core_kernel_classes_Property(RDF_TYPE), 'http://www.tao.lu/Ontologies/TAO.rdf#TaoSubjectRole');
			// Set foreign property
			$instance->setPropertyValue(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/generis.rdf#userDefLg'), 'http://www.tao.lu/Ontologies/TAO.rdf#LangEN');
			$instance->setPropertyValue(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/generis.rdf#userUiLg'), 'http://www.tao.lu/Ontologies/TAO.rdf#LangFR');
		}
	}
	
	public function testGetOnePropertyValue (){
		foreach ($this->targetSubjectClass->getInstances() as $instance){
			
			// Specific case show later
			$prop = $instance->getOnePropertyValue(new core_kernel_classes_Property(RDF_TYPE));
			$this->assertTrue($prop instanceof core_kernel_classes_Resource);
			
			// Get single property label
			$prop = $instance->getOnePropertyValue(new core_kernel_classes_Property(RDFS_LABEL));
			$this->assertTrue($prop instanceof core_kernel_classes_Literal);
			
			// Get single property value
			$prop = $instance->getOnePropertyValue(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOGroup.rdf#Members'));
			$this->assertTrue($prop instanceof core_kernel_classes_Resource);
		}
	}
	
	public function testGetPropertyValues () {
		foreach ($this->targetSubjectClass->getInstances() as $instance){
			
			// Get property values on single (literal) property 
			$props = $instance->getPropertyValues(new core_kernel_classes_Property(RDFS_LABEL));
			$this->assertEqual(count($props), 1);
			foreach ($props as $prop){
				$this->assertTrue(is_string($prop));
			}
			// Get property values on single (resource) property 
			$props = $instance->getPropertyValues(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/generis.rdf#userDefLg'));
			$this->assertEqual(count($props), 1);
			foreach ($props as $prop){
				$this->assertTrue(common_Utils::isUri($prop));
			}
			// Get property values on mutltiple property
			$props = $instance->getPropertyValues(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOGroup.rdf#Members'));
			$this->assertEqual(count($props), 2);
			foreach ($props as $prop){
				$this->assertTrue(common_Utils::isUri($prop));
			}
			// Get property values on mutltiple (by lg) property
			// Common behavior is to return reccords function of a defined language or function of the default system language if the record is language dependent
//			$props = $instance->getPropertyValues (new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOTest.rdf#TestContent'));
//			$this->assertEqual (count($props), 1);
//			foreach ($props as $prop){
//				$this->assertTrue (is_string($prop));
//			}		
		}
	}
			
	public function testGetPropertyValuesCollection (){
		foreach ($this->targetSubjectClass->getInstances() as $instance){
			
			$props = $instance->getPropertyValuesCollection(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOGroup.rdf#Members'));
			$this->assertTrue($props instanceof core_kernel_classes_ContainerCollection);
			$this->assertEqual($props->count(), 2);
			foreach ($props->getIterator() as $prop){
				
				$this->assertTrue($prop instanceof core_kernel_classes_Resource);
			}		
		}
	}
	
	public function testGetPropertyValuesByLg (){
		foreach ($this->targetSubjectClass->getInstances() as $instance){	
			
			$props = $instance->getPropertyValuesByLg (new core_kernel_classes_Property(RDFS_LABEL), 'FR');
			$this->assertEqual($props->count(), 1);
			$this->assertTrue($props->get(0) instanceof core_kernel_classes_Literal);
			$this->assertEqual((string)$props->get(0), 'LABEL FR');
		}
	}
	
	public function testRemovePropertyValues (){
		foreach ($this->targetSubjectClass->getInstances() as $instance){	
			
			// Remove foreign single property
			$instance->removePropertyValues(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/generis.rdf#userDefLg'));
			$props = $instance->getPropertyValues(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/generis.rdf#userDefLg'));
			$this->assertTrue(empty($props));
			
			// Remove literal multiple property
			$instance->removePropertyValues(new core_kernel_classes_Property(RDFS_LABEL));
			$props = $instance->getPropertyValues(new core_kernel_classes_Property(RDFS_LABEL));
			$this->assertTrue(empty($props));
			
			// Remove foreign multiple property
			$instance->removePropertyValues(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOGroup.rdf#Members'));
			$props = $instance->getPropertyValues(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOGroup.rdf#Members'));
			$this->assertTrue(empty($props));
			
		}
	}
	
	public function testRemovePropertyValuesByLg (){
		
		$this->testSetProperties();
		
		foreach ($this->targetSubjectClass->getInstances() as $instance){	
			
			// Remove foreign single property
			$instance->removePropertyValueByLg(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/generis.rdf#userDefLg'), 'FR');
			$props = $instance->getPropertyValues(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/generis.rdf#userDefLg'));
			$this->assertFalse(empty($props));
			
			// Remove literal multiple property
			$instance->removePropertyValueByLg(new core_kernel_classes_Property(RDFS_LABEL), 'FR');
			$props = $instance->getPropertyValues(new core_kernel_classes_Property(RDFS_LABEL));
			$this->assertTrue(empty($props));
			
			// Remove foreign multiple property
			$instance->removePropertyValueByLg(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOGroup.rdf#Members'), 'FR');
			$props = $instance->getPropertyValues(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOGroup.rdf#Members'));
			$this->assertFalse(empty($props));
			
		}
	}
	
	public function testClean (){
		// Remove the resources
		foreach ($this->targetSubjectClass->getInstances() as $instance){
			$instance->delete();
			$this->assertFalse(core_kernel_persistence_hardapi_ResourceReferencer::singleton()->isResourceReferenced($instance));
		}
		foreach ($this->targetSubjectSubClass->getInstances() as $instance){
			$instance->delete();
			$this->assertFalse(core_kernel_persistence_hardapi_ResourceReferencer::singleton()->isResourceReferenced($instance));
		}
		
		// unreference the subject class
		core_kernel_persistence_hardapi_ResourceReferencer::singleton()->unReferenceClass($this->targetSubjectClass);
		$this->assertFalse(core_kernel_persistence_hardapi_ResourceReferencer::singleton()->isClassReferenced($this->targetSubjectClass));
		$this->assertTrue(core_kernel_persistence_ClassProxy::singleton()->getImpToDelegateTo($this->targetSubjectClass) instanceof core_kernel_persistence_smoothsql_Class);
		$this->targetSubjectClass->delete(true);
		$this->subject1->delete();
		
		// unreference the subject sub class
		$this->assertTrue(core_kernel_persistence_hardapi_ResourceReferencer::singleton()->unReferenceClass($this->targetSubjectSubClass));
		$this->assertFalse(core_kernel_persistence_hardapi_ResourceReferencer::singleton()->isClassReferenced($this->targetSubjectSubClass));
		$this->assertTrue(core_kernel_persistence_ClassProxy::singleton()->getImpToDelegateTo($this->targetSubjectSubClass) instanceof core_kernel_persistence_smoothsql_Class);
		$this->targetSubjectSubClass->delete(true);
		$this->subject2->delete();
		
		core_kernel_persistence_hardapi_ResourceReferencer::singleton()->unreferenceClass(new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAO.rdf#Languages'));
		
		$referencer = core_kernel_persistence_hardapi_ResourceReferencer::singleton();
		$referencer->unReferenceClass($this->targetWorkClass);
		$referencer->unReferenceClass($this->targetMovieClass);
		$this->targetWorkClass->delete(true);
		$this->targetMovieClass->delete(true);
		$this->assertFalse($this->targetWorkClass->exists());
		$this->assertFalse($this->targetWorkClass->exists());
	}
	
	public function testFilterByLanguage() {
		return;
		$session = core_kernel_classes_Session::singleton();
		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		$true = new core_kernel_classes_Resource(GENERIS_TRUE);
		
		$this->object->setStatement($true->uriResource,RDFS_SEEALSO,'test1', '');
		$this->object->setStatement($true->uriResource,RDFS_SEEALSO,'test2', '');
		$this->object->setStatement($true->uriResource,RDFS_SEEALSO,'testing', 'EN');
		$this->object->setStatement($true->uriResource,RDFS_SEEALSO,'essai', 'FR');
		$this->object->setStatement($true->uriResource,RDFS_SEEALSO,'testung1', 'SE');
		$this->object->setStatement($true->uriResource,RDFS_SEEALSO,'testung2', 'SE');
		
		// Get some propertyValues as if it was obtained by an SQL Statement.
		// First test is made with the default language selected.
		$modelIds	= implode(',',array_keys($session->getLoadedModels()));
        $query =  "SELECT object, l_language FROM statements 
		    		WHERE subject = ? AND predicate = ?
		    		AND (l_language = '' OR l_language = ? OR l_language = ?)
		    		AND modelID IN ({$modelIds})";
		    		
        $result	= $dbWrapper->query($query, array(
        	GENERIS_TRUE,
        	RDFS_SEEALSO,
        	$session->defaultLg,
        	$session->getDataLanguage()
        ));
        
        $result = $result->fetchAll();
        
        $sorted = core_kernel_persistence_smoothsql_Utils::sortByLanguage($result, 'l_language');
        $filtered = core_kernel_persistence_smoothsql_Utils::getFirstLanguage($sorted);
        $this->assertTrue(count($sorted) == 3 && $sorted[0]['value'] == 'testing');
        $this->assertTrue(count($filtered) == 1 && $filtered[0] == 'testing');
       
        // Second test is based on a particular language.
        $session->setDataLanguage('FR');
        $result	= $dbWrapper->query($query, array(
        	GENERIS_TRUE,
        	RDFS_SEEALSO,
        	$session->defaultLg,
        	$session->getDataLanguage()
        ));
        
        $result = $result->fetchAll();
        
        $sorted = core_kernel_persistence_smoothsql_Utils::sortByLanguage($result, 'l_language');
        $filtered = core_kernel_persistence_smoothsql_Utils::getFirstLanguage($sorted);
        $this->assertTrue(count($sorted) == 4 && $sorted[0]['value'] == 'essai');
        $this->assertTrue(count($filtered) == 1 && $filtered[0] == 'essai');
		
		// Third test looks if the default language is respected.
		// No japanese values here, but default language set to EN.
		// Here we use the function filterByLanguage which aggregates sortByLanguage
		// and getFirstLanguage.
		$session->setDataLanguage('JA');
        $result	= $dbWrapper->query($query, array(
        	GENERIS_TRUE,
        	RDFS_SEEALSO,
        	$session->defaultLg,
        	$session->getDataLanguage()
        ));
        
        $result = $result->fetchAll();
        
        $filtered = core_kernel_persistence_smoothsql_Utils::filterByLanguage($result, 'l_language');
        $this->assertTrue(count($filtered) == 1 && $filtered[0] == 'testing');
		
		$session->setDataLanguage('');
		
		// Set back ontology to normal.
		$this->object->removeStatement($true->uriResource,RDFS_SEEALSO,'test1', '');
		$this->object->removeStatement($true->uriResource,RDFS_SEEALSO,'test2', '');
		$this->object->removeStatement($true->uriResource,RDFS_SEEALSO,'testing', 'EN');
		$this->object->removeStatement($true->uriResource,RDFS_SEEALSO,'essai', 'FR');
		$this->object->removeStatement($true->uriResource,RDFS_SEEALSO,'testung1', 'SE');
		$this->object->removeStatement($true->uriResource,RDFS_SEEALSO,'testung2', 'SE');
	}
	
	public function testIdentifyFirstLanguage() {
		return;
		$values = array(
			array('language' => 'EN', 'value' => 'testFallback'),
			array('language' => '', 'value' => 'testEN')
		);
		
		$this->assertTrue(core_kernel_persistence_smoothsql_Utils::identifyFirstLanguage($values) == 'EN');
		
		$values = array(
			array('language' => 'JA', 'value' => 'testJA1'),
			array('language' => 'JA', 'value' => 'testJA2'),
			array('language' => 'EN', 'value' => 'testEN1'),
			array('language' => 'EN', 'value' => 'testEN1'),
			array('language' => '', 'value' => 'testFallback1'),
			array('language' => '', 'value' => 'testFallback2')	
		);
		
		$this->assertTrue(core_kernel_persistence_smoothsql_Utils::identifyFirstLanguage($values) == 'JA');
	}

}
?>
