<?php
require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
require_once INCLUDES_PATH.'/simpletest/autorun.php';


class PersistenceSwitcherTestCase extends UnitTestCase {
	
	private $hardifySubject = true;
	private $hardifyGroups = false;
	
	public function setUp(){

	    TestRunner::initTest();

	}

//	public function testPropertyFinder(){
//		$testTakerClass = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject');
//		
//		$ps = new core_kernel_persistence_switcher_PropertySwitcher($testTakerClass, new core_kernel_classes_Class('http://www.tao.lu/Ontologies/generis.rdf#User'));
//		$result = $ps->getProperties();
//		$this->assertEqual(count($result), 8);
//		$this->assertTrue(array_key_exists('http://www.tao.lu/Ontologies/generis.rdf#userFirstName', $result));
//		$this->assertTrue(array_key_exists('http://www.tao.lu/Ontologies/generis.rdf#userLastName', $result));
//		$this->assertTrue(array_key_exists('http://www.tao.lu/Ontologies/generis.rdf#password', $result));
//		$this->assertTrue(array_key_exists('http://www.tao.lu/Ontologies/generis.rdf#login', $result));
//		$this->assertTrue(array_key_exists('http://www.tao.lu/Ontologies/generis.rdf#userMail', $result));
//		$this->assertTrue(array_key_exists('http://www.tao.lu/Ontologies/generis.rdf#userDefLg', $result));
//		$this->assertTrue(array_key_exists('http://www.tao.lu/Ontologies/generis.rdf#userUILg', $result));
//		
//		
//		$ps = new core_kernel_persistence_switcher_PropertySwitcher($testTakerClass, new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAO.rdf#TAOObject'));
//		$result = $ps->getProperties();
//		$this->assertEqual(count($result), 1);
//		$this->assertTrue(array_key_exists(RDFS_LABEL, $result));
//		
//		$ps = new core_kernel_persistence_switcher_PropertySwitcher($testTakerClass);
//		$result = $ps->getProperties();
//		$this->assertTrue(array_key_exists(RDFS_LABEL, $result));
//	}
	
	public function testHardify(){
		if (true){
			$languagesClass = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAO.rdf#Languages');
			$topClass		= new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAO.rdf#TAOObject');	
			core_kernel_persistence_Switcher::hardifier($languagesClass, array(
				'topClass'		=> $topClass,
				'rmSources'		=> false
			));
		}
		
		if ($this->hardifySubject){
			$testTakerClass = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject');
			$userClass		= new core_kernel_classes_Class('http://www.tao.lu/Ontologies/generis.rdf#User');	
			core_kernel_persistence_Switcher::hardifier($testTakerClass, array(
				'topClass'				=> $userClass,
				'recursive'				=> true,
				'createForeigns'		=> true,
				'additionalProperties' 	=> array (new core_kernel_classes_Property (RDF_TYPE)),
				'rmSources'				=> true
			));
		}
		if ($this->hardifyGroups){
			$groupClass = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAOGroup.rdf#Group');
			$topClass		= new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAO.rdf#TAOObject');
			core_kernel_persistence_Switcher::hardifier($groupClass, array(
				'topClass'		=> $topClass,
				'recursive'		=> true,
				'createForeigns'=> true,
				'rmSources'		=> false
			));
		}
	}
	
	public function testHardifiedSubject(){
		return;
		// Get TaoSubjects Instance
		$subjectClass = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject');
		$subjectInstances = $subjectClass->getInstances();
		$this->assertIsA ($subjectInstances, 'array');
		$this->assertEqual (count($subjectInstances), 1000);
		// Get the current user
//	    $userUri = Session::getAttribute(core_kernel_users_Service::AUTH_TOKEN_KEY);
//	    $this->assertNotNull($userUri);
//        $currentUser = new core_kernel_classes_Resource($userUri);
//  		$login = (string)$currentUser->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_USER_LOGIN));
//  		$this->assertEqual ($login, 'generis');
//	        		$password 		= (string)$currentUser->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_USER_PASSWORD));
//					try{
//	        			$dataLang 	= (string)$currentUser->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_USER_DEFLG));
//					}
//					catch(common_Exception $ce){
//						$dataLang 	= 'EN';
//					}
	}
	
//	public function testHardifyMultiple(){

//	}
	
} 
?>
