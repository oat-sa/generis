<?php

class generis_actions_RestResource extends Module {

    public function __construct(){
        //TODO link with HTTP auth below
        if(defined('ENABLE_SUBSCRIPTION') 	&& ENABLE_SUBSCRIPTION ){
            core_control_FrontController::connect(SYS_USER_LOGIN, SYS_USER_PASS,DATABASE_NAME);
        }
        else{
              header('HTTP/1.1 401 Unauthorized');
              die('Unauthorized to access this area.');
        }
    }

    public function __destruct(){
        core_control_FrontController::logOff();
    }

    public function auth(){
        if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']))
        { 
                header('HTTP/1.1 401 Unauthorized');
                die('Unauthorized to access this area.');
        }
        else
        {
            try
            {
                @core_control_FrontController::connect($_SERVER['PHP_AUTH_USER'],
                md5($_SERVER['PHP_AUTH_PW']),
                DATABASE_NAME);
            }
            catch (Exception $e)
            {
                core_control_FrontController::logOff();
                header('HTTP/1.1 401 Unauthorized');
                die('Unauthorized to access this area.');

            }
        }
    }
    public function index(){
        echo 'index';
    }
    
 public function propertyValues(){


        switch($this->getRequestMethod()) {

            case 'GET' :
                try {
					
                    if($this->hasRequestParameter('uri')){
                        $resource = new core_kernel_classes_Resource($this->getRequestParameter('uri'));
                    }
                    else {
                        core_control_FrontController::logOff();
                        header('HTTP/1.1 400 Bad Request');
                        die('uri parameter is missing');
                    }
                    if($this->hasRequestParameter('property')){
                        $property = new core_kernel_classes_Property($this->getRequestParameter('property'));
                    }
                    else {
                        core_control_FrontController::logOff();
                        header('HTTP/1.1 400 Bad Request');
                        die('property parameter is missing');
                    }

                    header('HTTP 1.1/ 200 OK');
                    header('Content-type: text/xml; charset=UTF-8');
                    $propValues = $resource->getPropertyValuesCollection($property);
                    echo "<propertyValues>\n";
                    foreach ($propValues->getIterator() as $value ){
                        if($value instanceof core_kernel_classes_Resource){
                             echo "<propertyValue>" . $value->uriResource.'</propertyValue>';
                        }
                        else {
                              echo "<propertyValue>" . common_Utils::fullTrim($value).'</propertyValue>';
                        }

                    }
                    echo "</propertyValues>\n";
                    
                    
                    core_control_FrontController::logOff();
                    break;
                }

                catch (Exception $e)
                {
                    core_control_FrontController::logOff();
                    header('WWW-Authenticate: Basic realm="' . PIAAC_HTTP_API_REALM . '"');
                    header('HTTP/1.1 401 Unauthorized');
                    die('Unauthorized');
                }
            default:
                header('HTTP/1.1 405 Method Not Allowed');
                die('Only the GET method is supported for classes');
                break;

        }
    }
    

    public function infos(){


        switch($this->getRequestMethod()) {

            case 'GET' :
                try {
					$this->auth();
                    if($this->hasRequestParameter('uri')){
                        $resource = new core_kernel_classes_Resource($this->getRequestParameter('uri'));
                    }
                    else {
                        core_control_FrontController::logOff();
                        header('HTTP/1.1 400 Bad Request');
                        die('uri parameter is missing');
                    }

                    header('HTTP 1.1/ 200 OK');
                    header('Content-type: text/xml; charset=UTF-8');

                    echo "<resource>\n";
                    echo " <label>" . common_Utils::fullTrim($resource->getLabel()). "</label>\n";
                    echo " <comment>" . common_Utils::fullTrim($resource->getLabel()). "</comment>\n";
                    echo "</resource>\n";
                    core_control_FrontController::logOff();
                    break;
                }

                catch (common_Exception $e)
                {
                    core_control_FrontController::logOff();
                    header('WWW-Authenticate: Basic realm="GENERIS_REALM"');
                    header('HTTP/1.1 401 Unauthorized');
                    die('Unauthorized');
                }
            default:
                header('HTTP/1.1 405 Method Not Allowed');
                die('Only the GET method is supported for classes');
                break;

        }
    }
}
