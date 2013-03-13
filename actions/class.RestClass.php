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
	
class generis_actions_RestClass extends generis_actions_RestResource {



    public function subClasses(){
      


        switch($this->getRequestMethod()) {

            case 'GET' :
            	$userService = core_kernel_users_Service::singleton();
                try {
					
                    if(!$this->hasRequestParameter('clazz')){
                        $clazz = new core_kernel_classes_Class(CLASS_GENERIS_RESOURCE);
                    }
                    else{
                        $clazz = new core_kernel_classes_Class($this->getRequestParameter('clazz'));
                    }
                    $detailled = false;
                    if($this->hasRequestParameter('detailled')){
                        $detailled = strtolower($this->getRequestParameter('detailled')) == 'true';
                    }
                    $recursive = false;
                    if($this->hasRequestParameter('recursive')){
                        $recursive = strtolower($this->getRequestParameter('recursive')) == 'true';
                    }

                    $allSubclass = $clazz->getSubClasses();

                    header('HTTP 1.1/ 200 OK');
                    header('Content-type: text/xml; charset=UTF-8');
                    echo "<classes>\n";
                    foreach ($allSubclass as $uri => $resource){
                        echo "<class>\n";
                        echo " <uri>" . $uri. "</uri>\n";
                        if($detailled){
                            echo " <label>" . common_Utils::fullTrim($resource->getLabel()). "</label>\n";
                            echo " <comment>" . common_Utils::fullTrim($resource->getLabel()). "</comment>\n";
                        }
                        echo "</class>\n";

                    }
                    echo "</classes>";
                    
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
    
 public function properties(){
        

        switch($this->getRequestMethod()) {

            case 'GET' :
            	
            	$userService = core_kernel_users_Service::singleton();
            	
                try {
					
                    if(!$this->hasRequestParameter('clazz')){
                        $clazz = new core_kernel_classes_Class(CLASS_GENERIS_RESOURCE);
                    }
                    else{
                        $clazz = new core_kernel_classes_Class($this->getRequestParameter('clazz'));
                    }
                    $detailled = false;
                    if($this->hasRequestParameter('detailled')){
                        $detailled = strtolower($this->getRequestParameter('detailled')) == 'true';
                    }
                    $recursive = false;
                    if($this->hasRequestParameter('recursive')){
                        $recursive = strtolower($this->getRequestParameter('recursive')) == 'true';
                    }


                    $allResource = $clazz->getProperties($recursive);

                    header('HTTP 1.1/ 200 OK');
                    header('Content-type: text/xml; charset=UTF-8');
                    
                    echo "<classes>\n";
                    foreach ($allResource as $uri => $resource){
                        echo "<class>\n";
                        echo " <uri>" . $uri. "</uri>\n";
                        if($detailled){
                            echo " <label>" . common_Utils::fullTrim($resource->getLabel()). "</label>\n";
                            echo " <comment>" . common_Utils::fullTrim($resource->getLabel()). "</comment>\n";
                        }
                        echo "</class>\n";

                    }
                    echo "</classes>";
                    
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
    
 public function instances(){
       

        switch($this->getRequestMethod()) {

            case 'GET' :
            	$userService = core_kernel_users_Service::singleton();
                try {
					
                    if(!$this->hasRequestParameter('clazz')){
                        $clazz = new core_kernel_classes_Class(CLASS_GENERIS_RESOURCE);
                    }
                    else{
                        $clazz = new core_kernel_classes_Class($this->getRequestParameter('clazz'));
                    }
                    $detailled = false;
                    if($this->hasRequestParameter('detailled')){
                        $detailled = strtolower($this->getRequestParameter('detailled')) == 'true';
                    }
                    $recursive = false;
                    if($this->hasRequestParameter('recursive')){
                        $recursive = strtolower($this->getRequestParameter('recursive')) == 'true';
                    }


                    $allResource = $clazz->getInstances($recursive);

                    header('HTTP 1.1/ 200 OK');
                    header('Content-type: text/xml; charset=UTF-8');
                    
                    echo "<resources>\n";
                    foreach ($allResource as $uri => $resource){
                        echo "<resource>\n";
                        echo " <uri>" . $uri. "</uri>\n";
                        if($detailled){
                            echo " <label>" . common_Utils::fullTrim($resource->getLabel()). "</label>\n";
                            echo " <comment>" . common_Utils::fullTrim($resource->getLabel()). "</comment>\n";
                        }
                        echo "</resource>\n";

                    }
                    echo "</resources>";
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