<?php
error_reporting(E_ALL);
require_once dirname(__FILE__).'/../../common/common.php';
require_once INCLUDES_PATH.'/simpletest/autorun.php';

/**
 * Test class for Expression.
*/

class DynamicTextTestCase extends UnitTestCase {
	
	protected $object;
    /**
     * Setting the  test
     *
     */
    public function setUp(){
		core_kernel_impl_ApiModelOO::singleton()->logIn(LOGIN,md5(PASS),MODULE,true);
		$this->object = new core_kernel_classes_DynamicText('http://127.0.0.1/middleware/Interview.rdf#i122243260856250');
	}
	
	public function testGetDiplayedText(){
//		var_dump($this->object->getTrueText());
//		var_dump($this->object->getFalseText());
//		var_dump($this->object->getRule());

		$recu = new core_kernel_classes_DynamicText('#i1223310341067563900');
		$this->assertEqual($this->object->getDisplayedText(),'What education are you currently enrolled in?');
		var_dump($recu->getDisplayedText());
	}
	
	public function testGetRule(){
		$this->assertIsA($this->object->getRule(),'core_kernel_events_Rule');
		$this->assertEqual($this->object->getRule()->uriResource, 'http://127.0.0.1/middleware/Interview.rdf#i122244623130956');
	}
	
	public function testGetTrueText(){
		$this->assertIsA($this->object->getTrueText(),'core_kernel_classes_Literal');
		$this->assertEqual($this->object->getTrueText()->literal,'What education are you currently enrolled in?');
		
	}
	
	public function testGetFalseText(){
		$this->assertIsA($this->object->getFalseText(), 'core_kernel_classes_Literal');
		$this->assertEqual($this->object->getFalseText()->literal,'You indicated that you are a student/pupil, can you tell me what education are you currently enrolled in?');
	}
}
?>