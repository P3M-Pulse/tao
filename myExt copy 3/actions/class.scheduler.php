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
 use oat\tao\helpers\Template;


class myExt_actions_Scheduler extends tao_actions_CommonModule {
	
	public function __construct(){
		//$this->service = taoDelivery_models_classes_DeliveryServerService::singleton();
		$this->executionService = taoDelivery_models_classes_execution_ServiceProxy::singleton();
	}

	public function run(){
	
		$now = date('Y-m-d H:i:s');
		$scheduledDeliveries = array();
		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
    	$query = 'SELECT * FROM delivery_reminders WHERE startDate >= ? ';
        $result = $dbWrapper->query($query, array($now));
        //$statement = $result->fetch();
        
        //echo "<pre>";
       //print_r($statement);
        
        while ($row = $result->fetch()){
        
        	$scheduledDeliveries[$row['ID']]['deliveryID'] = $row['deliveryID'];
        	$scheduledDeliveries[$row['ID']]['userID'] = $row['userID'];
        	$scheduledDeliveries[$row['ID']]['endDate'] = $row['endDate'];
        }
		foreach($scheduledDeliveries as $delivery){
			
			$this->sendEmails($delivery['deliveryID'],$delivery['userID'], $delivery['endDate']);
			
		}

	}
	
	//To DO: See if there is a away to get the Delivery OBJECT not only URI. 
	 protected function sendEmails($uri, $userUri,$endDate){
		
		//Current Delivery URI
		//$uri = $this->getCurrentInstance();
		
		//Load Classes
		//$groupClass = new core_kernel_classes_Class(TAO_GROUP_CLASS);
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
		 /*$groups = $groupClass->searchInstances(array(
            PROPERTY_GROUP_DELVIERY => $uri
        ), array('recursive' => true, 'like' => false));*/
   
        //Get all users under a group
        /*foreach ($groups as $group) {
            $users = array_merge($users, $group->getPropertyValues($memberProp));
        }*/
        
		
		
		$user = new core_kernel_classes_Resource($userUri);
		            
        //Get Peers emails
        foreach($subjectClassProps as $prop){
    			$testtakerPeersGroup[] = $user->getOnePropertyValue($prop);
    	}
    	//die(print_r($this->executionService->getDeliveryExecutionsByStatus($userUri,INSTANCE_DELIVERYEXEC_FINISHED)));
        $testtakers[$userUri]['firstName'] = $user->getOnePropertyValue($userFirstNameProp)->literal;
        $testtakers[$userUri]['lastName'] = $user->getOnePropertyValue($userLastNameProp)->literal;
        $testtakers[$userUri]['email']= $user->getOnePropertyValue($userMailProp)->literal;
		
		
		foreach ($testtakerPeersGroup as $peerGroup) {
		
            $peerUsers = array_merge($peerUsers, $peerGroup->getPropertyValues($memberProp));
        }
       
        foreach($peerUsers as $peerUri){
			$peerUser = new core_kernel_classes_Resource($peerUri);
            $peerUserEmails[$peerUri]['firstName'] = $peerUser->getOnePropertyValue($userFirstNameProp)->literal;
            $peerUserEmails[$peerUri]['lastName'] = $peerUser->getOnePropertyValue($userLastNameProp)->literal;
            $peerUserEmails[$peerUri]['email']= $peerUser->getOnePropertyValue($userMailProp)->literal;
		}
		
		
		$recipients = array_merge($testtakers,$peerUserEmails);
		//Send testTaker Emails
		$messages=array();
		$resultMessage[]=array();
        foreach($recipients as $recipient)
		{
			//echo $recipient['email'];
			
			$message =  new tao_helpers_transfert_Message();
        	$message->setTitle("New Test is available for you.");
        	$message->setFrom("mail@ahmedjabar.com");

		   $message->setBody("<p>Dear ".$recipient['firstName'].' '.$recipient['lastName'].",</p> <p>a new test is available for you. Please login AIM-Pulse to take the test. The deadline to take this test is ".date('l, d F Y ',strtotime($endDate))."</p>");
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
?>
