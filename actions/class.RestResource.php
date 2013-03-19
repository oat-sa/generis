<?php
/*  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);\n *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
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
                             echo "<propertyValue>" . $value->getUri().'</propertyValue>';
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
