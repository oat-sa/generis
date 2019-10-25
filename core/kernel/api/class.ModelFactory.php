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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author "Lionel Lecaque, <lionel@taotesting.com>"
 * @license GPLv2
 * @package generis
 
 *
 */

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;
use common_persistence_SqlPersistence as Persistence;
use oat\generis\persistence\PersistenceManager;
use oat\oatbox\log\LoggerAwareTrait;
use oat\oatbox\service\ConfigurableService;

abstract class core_kernel_api_ModelFactory extends ConfigurableService {

    use LoggerAwareTrait;
        
    const SERVICE_ID = 'generis/modelFactory';
    const OPTION_PERSISTENCE = 'persistence';
    const DEFAULT_AUTHOR = 'http://www.tao.lu/Ontologies/TAO.rdf#installator';

    /**
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param string $namespace
     * @return string
     */
    public function getModelId($namespace){
        if (substr($namespace, -1) !== '#') {
            $namespace .= '#';
        }
        
        $query = 'SELECT modelid FROM models WHERE (modeluri = ?)';
        $results = $this->getPersistence()->query($query, array($namespace));
       
        return $results->fetchColumn(0);

    }
    
    /**
     * Creates a new model in the ontology.
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param string $namespace
     * @return string|int new added model id
     * @throws RuntimeException when a problem occurs when creating the new model in db.
     */
    abstract public function addNewModel($namespace);
    
    /**
     * Creates a new model.
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param string $namespace
     * @param string $data xml content
     * @return bool Were triples added?
     */
    public function createModel($namespace, $data){

        $modelId = $this->getModelId($namespace);
        if($modelId === false){
            // $this->logInfo('modelId not found, need to add namespace '. $namespace);
            $modelId = $this->addNewModel($namespace);
        }
        $modelDefinition = new EasyRdf_Graph($namespace);
        if(is_file($data)){
            $modelDefinition->parseFile($data);
        }else {
            $modelDefinition->parse($data);
        }
        $graph = $modelDefinition->toRdfPhp();
        $resources = $modelDefinition->resources();
        $format = EasyRdf_Format::getFormat('php');
        
        $data = $modelDefinition->serialise($format);
        
        $returnValue = false;
        foreach ($data as $subjectUri => $propertiesValues){
            foreach ($propertiesValues as $prop=>$values){
                foreach ($values as $k => $v) {
                    $returnValue |= $this->addStatement($modelId, $subjectUri, $prop, $v['value'], $v['lang'] ?? null);
                }
            }
        }
        
        return $returnValue;
    }
    
    /**
     * Adds a statement to the ontology if it does not exist yet
     * 
     * @author "Joel Bout, <joel@taotesting.com>"
     * @param string|int $modelId
     * @param string $subject
     * @param string $predicate
     * @param string $object
     * @param string $lang
     * @param string $author
     * @return bool Was a row added?
     */
    public function addStatement($modelId, $subject, $predicate, $object, $lang = null, $author = self::DEFAULT_AUTHOR) {
        // Casting values and types.
        $object = (string)$object;
        if (is_null($lang)) {
            $lang = '';
        }
        
        // TODO: refactor this to use a triple store abstraction.
        $result = $this->getPersistence()->query(
            'SELECT count(*) FROM statements WHERE modelid = ? AND subject = ? AND predicate = ? AND object = ? AND l_language = ?',
            array($modelId, $subject, $predicate, $object, $lang)
        );
        
        if ((int)$result->fetchColumn() > 0) {
            return false;
        }
        
        return (bool)$this->getPersistence()->insert(
            'statements',
            $this->prepareStatement($modelId, $subject, $predicate, $object, $lang, is_null($author) ? '' : $author)
        );
    }

    /**
     * Prepares a statement to be inserted in the ontology.
     * @param string|int $modelId
     * @param string     $subject
     * @param string     $predicate
     * @param string     $object
     * @param string     $lang
     * @param string     $author
     * @return array
     */
    abstract public function prepareStatement($modelId, $subject, $predicate, $object, $lang, $author);

    /**
     * Creates a query for iterator on selected statements.
     * @param $modelIds
     * @return string
     */
    public function getIteratorQuery($modelIds)
    {
        $where = '';
        if ($modelIds !== null) {
            $where = 'WHERE ' . $this->buildModelSqlCondition($modelIds);
        }
        return sprintf('SELECT * FROM statements %s ORDER BY %s', $where, $this->getPropertySortingField());
    }
    
    /**
     * Prepares parameters for statement selection.
     * @param array $models
     *
     * @return string
     */
    public function buildModelSqlCondition(array $models)
    {
        return 'modelid IN (' . implode(',', $models) . ')';
    }
    
    /**
     * Returns the property to sort the statements on.
     * @return string
     */
    abstract public function getPropertySortingField();
    /**
     * Creates table schema for models.
     *
     * @param Schema $schema
     *
     * @return Table
     */
    abstract public function createModelsTable(Schema $schema);
    /**
     * Creates table schema for statements.
     *
     * @param Schema $schema
     *
     * @return Table
     */
    abstract public function createStatementsTable(Schema $schema);
    /**
     * @return Persistence
     */
    public function getPersistence()
    {
        $persistenceId = $this->hasOption(self::OPTION_PERSISTENCE) ?
            $this->getOption(self::OPTION_PERSISTENCE)
            : 'default';
        return $this->getServiceLocator()->get(PersistenceManager::SERVICE_ID)->getPersistenceById($persistenceId);
    }
}
