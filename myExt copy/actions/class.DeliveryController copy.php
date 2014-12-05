<?php 
use oat\tao\helpers\Template;

class myExt_actions_DeliveryController extends tao_actions_CommonModule{
	
	
	public function sayHello(){
		$this->setData('name', 'bertrand');
          $this->setView('hello.tpl');
	}
	
	public function sendEmails(){
	
		//Current Delivery URI
		$uri = new core_kernel_classes_Resource(tao_helpers_Uri::decode($this->getRequestParameter('uri')));
		
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
            		//$prop->getLabel();
            		//print_r($prop);
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
       if($adapter->send()== 1){
        $resultMessage[]= 'Emails were sent successfully.';
       }else{
        	$resultMessage[]= 'There was an error in sendin the email.';
       }

		//$message->setTo($testtakers);
        //$message->setFrom("mail@ahmedjabar.com");

       // $adapter = new tao_helpers_transfert_MailAdapter();
        //$adapter->setMessages(array($message));
       /*
 if($adapter->send()== 1){
            $resultMessage= 'Emails were sent successfully.';
        }else{
        	$resultMessage= 'There was an error in sendin the email.';
        }
*/
        
		$this->setData('message',$resultMessage);
		$this->setView('email.tpl');

		
		/*
echo "<pre>";
		print_r($testtakersEmails);
		echo "<br/><br/>";
		print_r($peerUserEmails);
*/

		/*

		 $subjects = $subjectClass->searchInstances(array(
            PROPERTY_USER_MAIL => 'testtaker1+ahmed.abduljabar@gmail.com'
        ), array('recursive' => true, 'like' => false));

		$properties_1 = tao_helpers_form_GenerisFormFactory::getClassProperties($subjectClass);
        
       	print_r($properties_1);
       	echo "<br>";
*/

		/*
$subjectClass = new core_kernel_classes_Class(TAO_CLASS_SUBJECT);
       
		 $subjects = $subjectClass->searchInstances(array(
            PROPERTY_USER_MAIL => 'asd@asd.com'
        ), array('recursive' => true, 'like' => false));
        
        print_r($subjects);
        echo "<br>";
        
       $properties_1 = tao_helpers_form_GenerisFormFactory::getClassProperties($subjectClass);
        
       print_r($properties_1);
       echo "<br>";
        
       

        $listValues = array();
        foreach($subjects as $subject){
        	$widgetProperty = tao_helpers_form_GenerisFormFactory::getClassProperties($subjectClass);
        		foreach($widgetProperty as $prop){
        			$listGroups[] = $subject->getPropertyValues($prop);
        		}
        }
        
       foreach($listGroups as $deliveryGroup)
		 $deliveryGroup = $groupClass->searchInstances(array(
            PROPERTY_GROUP_DELVIERY => $assembly->getUri()
        ), array('recursive' => true, 'like' => false));
        
        
        $peers=array();
        
        foreach ($groups as $group) {
            $users = array_merge($users, $group->getPropertyValues($memberProp));
            //print_r($group->getPropertyValues($memberProp));
        }
		
		print_r($users);
        echo "<br>";

*/

        
        /*
echo "<pre>";
        print_r($properties);
        
        foreach($properties as $user){
        	$listValues =$user->getPropertyValues(new core_kernel_classes_Property(RDF_PROPERTY));
        	
        	print_r($listValues);

        }
*/
        
		/*
$property = new core_kernel_classes_Property(PROPERTY_GROUP_DELVIERY);
        $tree = tao_helpers_form_GenerisTreeForm::buildReverseTree(new core_kernel_classes_Resource($uri), $property);
        $tree->setTitle(__('Assigned to'));
       // $tree->setTemplate(Template::getTemplate('form_groups.tpl'));
        $this->setData('groupTree', $tree->render());
*/
        
        
		/*
print_r($uri);
		echo "<br/>";
		
		$groupClass = new core_kernel_classes_Class(TAO_GROUP_CLASS);
       
		 $groups = $groupClass->searchInstances(array(
            PROPERTY_GROUP_DELVIERY => $uri
        ), array('recursive' => true, 'like' => false));
        
        print_r($groups);
        echo "<br>";
        
        foreacH($groups as $group){
        	echo $group->getPropertyValues($memberProp)
        }

		
		$currentUser= tao_models_classes_UserService::singleton()->getOneUser('ahmedjabar');
		$userMailProp = new core_kernel_classes_Property(PROPERTY_USER_MAIL);
		$myemail = $currentUser->getOnePropertyValue($userMailProp);
		/*

				$message =  new tao_helpers_transfert_Message();
                $message->setTitle("HEllo");
                $message->setBody("Ahmed");
                $message->setTo("ahmed.abduljabar@gmail.com");
                $message->setFrom("mail@ahmedjabar.com");

                $adapter = new tao_helpers_transfert_MailAdapter();
                $adapter->setMessages(array($message));
                if($adapter->send()== 1){
                    print('SUCCES');
                }else{
                	print('failure');
                }
*/
	//	$this->setView('listGroups.tpl');

		//echo $myemail;

	}
	
}

?>