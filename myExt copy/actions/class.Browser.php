<?php
/**
 * Docs Controller provide actions to manage docs
 * 
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package myExt
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * 
 */

class myExt_actions_Browser extends tao_actions_TaoModule {


	/**
	 * constructor: initialize the service and the default data
	 * @return Docs
	 */
	public function __construct(){
		
		parent::__construct();
		
		//the service is initialized by default
		$this->service = myExt_models_classes_DocsService::singleton();
		$this->defaultData();
	}
	
	/**
	 * @example method used to populate the tree widget
	 * render json data of the documents in the DOCS_PATH
	 * @return void
	 */
	public function getTreeData(){
	
		$data = array(
			'data' 	=> __("My Documents"),
			'attributes' => array(
					'id' => 1,
					'class' => 'node-class'
				),
			'children' => array()
			);
		$index = 2;
		foreach(myExt_helpers_FileUtils::parseFolder(DOCS_PATH, true) as $path => $file){
			$data['children'][] =  array(
				'data' 	=> $file,
				'attributes' => array(
						'id' => substr($path, strlen(DOCS_PATH)),
						'class' => 'node-instance'
					)
			);
			$index++;
		}
		echo json_encode($data);
	}
	
	/**
	 * this function must contain the word edit
	 */
	public function editDocument(){
		$filepath = $this->getRequestParameter('uri');
		
		// send data to the template
		$this->setData('filename', substr($filepath, strrpos($filepath, '/')+1));
		$this->setData('downloadpath', DOCS_URL.$filepath);
		
		// select the template
		$this->setView('editDocument.tpl');
	}
		
	/**
	 * @see TaoModule::getRootClass
	 * @abstract implement the abstract method
	 */
	public function getRootClass(){
		return null;
	}
}
?>
