<?php

error_reporting(E_ALL);

/**
 * Service methods to manage the Subjects business models using the RDF API.
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoSubjects
 * @subpackage models_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * The Service class is an abstraction of each service instance. 
 * Used to centralize the behavior related to every servcie instances.
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('tao/models/classes/class.Service.php');

/* user defined includes */
// section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017A5-includes begin
// section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017A5-includes end

/* user defined constants */
// section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017A5-constants begin
// section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017A5-constants end

/**
 * Service methods to manage the Subjects business models using the RDF API.
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoDocsFin
 * @subpackage models_classes
 */
class taoDocsFin_models_classes_DocsService
    extends tao_models_classes_Service
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---


    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    public function __construct()
    {
        // section 10-13-1-45-69571c33:1239d9f7146:-8000:0000000000001896 begin
		
	parent::__construct();

        // section 10-13-1-45-69571c33:1239d9f7146:-8000:0000000000001896 end
    }


 

} /* end of class taoDocsFin_models_classes_SubjectsService */

?>
