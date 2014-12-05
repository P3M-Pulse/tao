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
   /*
 public function __construct()
    {
        parent::__construct();
        
        // the service is initialized by default
        //$this->service = taoDelivery_models_classes_DeliveryAssemblyService::singleton();
        //$this->defaultData();
    }
*/

  
    public function printHello(){
    	echo "hello!";
    }
      
}