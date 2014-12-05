<?php


use oat\tao\helpers\Template;

class myExt_actions_DeliveryAction extends taoDelivery_actions_Delivery
{

    /**
     * constructor: initialize the service and the default data
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @return Delivery
     */
   
 public function __construct()
    {
        parent::__construct();
        
        // the service is initialized by default
        $this->service = taoDelivery_models_classes_DeliveryAssemblyService::singleton();
        $this->defaultData();
    }

	public function editDelivery()
    {
    	$clazz = $this->getCurrentClass();
        $delivery = $this->getCurrentInstance();
        
        
        $formContainer = new taoDelivery_actions_form_Delivery($clazz, $delivery);
        $myForm = $formContainer->getForm();
        
        $myForm->evaluate();
        
        if ($myForm->isSubmited()) {
        	
            if ($myForm->isValid()) {
                $propertyValues = $myForm->getValues();
                
                // then save the property values as usual
                $binder = new tao_models_classes_dataBinding_GenerisFormDataBinder($delivery);
                $delivery = $binder->bind($propertyValues);
                
                $this->setData('message', __('Delivery saved'));
                $this->setData('reload', true);
                
            }
        }
        $this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($delivery->getUri()));
        
        $this->setData('label', $delivery->getLabel());
        
        // history
        $this->setData('date', taoDelivery_models_classes_DeliveryAssemblyService::singleton()->getCompilationDate($delivery));
        if (taoDelivery_models_classes_execution_ServiceProxy::implementsMonitoring()) {
            $execs = taoDelivery_models_classes_execution_ServiceProxy::singleton()->getExecutionsByDelivery($delivery);
            $this->setData('exec', count($execs));
        }
        
        // define the groups related to the current delivery
        $property = new core_kernel_classes_Property(PROPERTY_GROUP_DELVIERY);
        $tree = tao_helpers_form_GenerisTreeForm::buildReverseTree($delivery, $property);
        $tree->setTitle(__('Assigned to'));
        $tree->setTemplate(Template::getTemplate('form_groups.tpl'));
        $this->setData('groupTree', $tree->render());
        
        // testtaker brick
        $this->setData('assemblyUri', $delivery->getUri());
        $groupClass = new core_kernel_classes_Class(TAO_GROUP_CLASS);
        $groups = $groupClass->searchInstances(array(
            PROPERTY_GROUP_DELVIERY => $delivery->getUri()
        ), array('recursive' => true, 'like' => false));
        
        $users = array();
        $memberProp = new core_kernel_classes_Property(TAO_GROUP_MEMBERS_PROP);
        foreach ($groups as $group) {
            $users = array_merge($users, $group->getPropertyValues($memberProp));
        }
        $this->setData('groupcount', count($groups));
        
        // define the subjects excluded from the current delivery
        $property = new core_kernel_classes_Property(TAO_DELIVERY_EXCLUDEDSUBJECTS_PROP);
        $excluded = $delivery->getPropertyValues($property);
        $this->setData('ttexcluded', count($excluded));

        $assigned = array_diff(array_unique($users), $excluded);
        $this->setData('ttassigned', count($assigned));
        
        if($myForm->isSubmited()){
        	//To-do: make sure to run this function on SAVE action only so it won't run everytime the edit delivery is called. 
       		//Also make sure to check for expired deliveries or already attempted deliveries. 
       		$this->scheduleEmails($assigned);
        }
        
        
        $this->setData('formTitle', __('Properties'));
        $this->setData('myForm', $myForm->render());
        
        if (common_ext_ExtensionsManager::singleton()->isEnabled('taoCampaign')) {
            $this->setData('campaign', taoCampaign_helpers_Campaign::renderCampaignTree($delivery));
        }
        $this->setView('form_assembly.tpl');

    }
  	public function index()
    {
        $this->setView('index.tpl');
    }

    public function moveResults(){
    	$service = taoResults_models_classes_ResultsService::singleton();
    	$executionService = taoDelivery_models_classes_execution_ServiceProxy::singleton();
    	$delivery = $this->getCurrentInstance();
    	$executedDeliveries = $executionService->getExecutionsByDelivery($delivery);
    	$deliveryResultClass =  new core_kernel_classes_Class(TAO_DELIVERY_RESULT);
    	    	
    	$subject = $delivery->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_RELATED_TEST));

    	
    	$subjectClassProps = tao_helpers_form_GenerisFormFactory::getClassProperties($deliveryResultClass);
    	
    	
    	$deliveryResults = $deliveryResultClass->searchInstances(array(
            PROPERTY_RESULT_OF_DELIVERY => $delivery
        ), array('recursive' => true, 'like' => false));
        
       foreach($deliveryResults as $prop){
       		$instance = new core_kernel_classes_Resource(tao_helpers_Uri::decode($prop));
			$deliveryResultFolder = $delivery->getOnePropertyValue(new core_kernel_classes_Property('http://localhost/tao.rdf#i141527390417252828'));
			foreach($deliveryResultClass->getSubClasses(false) as $subClass){
				if($subClass->getLabel() == $deliveryResultFolder){
					$service->changeClass($prop, $subClass);
				}
			}
       }
       	 echo "Success.";
    }
    
    public function scheduleEmails($users){
    	$dbWrapper = core_kernel_classes_DbWrapper::singleton();
    	$platform = $dbWrapper->getPlatForm();
    	$runQuery = false;
    	$returnValue = false;
    	
    	$delivery = $this->getCurrentInstance();
    	$startDateProp = new core_kernel_classes_Property(TAO_DELIVERY_START_PROP);
    	$endDateProp = new core_kernel_classes_Property(TAO_DELIVERY_END_PROP);
    	
    	$startDate = @$delivery->getOnePropertyValue($startDateProp)->literal;
    	$endDate = @$delivery->getOnePropertyValue($endDateProp)->literal;
    	
    	foreach($users as $user){    	
	    	//First check if the delivery is already added to the schedule
	    	$query = 'SELECT startDate, endDate FROM delivery_reminders WHERE deliveryID = ? AND userID = ? ';
	        $result = $dbWrapper->query($query, array($delivery->getUri(),$user));
	       	$queryResult = $result->fetch();
	        if(empty($queryResult)){
	        	$query = 'INSERT INTO delivery_reminders (startDate, endDate, updated, deliveryID, userID)
	        			VALUES  (?, ?, ?, ?, ?)';
	        	$runQuery = true;		
	        }else{
	       	 	$currentStartDate = $queryResult['startDate'];
	       	 	$currentEndDate = $queryResult['endDate'];
	        	if($startDate != strtotime($currentStartDate) || $endDate != strtotime($currentEndDate)){ 
	        		$query = ' UPDATE delivery_reminders set startDate = ?, endDate = ?, updated = ? where deliveryID = ? AND userID = ?';
	        		$runQuery = true;
	        	}
	        }
	    	
	    	
	    	if(!empty($startDate) && !empty($endDate) && $runQuery==true){
	   	        $returnValue = $dbWrapper->exec($query, array(
		       		date("Y-m-d H:i:s",$startDate),
		       		date("Y-m-d H:i:s",$endDate),
		            $platform->getNowExpression(), 
		            $delivery->getUri(), 
		            $user
		         ));
	    	}
		}//End foreach		
		
		//return (bool) $returnValue
    
    }//End scheduleEmails
    
    protected function sendEmails(){
	
		//Current Delivery URI
		$uri = $this->getCurrentInstance();
		
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
		
		
		
		//Get groups assigned to this delivery
		 $groups = $groupClass->searchInstances(array(
            PROPERTY_GROUP_DELVIERY => $uri->getUri()
        ), array('recursive' => true, 'like' => false));
   
        //Get all users under a group
        foreach ($groups as $group) {
            $users = array_merge($users, $group->getPropertyValues($memberProp));
        }
        
		foreach($users as $userUri){
			$user = new core_kernel_classes_Resource($userUri);
			            
            //Get Peers emails
            foreach($subjectClassProps as $prop){
        			$testtakerPeersGroup[] = $user->getOnePropertyValue($prop);
        	}
            $testtakers[$userUri]['firstName'] = $user->getOnePropertyValue($userFirstNameProp)->literal;
            $testtakers[$userUri]['lastName'] = $user->getOnePropertyValue($userLastNameProp)->literal;
            $testtakers[$userUri]['email']= $user->getOnePropertyValue($userMailProp)->literal;
		}
		
		foreach ($testtakerPeersGroup as $peerGroup) {
		
            $peerUsers = array_merge($peerUsers, $peerGroup->getPropertyValues($memberProp));
        }
        
        foreach($peerUsers as $userUri){
			$user = new core_kernel_classes_Resource($userUri);
            $peerUserEmails[$userUri]['firstName'] = $user->getOnePropertyValue($userFirstNameProp)->literal;
            $peerUserEmails[$userUri]['lastName'] = $user->getOnePropertyValue($userLastNameProp)->literal;
            $peerUserEmails[$userUri]['email']= $user->getOnePropertyValue($userMailProp)->literal;
		}
		
		$recipients = array_merge($testtakers,$peerUserEmails);
		//Send testTaker Emails
		$messages=array();
		$resultMessage[]=array();
        foreach($recipients as $recipient)
		{
			
			$message =  new tao_helpers_transfert_Message();
        	$message->setTitle("New Test is available for you.");
        	$message->setFrom("mail@ahmedjabar.com");

		   $message->setBody("<p>Dear ".$recipient['firstName'].' '.$recipient['lastName'].",</p> <p>a new test is available for you. Please login to TAO tool to take the test.</p>");
		   $message->setTo($recipient['email']);
		   $messages[]=$message;

		}
		
	   $adapter = new tao_helpers_transfert_MailAdapter();
       $adapter->setMessages($messages);
       if($adapter->send()>= 1){
        return true;
       }else{
        return false;
       }

	}
      
}