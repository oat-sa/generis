<?php

class generis_actions_RestResource extends Module {

    public function __construct(){
        //TODO link with HTTP auth below
        if(defined('ENABLE_SUBSCRIPTION') 	&& ENABLE_SUBSCRIPTION ){
        	$userService = core_kernel_users_Service::singleton();
        	$userService->login(SYS_USER_LOGIN, SYS_USER_PASS, new core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAO.rdf#TaoManagerRole'));
        }
        else{
              header('HTTP/1.1 401 Unauthorized');
              die('Unauthorized to access this area.');
        }
    }

    public function __destruct(){
        $userService = core_kernel_users_Service::singleton();
        $userService->logout();
    }

    public function auth(){
        if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']))
        { 
                header('HTTP/1.1 401 Unauthorized');
                die('Unauthorized to access this area.');
        }
        else
        {
        	$userService = core_kernel_users_Service::singleton();
        	
            try
            {
            	$userService->login(SYS_USER_LOGIN, SYS_USER_PASS, new core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAO.rdf#TaoManagerRole'));
            }
            catch (Exception $e)
            {
                $userService->logout();
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
            	
            	$userService = core_kernel_users_Service::singleton();
            	
                try {
					
                    if($this->hasRequestParameter('uri')){
                        $resource = new core_kernel_classes_Resource($this->getRequestParameter('uri'));
                    }
                    else {
                        $userService->logout();
                        header('HTTP/1.1 400 Bad Request');
                        die('uri parameter is missing');
                    }
                    if($this->hasRequestParameter('property')){
                        $property = new core_kernel_classes_Property($this->getRequestParameter('property'));
                    }
                    else {
                        $userService->logout();
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
                    
                    
                    $userService->logout();
                    break;
                }

                catch (Exception $e)
                {
                    $userService->logout();
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
            	$userService = core_kernel_users_Service::singleton();
            	
                try {
					$this->auth();
                    if($this->hasRequestParameter('uri')){
                        $resource = new core_kernel_classes_Resource($this->getRequestParameter('uri'));
                    }
                    else {
                        $userService->logout();
                        header('HTTP/1.1 400 Bad Request');
                        die('uri parameter is missing');
                    }

                    header('HTTP 1.1/ 200 OK');
                    header('Content-type: text/xml; charset=UTF-8');

                    echo "<resource>\n";
                    echo " <label>" . common_Utils::fullTrim($resource->getLabel()). "</label>\n";
                    echo " <comment>" . common_Utils::fullTrim($resource->getLabel()). "</comment>\n";
                    echo "</resource>\n";
                    $userService->logout();
                    break;
                }

                catch (common_Exception $e)
                {
                    $userService->logout();
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
