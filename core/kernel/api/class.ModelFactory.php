<?php

use oat\generis\model\data\ModelIdManagerInterface;

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
class core_kernel_api_ModelFactory
{
    /**
     * @var ModelIdManagerInterface
     */
    private $modelIdManager;

    /**
     * @param ModelIdManagerInterface $modelIdManager
     */
    public function __construct(ModelIdManagerInterface $modelIdManager)
    {
        $this->modelIdManager = $modelIdManager;
    }

    /**
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     *
     * @param string $namespace
     * @param string $data xml content
     *
     * @return bool
     *
     * @throws EasyRdf_Exception
     * @throws core_kernel_persistence_Exception
     */
    public function createModel($namespace, $data)
    {
        $modelId = $this->modelIdManager->getUserModelId();

        $modelDefinition = new EasyRdf_Graph($namespace);
        if (is_file($data)) {
            $modelDefinition->parseFile($data);
        } else {
            $modelDefinition->parse($data);
        }
        $graph = $modelDefinition->toRdfPhp();
        $resources = $modelDefinition->resources();
        $format = EasyRdf_Format::getFormat('php');

        $data = $modelDefinition->serialise($format);

        foreach ($data as $subjectUri => $propertiesValues) {
            foreach ($propertiesValues as $prop => $values) {
                foreach ($values as $k => $v) {
                    $this->addStatement(
                        $modelId,
                        $subjectUri,
                        $prop,
                        $v['value'],
                        isset($v['lang']) ? $v['lang'] : null
                    );
                }
            }
        }

        return true;
    }

    /**
     * Adds a statement to the ontology if it does not exist yet
     *
     * @author "Joel Bout, <joel@taotesting.com>"
     * @param int $modelId
     * @param string $subject
     * @param string $predicate
     * @param string $object
     * @param string $lang
     *
     * @throws core_kernel_persistence_Exception
     */
    private function addStatement($modelId, $subject, $predicate, $object, $lang = null)
    {
        $result = core_kernel_classes_DbWrapper::singleton()->query(
            'SELECT count(*) FROM statements WHERE modelid = ? AND subject = ? AND predicate = ? AND object = ? AND l_language = ?',
            array($modelId, $subject, $predicate, $object, (is_null($lang)) ? '' : $lang)
        );
        
        if (intval($result->fetchColumn()) === 0) {
            $dbWrapper = core_kernel_classes_DbWrapper::singleton();
            $date = $dbWrapper->getPlatForm()->getNowExpression();

            $dbWrapper->insert(
                'statements',
                array(
                    'modelid' =>  $modelId,
                    'subject' =>$subject,
                    'predicate'=> $predicate,
                    'object' => $object,
                    'l_language' => is_null($lang) ? '' : $lang,
                    'author' => 'http://www.tao.lu/Ontologies/TAO.rdf#installator',
                    'epoch' => $date
                )
            );
        }
    }
}
