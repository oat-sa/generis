TODO changes
<?php
/**  
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
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               2017 (update and modification) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

use core_kernel_api_ModelFactory as ModelFactory;
use oat\generis\model\OntologyRdf;
use oat\oatbox\session\SessionService;
use oat\oatbox\user\UserLanguageServiceInterface;
use oat\generis\model\kernel\uri\UriProvider;

/**
 * Short description of class core_kernel_persistence_smoothsql_Resource
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package generis
 
 */
class core_kernel_persistence_smoothsql_Resource
    implements core_kernel_persistence_ResourceInterface
{
    /** @var ModelFactory */
    protected $modelFactory;

    /**
     * @var core_kernel_persistence_smoothsql_SmoothModel
     */
    private $model;
    
    public function __construct(core_kernel_persistence_smoothsql_SmoothModel $model) {
        $this->modelFactory = $this->getServiceLocator()->get(ModelFactory::SERVICE_ID);
        $this->model = $model;
    }
    
    protected function getModel() {
        return $this->model;
    }
    
    /**
     * @return common_persistence_SqlPersistence
     */
    protected function getPersistence() {
        return $this->model->getPersistence();
    }
    
    protected function getModelReadSqlCondition() {
        return $this->modelFactory->quoteModelSqlCondition($this->model->getReadableModels());
    }
    
    protected function getModelWriteSqlCondition() {
        return $this->modelFactory->quoteModelSqlCondition($this->model->getWritableModels());
    }
    
    protected function getNewTripleModelId() {
        return $this->model->getNewTripleModelId();
    }
    
    
    
    /**
     * returns an array of types the ressource has
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @return array
     */
    public function getTypes( core_kernel_classes_Resource $resource)
    {
        $returnValue = array();

        // TODO: move sql query to implementation. 
		$sqlQuery = 'SELECT object FROM statements WHERE subject = ? and predicate = ?';
        $sth = $this->getPersistence()->query($sqlQuery,array($resource->getUri(), OntologyRdf::RDF_TYPE));

        while ($row = $sth->fetch()){
            $uri = $this->getPersistence()->getPlatForm()->getPhpTextValue($row['object']);
            $returnValue[$uri] = $this->getModel()->getClass($uri);
        }        
        

        return (array) $returnValue;
    }

    /**
     * Short description of method getPropertyValues
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  core_kernel_classes_Resource resource
     * @param  core_kernel_classes_Property property
     * @param  array options
     * @return array
     * @throws core_kernel_persistence_Exception
     */
    public function getPropertyValues( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property, $options = array())
    {
        $returnValue = array();

        
        $one = isset($options['one']) && $options['one'] == true ? true : false;
        if (isset($options['last'])) {
            throw new core_kernel_persistence_Exception('Option \'last\' no longer supported');
        }
		$platform = $this->getPersistence()->getPlatForm();
		
    	// Define language if required
		$defaultLg = '';
		if (isset($options['lg'])) {
			$lang = $options['lg'];
		} else {
            $lang = $this->getServiceLocator()->get(SessionService::SERVICE_ID)->getCurrentSession()->getDataLanguage();
            $default = $this->getServiceLocator()->get(UserLanguageServiceInterface::SERVICE_ID)->getDefaultLanguage();
            $defaultLg = ' OR l_language = ' . $this->getPersistence()->quote($default);
		}

        // TODO: move sql query to implementation.
        $query =  'SELECT object, l_language
        			FROM statements 
		    		WHERE subject = ? 
		    		AND predicate = ?
					AND (l_language = ? OR l_language = ' . $this->getPersistence()->quote('') . $defaultLg . ')
		    		AND '.$this->getModelReadSqlCondition();
        
    	
		if ($one) {
            // Select first only
			$query .= ' ORDER BY ' . $this->modelFactory->getPropertySortingField() . ' DESC';
			$query = $platform->limitStatement($query, 1, 0);
		}
        $result = $this->getPersistence()->query($query,array($resource->getUri(), $property->getUri(), $lang));
        
		// Treat the query result
        if ($result == true) {
        	if (isset($options['lg'])) {
                // If a language has been defined, do not filter result by language
		    	while ($row = $result->fetch()) {
					$returnValue[] = $this->getPersistence()->getPlatForm()->getPhpTextValue($row['object']);
				}
        	} else {
                // Filter result by language and return one set of values (User language in top priority, default language in second and the fallback language (null) in third)
                $returnValue = core_kernel_persistence_smoothsql_Utils::filterByLanguage($this->getPersistence(), $result->fetchAll(), 'l_language', $lang, $default);
        	}
        }
        
        return (array) $returnValue;
    }

    /**
     * Short description of method getPropertyValuesByLg
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  core_kernel_classes_Resource resource
     * @param  core_kernel_classes_Property property
     * @param  string lg
     * @return core_kernel_classes_ContainerCollection
     */
    public function getPropertyValuesByLg( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property, $lg)
    {
        $returnValue = null;

        
        
        $options = array (
        	'lg' => $lg
        );
        
        $returnValue = new core_kernel_classes_ContainerCollection($resource);
        foreach ($this->getPropertyValues($resource, $property, $options) as $value){
            $returnValue->add(common_Utils::toResource($value));
        }
        
        

        return $returnValue;
    }

    /**
     * Short description of method setPropertyValue
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  core_kernel_classes_Resource resource
     * @param  core_kernel_classes_Property property
     * @param  string object
     * @param  string lg
     * @return boolean
     */
    public function setPropertyValue( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property, $object, $lg = null)
    {
        $userId = $this->getServiceLocator()->get(SessionService::SERVICE_ID)->getCurrentUser()->getIdentifier();
        $object  = $object instanceof core_kernel_classes_Resource ? $object->getUri() : (string) $object;

        // Define language if required
        $lang = '';
        if ($property->isLgDependent()){
        	if ($lg!=null){
        		$lang = $lg;
        	} else {
                $lang = $this->getServiceLocator()->get(SessionService::SERVICE_ID)->getCurrentSession()->getDataLanguage();
        	}
        }

        return $this->modelFactory->addStatement(
            $this->getNewTripleModelId(),
            $resource->getUri(),
            $property->getUri(),
            $object,
            $lang,
            $userId
        );
    }

    /**
     * Short description of method setPropertiesValues
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  core_kernel_classes_Resource resource
     * @param  array properties
     * @return boolean
     */
    public function setPropertiesValues( core_kernel_classes_Resource $resource, $properties)
    {
        $returnValue = false;

    	if (is_array($properties) && count($properties) > 0) {
        		
            $session = $this->getServiceLocator()->get(SessionService::SERVICE_ID)->getCurrentSession();

            $modelId = $this->getNewTripleModelId();
            $subject = $resource->getUri();
            $author = $session->getUser()->getIdentifier();

            foreach ($properties as $propertyUri => $value) {
                
                $property = $this->getModel()->getProperty($propertyUri);
                
                $lang = ($property->isLgDependent() ? $session->getDataLanguage() : '');
                $formatedValues = [];
                
                // @TODO: refactor this
                if ($value instanceof core_kernel_classes_Resource) {
                    $formatedValues[] = $value->getUri();
                    
                } elseif (is_array($value)) {
                    foreach($value as $val){
                        $formatedValues[] = ($val instanceof core_kernel_classes_Resource) ? $val->getUri() : $val;
                    }
                } else {
                    $formatedValues[] = ($value == null) ? '' : $value;
                }
                
                foreach ($formatedValues as $object) {
                    $returnValue |= $this->modelFactory->addStatement($modelId, $subject, $property->getUri(), $object, $lang, $author);
                }
            }
        }

        return $returnValue;
    }

    /**
     * Short description of method setPropertyValueByLg
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  core_kernel_classes_Resource resource
     * @param  core_kernel_classes_Property property
     * @param  string value
     * @param  string lg
     * @return boolean
     */
    public function setPropertyValueByLg( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property, $value, $lg)
    {
		$userId = $this->getServiceLocator()->get(SessionService::SERVICE_ID)->getCurrentUser()->getIdentifier();

        return $this->modelFactory->addStatement(
            $this->getNewTripleModelId(),
            $resource->getUri(),
            $property->getUri(),
            $value,
            ($property->isLgDependent() ? $lg : ''),
            $userId
        );
    }

    /**
     * Short description of method removePropertyValues
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  core_kernel_classes_Resource resource
     * @param  core_kernel_classes_Property property
     * @param  array options
     * @return boolean
     */
    public function removePropertyValues( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property, $options = array())
    {
        $returnValue = (bool) false;

		// Optional params
        $pattern = isset($options['pattern']) && !is_null($options['pattern']) ? $options['pattern'] : null;
        $like = isset($options['like']) && $options['like'] == true ? true : false;

        // TODO: move sql query to implementation.
		//build query:
		$query =  'DELETE FROM statements WHERE subject = ? AND predicate = ?';
		$objectType = $this->getPersistence()->getPlatForm()->getObjectTypeCondition();
		$conditions = array();
		if (is_string($pattern)) {
			if (!is_null($pattern)) {
				$searchPattern = core_kernel_persistence_smoothsql_Utils::buildSearchPattern($this->getPersistence(), $pattern, $like);
				$conditions[] = '( '.$objectType . ' ' .$searchPattern.' )';
			}
		} elseif (is_array($pattern)) {
			if (count($pattern) > 0) {
				$multiCondition =  "( ";
				foreach($pattern as $i => $patternToken) {
					$searchPattern = core_kernel_persistence_smoothsql_Utils::buildSearchPattern($this->getPersistence(), $patternToken, $like);
					if ($i > 0) {
                        $multiCondition .= " OR ";
                    }
					$multiCondition .= '('.$objectType. ' ' .$searchPattern.' )';
				}
				$conditions[] = "{$multiCondition} ) ";
			}
		}
			
        foreach ($conditions as $i => $additionalCondition) {
			$query .= " AND ( {$additionalCondition} ) ";
		}
        
		//be sure the property we try to remove is included in an updatable model
		$query .= ' AND '.$this->getModelWriteSqlCondition();
		
        if ($property->isLgDependent()) {
        	
        	$query .=  ' AND (l_language = ? OR l_language = ?) ';
        	$returnValue = $this->getPersistence()->exec($query,array(
	        		$resource->getUri(),
	        		$property->getUri(),
                    '',
                    $this->getServiceLocator()->get(SessionService::SERVICE_ID)->getCurrentSession()->getDataLanguage()
	        ));
        } else{
        	$returnValue = $this->getPersistence()->exec($query,array(
	        		$resource->getUri(),
	        		$property->getUri()
	        ));   
        }
        
        if (!$returnValue) {
        	$returnValue = false;
        }
        
        return (bool) $returnValue;
    }

    /**
     * Short description of method removePropertyValueByLg
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  core_kernel_classes_Resource resource
     * @param  core_kernel_classes_Property property
     * @param  string lg
     * @param  array options
     * @return boolean
     */
    public function removePropertyValueByLg( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property, $lg, $options = array())
    {
        // TODO: move sql query to implementation.
        $sqlQuery = 'DELETE FROM statements WHERE subject = ? and predicate = ? and l_language = ?';
        //be sure the property we try to remove is included in an updatable model
		$sqlQuery .= ' AND '.$this->getModelWriteSqlCondition();
        
        return (bool) $this->getPersistence()->exec($sqlQuery, array (
        	$resource->getUri(),
        	$property->getUri(),
            ($property->isLgDependent() ? $lg : '')
        ));
    }

    /**
     * returns the triples having as subject the current resource
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  core_kernel_classes_Resource resource
     * @return core_kernel_classes_ContainerCollection
     */
    public function getRdfTriples( core_kernel_classes_Resource $resource)
    {
        $returnValue = null;

        // TODO: move sql query to implementation.
        $query = 'SELECT * FROM statements WHERE subject = ? AND '.$this->getModelReadSqlCondition().' ORDER BY predicate';
        $result = $this->getPersistence()->query($query, array($resource->getUri()));
        
        $returnValue = new core_kernel_classes_ContainerCollection(new common_Object(__METHOD__));
        while ($statement = $result->fetch()) {
            $triple = new core_kernel_classes_Triple();
            $triple->modelid = $statement["modelid"];
            $triple->subject = $statement["subject"];
            $triple->predicate = $statement["predicate"];
            $triple->object = $statement["object"];
            $triple->id = $statement["id"];
            $triple->epoch = $statement["epoch"];
            $triple->lg = $statement["l_language"];
            $triple->author = $statement["author"];
            $returnValue->add($triple);
        }

        return $returnValue;
    }

    /**
     * Short description of method getUsedLanguages
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  core_kernel_classes_Resource resource
     * @param  core_kernel_classes_Property property
     * @return array
     */
    public function getUsedLanguages( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property)
    {
        $returnValue = array();

        // TODO: move sql query to implementation.
    	$sqlQuery = 'SELECT l_language FROM statements WHERE subject = ? AND predicate = ? ';
        $sqlResult = $this->getPersistence()->query($sqlQuery, array (
        	$resource->getUri(),
        	$property->getUri()
        ));
        while ($row = $sqlResult->fetch()) {
            if (!empty($row['l_language'])) {
                $returnValue[] = $row['l_language'];
            }
        }
        
        

        return (array) $returnValue;
    }

    /**
     * Short description of method duplicate
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  core_kernel_classes_Resource resource
     * @param  array excludedProperties
     * @return core_kernel_classes_Resource|null
     * @throws common_exception_Error
     */
    public function duplicate( core_kernel_classes_Resource $resource, $excludedProperties = array())
    {
        $newUri = $this->getServiceLocator()->get(UriProvider::SERVICE_ID)->provide();
    	$collection = $this->getRdfTriples($resource);

        if ($collection->count() === 0) {
            return null;
        }

            $user = $this->getServiceLocator()->get(SessionService::SERVICE_ID)->getCurrentUser()->getIdentifier();
        $modelId = $this->getNewTripleModelId();
        $addedRows = false;

    		foreach ($collection->getIterator() as $triple) {
    			if (!in_array($triple->predicate, $excludedProperties)) {
                $addedRows |= $this->modelFactory->addStatement(
                    $modelId,
                    $newUri,
                    $triple->predicate,
                    $triple->object ?? '',
                    $triple->lg ?? '',
                    $user
                );
    			}
	    	}

        	if ($addedRows) {
            	return $this->getModel()->getResource($newUri);
        	}

        return null;
    }

    /**
     * Short description of method delete
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  core_kernel_classes_Resource resource
     * @param  boolean deleteReference
     * @return boolean
     */
    public function delete( core_kernel_classes_Resource $resource, $deleteReference = false)
    {
        // TODO: move sql query to implementation.
		$query = 'DELETE FROM statements WHERE subject = ? AND '.$this->getModelWriteSqlCondition();
        $returnValue = $this->getPersistence()->exec($query, array($resource->getUri()));

        //if no rows affected return false
        if (!$returnValue){
        	$returnValue = false;
        } 
        else if($deleteReference){
            // TODO: move sql query to implementation.
        	$sqlQuery = 'DELETE FROM statements WHERE ' . $this->getPersistence()->getPlatForm()->getObjectTypeCondition() . ' = ? AND '.$this->getModelWriteSqlCondition();
        	$return = $this->getPersistence()->exec($sqlQuery, array ($resource->getUri()));
        	
        	if ($return !== false){
        		$returnValue = true;
        	}
        }
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method getPropertiesValues
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  core_kernel_classes_Resource resource
     * @param  array properties
     * @return array
     * @throws common_exception_Error
     */
    public function getPropertiesValues( core_kernel_classes_Resource $resource, $properties)
    {
        $returnValue = array();

        
        // check whenever or not properties is empty
        if (count($properties) == 0) {
        	return array();
        }
        
        /*foreach($properties as $property){
        	$returnValue[$property->getUri()] = $this->getPropertyValues($resource, $property);
        }*/
        
    	$predicatesQuery = '';
    	//build the predicate query
       	//$predicatesQuery = implode(',', $properties);
		foreach ($properties as $property) {
			$uri = (is_string($property) ? $property : $property->getUri());
			$returnValue[$uri] = array();
			$predicatesQuery .= ", " . $this->getPersistence()->quote($uri) ;
		}
    	$predicatesQuery=substr($predicatesQuery, 1);

        $platform = $this->getPersistence()->getPlatForm();
        $lang = $this->getServiceLocator()->get(SessionService::SERVICE_ID)->getCurrentSession()->getDataLanguage();
        $default = $this->getServiceLocator()->get(UserLanguageServiceInterface::SERVICE_ID)->getDefaultLanguage();

        // TODO: move sql query to implementation.
        //the unique sql query
        $query =  'SELECT predicate, object, l_language 
            FROM statements 
            WHERE 
                subject = '.$this->getPersistence()->quote($resource->getUri()).' 
                AND predicate IN ('.$predicatesQuery.')
                AND (l_language = ' . $this->getPersistence()->quote('') . 
                    ' OR l_language = '.$this->getPersistence()->quote($default).
                    ' OR l_language = '.$this->getPersistence()->quote($lang).')
                AND '.$this->getModelReadSqlCondition();
        $result	= $this->getPersistence()->query($query);
        
        $rows = $result->fetchAll();
        foreach($rows as $row){
        	$value = $platform->getPhpTextValue($row['object']);
			$returnValue[$row['predicate']][] = common_Utils::isUri($value)
				? $this->getModel()->getResource($value)
				: new core_kernel_classes_Literal($value);
        }
        
        

        return (array) $returnValue;
    }

    /**
     * Short description of method setType
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  core_kernel_classes_Resource resource
     * @param  core_kernel_classes_Class class
     * @return boolean
     */
    public function setType( core_kernel_classes_Resource $resource,  core_kernel_classes_Class $class)
    {
        $returnValue = $this->setPropertyValue($resource, $this->getModel()->getProperty(OntologyRdf::RDF_TYPE), $class);
        return (bool) $returnValue;
    }

    /**
     * Short description of method removeType
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  core_kernel_classes_Resource resource
     * @param  core_kernel_classes_Class class
     * @return boolean
     */
    public function removeType( core_kernel_classes_Resource $resource,  core_kernel_classes_Class $class)
    {
        // TODO: move sql query to implementation.
        $query =  'DELETE FROM statements 
		    		WHERE subject = ? AND predicate = ? AND '. $this->getPersistence()->getPlatForm()->getObjectTypeCondition() .' = ?';
        
        //be sure the property we try to remove is included in an updatable model
		$query .= ' AND '.$this->getModelWriteSqlCondition();
        
        $returnValue = $this->getPersistence()->exec($query,array(
        	$resource->getUri(),
			OntologyRdf::RDF_TYPE,
        	$class->getUri()
        ));
        
        $returnValue = true;
        
        

        return (bool) $returnValue;
    }

    /**
     * @return \Zend\ServiceManager\ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->getModel()->getServiceLocator();
    }
}
