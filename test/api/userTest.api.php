<?php

/**
*
* A very simple test for the new OO API 
*
**/

//make sure you are using php 5, the code will behave differently otherwise
if (0 > version_compare(PHP_VERSION, '5')) {
    trigger_error('This application requires PHP version 5', E_USER_ERROR);
}
/**
* @constant login for the generis module you wish to connect to 
*/
define("LOGIN", "test", true);
/**
* @constant password for the module you wish to connect to 
*/
define("PASS", "test", true);
/**
* @constant module for the module you wish to connect to 
*/
define("MODULE", "testTests", true);

//this will make all "require_once" to consider the generis/ directory instead of only the directory containg this file, This is illustrated below.
set_include_path("../../");

//include the oo implementation of the api
include_once("core/kernel/classes/class.ApiModelOO.php");

//creates the api instance
$generisApi = core_kernel_impl_ApiModelOO::singleton();

//Log in the module
$generisApi->logIn(LOGIN,md5(PASS),MODULE,true);




//you may also change environment to get sql level debug informations if needed
core_kernel_classes_DbWrapper::singleton()->dbConnector->debug=true;
core_kernel_classes_DbWrapper::singleton()->dbConnector->debug=false;



//retrieve classes that are not subclass of other classes in the knowledge base
$topClasses = $generisApi->getRootClasses();

//print it using html rendering of collection
echo $topClasses->toHtml();

//pop an element from the collection
$Class =   $topClasses->get(0);

//retrieve label comment uri
$uriResource = $Class->uriResource;

$label = $Class->label;
$comment = $Class->getComment();

//print it
echo $Class->toHtml();

//call methods on it 
$subClasses = $Class->getSubClasses();
$instances = $Class->getInstances();
$i=0;
//or loop among  all classes
while (!($topClasses->isEmpty()))
	{
		$i++;	
			//pop an element from the collection
			$Class =   $topClasses->get($i);
						
		//Calls made on the object to retrieve other resources

			//you may retrieve direct subclasses of the popped class
			$subClasses = $Class->getSubClasses();
				//print the collection
				echo $subClasses->toHtml();
				
					if (!($subClasses->isEmpty()))
					{
						//pop a subclass
						$aSubClass = $subClasses->get(0);
						//retrieve its label & comment
						$label = $aSubClass->label;
						$comment = $aSubClass->getComment();

						//print it
						echo $aSubClass->toHtml();

						//or recall getsubClasses, getInstances, etc... 
						//$subSubClasses = $aSubClass->getSubClasses();$instances = $aSubClass->getInstances();
					}
			
			//you may retrieve indirect subclasses of the popped class
			$allSubClasses = $Class->getIndirectSubClasses();
				echo $allSubClasses->toHtml();
			
			//you amy retrieve direct instances
			$classes =  $Class->getInstances();
				echo $classes->toHtml();

			//you may retrieve indirectinstances
			$properties =  $Class->getProperties();
				echo $properties->toHtml();

			//you amy retrieve parent classes
			$parentClasses =  $Class->getParentClasses(true);
				echo $parentClasses->toHtml();

		//checkings
			
				
			//check if it is a class (this method is located on (parent) resource), in the case of a class php object it always returns true 
			
			($Class->isClass()==true) or trigger_error("This is not a class");
			
			$typeProperty = new core_kernel_classes_Resource("http://www.w3.org/1999/02/22-rdf-syntax-ns#type");
			((!$typeProperty->isClass())	or (trigger_error("This is a class")));
			

			$PropertyClass = new core_kernel_classes_Resource("http://www.w3.org/1999/02/22-rdf-syntax-ns#Property");
			(($PropertyClass->isClass())	or (trigger_error("This is not a class")));

			//check if it is a subClassOf 		

			(((!($subClasses->isEmpty()))&&	($subClasses->get(0)->isSubClassOf($Class)==true))
				or	trigger_error("This is not  a subClass of ". $Class->uriResource));


		//modifying the object
	
	}

/**
* This section illustrates how to retrieve classes using metaclasses
*/
//retrieve metaclasses
$metaClasses = $generisApi->getMetaClasses();

//since the api is made as a singleton you may retrieve it and perform query using 
$metaClasses = core_kernel_impl_ApiModelOO::singleton()->getMetaClasses();

//loop among metaclasses using the collection object
while (!($metaClasses->isEmpty()))
	{
		//pop one metaclass
		$metaClasse = $metaClasses->get();

		//you may now call methods on this metaclass (being a rdfs:class)
		$classes = $metaClasse->getInstances();
	}
		
//print_r($properties);
	


/*if ($Class->uriResource == "http://www.w3.org/2000/01/rdf-schema#Resource")
				core_kernel_classes_DbWrapper::singleton()->dbConnector->debug=true;*/



?>