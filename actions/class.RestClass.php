<?php
	
class generis_actions_RestClass extends generis_actions_RestResource {



    public function subClasses(){
      


        switch($this->getRequestMethod()) {

            case 'GET' :
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
    
 public function properties(){
        

        switch($this->getRequestMethod()) {

            case 'GET' :
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
    
 public function instances(){
       

        switch($this->getRequestMethod()) {

            case 'GET' :
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