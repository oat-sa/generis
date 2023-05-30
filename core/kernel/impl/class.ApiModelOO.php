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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg
 *                         (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung
 *                         (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor
 *                         (under the project TAO-SUSTAIN & TAO-DEV);
 *               2017 (update and modification) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

use oat\generis\model\OntologyRdf;
use oat\generis\model\OntologyRdfs;
use Doctrine\DBAL\DBALException;
use oat\generis\model\data\import\RdfImporter;
use oat\oatbox\log\LoggerAwareTrait;
use oat\oatbox\service\ServiceManager;
use Zend\ServiceManager\ServiceLocatorInterface;

// phpcs:disable PSR1.Files.SideEffects
error_reporting(E_ALL);
// phpcs:enable PSR1.Files.SideEffects

/**
 * Generis Object Oriented API - core\kernel\impl\class.ApiModelOO.php
 *
 * Short description of class core_kernel_impl_ApiModelOO
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 29.03.2010, 15:28:05 with ArgoUML PHP module
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 * @package generis

 */
class core_kernel_impl_ApiModelOO extends core_kernel_impl_Api implements core_kernel_api_ApiModel
{
    use LoggerAwareTrait;

    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute instance
     *
     * @access private
     * @var self
     */
    private static $instance = null;

    /** @var string[] */
    private $namespaces = [];

    /** @var int */
    private $customNamespacesCounter = 0;

    // --- OPERATIONS ---


    /**
     * import xml rdf files into the knowledge base
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  string targetNameSpace
     * @param  string fileLocation
     * @return boolean
     */
    public function importXmlRdf($targetNameSpace, $fileLocation)
    {
        $importer = $this->getServiceLocator()->get(RdfImporter::class);
        return $importer->importFile($fileLocation);
    }



    /**
     * returns an xml rdf serialization for uriResource with all meta dat found
     * inferenced from te knowlege base about this resource
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  string uriResource
     * @return string
     */
    public function getResourceDescriptionXML($uriResource)
    {
        $returnValue = '';

        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
        $subject = $dbWrapper->quote($uriResource);

        try {
            $dom = new DOMDocument();
            $dom->formatOutput = true;
            $root = $dom->createElement('rdf:RDF');

            $this->addCoreNamespaces($root);
            $dom->appendChild($root);

            $description = $dom->createElement('rdf:Description');
            $description->setAttribute('rdf:about', $uriResource);

            $result = $dbWrapper->query('SELECT * FROM "statements" WHERE "subject" = ' . $subject);
            while ($row = $result->fetch()) {
                $predicate  = trim($row['predicate']);
                $object     = trim($row['object']);
                $lang       = trim($row['l_language']);

                if (strpos($predicate, '#') === false) {
                    continue;
                }

                [$namespace, $property] = explode('#', $predicate, 2);

                $namespaceId = $this->addCustomNamespace($root, $namespace);
                $nodeName    = "$namespaceId:$property";

                try {
                    $node = $dom->createElement($nodeName);
                    if (!empty($lang)) {
                        $node->setAttribute('xml:lang', $lang);
                    }

                    if (preg_match("/^http:\/\/(.*)#[a-zA-Z1-9]*/", $object)) {
                        $node->setAttribute('rdf:resource', $object);
                    } elseif (!empty($object)) {

                        /**
                         * Replace the CDATA section inside XML fields by a replacement tag:
                         * <![CDATA[ ]]> to <CDATA></CDATA>
                         * @todo check if this behavior is the right
                         */
                        $object = str_replace(['<![CDATA[', ']]>'], ['<CDATA>', '</CDATA>'], $object);

                        $node->appendChild($dom->createCDATASection($object));
                    }
                    $description->appendChild($node);
                } catch (DOMException $exception) {
                    $this->logCritical($exception->getMessage(), ['exception' => $exception]);
                }
            }
            $root->appendChild($description);
            $returnValue = $dom->saveXml();
        } catch (DomException $exception) {
            $this->logError($exception->getMessage(), ['exception' => $exception]);
            print $exception;
        }

        return (string) $returnValue;
    }

    /**
     * returns metaclasses tat are not subclasses of other metaclasses
     *
     * @access public
     * @author patrick.plichart@tudor.lu
     * @return core_kernel_classes_ContainerCollection
     */
    public function getMetaClasses()
    {
        $returnValue = $this->createClassCollection(__METHOD__);

        $classClass = new core_kernel_classes_Class(OntologyRdfs::RDFS_CLASS);
        foreach ($classClass->getSubClasses(true) as $subClass) {
            $returnValue->add($subClass);
        }


        return $returnValue;
    }

    /**
     * returns classes that are not subclasses of other classes
     *
     * @access public
     * @author patrick.plichart@tudor.lu
     * @return core_kernel_classes_ContainerCollection
     */
    public function getRootClasses()
    {
        $returnValue = $this->createClassCollection(__METHOD__);

        $query =  'SELECT DISTINCT subject FROM statements WHERE (predicate = ? AND object = ?) 
        			AND subject NOT IN (SELECT subject FROM statements WHERE predicate = ?)';
        $result = core_kernel_classes_DbWrapper::singleton()->query($query, [
            OntologyRdf::RDF_TYPE,
            OntologyRdfs::RDFS_CLASS,
            OntologyRdfs::RDFS_SUBCLASSOF
        ]);

        while ($row = $result->fetch()) {
            $returnValue->add(new core_kernel_classes_Class($row['subject']));
        }



        return $returnValue;
    }

    /**
     * add a new statment to the knowledge base
     *
     * @access public
     * @author patrick.plichart@tudor.lu
     * @param  string subject
     * @param  string predicate
     * @param  string object
     * @param  string language
     * @return boolean
     */
    public function setStatement($subject, $predicate, $object, $language)
    {
        $returnValue = (bool) false;


        $dbWrapper  = core_kernel_classes_DbWrapper::singleton();
        $platform   = $dbWrapper->getPlatForm();
        $localNs    = common_ext_NamespaceManager::singleton()->getLocalNamespace();
        $query = 'INSERT INTO statements (modelid,subject,predicate,object,l_language,author,epoch)
        			VALUES  (?, ?, ?, ?, ?, ? , ?);';

        try {
            $returnValue = $dbWrapper->exec($query, [
                $localNs->getModelId(),
                $subject,
                $predicate,
                $object,
                $language,
                common_session_SessionManager::getSession()->getUserUri(),
                $platform->getNowExpression()

            ]);
        } catch (DBALException $e) {
            if ($e->getCode() !== '00000') {
                throw new common_Exception(
                    "Unable to setStatement (SPO) {$subject}, {$predicate}, {$object} : " . $e->getMessage()
                );
            }
        }



        return (bool) $returnValue;
    }

    /**
     * Short description of method getAllClasses
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return core_kernel_classes_ContainerCollection
     */
    public function getAllClasses()
    {
        $returnValue = $this->createClassCollection(__METHOD__);

        $query =  'SELECT DISTINCT subject FROM statements WHERE (predicate = ? AND object = ?) OR predicate = ?';
        $result = core_kernel_classes_DbWrapper::singleton()->query($query, [
            OntologyRdf::RDF_TYPE,
            OntologyRdfs::RDFS_CLASS,
            OntologyRdfs::RDFS_SUBCLASSOF
        ]);

        while ($row = $result->fetch()) {
            $returnValue->add(new core_kernel_classes_Class($row['subject']));
        }



        return $returnValue;
    }

    /**
     * Short description of method getSubject
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  string predicate
     * @param  string object
     * @return core_kernel_classes_ContainerCollection
     */
    public function getSubject($predicate, $object)
    {
        $sqlQuery = "SELECT subject FROM statements WHERE predicate = ? AND object= ? ";
        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
        $sqlResult = $dbWrapper->query($sqlQuery, [
            $predicate,
            $object
        ]);
        $returnValue = new core_kernel_classes_ContainerCollection(new common_Object());
        while ($row = $sqlResult->fetch()) {
            $container = new core_kernel_classes_Resource($row['subject'], __METHOD__);
            $container->debug = __METHOD__ ;
            $returnValue->add($container);
        }

        return $returnValue;
    }

    /**
     * Short description of method removeStatement
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  string subject
     * @param  string predicate
     * @param  string object
     * @param  string language
     * @return boolean
     */
    public function removeStatement($subject, $predicate, $object, $language)
    {
        $dbWrapper  = core_kernel_classes_DbWrapper::singleton();

        $query = "DELETE FROM statements WHERE subject = ?
        			AND predicate = ? AND object = ?
        			AND (l_language = ? OR l_language = '')";

        $returnValue = $dbWrapper->exec($query, [
            $subject,
            $predicate,
            $object,
            $language
        ]);

        return (bool) $returnValue;
    }

    /**
     * Short description of method getObject
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  string subject
     * @param  string predicate
     * @return core_kernel_classes_ContainerCollection
     */
    public function getObject($subject, $predicate)
    {
        $sqlQuery = "SELECT object FROM statements WHERE subject = ? AND predicate = ?";
        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
        $sqlResult = $dbWrapper->query($sqlQuery, [
            $subject,
            $predicate
        ]);
        $returnValue = new core_kernel_classes_ContainerCollection(new common_Object());
        while ($row = $sqlResult->fetch()) {
            $value = $row['object'];
            if (!common_Utils::isUri($value)) {
                $container = new core_kernel_classes_Literal($value);
            } else {
                $container = new core_kernel_classes_Resource($value);
            }
            $container->debug = __METHOD__ ;
            $returnValue->add($container);
        }


        return $returnValue;
    }

    /**
     * Short description of method singleton
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return core_kernel_impl_ApiModelOO
     */
    public static function singleton()
    {
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c();
        }

        return self::$instance;
    }

    /**
     * Short description of method __construct
     *
     * @access private
     * @author firstname and lastname of author, <author@example.org>
     * @return void
     */
    private function __construct()
    {
    }

    /**
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return ServiceManager::getServiceManager();
    }

    private function createClassCollection(string $debug = ''): core_kernel_classes_ContainerCollection
    {
        return new core_kernel_classes_ContainerCollection(new core_kernel_classes_Container(), $debug);
    }

    private function addCoreNamespaces(DOMElement $root): void
    {
        $this->namespaces = [
            'http://www.w3.org/1999/02/22-rdf-syntax-ns' => 'rdf',
            'http://www.w3.org/2000/01/rdf-schema'       => 'rdfs',
        ];

        foreach ($this->namespaces as $namespace => $namespaceId) {
            $this->addNamespace($root, $namespaceId, $namespace);
        }

        $this->customNamespacesCounter = 0;
        $this->addCustomNamespace($root, LOCAL_NAMESPACE);
    }

    /**
     * @param DOMElement $root
     * @param string $namespace
     *
     * @return string|null A namespace ID
     */
    private function addCustomNamespace(DOMElement $root, string $namespace): string
    {
        if (!isset($this->namespaces[$namespace])) {
            $namespaceId = sprintf('ns%u', ++$this->customNamespacesCounter);
            $this->namespaces[$namespace] = $namespaceId;
            $this->addNamespace($root, $namespaceId, $namespace);
        }

        return $this->namespaces[$namespace];
    }

    private function addNamespace(DOMElement $root, string $namespaceId, string $namespace): void
    {
        $root->setAttribute("xmlns:$namespaceId", "$namespace#");
    }
}
