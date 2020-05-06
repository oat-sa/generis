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

use oat\generis\model\OntologyRdf;
use oat\oatbox\session\SessionService;
use oat\oatbox\user\UserLanguageServiceInterface;
use oat\generis\model\kernel\uri\UriProvider;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Short description of class core_kernel_persistence_smoothsql_Resource
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package generis

 */
class core_kernel_persistence_smoothsql_Resource implements core_kernel_persistence_ResourceInterface
{

    /**
     * @var core_kernel_persistence_smoothsql_SmoothModel
     */
    private $model;
    
    public function __construct(core_kernel_persistence_smoothsql_SmoothModel $model)
    {
        $this->model = $model;
    }
    
    protected function getModel()
    {
        return $this->model;
    }
    
    /**
     * @return common_persistence_SqlPersistence
     */
    protected function getPersistence()
    {
        return $this->model->getPersistence();
    }
    
    protected function getModelReadSqlCondition()
    {
        return 'modelid IN (' . implode(',', $this->model->getReadableModels()) . ')';
    }
    
    protected function getModelWriteSqlCondition()
    {
        return 'modelid IN (' . implode(',', $this->model->getWritableModels()) . ')';
    }
    
    protected function getNewTripleModelId()
    {
        return $this->model->getNewTripleModelId();
    }

    /**
     * returns an array of types the resource has
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  core_kernel_classes_Resource resource
     * @return array
     */
    public function getTypes(core_kernel_classes_Resource $resource)
    {
        $returnValue = [];

        // TODO: refactor this to use a triple retrieving method.
        $sqlQuery = 'SELECT object FROM statements WHERE subject = ? and predicate = ?';
        $sth = $this->getPersistence()->query($sqlQuery, [$resource->getUri(), OntologyRdf::RDF_TYPE]);

        while ($row = $sth->fetch()) {
            $uri = $this->getPersistence()->getPlatForm()->getPhpTextValue($row['object']);
            $returnValue[$uri] = $this->getModel()->getClass($uri);
        }
        

        return (array) $returnValue;
    }

    /**
     * Short description of method getPropertyValues
     *
     * @access public
     * @param core_kernel_classes_Resource $resource
     * @param core_kernel_classes_Property $property
     * @param array $options
     * @return array
     * @throws core_kernel_persistence_Exception
     * @author Joel Bout, <joel.bout@tudor.lu>
     */
    public function getPropertyValues(core_kernel_classes_Resource $resource, core_kernel_classes_Property $property, $options = [])
    {
        $returnValue = [];

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
            $lang = $this->getDataLanguage();
            $default = $this->getServiceLocator()->get(UserLanguageServiceInterface::SERVICE_ID)->getDefaultLanguage();
            $defaultLg = ' OR l_language = ' . $this->getPersistence()->quote($default);
        }

        // TODO: refactor this to use a triple retrieving method.
        $query =  'SELECT object, l_language
        			FROM statements 
		    		WHERE subject = ? 
		    		AND predicate = ?
					AND (l_language = ? OR l_language = ' . $this->getPersistence()->quote('') . $defaultLg . ')
		    		AND ' . $this->getModelReadSqlCondition();
        
        if ($one) {
            // Select first
            $query .= ' ORDER BY id DESC';
            $query = $platform->limitStatement($query, 1, 0);
            $result = $this->getPersistence()->query($query, [$resource->getUri(), $property->getUri(), $lang]);
        } else {
            // Select All
            $result = $this->getPersistence()->query($query, [$resource->getUri(), $property->getUri(), $lang]);
        }
        
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
     * @param core_kernel_classes_Resource $resource
     * @param core_kernel_classes_Property $property
     * @param string $lg
     * @return core_kernel_classes_ContainerCollection
     * @throws core_kernel_persistence_Exception
     * @author Joel Bout, <joel.bout@tudor.lu>
     */
    public function getPropertyValuesByLg(core_kernel_classes_Resource $resource, core_kernel_classes_Property $property, $lg)
    {
        $options =  ['lg' => $lg];
        
        $returnValue = new core_kernel_classes_ContainerCollection($resource);
        foreach ($this->getPropertyValues($resource, $property, $options) as $value) {
            $returnValue->add(common_Utils::toResource($value));
        }
        
        return $returnValue;
    }

    /**
     * Short description of method setPropertyValue
     *
     * @access public
     * @param core_kernel_classes_Resource $resource
     * @param core_kernel_classes_Property $property
     * @param $object
     * @param null $lg
     * @return boolean
     * @author Joel Bout, <joel.bout@tudor.lu>
     */
    public function setPropertyValue(core_kernel_classes_Resource $resource, core_kernel_classes_Property $property, $object, $lg = null)
    {
        $object  = $object instanceof core_kernel_classes_Resource ? $object->getUri() : (string) $object;
        if ($property->isLgDependent()) {
            $lang = ((null != $lg)
                ? $lg
                : $this->getDataLanguage());
        } else {
            $lang = '';
        }
        $triple = core_kernel_classes_Triple::createTriple(
            $this->getNewTripleModelId(),
            $resource->getUri(),
            $property->getUri(),
            $object,
            $lang
        );
        return $this->getModel()->getRdfInterface()->add($triple);
    }

    /**
     * Short description of method setPropertiesValues
     *
     * @access public
     * @param core_kernel_classes_Resource $resource
     * @param Resource resource
     * @return boolean
     * @author Joel Bout, <joel.bout@tudor.lu>
     */
    public function setPropertiesValues(core_kernel_classes_Resource $resource, $properties)
    {
        $returnValue = false;
        $triples = [];

        if (is_array($properties) && count($properties) > 0) {

            /** @var common_session_Session $session */
            $session = $this->getServiceLocator()->get(SessionService::SERVICE_ID)->getCurrentSession();

            $triples = $this->buildTrippleArray(
                $resource,
                $properties,
                (string)$session->getUser()->getIdentifier(),
                $session->getDataLanguage()
            );
        }

        if (!empty($triples)) {
            $returnValue = $this->getModel()->getRdfInterface()->addTripleCollection($triples);
        }

        return $returnValue;
    }

    /**
     * Short description of method setPropertyValueByLg
     *
     * @access public
     * @param core_kernel_classes_Resource $resource
     * @param core_kernel_classes_Property $property
     * @param $value
     * @param $lg
     * @return boolean
     * @author Joel Bout, <joel.bout@tudor.lu>
     */
    public function setPropertyValueByLg(core_kernel_classes_Resource $resource, core_kernel_classes_Property $property, $value, $lg)
    {
        $triple = core_kernel_classes_Triple::createTriple(
            $this->getNewTripleModelId(),
            $resource->getUri(),
            $property->getUri(),
            $value,
            ($property->isLgDependent() ? $lg : '')
        );
        return $this->getModel()->getRdfInterface()->add($triple);
    }

    /**
     * Short description of method removePropertyValues
     *
     * @access public
     * @param core_kernel_classes_Resource $resource
     * @param core_kernel_classes_Property $property
     * @param array options
     * @return boolean
     * @author Joel Bout, <joel.bout@tudor.lu>
     */
    public function removePropertyValues(core_kernel_classes_Resource $resource, core_kernel_classes_Property $property, $options = [])
    {
        // Optional params
        $pattern = isset($options['pattern']) && !is_null($options['pattern']) ? $options['pattern'] : null;
        $like = isset($options['like']) && $options['like'] == true ? true : false;

        // TODO: refactor this to use a triple store abstraction
        //build query:
        $query = 'DELETE FROM statements WHERE subject = ? AND predicate = ?';
        $objectType = $this->getPersistence()->getPlatForm()->getObjectTypeCondition();
        $conditions = [];
        if (is_string($pattern)) {
            if (!is_null($pattern)) {
                $searchPattern = core_kernel_persistence_smoothsql_Utils::buildSearchPattern($this->getPersistence(), $pattern, $like);
                $conditions[] = '( ' . $objectType . ' ' . $searchPattern . ' )';
            }
        } elseif (is_array($pattern)) {
            if (count($pattern) > 0) {
                $multiCondition =  "( ";
                foreach ($pattern as $i => $patternToken) {
                    $searchPattern = core_kernel_persistence_smoothsql_Utils::buildSearchPattern($this->getPersistence(), $patternToken, $like);
                    if ($i > 0) {
                        $multiCondition .= " OR ";
                    }
                    $multiCondition .= '(' . $objectType . ' ' . $searchPattern . ' )';
                }
                $conditions[] = "{$multiCondition} ) ";
            }
        }
            
        foreach ($conditions as $i => $additionalCondition) {
            $query .= " AND ( {$additionalCondition} ) ";
        }
        
        //be sure the property we try to remove is included in an updatable model
        $query .= ' AND ' . $this->getModelWriteSqlCondition();
        
        if ($property->isLgDependent()) {
            $query .=  ' AND (l_language = ? OR l_language = ?) ';
            $returnValue = $this->getPersistence()->exec($query, [
                $resource->getUri(),
                $property->getUri(),
                '',
                $this->getDataLanguage()
            ]);
        } else {
            $returnValue = $this->getPersistence()->exec($query, [
                $resource->getUri(),
                $property->getUri()
            ]);
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
     * @param core_kernel_classes_Resource $resource
     * @param core_kernel_classes_Property $property
     * @param Resource resource
     * @param array $options
     * @return boolean
     * @author Joel Bout, <joel.bout@tudor.lu>
     */
    public function removePropertyValueByLg(core_kernel_classes_Resource $resource, core_kernel_classes_Property $property, $lg, $options = [])
    {
        $sqlQuery = 'DELETE FROM statements WHERE subject = ? and predicate = ? and l_language = ?';
        //be sure the property we try to remove is included in an updatable model
        $sqlQuery .= ' AND ' . $this->getModelWriteSqlCondition();
        
        $returnValue = $this->getPersistence()->exec($sqlQuery, [
            $resource->getUri(),
            $property->getUri(),
            ($property->isLgDependent() ? $lg : ''),
        ]);
        
        if (!$returnValue) {
            $returnValue = false;
        }

        return (bool) $returnValue;
    }

    /**
     * returns the triples having as subject the current resource
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  core_kernel_classes_Resource resource
     * @return core_kernel_classes_ContainerCollection
     */
    public function getRdfTriples(core_kernel_classes_Resource $resource)
    {
        // TODO: refactor this to use a triple store abstraction
        $query = 'SELECT * FROM statements WHERE subject = ? AND ' . $this->getModelReadSqlCondition() . ' ORDER BY predicate';
        $result = $this->getPersistence()->query($query, [$resource->getUri()]);
        
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
     * @param core_kernel_classes_Resource $resource
     * @param core_kernel_classes_Property $property
     * @return array
     * @author Joel Bout, <joel.bout@tudor.lu>
     */
    public function getUsedLanguages(core_kernel_classes_Resource $resource, core_kernel_classes_Property $property)
    {
        $returnValue = [];

        $sqlQuery = 'SELECT l_language FROM statements WHERE subject = ? AND predicate = ? ';
        $sqlResult = $this->getPersistence()->query($sqlQuery, [
            $resource->getUri(),
            $property->getUri(),
        ]);
        while ($row = $sqlResult->fetch()) {
            if (!empty($row['l_language'])) {
                $returnValue[] = $row['l_language'];
            }
        }

        return $returnValue;
    }

    /**
     * Short description of method duplicate
     *
     * @access public
     * @param core_kernel_classes_Resource $resource
     * @param array excludedProperties
     * @return core_kernel_classes_Resource
     * @author Joel Bout, <joel.bout@tudor.lu>
     */
    public function duplicate(core_kernel_classes_Resource $resource, $excludedProperties = [])
    {
        $returnValue = null;
        $newUri = $this->getServiceLocator()->get(UriProvider::SERVICE_ID)->provide();
        $collection = $this->getRdfTriples($resource);

        if ($collection->count() > 0) {
            $user = $this->getServiceLocator()->get(SessionService::SERVICE_ID)->getCurrentUser()->getIdentifier();
            $triples = [];

            foreach ($collection->getIterator() as $triple) {
                if (!in_array($triple->predicate, $excludedProperties)) {
                    $triples[] = core_kernel_classes_Triple::createTriple(
                        $this->getNewTripleModelId(),
                        $newUri,
                        $triple->predicate,
                        ($triple->object == null) ? '' : $triple->object,
                        ($triple->lg == null) ? '' : $triple->lg,
                        (string)$user
                    );
                }
            }

            if (!empty($triples)) {
                $this->getModel()->getRdfInterface()->addTripleCollection($triples);
                $returnValue = $this->getModel()->getResource($newUri);
            }
        }

        return $returnValue;
    }

    /**
     * Short description of method delete
     *
     * @access public
     * @param core_kernel_classes_Resource $resource
     * @param boolean deleteReference
     * @return boolean
     * @author Joel Bout, <joel.bout@tudor.lu>
     */
    public function delete(core_kernel_classes_Resource $resource, $deleteReference = false)
    {
        $query = 'DELETE FROM statements WHERE subject = ? AND ' . $this->getModelWriteSqlCondition();
        $returnValue = $this->getPersistence()->exec($query, [$resource->getUri()]);

        //if no rows affected return false
        if (!$returnValue) {
            $returnValue = false;
        } elseif ($deleteReference) {
            $sqlQuery = 'DELETE FROM statements WHERE ' . $this->getPersistence()->getPlatForm()->getObjectTypeCondition() . ' = ? AND ' . $this->getModelWriteSqlCondition();
            $return = $this->getPersistence()->exec($sqlQuery, [$resource->getUri()]);
            
            if ($return !== false) {
                $returnValue = true;
            }
        }
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method getPropertiesValues
     *
     * @access public
     * @param core_kernel_classes_Resource $resource
     * @param Resource resource
     * @return array
     * @author Joel Bout, <joel.bout@tudor.lu>
     */
    public function getPropertiesValues(core_kernel_classes_Resource $resource, $properties)
    {
        $returnValue = [];

        // check whenever or not properties is empty
        if (count($properties) == 0) {
            return [];
        }
        
        $predicatesQuery = '';
        //build the predicate query
        //$predicatesQuery = implode(',', $properties);
        foreach ($properties as $property) {
            $uri = (is_string($property) ? $property : $property->getUri());
            $returnValue[$uri] = [];
            $predicatesQuery .= ", " . $this->getPersistence()->quote($uri);
        }
        $predicatesQuery = substr($predicatesQuery, 1);

        $platform = $this->getPersistence()->getPlatForm();
        $lang = $this->getDataLanguage();
        $default = $this->getServiceLocator()->get(UserLanguageServiceInterface::SERVICE_ID)->getDefaultLanguage();

        // TODO: refactor this to use a triple store abstraction
        //the unique sql query
        $query =  'SELECT predicate, object, l_language 
            FROM statements 
            WHERE 
                subject = ' . $this->getPersistence()->quote($resource->getUri()) . ' 
                AND predicate IN (' . $predicatesQuery . ')
                AND (l_language = ' . $this->getPersistence()->quote('') .
                    ' OR l_language = ' . $this->getPersistence()->quote($default) .
                    ' OR l_language = ' . $this->getPersistence()->quote($lang) . ')
                AND ' . $this->getModelReadSqlCondition();
        $result = $this->getPersistence()->query($query);
        
        $rows = $result->fetchAll();
        foreach ($rows as $row) {
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
     * @param core_kernel_classes_Resource $resource
     * @param core_kernel_classes_Class $class
     * @return boolean
     * @author Joel Bout, <joel.bout@tudor.lu>
     */
    public function setType(core_kernel_classes_Resource $resource, core_kernel_classes_Class $class)
    {
        return $this->setPropertyValue($resource, $this->getModel()->getProperty(OntologyRdf::RDF_TYPE), $class);
    }

    /**
     * Short description of method removeType
     *
     * @access public
     * @param core_kernel_classes_Resource $resource
     * @param core_kernel_classes_Class $class
     * @return boolean
     * @author Joel Bout, <joel.bout@tudor.lu>
     */
    public function removeType(core_kernel_classes_Resource $resource, core_kernel_classes_Class $class)
    {
        $query =  'DELETE FROM statements 
		    		WHERE subject = ? AND predicate = ? AND ' . $this->getPersistence()->getPlatForm()->getObjectTypeCondition() . ' = ?';
        
        //be sure the property we try to remove is included in an updatable model
        $query .= ' AND ' . $this->getModelWriteSqlCondition();
        
        $returnValue = $this->getPersistence()->exec($query, [
            $resource->getUri(),
            OntologyRdf::RDF_TYPE,
            $class->getUri()
        ]);
        
        $returnValue = true;

        return $returnValue;
    }

    /**
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->getModel()->getServiceLocator();
    }

    /**
     * @param core_kernel_classes_Resource $resource
     * @param array $properties
     * @param string $userIdentifier
     * @param string $dataLanguage
     * @return array
     */
    private function buildTrippleArray(
        core_kernel_classes_Resource $resource,
        array $properties,
        string $userIdentifier,
        string $dataLanguage
    ) {
        $triples = [];
        foreach ($properties as $propertyUri => $value) {
            $property = $this->getModel()->getProperty($propertyUri);

            $lang = ($property->isLgDependent() ? $dataLanguage : '');

            $formattedValues = $this->normalizePropertyValues($value);

            foreach ($formattedValues as $object) {
                $triples[] = core_kernel_classes_Triple::createTriple(
                    $this->getNewTripleModelId(),
                    $resource->getUri(),
                    $property->getUri(),
                    $object,
                    $lang,
                    $userIdentifier
                );
            }
        }

        return $triples;
    }

    /**
     * @param $value
     * @param array $formattedValues
     * @return array
     */
    private function normalizePropertyValues($value)
    {
        $normalizedValues = [];
        if ($value instanceof core_kernel_classes_Resource) {
            $normalizedValues[] = $value->getUri();
        } elseif (is_array($value)) {
            foreach ($value as $val) {
                if ($val !== null) {
                    $normalizedValues[] = $val instanceof core_kernel_classes_Resource
                        ? $val->getUri()
                        : $val;
                }
            }
        } else {
            $normalizedValues[] = ($value == null) ? '' : $value;
        }
        return $normalizedValues;
    }

    /**
     * @return mixed
     */
    private function getDataLanguage()
    {
        return $this->getServiceLocator()->get(SessionService::SERVICE_ID)->getCurrentSession()->getDataLanguage();
    }
}
