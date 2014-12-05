<?php
require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
include_once dirname(__FILE__) . '/../includes/raw_start.php';

/**
 * This class enable you to test the models managment of the taoSubjects extension
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoSubjects
 * @subpackage test
 */
class SubecjtsServiceTestCase extends UnitTestCase {
	
	/**
	 * tests initialization
	 */
	public function setUp(){		
		TestRunner::initTest();
	}
	
	/**
	 * Test the subject models management methods from the taoSubjects_models_classes_SubjectsService class
	 * @see taoSubjects_models_classes_SubjectsService
	 */
	public function testDocService(){
		
		$subjectService = tao_models_classes_ServiceFactory::get('taoSubjects_models_classes_SubjectsService');
		$this->assertIsA($subjectService, 'taoSubjects_models_classes_SubjectsService');
	}
	
}
?>
