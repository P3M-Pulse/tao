<?php

class myExt_actions_Authoring extends taoSimpleDelivery_actions_Authoring
{
	public function wizard()
    {
        $this->defaultData();
        /*
try {
            $formContainer = new \taoSimpleDelivery_actions_form_WizardForm(array('class' => $this->getCurrentClass()));
            $myForm = $formContainer->getForm();
             
            if ($myForm->isValid() && $myForm->isSubmited()) {
*/
           /*
echo "<pre>";
            die($myForm->getValue('classUri'));
*/
                $label = 'Hello';
                $test = new core_kernel_classes_Resource('http://localhost/tao.rdf#i1411841665853547');
                $label = __("Delivery of %s", 'It is a new day');
                $deliveryClass = new core_kernel_classes_Class('http://localhost/tao.rdf#i14147807409055728');
                $report = taoSimpleDelivery_models_classes_SimpleDeliveryService::singleton()->create($deliveryClass, $test, $label);
                if ($report->getType() == common_report_Report::TYPE_SUCCESS) {
                    $assembly = $report->getdata();
                   // die($assembly->getUri());
                   /*
 $this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($assembly->getUri()));
                    $this->setData('reload', true);
                    $this->setData('message', __('Delivery created'));
                    $this->setData('formTitle', __('Create a new delivery'));
                    $this->setView('form_container.tpl', 'tao');
*/
					$this->editDeliveryDetails($assembly);
					
					
                } else {
                    $this->setData('report', $report);
                    $this->setData('title', __('Error'));
                    $this->setView('report.tpl', 'tao');
                }
            /*} 
else {
                $this->setData('myForm', $myForm->render());
                $this->setData('formTitle', __('Create a new delivery'));
                $this->setView('form_container.tpl', 'tao');
            }
            
        } catch (taoSimpleDelivery_actions_form_NoTestsException $e) {
            $this->setView('wizard_error.tpl');
        }
*/
    }
	
	public function editDeliveryDetails($delivery){
	
		//$property[TAO_DELIVERY_START_PROP] = time(); 
    	//$property[TAO_DELIVERY_END_PROP] = time();
    	$delivery->editPropertyValues(new core_kernel_classes_Property(TAO_DELIVERY_START_PROP),time());
    	$delivery->editPropertyValues(new core_kernel_classes_Property('http://localhost/tao.rdf#i14147807748102729'),'http://localhost/tao.rdf#i14136647491602151');
    	//$binder = new tao_models_classes_dataBinding_GenerisFormDataBinder($delivery);
        //$delivery = $binder->bind($property); 
	
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