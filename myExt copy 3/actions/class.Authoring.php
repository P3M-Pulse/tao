<?php

class myExt_actions_Authoring extends taoSimpleDelivery_actions_Authoring
{
	private $ResultClass;
	public function wizard()
    {
    	$uri = $this->getCurrentInstance();
    	$this->defaultData();
    	
    	
    	$ResultService = taoResults_models_classes_ResultsService::singleton();
    	$newDeliveryResultClass = new core_kernel_classes_class(TAO_DELIVERY_RESULT);
	    $this->ResultClass = $ResultService->createSubClass($newDeliveryResultClass);

    	
    	//Load Classes
		$groupClass = new core_kernel_classes_Class(TAO_GROUP_CLASS);
		$subjectParentClass = new core_kernel_classes_Class(TAO_CLASS_SUBJECT);
		
		//Loop through subclasses of Test Takers Parent Class
		foreach($subjectParentClass->getSubClasses(false) as $subClass){
			//Only get properties of Test Takers subClass
			if($subClass->getLabel()=='Test Takers'){
				$subjectClass = $subClass;
			}
		}
       	
       	//Load Properties
       	$memberProp = new core_kernel_classes_Property(TAO_GROUP_MEMBERS_PROP);
       	$subjectClassProps = tao_helpers_form_GenerisFormFactory::getClassProperties($subjectClass);
        $userMailProp = new core_kernel_classes_Property(PROPERTY_USER_MAIL);
        $userFirstNameProp = new core_kernel_classes_Property(PROPERTY_USER_FIRSTNAME);
        $userLastNameProp = new core_kernel_classes_Property(PROPERTY_USER_LASTNAME);
        //Initiate Arrays
        $users=array();
		$testtakers = array();
		$testtakerPeersGroup = array();
		$peerUsers = array();
		$peerUserEmails = array();
		
		$startDateProp = new core_kernel_classes_Property(TAO_DELIVERY_START_PROP);
    	$endDateProp = new core_kernel_classes_Property(TAO_DELIVERY_END_PROP);
    	
    	$startDate = @$uri->getOnePropertyValue($startDateProp)->literal;
    	$endDate = @$uri->getOnePropertyValue($endDateProp)->literal;
		
		
		//Get groups assigned to this delivery
		 $groups = $groupClass->searchInstances(array(
            PROPERTY_GROUP_DELVIERY => $uri->getUri()
        ), array('recursive' => true, 'like' => false));
   
        //Get all users under a group
        foreach ($groups as $group) {
            $users = array_merge($users, $group->getPropertyValues($memberProp));
        }
        
        ;
		foreach($users as $userUri){
			$testtakerPeersGroup = array();
			$user = new core_kernel_classes_Resource($userUri);
			            
            //Get Peers emails
            foreach($subjectClassProps as $prop){
            	//echo $prop->getLabel()."<br/>";
        			$testtakerPeersGroup[] = $user->getOnePropertyValue($prop);
        	}
        	
        	//print_r($testtakerPeersGroup);
        	
        	
            $TestTakerfirstName = $user->getOnePropertyValue($userFirstNameProp)->literal;
            $TestTakerlastName = $user->getOnePropertyValue($userLastNameProp)->literal;
            
            $testTakerName = $TestTakerfirstName.' '.$TestTakerlastName;
          
            foreach ($testtakerPeersGroup as $peerGroup) {
	
	           	 $deliveryID = $this->createDelivery($testTakerName);
	           	 $this->editDeliveryDetails($deliveryID, $peerGroup, $startDate, $endDate);
	           	 
	           	 
	           	 //echo $deliveryID;
	           	 
        	}
        	echo "Success.";
           
    }
	
	protected function createDelivery($testTakerName){
		//$label = 'Hello';
        $test = new core_kernel_classes_Resource('http://localhost/tao.rdf#i141528007374493686');
        $label = __("Review %s", $testTakerName);
        $deliveryClass = new core_kernel_classes_Class('http://localhost/tao.rdf#i14151061259771043');
        $report = taoSimpleDelivery_models_classes_SimpleDeliveryService::singleton()->create($deliveryClass, $test, $label);
        if ($report->getType() == common_report_Report::TYPE_SUCCESS) {
        
            return $assembly = $report->getdata();
        } else {
            $this->setData('report', $report);
            $this->setData('title', __('Error'));
            $this->setView('report.tpl', 'tao');
        }

	
	}
	public function editDeliveryDetails($delivery, $group, $startDate, $endDate){
		$property = new core_kernel_classes_Property(PROPERTY_GROUP_DELVIERY);
    	$group->editPropertyValues(new core_kernel_classes_Property(PROPERTY_GROUP_DELVIERY),$delivery->getUri());
    	$delivery->editPropertyValues(new core_kernel_classes_Property(TAO_DELIVERY_START_PROP),$startDate);
    	$delivery->editPropertyValues(new core_kernel_classes_Property(TAO_DELIVERY_END_PROP),$endDate);
    	
    	//Specify Result Folder
    	$delivery->editPropertyValues(new core_kernel_classes_Property('http://localhost/tao.rdf#i141527390417252828'),$this->ResultClass->getLabel());
    	
	
	}
	
	public function save()
    {
        $saved = false;
         
        $instance = $this->getCurrentInstance();
        $testUri = tao_helpers_Uri::decode($this->getRequestParameter(tao_helpers_Uri::encode(PROPERTY_DELIVERYCONTENT_TEST)));
    
        $saved = $instance->editPropertyValues(new core_kernel_classes_Property(PROPERTY_DELIVERYCONTENT_TEST ), $testUri);
         
        echo json_encode(array(
            'saved' => $saved.' Hello World!'
        ));
    }
}